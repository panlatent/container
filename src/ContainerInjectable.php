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

interface ContainerInjectable extends Injectable
{
    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer();

    /**
     * @param \Psr\Container\ContainerInterface $container
     * @return mixed
     */
    public function setContainer(ContainerInterface $container);
}