<?php

namespace Snovio\Amqp;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Closure;

class AmqpConsumer
{
    /**
     * @var AmqpConnectionInterface
     */
    protected $AMQPConnection;

    /**
     * RabbitMQModel constructor.
     * @param AmqpConnectionInterface $AMQPConnection
     */
    public function __construct(AmqpConnectionInterface $AMQPConnection)
    {
        gc_enable();

        $this->AMQPConnection = $AMQPConnection;
    }

    /**
     * @param string $queue
     * @param Closure $closure
     * @param array $properties
     * @throws \ErrorException
     */
    public function consume(string $queue, Closure $closure, array $properties = []): void
    {
        $table = null;
        if (!empty($properties['priority'])) {
            $table = new AMQPTable();
            $table->set('x-max-priority', $properties['priority']);
        }

        $this->AMQPConnection->getChannel()->queue_declare(
            $queue, #queue
            false,  #passive
            true,   #durable, make sure that RabbitMQ will never lose our queue if a crash occurs
            false,  #exclusive - queues may only be accessed by the current connection
            false,  #auto delete - the queue is deleted when all consumers have finished using it
            false,
            $table
        );

        $this->AMQPConnection->getChannel()->basic_qos(
            null,   #prefetch size - prefetch window size in octets, null meaning "no specific limit"
            1,      #prefetch count - prefetch window in terms of whole messages
            null    #global - global=null to mean that the QoS settings should apply per-consumer, global=true to mean that the QoS settings should apply per-channel
        );

        $this->AMQPConnection->getChannel()->basic_consume(
            $queue, #queue
            '',     #consumer tag - Identifier for the consumer, valid within the current channel. just string
            false,  #no local - TRUE: the server will not send messages to the connection that published them
            false,  #no ack, false - acks turned on, true - off. send a proper acknowledgment from the worker, once we're done with a task
            false,  #exclusive - queues may only be accessed by the current connection
            false,  #no wait - TRUE: the server will not respond to the method. The client should not wait for a reply method
            function (AMQPMessage $message) use ($closure) {
                $closure($message);

                gc_collect_cycles();
                gc_mem_caches();
            }  #callback
        );

        while (count($this->AMQPConnection->getChannel()->callbacks)) {
            $this->AMQPConnection->getChannel()->wait();
        }
    }
}
