<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConverterController extends Controller
{
    const UPLOAD_DIRECTORY = '/temp';

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
            $tempFilePath = $this->getUploadPath().'/'.$fileName;
            $this->get('app.converter.api')->addConvertTask(
                file_get_contents($tempFilePath),
                $fileName,
                $format
            );

            unlink($tempFilePath);

            $statusCode = 200;
            $data = null;
        }

        return new JsonResponse($data, $statusCode);
    }

    public function getStatusAction($uid)
    {
        $convStatus = $this->get('app.converter.api')->getConvertStatus($uid);

        return new JsonResponse($convStatus, 200);
    }

    private function getUploadPath()
    {
        return __DIR__.'/../../..'.self::UPLOAD_DIRECTORY;
    }
}
