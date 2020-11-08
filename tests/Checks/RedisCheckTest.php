<?php

declare(strict_types=1);

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\RedisCheck;
use PHPUnit\Framework\TestCase;

class RedisCheckTest extends TestCase
{
    public function test_redis_is_up()
    {
        $check = new RedisCheck('localhost:8003');

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_redis_with_password_is_up()
    {
        $check = (new RedisCheck('localhost:8004'))->setAuth('test_pass');

        $this->assertTrue($check->getStatus()->isUp());
    }
}
