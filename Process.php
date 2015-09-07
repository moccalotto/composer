<?php

namespace Moccalotto\Process;

use InvalidArgumentException;

/**
 * Represents and manages a running process.
 *
 * Is basically a wrapper for PHPs proc_ functions
 */
class Process
{
    /**
     * The current process resource
     *
     * @var resource
     */
    protected $process;

    protected $stdin;
    protected $stdout;
    protected $stderr;

    public function __construct($process, $stdin, $stdout, $stderr)
    {
        if (!(is_resource($process) && get_resource_type($process) === 'process')) {
            throw new InvalidArgumentException(sprintf(
                'The parameter "proces" must be a process resource. %s was passed',
                gettype($process)
            ));
        }
        foreach (compact('stdin', 'stdout', 'stderr') as $name => $value) {
            if (!(is_resource($value) && get_resource_type($value) === 'stream')) {
                throw new InvalidArgumentException(sprintf(
                    'The parameter "%s" must be a stream. %s was passed',
                    $name,
                    gettype($value)
                ));
            }
        }
        $this->process = $process;
        $this->stdin = $stdin;
        $this->stdout = $stdout;
        $this->stderr = $stderr;
    }

    public function __destruct()
    {
        $this->closeStreams();
        $this->stop();
    }

    public static function execute($command)
    {
        $pipe_descriptors = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w'],
        ];
        $process = proc_open($command, $pipe_descriptors, $outpipes);
        return new static($process, ...$outpipes);
    }

    public function stop()
    {
        if (is_resource($this->process)) {
            return proc_close($this->process);
        }
        return false;
    }

    public function signalStop($signal = SIGTERM)
    {
        if (is_resource($this->process)) {
            return proc_terminate($this->process);
        }
        return false;
    }

    public function closeStreams()
    {
        if (is_resource($this->stdin)) {
            fclose($this->stdin);
            $this->stdin = false;
        }
        if (is_resource($this->stdout)) {
            fclose($this->stdout);
            $this->stdout = false;
        }
        if (is_resource($this->stderr)) {
            fclose($this->stderr);
            $this->stderr = false;
        }
    }

    /**
     * Write $string to stdin
     * @return int the number of bytes written.
     */
    public function write($string)
    {
        return fwrite($this->stdin, $string);
    }

    /**
     * Read $length bytes from stdout
     * @return string
     */
    public function read($length)
    {
        return fread($this->stdout, $length);
    }

    /**
     * Read line from stdout
     * @return string
     */
    public function readline()
    {
        return fgets($this->stdout);
    }

    /**
     * Read stdout to the end of stream.
     * @return string
     */
    public function readToEnd()
    {
        return stream_get_contents($this->stdout);
    }

    /**
     * Read line from stderr
     * @return string
     */
    public function readError($length)
    {
        return fread($this->stderr, $length);
    }

    /**
     * Read line from stderr
     * @return string
     */
    public function readErrorLine()
    {
        return fgets($this->stderr);
    }

    /**
     * Read stderr to the end of stream.
     * @return string
     */
    public function readErrorToEnd()
    {
        return stream_get_contents($this->stderr);
    }

    /**
     * The the process status.
     * @link http://php.net/manual/en/function.proc-get-status.php
     * 
     * @return array
     */
    public function status()
    {
        return proc_get_status($this->process);
    }

    /**
     * Is the process running?
     * @return boolean
     */
    public function running()
    {
        return $this->status()['running'];
    }

    /**
     * Get the process PID
     * @return int
     */
    public function pid()
    {
        return $this->status()['pid'];
    }

    /**
     * Return the command string that executed this process.
     * @return string
     */
    public function command()
    {
        return $this->status()['command'];
    }

    /**
     * Get the exit code of a terminated program.
     * @return int|false
     */
    public function exitCode()
    {
        return $this->status()['exitcode'];
    }

    /**
     * Return the resource handle to the process' stdin stream
     *
     * @return resource
     */
    public function stdin()
    {
        return $this->stdin;
    }

    /**
     * Return the resource handle to the process' stdout stream
     *
     * @return resource
     */
    public function stdout()
    {
        return $this->stdout;
    }

    /**
     * Return the resource handle to the process' stderr stream
     *
     * @return resource
     */
    public function stderr()
    {
        return $this->stderr;
    }

    /**
     * Return the process handle.
     *
     * @return resource
     */
    public function processHandle()
    {
        return $this->process;
    }
}
