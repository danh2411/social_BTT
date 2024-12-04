<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST'),
            env('RABBITMQ_PORT'),
            env('RABBITMQ_USER'),
            env('RABBITMQ_PASSWORD')
        );
        $this->channel = $this->connection->channel();
    }

    /**
     * Publish a message to a RabbitMQ queue.
     *
     * @param string $queue
     * @param array|string $data
     * @param array $options
     */
    public function publish(string $queue, array|string $data)
    {
        $this->getChannel()->queue_declare($queue, false, true, false, false);

        $messageBody = is_array($data) ? json_encode($data) : $data;

        $message = new AMQPMessage(
            $messageBody,
            ['content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $this->getChannel()->basic_publish($message, '', $queue);
    }


    /**
     * Consume messages from a RabbitMQ queue.
     *
     * @param string $queue
     * @param callable $callback
     */
    public function consume(string $queue, callable $callback)
    {
        // Declare queue if it does not exist
        $this->channel->queue_declare($queue, false, true, false, false);

        // Set up the consumer
        $this->channel->basic_consume($queue, '', false, true, false, false, $callback);

        // Keep listening for messages
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
    /**
     * Lấy channel. Tạo mới nếu chưa được khởi tạo.
     *
     * @return \PhpAmqpLib\Channel\AMQPChannel
     */
    public function getChannel()
    {
        if (!$this->channel) {
            $this->channel = $this->connection->channel();
        }

        return $this->channel;
    }

    /**
     * Close the connection and channel.
     */
    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
