<?php

namespace Snovio\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class AmqpConnection implements AmqpConnectionInterface
{
    /**
     * @var AMQPStreamConnection
     */
    protected $connection;

    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var AmqpConnectionOptions
     */
    protected $connectionOptions;

    /**
     * RabbitMQConnectionService constructor.
     */
    public function __construct()
    {
        $this->connectionOptions = $this->setConnectionOptions();
        $this->connect();
    }

    protected function connect(): void
    {
        $this->connection = new AMQPStreamConnection(
            $this->connectionOptions->getHost(),
            $this->connectionOptions->getPort(),
            $this->connectionOptions->getLogin(),
            $this->connectionOptions->getPassword()
        );

        $this->channel = $this->connection->channel();
    }

    /**
     * @return AMQPChannel
     */
    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    abstract protected function setConnectionOptions(): AmqpConnectionOptions;
}
