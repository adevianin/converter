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

        $statusRpc = $this->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\RpcClient')
            ->disableOriginalConstructor()
            ->getMock();

        $converterProducer = $this->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();
        $converterProducer->expects($this->once())->method('publish')->with(serialize(array(
            'fileContent' => $fileContent,
            'fileName' => $fileName,
            'format' => $format,
        )));

        $converterApi = new ConverterApi($converterProducer, $statusRpc);
        $converterApi->addConvertTask($fileContent, $fileName, $format);
    }

    public function testGetConvertStatus()
    {
        $uid = '553d505302270';
        $expStatus = 55;

        $statusRpc = $this->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\RpcClient')
            ->disableOriginalConstructor()
            ->getMock();
        $statusRpc->expects($this->once())->method('addRequest')->with(serialize($uid), 'converting_status', 'convert_status');
        $statusRpc->expects($this->once())->method('getReplies')->will($this->returnValue(['convert_status' => $expStatus]));

        $converterProducer = $this->getMockBuilder('OldSound\RabbitMqBundle\RabbitMq\Producer')
            ->disableOriginalConstructor()
            ->getMock();

        $converterApi = new ConverterApi($converterProducer, $statusRpc);
        $actualStatus = $converterApi->getConvertStatus($uid);

        $this->assertEquals($expStatus, $actualStatus);
    }
}
