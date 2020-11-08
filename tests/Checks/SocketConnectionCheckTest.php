<?php

declare(strict_types=1);

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\SocketConnectionCheck;
use PHPUnit\Framework\TestCase;

class SocketConnectionCheckTest extends TestCase
{
    public function test_mysql57_is_up()
    {
        $check = new SocketConnectionCheck('127.0.0.1:8006');

        $this->assertTrue($check->getStatus()->isUp());
    }
}
