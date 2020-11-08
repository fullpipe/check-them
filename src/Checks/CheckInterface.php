<?php

declare(strict_types=1);

namespace Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Status;

interface CheckInterface
{
    public function getStatus(): Status;
}
