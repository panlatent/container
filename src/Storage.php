<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

class Storage implements \ArrayAccess, \Countable, \Iterator {

    /**
     * @var array
     */
    protected $storage = [];

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->storage[$name];
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->storage[$name]);
    }

    /**
     * @param $name
     * @param $object
     * @throws \Panlatent\Container\Exception
     */
    public function set($name, $object)
    {
        if ( ! is_object($object)) {
            throw new Exception("");
        }

        $this->storage[$name] = $object;
    }

    /**
     * @param $name
     */
    public function remove($name)
    {
        unset($this->storage[$name]);
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
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    public function rewind()
    {
        reset($this->storage);
    }

    public function current()
    {
        return current($this->storage);
    }

    public function next()
    {
        next($this->storage);
    }

    public function key()
    {
        return key($this->storage);
    }

    public function valid()
    {
        return false !== $this->current();
    }

}