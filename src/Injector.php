<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Psr\Container\ContainerInterface;

class Injector
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Injector constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Resolve $resolve
     * @return mixed
     */
    public function make(Resolve $resolve)
    {
        $classes = $resolve->getRequiredClasses();
        $params = [];
        foreach ($classes as $class) {
            if (! $this->container->has($class)) {
                throw new NotFoundException("Unable get a resolve depend object: $class");
            }
            $params[] = $this->container->get($class);
        }

        return array_combine($classes, $params);
    }
}