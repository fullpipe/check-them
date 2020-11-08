<?php

declare(strict_types=1);

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\PredisCheck;
use PHPUnit\Framework\TestCase;
use Predis\Client;

class PredisCheckTest extends TestCase
{
    public function test_redis_is_up()
    {
        $check = new PredisCheck(new Client('localhost:8003'));

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_fails_if_host_not_exists()
    {
        $check = new PredisCheck(new Client('localhost:8001'));

        $this->assertFalse($check->getStatus()->isUp());
    }
}
