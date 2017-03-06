<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface, Containable, Singleton, \ArrayAccess, \Countable
{

    /**
     * @var static
     */
    static protected $singleton;

    /**
     * @var \Panlatent\Container\Storage
     */
    protected $generators;

    /**
     * @var \Panlatent\Container\ObjectStorage
     */
    protected $storage;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->generators = new Storage();
        $this->storage = new ObjectStorage();
    }

    /**
     * @return \Panlatent\Container\Container
     */
    public static function singleton()
    {
        if (null === static::$singleton) {
            static::$singleton = new static();
        }

        return static::$singleton;
    }

    /**
     * @param string $class
     * @return object
     */
    public function injectNew($class)
    {
        $injector =  new Injector($this, $class, true);
        return $injector->handle();
    }

    /**
     * @param callable $callable
     * @param array    $params
     * @return mixed
     */
    public function injectCall($callable, $params = [])
    {
        $injector =  new Injector($this, $callable);

        return $injector->handle($params);
    }

    public function get($name)
    {
        if ($this->storage->has($name)) {
            return $this->storage->get($name);
        } elseif ($this->generators->has($name)) {
            /** @var \Panlatent\Container\Generator $generator */
            $generator = $this->generators->get($name);
            if ( ! ($object = $generator->make())) {
                throw new NotFoundException("Not found $name");
            }
            if ($generator->singleton() || $object instanceof Singleton) {
                $this->storage->set($name, $object);
            }
            return $object;
        }

        throw new NotFoundException("Not found $name");
    }

    public function has($name)
    {

    }

    public function remove($name)
    {

    }

    public function set($name, $builder, $singleton = false)
    {
        if (is_string($builder) || is_callable($builder)) {
            $this->generators->set($name, new Generator($this, $builder, $singleton));
        } elseif (is_object($builder)) {
            $this->storage->set($name, $builder);
        } else {
            throw new Exception("");
        }
    }

    public function count()
    {
        return count($this->storage);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

}