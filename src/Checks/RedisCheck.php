<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Exception;
use Fullpipe\CheckThem\SocketClient;
use Fullpipe\CheckThem\Status;

class RedisCheck implements CheckInterface
{
    /**
     * @var string
     */
    private $socket;

    /**
     * @var int
     */
    private $connectionTimeout = 1;

    /**
     * @var int
     */
    private $streamTimeout = 1;

    /**
     * @var string
     */
    private $password;

    /**
     * @var SocketClient
     */
    private $client;

    public function __construct(string $socket)
    {
        $this->socket = $socket;
    }

    public function setAuth(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setConnectionTimeout(int $connectionTimeout): self
    {
        $this->connectionTimeout = $connectionTimeout;

        return $this;
    }

    public function setStreamTimeout(int $streamTimeout): self
    {
        $this->streamTimeout = $streamTimeout;

        return $this;
    }

    public function getStatus(): Status
    {
        $status = new Status();

        try {
            $this->getClient()->connect();

            if ($this->password) {
                $this->getClient()->write($this->getAuthCommand());
                $responce = $this->getClient()->readLine();
                if ('+OK' !== $responce) {
                    throw new Exception($responce);
                }
            }

            $this->getClient()->write($this->getPingCommand());
            $responce = $this->getClient()->readLine();

            $status->setUp('+PONG' === $responce);

            $this->getClient()->disconnect();
        } catch (Exception $e) {
            $status->setError([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

        return $status;
    }

    private function getClient(): SocketClient
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new SocketClient($this->socket, $this->connectionTimeout, $this->streamTimeout);

        return $this->client;
    }

    private function getAuthCommand(): string
    {
        $commandName = 'AUTH';
        $commandLen = \mb_strlen($commandName);
        $command[] = "*2\r\n\${$commandLen}\r\n{$commandName}\r\n";

        $passwordLen = \mb_strlen($this->password);
        $command[] = "\${$passwordLen}\r\n{$this->password}\r\n";

        return \implode('', $command);
    }

    private function getPingCommand(): string
    {
        return "*1\r\n$4\r\nPING\r\n";
    }

    public function __destruct()
    {
        $this->getClient()->disconnect();
    }
}
