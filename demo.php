#!/usr/bin/env php
<?php

use Moccalotto\Process\Process;

require 'vendor/autoload.php';

$proc = Process::execute('ls -a');
echo $proc->readToEnd();

echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

$proc = Process::execute('cat');
$proc->write('========================' . PHP_EOL);
$proc->write('====== CAT EXAMPLE =====' . PHP_EOL);
$proc->write('========================' . PHP_EOL);
echo $proc->readline();
echo $proc->readline();
echo $proc->readline();

$proc->signalStop();
echo $proc->readErrorToEnd();

/**
 * Bash session example
 * where we do multiple calls.
 */

echo PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;

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

// we should exit before calling readToEnd- otherwise the stream will block
$proc->write("exit");
usleep(100000);
$proc->signalStop();
echo $proc->readToEnd();
