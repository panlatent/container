<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Panlatent\Boost\Storable;
use Panlatent\Boost\Storage;

class ObjectStorage extends Storage implements \ArrayAccess, \Countable, \Iterator, Storable
{
    public function set($name, $object)
    {
        if ( ! is_object($object)) {
            throw new Exception("");
        }

        $this->storage[$name] = $object;
    }

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