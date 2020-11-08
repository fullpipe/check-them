<?php

namespace tests\Fullpipe\CheckThem\Checks;

use Fullpipe\CheckThem\Checks\AllInOneCheck;
use Fullpipe\CheckThem\Checks\CheckInterface;
use Fullpipe\CheckThem\Status;
use PHPUnit\Framework\TestCase;

class AllInOneCheckTest extends TestCase
{
    public function test_it_implements_Check()
    {
        $check = new AllInOneCheck();

        $this->assertInstanceOf(CheckInterface::class, $check);
    }

    public function test_it_is_up_when_empty()
    {
        $check = new AllInOneCheck();

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_it_is_up_if_every_child_is_up()
    {
        $check = new AllInOneCheck();

        $check->add($this->newCheckMock(true));
        $check->add($this->newCheckMock(true));

        $this->assertTrue($check->getStatus()->isUp());
    }

    public function test_it_is_down_if_some_of_child_is_down()
    {
        $check = new AllInOneCheck();

        $check->add($this->newCheckMock(true));
        $check->add($this->newCheckMock(false));
        $check->add($this->newCheckMock(true));

        $this->assertFalse($check->getStatus()->isUp());
    }

    public function test_it_collects_errors()
    {
        $check = new AllInOneCheck();

        $check->add($this->newCheckMock(false, ['foo' => 1]));
        $check->add($this->newCheckMock(false, ['bar' => 2]));
        $check->add($this->newCheckMock(true));

        $this->assertFalse($check->getStatus()->isUp());
        $this->assertEquals([['foo' => 1], ['bar' => 2]], $check->getStatus()->getError());
    }

    public function newCheckMock(bool $isUp, $error = null): CheckInterface
    {
        $check = $this->createMock(CheckInterface::class);
        $status = new Status();
        $status->setUp($isUp);
        if ($error) {
            $status->setError($error);
        }
        $check->method('getStatus')->willReturn($status);

        return $check;
    }
}
