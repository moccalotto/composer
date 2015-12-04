# Process
[![Build Status](https://travis-ci.org/moccalotto/process.svg?branch=master)](https://travis-ci.org/moccalotto/process)

A PHP process execution helper.

It assists in I/O from processes. See examples below or run `demo.php` to see for yourself.

## Installation

To add this package as a local, per-project dependency to your project, simply add a dependency on
 `moccalotto/process` to your project's `composer.json` file like so:

```json
{
    "require": {
        "moccalotto/process": "~0.6"
    }
}
```

Alternatively simply call `composer require moccalotto/process`


## Demo

```php
#!/usr/bin/env php
<?php

use Moccalotto\Process\Process;

require 'vendor/autoload.php';



/*
| Execute ls
|------------
| Execute ls and capture the output.
| Then print all the captured output.
*/
$proc = Process::execute('ls -a');
echo $proc->readToEnd();

echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

/*
| Execute cat and talk to it interactively
|------------------------------------------
| Execute cat, and input a few lines into stdin.
| Cat should echo those lines out to stdout.
| Then, we read those lines from stdout.
| Then we ask cat to terminate.
| Then we print cat's exitcode.
*/
$proc = Process::execute('cat');
$proc->write('========================' . PHP_EOL);
$proc->write('====   CAT EXAMPLE  ====' . PHP_EOL);
$proc->write('==== cat is working ====' . PHP_EOL);
$proc->write('========================' . PHP_EOL);

// read the lines again.
// Beware that this process is blocking.
// if you call readline on a process that
// has not yet printed a newline character,
// your program will hang!
echo $proc->readline();
echo $proc->readline();
echo $proc->readline();
echo $proc->readline();

$proc->signalStop();
echo $proc->readErrorToEnd();
echo PHP_EOL;
printf('Exit code: %d', $proc->exitCode());

echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

/*
| Execute a terminal and run commands in it
|-------------------------------------------
| Execute bash.
| Run a few commands inside it, and capture their output.
| Then wait for the commands to finish executing,
| Then run exit to make bash exit neatly.
| Then ask bash to terminate if it has not executed the 'exit' command yet.
| Then print all the captured output.
*/
$proc = Process::execute('/bin/bash');
$proc->write('echo "=========================="' . PHP_EOL);
$proc->write('echo "====   BASH EXAMPLE   ===="' . PHP_EOL);
$proc->write('echo "==== several commands ===="' . PHP_EOL);
$proc->write('echo "====    in a shell    ===="' . PHP_EOL);
$proc->write('echo "=========================="' . PHP_EOL);
echo $proc->readline();
echo $proc->readline();
echo $proc->readline();
echo $proc->readline();
echo $proc->readline();
$proc->write('ls' . PHP_EOL);
$proc->write('echo ==========================' . PHP_EOL);
$proc->write('uname' . PHP_EOL);
$proc->write('echo ==========================' . PHP_EOL);
$proc->write('export | head' . PHP_EOL);
$proc->write('echo ==========================' . PHP_EOL);
$proc->write('echo bye...' . PHP_EOL);
$proc->write("exit");

// give all commands time to execute.
usleep(100000);

$proc->signalStop();
echo $proc->readToEnd();
```
