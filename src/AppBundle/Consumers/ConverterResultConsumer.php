<?php

namespace AppBundle\Consumers;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ConverterResultConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg)
    {
        $data = unserialize($msg->body);

        $fileName = $data['fileName'].'.'.$data['format'];
        file_put_contents(__DIR__.'/../../../results/'.$fileName, $data['fileContent']);
    }
}
