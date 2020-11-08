<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Exception;
use Fullpipe\CheckThem\Status;

class SocketConnectionCheck extends SocketCheck
{
    public function getStatus(): Status
    {
        $status = new Status();

        try {
            $this->getClient()->connect();

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
}
