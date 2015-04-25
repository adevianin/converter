<?php

namespace AppBundle\Services;


class ConverterApi
{
    private $converter;

    public function __construct($converter)
    {
        $this->converter = $converter;
    }

    public function addConvertTask($fileContent, $fileName, $format)
    {
        $this->converter->publish(serialize(array(
            'fileContent' => $fileContent,
            'fileName' => $fileName,
            'format' => $format,
        )));
    }

    public function getConvertStatus($id)
    {
    }

    public function getConvertedFile($id)
    {
    }
}
