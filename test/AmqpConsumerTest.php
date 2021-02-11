<?php

namespace Snovio\Amqp\Test;

use PhpAmqpLib\Channel\AMQPChannel;
use PHPUnit\Framework\TestCase;
use Snovio\Amqp\AmqpConnectionInterface;
use Snovio\Amqp\AmqpConsumer;

class AmqpConsumerTest extends TestCase
{
    /**
     * @dataProvider publishDataProvider
     * @param array $data
     */
    public function testConsume(array $data): void
    {
        $AMQPChannelMock = $this->createMock(AMQPChannel::class);
        $AMQPConnectionMock = $this->createMock(AmqpConnectionInterface::class);

        $AMQPConnectionMock
            ->expects(self::atLeast(4))
            ->method('getChannel')
            ->willReturn($AMQPChannelMock);

        $AMQPChannelMock
            ->expects(self::once())
            ->method('queue_declare');

        $AMQPChannelMock
            ->expects(self::once())
            ->method('basic_qos');

        $AMQPChannelMock
            ->expects(self::once())
            ->method('basic_consume');

        $baseConsumerMock = $this
            ->getMockBuilder(AmqpConsumer::class)
            ->setConstructorArgs([$AMQPConnectionMock])
            ->getMockForAbstractClass();

        $AMQPChannelMock->callbacks = $data['messages'];
        $AMQPChannelMock
            ->expects(self::exactly(count($data['messages'])))
            ->method('wait')
            ->with(null, false, 0)
            ->willReturnCallback(
                static function () use ($AMQPChannelMock) {
                    /** remove an element on each loop like ... simulate an ACK */
                    array_splice($AMQPChannelMock->callbacks, 0, 1);
                }
            );

        $callback = static function ($params) {
            return $params;
        };

        $baseConsumerMock->consume('test_queue', $callback);
    }

    /**
     * @return array
     */
    public function publishDataProvider(): array
    {
        $testCases['All ok 4 callbacks'] = [
            [
                'messages' => [
                    'msgCallback1',
                    'msgCallback2',
                    'msgCallback3',
                    'msgCallback4',
                ]
            ]
        ];

        $testCases['No callbacks'] = [
            [
                'messages' => []
            ]
        ];

        return $testCases;
    }
}
