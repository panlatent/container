<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Panlatent\Boost\Storage;
use Panlatent\Container\Injector\ClassInjector;
use Panlatent\Container\Injector\FunctionInjector;

class Container implements Containable, Singleton, \ArrayAccess, \Countable
{
    /**
     * @var static
     */
    protected static $singleton;

    /**
     * @var \Panlatent\Boost\Storage
     */
    protected $generators;

    protected $injectors;

    /**
     * @var \Panlatent\Container\ObjectStorage
     */
    protected $storage;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        static::$singleton = $this;

        $this->generators = new Storage();
        $this->injectors = new Storage();
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
    public function injectClass($class)
    {
        $injector = new ClassInjector($this, $class);
        $injector->setOption(ClassInjector::WITH_INTERFACE |
            ClassInjector::WITH_SETTER |
            ClassInjector::WITH_SETTER_ANNOTATE)
            ->withConstructor()
            ->withInterface(Injectable::class)
            ->handle();

        return $injector->getInstance();
    }

    public function injectMethod($object, $method, $params = [])
    {
        $injector =  new ClassInjector($this, $object);
        $injector->withoutConstructor()
            ->handle();

        return $injector->getReturn($method, $params);
    }

    public function injectFunction($callable, $params = [])
    {
        $injector = new FunctionInjector($this, $callable);
        $injector->handle();

        return $injector->getReturn($params);
    }

    public function bind($className)
    {

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
            if ($generator->isSingleton() || $object instanceof Singleton) {
                $this->storage->set($name, $object);
            }

            return $object;
        }

        throw new NotFoundException("Not found $name");
    }

    public function has($name)
    {
        if ( ! $this->storage->has($name) && ! $this->generators->has($name)) {
            return false;
        } else {
            return true;
        }
    }

    public function remove($name)
    {
        $removed = false;
        if ($this->storage->has($name)) {
            $this->storage->destroy($name);
            $removed = true;
        }
        if ($this->generators->has($name)) {
            $this->generators->destroy($name);
            $removed = true;
        }

        if ( ! $removed) {
            throw new Exception();
        }
    }

    public function set($name, $builder, $singleton = false)
    {
        if (is_string($builder) || is_callable($builder)) {
            $this->generators->set($name, new Generator($this, $builder, $singleton));
        } elseif (is_object($builder)) {
            if ($singleton) {
                $this->storage->set($name, $builder);
            } else {
                if ( ! $builder instanceof Generator) {
                    $builder = new Generator($this, $builder, false);
                }
                $this->storage->set($name, $builder);
            }
        } else {
            throw new Exception();
        }
    }

    public function setService($name, $builder)
    {
        $this->set($name, $builder, true);
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