<?php

namespace Snovio\Amqp;

use PhpAmqpLib\Channel\AMQPChannel;

interface AmqpConnectionInterface
{
    public function getChannel(): AMQPChannel;
}
