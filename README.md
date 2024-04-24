# Monitor

Monitor long running script

## Requirments

- PHP >= 8

## Installation

Using [composer](https://getcomposer.org/)

```shell
composer require xtompie/validation
```

## Doc

### Basic exmaple

```php
<?php

use Xtompie\Monitor\Monitor;

$monitor = new Monitor(name: 'import', stdout: true, frequency: 1);
foreach (input() as $input) {
    $imported = import($input);
    $imported ? $monitor->up('done') : $monitor->up('skip');
}
$monitor->show();
```

It will generator output in stdout:

```plain
#import | 2023-12-23 12:06:06 | 2023-12-23 12:06:10 | 0:00:00:04 | 2 mb | done: 2
#import | 2023-12-23 12:06:06 | 2023-12-23 12:06:12 | 0:00:00:06 | 2 mb | done: 5
#import | 2023-12-23 12:06:06 | 2023-12-23 12:06:12 | 0:00:00:06 | 2 mb | done: 5 | skip: 3
```

A new line with the monitor status is generated not Czech than `frequency` when the monitor status changes.

For more check source code: [Monitor](https://github.com/xtompie/monitor/blob/master/src/Monitor.php)

### Creating specific monitor

```php
<?php

use Xtompie\Monitor\Monitor;

class ImportMonitor extends Monitor
{
    public function done(): void
    {
        $this->up('done');
    }

    public function skip(): void
    {
        $this->up('skip');
    }
}
```
