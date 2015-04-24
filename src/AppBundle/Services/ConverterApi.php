<?php

namespace AppBundle\Services;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConverterApi
{
    const CONVERT_TASK_QUEUE_NAME = 'tasks';
    private $connection;

    public function setConnection(AMQPConnection $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function addConvertTask(AMQPMessage $msg)
    {
        $channel = $this->connection->channel();
        $channel->queue_declare(self::CONVERT_TASK_QUEUE_NAME, false, false, false, false);
        $channel->basic_publish($msg, '', self::CONVERT_TASK_QUEUE_NAME);
        $channel->close();
    }

    public function getConvertStatus($id)
    {
    }

    public function getConvertedFile($id)
    {
    }
}
