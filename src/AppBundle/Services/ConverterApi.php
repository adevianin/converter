<?php

namespace AppBundle\Services;

class ConverterApi
{
    private $converter;
    private $statusRpc;

    public function __construct($converter, $statusRpc)
    {
        $this->converter = $converter;
        $this->statusRpc = $statusRpc;
    }

    public function addConvertTask($fileContent, $fileName, $format)
    {
        $this->converter->publish(serialize(array(
            'fileContent' => $fileContent,
            'fileName' => $fileName,
            'format' => $format,
        )));
    }

    public function getConvertStatus($uid)
    {
        $this->statusRpc->addRequest(serialize($uid), 'converting_status', 'convert_status');
        $replies = $this->statusRpc->getReplies();

        return $replies['convert_status'];
    }
}
