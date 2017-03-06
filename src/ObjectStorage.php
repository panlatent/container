<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

class ObjectStorage extends Storage implements \ArrayAccess, \Countable, \Iterator
{

    public function find($className)
    {
        foreach ($this->storage as $object) {
            if (get_class($object) == $className) {
                return $object;
            }
        }

        return false;
    }

    public function findInstanceOf($className)
    {
        foreach ($this->storage as $object) {
            if ($object instanceof $className) {
                return $object;
            }
        }

        return false;
    }

}