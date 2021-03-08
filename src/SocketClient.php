<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem;

use Exception;

class SocketClient
{
    /**
     * @var string
     */
    private $dsn;
    /**
     * @var string
     */
    private $remoteSocket;

    /**
     * @var int
     */
    private $connectionTimeout;

    /**
     * @var int
     */
    private $streamTimeout;

    /**
     * @var resourse
     */
    private $socket;

    public function __construct(
        string $dsn,
        int $connectionTimeout = 1,
        int $streamTimeout = 1
    ) {
        $this->dsn = $dsn;
        $this->connectionTimeout = $connectionTimeout;
        $this->streamTimeout = $streamTimeout;
    }

    public function connect()
    {
        $validTransports = \stream_get_transports();
        $parsed = \parse_url($this->dsn);
        $remoteSocket = '';

        if (\in_array($parsed['scheme'] ?? null, $validTransports)) {
            $remoteSocket .= $parsed['scheme'].'://';
        }

        $remoteSocket .= $parsed['host'];

        if ($parsed['port'] ?? null) {
            $remoteSocket .= ':'.$parsed['port'];
        }

        if ($parsed['path'] ?? null) {
            $remoteSocket .= '/'.$parsed['path'];
        }

        $this->remoteSocket = $remoteSocket;

        $this->socket = @\stream_socket_client(
            $this->remoteSocket,
            $errno,
            $errstr,
            $this->connectionTimeout
        );

        if (!$this->socket) {
            throw new Exception("Cannot connect to {$this->remoteSocket}: {$errstr}", $errno);
        }
    }

    public function disconnect()
    {
        if ($this->socket) {
            if (!\fclose($this->socket)) {
                throw new Exception('Error while closing socket');
            }

            $this->socket = null;
        }
    }

    public function write($str): void
    {
        \stream_set_timeout($this->socket, $this->streamTimeout);
        $ok = \fputs($this->socket, $str);

        if (false === $ok) {
            throw new Exception('Error while writing to socket.');
        }
    }

    public function getChar(): string
    {
        \stream_set_timeout($this->socket, $this->streamTimeout);
        $c = \fgetc($this->socket);

        if (false === $c || '' === $c) {
            throw new Exception('Error while reading char from socket.');
        }

        return $c;
    }

    public function readLine(): string
    {
        return $this->readTill("\r\n");
    }

    public function readTill(string $term): string
    {
        if (!$this->socket) {
            throw new Exception('Open connection first', 1);
        }

        \stream_set_timeout($this->socket, $this->streamTimeout);

        $termLen = \mb_strlen($term);
        $value = '';
        do {
            $c = \fgets($this->socket);

            if (false === $c || '' === $c) {
                throw new Exception('Error while reading line from socket.');
            }

            $value .= $c;
        } while ($term !== \mb_substr($value, -$termLen));

        return \mb_substr($value, 0, -$termLen);
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
