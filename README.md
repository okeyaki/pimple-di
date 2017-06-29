# Pimple DI

Pimple DI integrates DI functionality with [Pimple](http://pimple.sensiolabs.org/).

## Features

- Integrating DI (*constructor Injection*) functionality with Pimple.
- Easy to use.

## Installation

Add `okeyaki/pimple` to your `composer.json`:

```
$ composer require okeyaki/pimple-di
```

## Usage

At first, create a sub-class of `Pimple\Container` and mixin `Okeyaki\Pimple\DiTrait`:

```php
class Container extends \Pimple\Container
{
    use \Okeyaki\Pimple\DiTrait;
}
```

Then, create an instance of the class:

```php
$container = new Container();
```

### Constructor Injection

Pimple DI resolves dependencies automatically by the classes of constructor parameters:

```php
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

$foo = $container[Foo::class];

$bar = $contaienr[Bar::class];
$bar->foo();
```

### Class Binding

You can bind classes to an existing ID:

```php
class Foo
{
}

$container['foo'] = function () {
    return new Foo();
};

$container->bind(Foo::class, 'foo');

$foo = $container[Foo::class];
```

This is useful on using [Silex](https://silex.sensiolabs.org/) proprietary or third-party providers.

### Instanciation

You can instanciate a class and resolve its dependencies automatically:

```
class Foo
{
}

class Bar
{
    private $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;

        $this->name = $name;
    }

    public function foo()
    {
        return $this->foo;
    }
}

$bar = $container->make(Bar::class);
$bar->foo();
```

If a constructor has unresolvable parameters:

```
class Bar
{
    private $foo;

    private $baz;

    public function __construct(Foo $foo, $baz)
    {
        $this->foo = $foo;

        $this->baz = $baz;
    }

    public function foo()
    {
        return $this->foo;
    }

    public function baz()
    {
        return $this->baz;
    }
}

$bar = $container->make(Bar::class, [
    'baz' => 'baz',
]);

$bar->foo();
$bar->baz();
```

## Examples

### Integrating with Silex

```
class App extends \Silex\Application
{
    use \Okeyaki\Pimple\DiTrait;
}
```
