# Health checks for external services

## Install

```bash
composer require fullpipe/check-them
```

## Usage

```php
use Fullpipe\CheckThem\Checks\PDOCheck;

$mysqlCheck = new PDOCheck('mysql:dbname=test_db;host=127.0.0.1:3306', 'username', 'password');
$status = $mysqlCheck->getStatus();

if(!$status->isUp()) {
    $this->logger->warn('Mysql server is down', $status->getError());
    exit;
}
```

or use AllInOneCheck

```php

use Fullpipe\CheckThem\Checks\AllInOneCheck;
use Fullpipe\CheckThem\Checks\PDOCheck;
use Fullpipe\CheckThem\Checks\HttpCheck;
use Fullpipe\CheckThem\Checks\RedisChecker;

...

$allInOne = new AllInOneCheck();

$allInOne->add(new PDOCheck('mysql:dbname=test_db;host=127.0.0.1:3306', 'username', 'password'));
$allInOne->add(new HttpCheck('user_service:8080'));
$allInOne->add(new RedisChecker('redis:6379'));

$status = $allInOne->getStatus();

if(!$status->isUp()) {
    $this->logger->warn('Something is down', $status->getError());
    exit;
}

// everything is fine
```

## Available checks

### PDOCheck

It is just a simple wrapper over [php.PDO](https://www.php.net/manual/en/book.pdo.php).
It has the same constructor signature as the `PDO` class. In theory, it works with all
[PDO drivers](https://www.php.net/manual/en/pdo.drivers.php), but was tested
only against MySQL and Postgres.


#### Examples

```php
use Fullpipe\CheckThem\Checks\PDOCheck;

...

$mysqlCheck = new PDOCheck('mysql:dbname=test_db;host=127.0.0.1:3306', 'username', 'password');
$pgCheck = new PDOCheck('pgsql:host=localhost;port=8002;dbname=test_db', 'username', 'password');
```

### HttpCheck

Check external service by http request. To be `up` service should respond with
`200` http code.

#### Examples

```php
use Fullpipe\CheckThem\Checks\HttpCheck;

...

$userCheck = new HttpCheck('http://user_service:8080/healthz');
$webCheck = new HttpCheck('https://google.com/');
```

#### Config

```php
$check = (new HttpCheck('http://user_service:8080/healthz'))
    ->setConnectionTimeout(3) // change connection timeout, default 1 second
    ;
```

### RedisCheck

Checks redis server with `PING` -> `PONG` request.

#### Examples

```php
use Fullpipe\CheckThem\Checks\RedisCheck;

...

$check = new RedisCheck('tcp://10.0.0.1:6379');
$check = new RedisCheck('unix:/path/to/redis.sock');
```

#### Config

```php
$check = (new RedisCheck('redis:6379'))
    ->setAuth('test_pass') // use password if required
    ->setConnectionTimeout(4) // timeout for server connection, default 1 second
    ->setStreamTimeout(3) // timeout for socket read/write operations, default 1 second
    ;
```

### PredisCheck

If you already work with redis using [predis](https://github.com/predis/predis).
You could use predis client for `PING` check.

#### Examples

```php
use Fullpipe\CheckThem\Checks\PredisCheck;

...

$client = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => '10.0.0.1',
    'port'   => 6379,
]);
$check = new PredisCheck($client);
```

### SocketCheck

Connects to service and waits for single char from service over a socket connection.

#### Examples

```php
use Fullpipe\CheckThem\Checks\SocketCheck;

...

// you could use this check for mysql,
// it work fine and you don't need a password
$check = new SocketCheck('mysql:3306');
```

#### Config

```php
$check = (new SocketCheck('mysql:3306'))
    ->setConnectionTimeout(4) // timeout for server connection, default 1 second
    ->setStreamTimeout(3) // timeout for socket read/write operations, default 1 second
    ;
```

### SocketConnectionCheck

Checks only that socket connection is working. It is not the check that you
could rely on.

#### Examples

```php
use Fullpipe\CheckThem\Checks\SocketConnectionCheck;

...

$check = new SocketConnectionCheck('rabbitmq:5672');
```

#### Config

```php
$check = (new SocketConnectionCheck('mysql:3306'))
    ->setConnectionTimeout(4) // timeout for server connection, default 1 second
    ;
```

### AllInOneCheck

Checks all children to be available.

#### Examples

```php
use Fullpipe\CheckThem\Checks\AllInOneCheck;
use Fullpipe\CheckThem\Checks\SocketCheck;
use Fullpipe\CheckThem\Checks\RedisCheck;

...

$check = (new AllInOneCheck())
    ->add(new SocketCheck('mysql:3306'))
    ->add((new RedisCheck('tcp://10.0.0.1:6379'))->setAuth('redisPass'))
    ;
```

## Test

```bash
composer install
docker-compose -f tests/docker-compose.yml up -d
./vendor/bin/phpunit
```

or if you want to play with service availability

```bash
docker-compose -f tests/docker-compose.yml up -d
php ./tests/realtime_test.php
docker-compose -f tests/docker-compose.yml restart mysql57
```

