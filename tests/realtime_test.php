<?php

include 'vendor/autoload.php';

use Fullpipe\CheckThem\Checks\HttpCheck;
use Fullpipe\CheckThem\Checks\PDOCheck;
use Fullpipe\CheckThem\Checks\PredisCheck;
use Fullpipe\CheckThem\Checks\RedisCheck;
use Fullpipe\CheckThem\Checks\SocketCheck;
use Fullpipe\CheckThem\Checks\SocketConnectionCheck;
use Predis\Client as PredisClient;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;

(new SingleCommandApplication())
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $checks = [];

        $checks['mysql 5.7'] = new PDOCheck('mysql:dbname=test_db;host=localhost:8001', 'test_user', 'test_pass');
        $checks['mysql 5.7 [telnet]'] = new SocketCheck('localhost:8001');
        $checks['postgres13'] = new PDOCheck('pgsql:host=localhost;port=8002;dbname=test_db', 'test_user', '123');

        $predis = new PredisClient('localhost:8003');
        $checks['redis [predis]'] = new PredisCheck($predis);

        $checks['redis [raw]'] = new RedisCheck('localhost:8003');
        $checks['redis_with_password [raw]'] = (new RedisCheck('localhost:8004'))->setAuth('test_pass');
        $checks['http'] = new HttpCheck('http://localhost:8005/get_ok');

        $checks['rabbitmq [socket]'] = new SocketConnectionCheck('localhost:8006');

        $table = new Table($output);
        $table->setHeaders(\array_keys($checks));
        $table->setHorizontal(true);

        $table->render();

        $tableHeight = \count($checks) + 2;
        $stats = [];
        $ticks = 0;

        while (true) {
            ++$ticks;
            $table->setFooterTitle("ticks: {$ticks}");

            $rowStats = [];
            foreach ($checks as $check) {
                $rowStats[] = $check->getStatus()->isUp() ? '🌞' : '🧨';
            }

            if (\count($stats) > 15) {
                \array_shift($stats);
            }

            $stats[] = $rowStats;

            $table->setRows($stats);
            echo "\033[{$tableHeight}A";
            $table->render();

            \sleep(1);
        }
    })
    ->run();
