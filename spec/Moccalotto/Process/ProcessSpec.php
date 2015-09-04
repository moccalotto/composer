<?php

namespace spec\Moccalotto\Process;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ProcessSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $stream1 = fopen('php://temp', 'w+');
        $stream2 = fopen('php://temp', 'w+');
        $stream3 = fopen('php://temp', 'w+');
        $fake_process = proc_open('echo foobar', [$stream1, $stream2, $stream3], $streams_out);

        $this->beConstructedWith($fake_process, $stream1, $stream2, $stream3);
        $this->shouldHaveType('Moccalotto\Process\Process');
    }

    function it_throws_exception_if_created_with_invalid_arguments()
    {
        $stream1 = fopen('php://temp', 'w+');
        $stream2 = fopen('php://temp', 'w+');
        $stream3 = fopen('php://temp', 'w+');
        $fake_process = proc_open('echo foobar', [$stream1, $stream2, $stream3], $streams_out);

        $this->beConstructedWith( null, $stream1, $stream2, $stream3);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();

        $this->beConstructedWith( $fake_process, null, $stream2, $stream3);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();

        $this->beConstructedWith( $fake_process, $stream1, null, $stream3);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();

        $this->beConstructedWith( $fake_process, $stream1, $stream2, null);
        $this->shouldThrow('\InvalidArgumentException')->duringInstantiation();
    }

    function it_can_be_created_via_helper_constructor()
    {
        $this->beConstructedThrough('execute', ['echo foo']);
        $this->shouldHaveType('Moccalotto\Process\Process');
    }

    function it_can_execute_echo()
    {
        $this->beConstructedThrough('execute', ['echo foo']);
        $this->shouldHaveType('Moccalotto\Process\Process');
    }

    function it_can_read_3_characters_from_stdout()
    {
        $this->beConstructedThrough('execute', ['echo foobar']);
        $this->read(3)->shouldBe('foo');
    }

    function it_can_read_stdout_to_end()
    {
        $this->beConstructedThrough('execute', ['echo foo']);
        $this->readToEnd()->shouldBe('foo' . PHP_EOL);
    }

    function it_can_read_a_line_from_stdout()
    {
        $this->beConstructedThrough('execute', ['echo foo']);
        $this->readLine()->shouldBe('foo' . PHP_EOL);
    }

    function it_can_read_3_characters_from_stderr()
    {
        $this->beConstructedThrough('execute', ['echo foobar > /dev/stderr']);
        $this->readError(3)->shouldBe('foo');
    }

    function it_can_read_stderr_to_end()
    {
        $this->beConstructedThrough('execute', ['echo foo > /dev/stderr']);
        $this->readErrorToEnd()->shouldBe('foo' . PHP_EOL);
    }

    function it_can_read_a_line_from_stderr()
    {
        $this->beConstructedThrough('execute', ['echo foo > /dev/stderr']);
        $this->readErrorLine()->shouldBe('foo' . PHP_EOL);
    }

    function it_can_write_to_stdin()
    {
        $this->beConstructedThrough('execute', ['cat']);
        $this->write('line1' . PHP_EOL . 'line2')->shouldBe(11);
        $this->readLine()->shouldBe('line1' . PHP_EOL);
        $this->read(5)->shouldBe('line2');
    }

    function it_can_stop_a_running_command()
    {
        $this->beConstructedThrough('execute', ['cat']);
        $this->stop()->shouldBe(0);
    }

    function it_grants_access_to_io_streams()
    {
        $this->beConstructedThrough('execute', ['cat']);
        $this->write('foo')->shouldBe(3);
        $this->stdin()->shouldBeStream();
        $this->stdout()->shouldBeStream();
        $this->stderr()->shouldBeStream();
    }

    function it_grants_access_to_proess_handle()
    {
        $this->beConstructedThrough('execute', ['cat']);
        $this->processHandle()->shouldBeProcess();
    }

    function it_can_close_all_streams()
    {
        $this->beConstructedThrough('execute', ['cat']);
        $this->write('foo')->shouldBe(3);
        $this->closeStreams();
        $this->stdin()->shouldBe(false);
        $this->stdout()->shouldBe(false);
        $this->stderr()->shouldBe(false);
    }

    function it_shows_process_id()
    {
        $this->beConstructedThrough('execute', ['echo shawarma']);
        $this->processHandle()->shouldBeProcess();
        $this->pid()->shouldBeInteger();
    }

    public function getMatchers()
    {
        return [
            'beStream' => function($resource) {
                return is_resource($resource) && get_resource_type($resource) === 'stream';
            },
            'beProcess' => function($resource) {
                return is_resource($resource) && get_resource_type($resource) === 'process';
            }
        ];
    }
}
