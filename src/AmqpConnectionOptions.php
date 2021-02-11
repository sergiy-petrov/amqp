<?php

namespace Snovio\Amqp;

class AmqpConnectionOptions
{
    /**
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @var int
     */
    private $port = 5672;

    /**
     * @var string
     */
    private $login = 'guest';

    /**
     * @var string
     */
    private $password = 'guest';

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     * @return AmqpConnectionOptions
     */
    public function setHost(string $host): AmqpConnectionOptions
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @param int $port
     * @return AmqpConnectionOptions
     */
    public function setPort(int $port): AmqpConnectionOptions
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return AmqpConnectionOptions
     */
    public function setLogin(string $login): AmqpConnectionOptions
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return AmqpConnectionOptions
     */
    public function setPassword(string $password): AmqpConnectionOptions
    {
        $this->password = $password;

        return $this;
    }
}
