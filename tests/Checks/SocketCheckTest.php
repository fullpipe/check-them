<?php

declare(strict_types=1);

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\SocketCheck;
use PHPUnit\Framework\TestCase;

class SocketCheckTest extends TestCase
{
    public function test_mysql57_is_up()
    {
        $check = new SocketCheck('127.0.0.1:8001');

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_fails_if_host_not_exists()
    {
        $check = new SocketCheck('127.0.0.2:8001');

        $this->assertFalse($check->getStatus()->isUp());
    }

    public function test_fails_if_wrong_port()
    {
        $check = new SocketCheck('127.0.0.1:80999');

        $this->assertFalse($check->getStatus()->isUp());
    }
}
