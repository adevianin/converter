<?php

namespace AppBundle\Tests\Services;

use AppBundle\Services\ConverterApi;
use PhpAmqpLib\Message\AMQPMessage;

class ConverterApiTest extends \PHPUnit_Framework_TestCase
{
    public function testAddConvertTask()
    {
        $msg = new AMQPMessage();

        $channel = $this->getMockBuilder('PhpAmqpLib\Channel\AMQPChannel')
            ->disableOriginalConstructor()
            ->getMock();
        $channel->expects($this->once())->method('queue_declare')->with(ConverterApi::CONVERT_TASK_QUEUE_NAME);
        $channel->expects($this->once())->method('basic_publish')->with($msg, '', ConverterApi::CONVERT_TASK_QUEUE_NAME);
        $channel->expects($this->once())->method('close');

        $connection = $this->getMockBuilder('PhpAmqpLib\Connection\AMQPConnection')
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->any())->method('channel')->will($this->returnValue($channel));

        $converter = new ConverterApi();
        $converter->setConnection($connection);

        $converter->addConvertTask($msg);
    }
}
