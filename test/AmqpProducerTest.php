<?php

namespace Snovio\Amqp\Test;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;
use Snovio\Amqp\AmqpConnectionInterface;
use Snovio\Amqp\AmqpProducer;

class AmqpProducerTest extends TestCase
{
    /**
     * @dataProvider publishDataProvider
     * @param array $message
     * @param string $queue
     * @param int $priority
     */
    public function testPublish(array $message, string $queue, int $priority): void
    {
        $AMQPChannelMock = $this->createMock(AMQPChannel::class);
        $AMQPConnectionMock = $this->createMock(AmqpConnectionInterface::class);

        $AMQPConnectionMock
            ->expects(self::once())
            ->method('getChannel')
            ->willReturn($AMQPChannelMock);

        $msgAttr = [];
        if ($priority) {
            $msgAttr['priority'] = $priority;
        }
        $msg = new AMQPMessage(json_encode($message, JSON_THROW_ON_ERROR), $msgAttr);
        $AMQPChannelMock
            ->expects(self::once())
            ->method('basic_publish')
            ->with($msg, '', 'test_queue');

        $producer = new AmqpProducer($AMQPConnectionMock);
        $producer->publish($message, $queue, $priority);
    }

    /**
     * @return array
     */
    public function publishDataProvider(): array
    {
        $testCases['without priority'] = [
            'message' => ['test'],
            'queue' => 'test_queue',
            'priority' => 0
        ];

        $testCases['with priority'] = [
            'message' => ['test'],
            'queue' => 'test_queue',
            'priority' => 5
        ];

        return $testCases;
    }
}
