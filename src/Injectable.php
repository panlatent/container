<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

interface Injectable {

    /**
     * @return \Panlatent\Container\Containable
     */
    public function getContainer();

    /**
     * @param \Panlatent\Container\Containable $container
     * @return mixed
     */
    public function setContainer(Containable $container);

}