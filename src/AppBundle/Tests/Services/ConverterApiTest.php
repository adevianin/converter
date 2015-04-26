<?php

namespace AppBundle\Tests\Services;

use AppBundle\Services\ConverterApi;

class ConverterApiTest extends \PHPUnit_Framework_TestCase
{
    public function testAddConvertTask()
    {
        $fileContent = 'content';
        $fileName = 'filename';
        $format = 'mp3';

        $converterProducer = $this->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();
        $converterProducer->expects($this->once())->method('publish')->with(serialize(array(
            'fileContent' => $fileContent,
            'fileName' => $fileName,
            'format' => $format,
        )));

        $converterApi = new ConverterApi($converterProducer);
        $converterApi->addConvertTask($fileContent, $fileName, $format);
    }
}
