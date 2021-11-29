<?php

namespace Snovio\Amqp;

use PhpAmqpLib\Message\AMQPMessage;

class AmqpProducer
{
    /**
     * @var AmqpConnectionInterface
     */
    protected $AMQPConnection;

    /**
     * @param AmqpConnectionInterface $AMQPConnection
     */
    public function __construct(AmqpConnectionInterface $AMQPConnection)
    {
        $this->AMQPConnection = $AMQPConnection;
    }

    /**
     * @param string $message
     * @param string $queue
     * @param int $priority
     */
    public function publish(string $message, string $queue, int $priority = 0): void
    {
        $msgAttr = [];
        if ($priority !== 0) {
            $msgAttr['priority'] = $priority;
        }

        $this->AMQPConnection->getChannel()->basic_publish(new AMQPMessage($message, $msgAttr), '', $queue);
    }

    /**
     * @param string $queue
     * @return int
     */
    public function getQueuedMessagesCount(string $queue): int
    {
        $queueInfo = $this->AMQPConnection->getChannel()->queue_declare($queue, true);
        return $queueInfo[1] ?? 0;
    }
}
