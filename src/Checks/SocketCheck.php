<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Exception;
use Fullpipe\CheckThem\SocketClient;
use Fullpipe\CheckThem\Status;

class SocketCheck implements CheckInterface
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

            $this->getClient()->getChar();

            $status->setUp(true);

            $this->getClient()->disconnect();
        } catch (Exception $e) {
            $status->setError([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

        return $status;
    }

    protected function getClient(): SocketClient
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new SocketClient($this->socket, $this->connectionTimeout, $this->streamTimeout);

        return $this->client;
    }

    public function __destruct()
    {
        $this->getClient()->disconnect();
    }
}
