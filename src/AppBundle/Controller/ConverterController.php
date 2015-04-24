<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ConverterController extends Controller
{
    const UPLOAD_DIRECTORY = '/uploads';

    public function uploadAction(Request $request)
    {
        $file = $request->files->get('audio');

        $validationErrors = $this->get('app.uploaded.file.validator')->validate($file);
        $statusCode = 400;
        $data = $validationErrors;

        if (count($validationErrors) == 0) {
            $file->move($this->getUploadPath(), uniqid());
            $statusCode = 200;
            $data = null;
        }

        return new JsonResponse($data, $statusCode);
    }

    private function getUploadPath()
    {
        return __DIR__.'/../../../web'.self::UPLOAD_DIRECTORY;
    }
}
