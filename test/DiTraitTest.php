<?php

class Container extends \Pimple\Container
{
    use \Okeyaki\Pimple\DiTrait;
}

class Foo
{
}

class Bar
{
    private $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }

    public function foo()
    {
        return $this->foo;
    }
}

class Baz
{
    private $foo;

    private $bar;

    public function __construct(Foo $foo, $bar)
    {
        $this->foo = $foo;

        $this->bar = $bar;
    }

    public function foo()
    {
        return $this->foo;
    }

    public function bar()
    {
        return $this->bar;
    }
}

class DiTraitTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $this->container = new Container();
    }

    public function testResolve()
    {
        $this->assertInstanceOf(Bar::class, $this->container[Bar::class]);

        $this->assertInstanceOf(Foo::class, $this->container[Bar::class]->foo());
    }

    public function testBind()
    {
        $this->container['foo'] = 'foo';

        $this->container->bind(Foo::class, 'foo');

        $this->assertSame('foo', $this->container[Foo::class]);
    }

    public function testMake()
    {
        $baz = $this->container->make(Baz::class, [
            'bar' => 'bar',
        ]);

        $this->assertInstanceOf(Baz::class, $baz);

        $this->assertSame('bar', $baz->bar());

        $this->assertInstanceOf(Foo::class, $baz->foo());
    }
}
