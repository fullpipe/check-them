<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Exception;
use Fullpipe\CheckThem\Status;

class HttpCheck implements CheckInterface
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $connectionTimeout = 1;

    public function __construct(string $url)
    {
        $this->url = $this->normilizeUrl($url);
    }

    public function setConnectionTimeout(int $connectionTimeout): self
    {
        $this->connectionTimeout = $connectionTimeout;

        return $this;
    }

    public function getStatus(): Status
    {
        $status = new Status();

        try {
            $context = \stream_context_create(['http' => [
                'ignore_errors' => true,
                'method' => 'GET',
                'timeout' => $this->connectionTimeout,
            ]]);
            $headers = @\get_headers($this->url, 0, $context);

            if (isset($headers[0]) && 'HTTP/1.0 200 OK' === $headers[0]) {
                $status->setUp(true);
            }
        } catch (Exception $e) {
            $status->setError([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

        return $status;
    }

    private function normilizeUrl($url): string
    {
        $parsedUrl = \parse_url($url);
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'].'://' : 'http://';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port = isset($parsedUrl['port']) ? ':'.$parsedUrl['port'] : '';
        $user = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass = isset($parsedUrl['pass']) ? ':'.$parsedUrl['pass'] : '';
        $pass = ($user || $pass) ? "{$pass}@" : '';
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query = isset($parsedUrl['query']) ? '?'.$parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#'.$parsedUrl['fragment'] : '';

        return "{$scheme}{$user}{$pass}{$host}{$port}{$path}{$query}{$fragment}";
    }
}
