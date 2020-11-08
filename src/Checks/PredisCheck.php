<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Exception;
use Fullpipe\CheckThem\Status;
use Predis\ClientInterface;

class PredisCheck implements CheckInterface
{
    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getStatus(): Status
    {
        $status = new Status();

        try {
            $this->client->connect();
            $pong = (string) $this->client->ping();
            $status->setUp('PONG' === $pong);
        } catch (Exception $e) {
            $status->setError([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

        $this->client->disconnect();

        return $status;
    }
}
