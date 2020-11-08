<?php

declare(strict_types=1);

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\HttpCheck;
use PHPUnit\Framework\TestCase;

class HttpCheckTest extends TestCase
{
    public function test_get_is_up()
    {
        $check = new HttpCheck('http://localhost:8005/get_ok');

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_it_normilize_url()
    {
        $check = new HttpCheck('localhost:8005/get_ok');

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_get_fails()
    {
        $check = new HttpCheck('localhost:8005/get_fail');

        $this->assertFalse($check->getStatus()->isUp());
    }

    public function test_get_fails_when_connecting_to_mysql()
    {
        $check = new HttpCheck('http://localhost:8001');

        $this->assertFalse($check->getStatus()->isUp());
    }
}
