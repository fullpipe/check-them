<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Status;
use PDO;

class PDOCheck implements CheckInterface
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $driverOptions;

    public function __construct(
        string $dsn,
        string $username = null,
        string $password = null,
        array $driverOptions = null
    ) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->driverOptions = $driverOptions;
    }

    public function getStatus(): Status
    {
        $status = new Status();

        try {
            $up = $this->getPDO()->query('SELECT 1;')->execute();
            $status->setUp($up);
        } catch (\PDOException $e) {
            $status->setUp(false);
            $status->setError([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

        return $status;
    }

    private function getPDO(): PDO
    {
        return new PDO($this->dsn, $this->username, $this->password, $this->driverOptions);
    }
}
