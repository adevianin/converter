<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConverterController extends Controller
{
    const UPLOAD_DIRECTORY = '/uploads';

    public function uploadAction(Request $request)
    {
        $format = $request->query->get('format');
        $file = $request->files->get('audio');

        $validationErrors = $this->get('app.uploaded.file.validator')->validate($file);
        $statusCode = 400;
        $data = $validationErrors;

        if (!$format) {
            $data[] = 'format parameters is required';
        }

        if (count($validationErrors) == 0 && $format) {
            $fileName = uniqid();
            $file->move($this->getUploadPath(), $fileName);
            $this->addConvertTask($fileName, $format);

            $statusCode = 200;
            $data = null;
        }

        return new JsonResponse($data, $statusCode);
    }

    private function getUploadPath()
    {
        return __DIR__.'/../../../web'.self::UPLOAD_DIRECTORY;
    }

    private function addConvertTask($fileName, $format)
    {
        $connection = new AMQPConnection(
            $this->container->getParameter('rabbit_server_ip'),
            $this->container->getParameter('rabbit_port'),
            $this->container->getParameter('rabbit_login'),
            $this->container->getParameter('rabbit_pass')
        );
        $converter = $this->get('app.converter.api');
        $converter->setConnection($connection);
        $converter->addConvertTask(new AMQPMessage(serialize(array('fileName' => $fileName, 'format' => $format))));
        $connection->close();
    }
}
