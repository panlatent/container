<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

/**
 * Interface Singleton
 *
 * @package Panlatent\Container
 */
interface Singleton
{
    /**
     * Return a singleton object.
     *
     * @return static
     */
    public static function singleton();
}