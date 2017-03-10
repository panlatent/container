<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container\Injector;

use Panlatent\Container\Injector;
use Psr\Container\ContainerInterface;
use ReflectionFunction;

/**
 * Class FunctionInjector
 *
 * @package Panlatent\Container\Injector
 */
class FunctionInjector extends Injector
{
    /**
     * @var \ReflectionFunction
     */
    protected $function;

    /**
     * @var array
     */
    protected $parameterTypes;

    /**
     * FunctionInjector constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param                                   $context
     */
    public function __construct(ContainerInterface $container, $context)
    {
        parent::__construct($container, $context);

        $this->function = new ReflectionFunction($context);
    }

    /**
     *
     */
    public function handle()
    {
        if (($parameters = $this->function->getParameters())) {
            $this->parameterTypes = $this->getParameterTypes($parameters);
        }
    }

    /**
     * @param array $extraParameterValues
     * @return mixed
     */
    public function getReturn($extraParameterValues = [])
    {
        if ($this->parameterTypes) {
            $dependValues = $this->getParameterDependValues
            ($this->parameterTypes, $extraParameterValues);

            return $this->function->invokeArgs($dependValues);
        }

        return $this->function->invoke();
    }
}