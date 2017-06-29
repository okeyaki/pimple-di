<?php

namespace Okeyaki\Pimple;

use Pimple\Container;

trait DiTrait
{
    /**
     * @var array
     */
    private $bound = [];

    /**
     * @var array
     */
    private $resolved = [];

    /**
     * @see Pimple\Container
     */
    public function offsetGet($id)
    {
        if (parent::offsetExists($id)) {
            return parent::offsetGet($id);
        }

        $resolved = $this->resolve($id);
        if (!$resolved) {
            throw new \LogicException(sprintf(
                'Failed to resolve the ID "%s".',
                $id
            ));
        }

        return $resolved;
    }

    /**
     * @see Pimple\Container
     */
    public function offsetExists($id)
    {
        return parent::offsetExists($id) || $this->resolve($id);
    }

    /**
     * @param string $class
     * @param string $id
     */
    public function bind($class, $id)
    {
        if (!interface_exists($class) && !class_exists($class)) {
            throw new \LogicException(sprintf(
                  'Failed to bind the unknown interface or class name "%s" to the ID "%s".',
                  $class,
                  $id
            ));
        }

        $this->bound[$class] = $id;
    }

    /**
     * @param string $class
     * @param array  $params
     *
     * @return mixed
     */
    public function make($class, array $params = [])
    {
        $rClass = new \ReflectionClass($class);

        $rConstructor = $rClass->getConstructor();
        if (!$rConstructor) {
            return $rClass->newInstance();
        }

        return $rClass->newInstanceArgs(array_map(
            function ($rParam) use ($params) {
                if (isset($params[$rParam->name])) {
                    return $params[$rParam->name];
                }

                if ($rParam->isDefaultValueAvailable()) {
                    return $rParam->getDefaultValue();
                }

                $rParamClass = $rParam->getClass();
                if ($rParamClass->name === Container::class
                    || $rParamClass->isSubclassOf(Container::class)
                ) {
                    return $this;
                }

                return $this->offsetGet($rParamClass->name);
            },
            $rConstructor->getParameters()
        ));
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    protected function resolve($id)
    {
        if (!isset($this->resolved[$id])) {
            $real = isset($this->bound[$id]) ? $this->bound[$id] : $id;

            if (parent::offsetExists($real)) {
                return parent::offsetGet($real);
            }

            if (!class_exists($real)) {
                return null;
            }

            $this->resolved[$id] = $this->make($real);
        }

        return $this->resolved[$id];
    }
}
