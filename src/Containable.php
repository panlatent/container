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

interface Containable extends ContainerInterface
{
    /**
     * Gets an object from this container.
     *
     * @param string $name
     * @return object|bool Returns a object if the container can be obtained, FALSE otherwise.
     */
    public function get($name);

    /**
     * Returns whether this depend name exists or registered in this container.
     *
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * Set a depend name to this container.
     *
     * @param string                 $name
     * @param string|object|callable $builder
     * @param bool                   $singleton
     * @return void
     */
    public function set($name, $builder, $singleton = false);

    /**
     * Delete a depend name.
     *
     * @param string $name
     * @return void
     */
    public function remove($name);
}