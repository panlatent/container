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

/**
 * Class ObjectStorage
 *
 * @package Panlatent\Container
 */
class ObjectStorage extends Storage
{
    /**
     * @param string $className
     * @return object|bool
     */
    public function find($className)
    {
        foreach ($this->storage as $object) {
            if (get_class($object) == $className) {
                return $object;
            }
        }

        return false;
    }

    /**
     * @param string $className
     * @return object|bool
     */
    public function findInstanceOf($className)
    {
        foreach ($this->storage as $object) {
            if ($object instanceof $className) {
                return $object;
            }
        }

        return false;
    }

    /**
     * @param string $name
     * @param object $object
     * @throws \Panlatent\Container\Exception
     */
    public function set($name, $object)
    {
        if ( ! is_object($object)) {
            throw new Exception("");
        }

        $this->storage[$name] = $object;
    }
}