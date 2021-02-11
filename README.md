# ryaremenko/amqp

AMQP wrapper to publish and consume messages especially from RabbitMQ

## Installation

### Composer

Add the following to your require part within the composer.json:

```
$ php composer require ryaremenko/amqp
```

## Integration

### Lumen

Create a **connection** class

Adjust the properties to your needs.

```php
class BaseConnection extends AmqpConnection
{
    protected function setConnectionOptions(): AmqpConnectionOptions
    {
        return  (new AmqpConnectionOptions())
            ->setHost('127.0.0.1')
            ->setPort(5672)
            ->setLogin('guest')
            ->setPassword('guest');
    }
}
```

Register connection class as singleton:

```php
/*
|--------------------------------------------------------------------------
|  Laravel example
|--------------------------------------------------------------------------
*/

//...

$this->app->singleton(BaseConnection::class);
$this->app->bind(AMQPConnectionInterface::class, BaseConnection::class);

//...
```

## Publishing a message

```php
    (new AmqpProducer)->publish(['data'], 'queue_name');
```

## Consuming messages

```php
class AMQPHandlersService 
{
    private const HANDLERS = [
        'queue_name' => TestHandler::class
    ];
    
    private const PRIORITY_HANDLERS = [
        'queue_name'
    ];
    
    private $amqpConsumer;
    
    public function __construct(AmqpConsumer $amqpConsumer) {
        $this->amqpConsumer = $amqpConsumer;
    }
    
    public function handle(string $queueName) {
        $properties = [];
        if (in_array($queueName, self::PRIORITY_HANDLERS, true)){
            $properties['priority'] = true;
        }
    
        $handler = app(self::HANDLERS[$queueName]);
        $this->amqpConsumer->consume($queueName, function ($message) use ($handler) {
            try {
                $handler->handle($message->body);
            } catch (\Exception $exception) {
                // exception handler
            }
        },
        $properties);
    }
}
```