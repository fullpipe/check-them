<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem;

// todo: interaface
class Status
{
    /**
     * @var bool
     */
    private $up = false;

    /**
     * @var array
     */
    private $error;

    public function __construct()
    {
    }

    public function isUp(): bool
    {
        return $this->up;
    }

    public function setUp(bool $up)
    {
        $this->up = $up;
    }

    public function setError(array $error)
    {
        $this->error = $error;
    }

    public function getError(): ?array
    {
        return $this->error;
    }
}
