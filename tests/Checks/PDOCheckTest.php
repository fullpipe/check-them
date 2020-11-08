<?php

declare(strict_types=1);

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\PDOCheck;
use PHPUnit\Framework\TestCase;

class PDOCheckTest extends TestCase
{
    public function test_mysql57_is_up()
    {
        $check = new PDOCheck('mysql:dbname=test_db;host=localhost:8001', 'test_user', 'test_pass');

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_mysql57_is_down_on_wrong_credentials()
    {
        // no password
        $check = new PDOCheck('mysql:dbname=test_db;host=localhost:8001', 'test_user');
        $this->assertFalse($check->getStatus()->isUp());
        $this->assertEquals(1045, $check->getStatus()->getError()['code']);

        // wrong password
        $check = new PDOCheck('mysql:dbname=test_db;host=localhost:8001', 'test_user', '321');
        $this->assertFalse($check->getStatus()->isUp());
        $this->assertEquals(1045, $check->getStatus()->getError()['code']);

        // wrong host
        $check = new PDOCheck('mysql:dbname=test_db;host=fake:8001', 'test_user', 'test_pass');
        $this->assertFalse($check->getStatus()->isUp());
        $this->assertEquals(2002, $check->getStatus()->getError()['code']);
    }

    public function test_postgres13_is_up()
    {
        $check = new PDOCheck('pgsql:host=localhost;port=8002;dbname=test_db', 'test_user', '123');

        $this->assertTrue($check->getStatus()->isUp());
    }
}
