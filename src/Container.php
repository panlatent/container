<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use ArrayAccess;
use Closure;
use Countable;
use InvalidArgumentException;
use Panlatent\Container\Resolve\ClassResolve;
use Panlatent\Container\Resolve\FunctionResolve;
use Panlatent\Container\Resolve\MethodResolve;

/**
 * Class Container
 *
 * @package Panlatent\Container
 */
class Container implements Containable, ArrayAccess, Countable
{
    /**
     * @var array
     */
    protected $relationships = [];
    /**
     * @var Injector
     */
    protected $injector;
    /**
     * @var array|Builder[]
     */
    protected $elements = [];

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->injector = new Injector($this);
    }

    /**
     * @param string $class
     * @param array  $tailParams
     * @return object
     * @throws NotFoundException
     * @throws ResolveException
     */
    public function new(string $class, array $tailParams = [])
    {
        $resolve = new ClassResolve($class);
        $params = $this->injector->make($resolve);
        $params = array_merge($params, $tailParams);

        return $resolve->getInstance($params);
    }

    /**
     * @param callable $callable
     * @param array    $frontParams
     * @return mixed
     * @throws NotFoundException
     */
    public function call(callable $callable, array $frontParams = [])
    {
        if (is_string($callable) || $callable instanceof Closure) {
            $resolve = new FunctionResolve($callable);
        } elseif (is_array($callable) && count($callable) == 2) {
            $resolve = new MethodResolve($callable[0], $callable[1]);
        } else {
            throw new InvalidArgumentException('Invalid call function or method');
        }

        $params = $this->injector->make($resolve);
        $params = array_merge($frontParams, $params);

        return $resolve->getReturn($params);
    }

    /**
     * Gets a depend from the container.
     *
     * @param string $name
     * @return callable|mixed|object|string
     * @throws \Panlatent\Container\NotFoundException
     */
    public function get($name)
    {
        if (! isset($this->elements[$name])) {
            throw new NotFoundException("Not found object: $name");
        }

        return $this->elements[$name]->build($this->injector);
    }

    /**
     * Returns a bool TRUE that the name in the container,
     * otherwise it does not exist.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->elements[$name]);
    }

    /**
     * Remove a container object or definition.
     *
     * @param string $name
     * @throws \Panlatent\Container\Exception
     */
    public function remove($name)
    {
        if (! isset($this->elements[$name])) {
            throw new Exception("Not exists container element: $name");
        }
        unset($this->elements[$name]);
    }

    /**
     * Sets a depend object definition to container.
     *
     * @param string $name
     * @param mixed  $definition
     * @param bool   $singleton
     */
    public function set($name, $definition, $singleton = false)
    {
        $builder = new Builder($definition);
        $builder->setSingleton($singleton);
        $this->elements[$name] = $builder;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @param string $offset
     * @param mixed  $value
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
}