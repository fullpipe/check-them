<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Status;

class AllInOneCheck implements CheckInterface
{
    /**
     * @var CheckInterface[]
     */
    private $checks = [];

    public function getStatus(): Status
    {
        $status = new Status();
        $status->setUp(true);
        $errors = [];

        foreach ($this->checks as $check) {
            $checkStatus = $check->getStatus();
            if (!$checkStatus->isUp()) {
                $status->setUp(false);
                $errors[] = $checkStatus->getError();
            }
        }

        if (!$status->isUp()) {
            $status->setError($errors);
        }

        return $status;
    }

    public function add(CheckInterface $check): self
    {
        $this->checks[] = $check;

        return $this;
    }
}
