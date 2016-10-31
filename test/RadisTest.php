<?php

include_once('vendor/autoload.php');

\Cake\Core\Configure::write('App.name', 'RadisTest');

class RadisTest extends \PHPUnit_Framework_TestCase
{
    public $stack = [];

    protected function lastMessage()
    {
        $json = array_shift($this->stack);
        return (array) json_decode($json);
    }

    protected function redis_mock($host = 'foo', $port = 123, $queue = 'bar') {
        $mock = $this->getMockBuilder('Redis')
                     ->setMethods(['pconnect', 'lPush'])
                     ->getMock();

        $server = $host.':'.$port;

        $mock->expects($this->once())
             ->method('pconnect')
             ->with($this->equalTo($host),
                    $this->equalTo($port))
             ->will($this->returnValue(TRUE));

        $self = $this;
        $mock->expects($this->any())
             ->method('lPush')
             ->with($this->equalTo($queue),
                    $this->callback(function ($json) use ($self) {
                        $self->stack[] = $json;
                        return true;
                    }))
             ->will($this->returnValue(1));

        return $mock;
    }

    public function test001()
    {
        $mock = $this->redis_mock('foo', 123, 'test');

        \Cake\Log\Log::config('default', [
            'engine' => 'Radis',
            'server' => 'foo:123',
            'queue' => 'test',
            'redis_mock' => $mock,
        ]);

        \Cake\Log\Log::info('foobar');

        $msg = $this->lastMessage();
        $this->assertEquals(7, $msg['level']);
        $this->assertEquals(gethostname(), $msg['host']);
        $this->assertEquals($_SERVER['SCRIPT_FILENAME'], $msg['_php_script']);
        $this->assertEquals('foobar', $msg['message']);
        $this->assertEquals('RadisTest', $msg['_cake_application']);
    }
}


