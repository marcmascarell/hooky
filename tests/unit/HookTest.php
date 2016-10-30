<?php


class HookTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testHooksAreAttached()
    {
        $hook = new \Mascame\Hooky\Hook();

        $hook->to('bar', '');

        $this->assertEquals(1, count($hook->getAll()));

        $hook->to('foo', '');
        $hook->to('rab', '');
        $hook->to('oof', ['', '']);

        $this->assertEquals(4, count($hook->getAll()));
    }

    public function testDataIsManipulable()
    {
        $hook = new \Mascame\Hooky\Hook();

        $hook->to('bar', new class() implements \Mascame\Hooky\HookContract {
            public function handle($data, $next)
            {
                $data = 'test';

                return $next($data);
            }
        });

        $data = $hook->fire('bar', 'coconut');

        $this->assertEquals($data, 'test');
    }

    public function testHandlerExecutionOrder()
    {
        $hook = new \Mascame\Hooky\Hook();

        $hook->to('bar', new class() implements \Mascame\Hooky\HookContract {
            public function handle($data, $next)
            {
                $data = $next($data);

                $data[] = 'first';

                return $data;
            }
        });

        $hook->to('bar', new class() implements \Mascame\Hooky\HookContract {
            public function handle($data, $next)
            {
                $data[] = 'second';

                return $next($data);
            }
        });

        $data = $hook->fire('bar', ['zero']);

        $this->assertEquals($data,
            [
                'zero',
                'second',
                'first'
            ]
        );
    }
}