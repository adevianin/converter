<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedFileValidator
{
    const MAX_FILE_SIZE = 50000000;

    private $supportedMimeTypes;
    
    public function __construct($supportedMimeTypes)
	{
		$this->supportedMimeTypes = $supportedMimeTypes;
	}

    public function validate(UploadedFile $file)
    {
        $errors = [];//errors array passed to validate methods by reference
        $this->validSize($file, $errors);
        $this->validMimeType($file, $errors);

        return $errors;
    }

    private function validSize(UploadedFile $file, &$errors)
    {
        if ($file->getClientSize() > self::MAX_FILE_SIZE) {
            $errors[] = 'file size must be less than '.self::MAX_FILE_SIZE.'kb';
        }
    }

    private function validMimeType(UploadedFile $file, &$errors)
    {
        if (!in_array($file->getMimeType(), $this->supportedMimeTypes)) {
            $errors[] = 'supported only '.implode(', ', $this->supportedMimeTypes).' types. given '.$file->getMimeType();
        }
    }
}
