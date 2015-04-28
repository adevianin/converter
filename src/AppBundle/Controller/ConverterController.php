<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class ConverterController extends Controller
{
    const UPLOAD_DIRECTORY = '/temp';
    const RESULT_DIRECTORY = 'results';

    public function uploadAction(Request $request)
    {
        $format = $request->query->get('format');
        $file = $request->files->get('audio');

        $validationErrors = $this->get('app.uploaded.file.validator')->validate($file);
        $validationErrors = array_merge($validationErrors, $this->get('app.format.validator')->validate($format));

        $statusCode = 400;
        $data = $validationErrors;

        if (count($validationErrors) == 0) {
            $fileName = uniqid();
            $file->move($this->getUploadPath(), $fileName);
            $tempFilePath = $this->getUploadPath().'/'.$fileName;
            $this->get('app.converter.api')->addConvertTask(
                file_get_contents($tempFilePath),
                $fileName,
                $format
            );

            unlink($tempFilePath);

            $statusCode = 200;
            $data = [
                'progressCheck' => $this->get('router')->generate('status', array('uid' => $fileName)),
                'download' => $this->get('router')->generate('download', array('fileName' => $fileName.'.'.$format)),
            ];
        }

        return new JsonResponse($data, $statusCode);
    }

    public function getStatusAction($uid)
    {
        $convStatus = $this->get('app.converter.api')->getConvertStatus($uid);

        return new JsonResponse($convStatus, 200);
    }

    public function getConvertedFileAction($fileName)
    {
        $filePath = $this->getResultsDirPath().'/'.$fileName;
        if (!file_exists($filePath)) {
            throw new NotFoundHttpException();
        }

        $resp = new Response(file_get_contents($filePath), 200);
        $resp->headers->set('Content-Type', mime_content_type($filePath));
        $resp->headers->set('Content-Disposition', 'attachment;');

        return $resp;
    }

    private function getUploadPath()
    {
        return __DIR__.'/../../..'.self::UPLOAD_DIRECTORY;
    }

    private function getResultsDirPath()
    {
        return __DIR__.'/../../../'.self::RESULT_DIRECTORY;
    }
}
