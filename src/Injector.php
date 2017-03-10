<?php
/**
 * Container - A depend inject container
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use Panlatent\Container\Injector\ClassInjector;
use Panlatent\Container\Injector\FunctionInjector;
use Psr\Container\ContainerInterface;

/**
 * Class Injector
 *
 * 依赖注入器使用反射器获得依赖关系并构造所需对象, 它提供了构造注入、接口注入和
 * setter注入三种方式。
 *
 * @package Panlatent\Container
 */
abstract class Injector
{
    /**
     * 找不到类依赖的情况下, 使用参数名查找依赖
     */
    const WITH_FIND_PARAMETER_NAME = 1;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @var bool
     */
    protected $isFindParameterName = true;

    /**
     * Injector constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param                                   $context
     */
    public function __construct(ContainerInterface $container, $context)
    {
        $this->container = $container;
        $this->context = $context;
    }

    /**
     * @param $container
     * @param $context
     * @return \Panlatent\Container\Injector\ClassInjector|\Panlatent\Container\Injector\FunctionInjector
     * @throws \Panlatent\Container\Exception
     */
    public static function bind($container, $context)
    {
        if (is_object($context)) {
            return new ClassInjector($container, $context);
        } elseif (is_callable($context)) {
            return new FunctionInjector($container, $context);
        } elseif (class_exists($context, true)) {
            return new ClassInjector($container, $context);
        }

        throw new Exception('The injector cannot be bound to an object, class or function');
    }

    /**
     * @return mixed
     */
    abstract public function handle();

    /**
     * @param $option
     * @return $this
     */
    public function withOption($option)
    {
        $this->setOption($this->getOption() | $option);

        return $this;
    }

    /**
     * @param $option
     * @return $this
     */
    public function withoutOption($option)
    {
        $this->setOption($this->getOption() ^ $option);

        return $this;
    }

    /**
     * @return bool
     */
    public function getOption()
    {
        return $this->isFindParameterName * static::WITH_FIND_PARAMETER_NAME;
    }

    /**
     * @param $option
     * @return $this
     */
    public function setOption($option)
    {
        $this->isFindParameterName = (($option &
            static::WITH_FIND_PARAMETER_NAME) ==
            static::WITH_FIND_PARAMETER_NAME);

        return $this;
    }

    /**
     * @param \ReflectionParameter[] $parameters
     * @return array
     */
    protected function getParameterTypes($parameters)
    {
        $types = [];
        foreach ($parameters as $parameter) {
            $type = [];
            $type['pos'] = $parameter->getPosition();
            $type['name'] = $parameter->getName();
            $type['optional'] = $parameter->isOptional();
            if (null !== ($class = $parameter->getClass())) {
                $type['class'] = $class->getName();
            } else {
                $type['class'] = false;
            }
            if ($type['optional']) {
                $type['defaultValue'] = $parameter->getDefaultValue();
            }
            $types[] = $type;
        }

        return $types;
    }

    /**
     * @param       $parameterTypes
     * @param array $extraParameterValues
     * @return array
     * @throws \Panlatent\Container\Exception
     */
    protected function getParameterDependValues($parameterTypes,
                                                $extraParameterValues = [])
    {
        $values = [];
        $extraParameterValues = array_reverse($extraParameterValues);
        $isUseExtraParameter = false;
        foreach ($parameterTypes as $type) {
            $pos = $type['pos'];
            if ($isUseExtraParameter) {
                if (empty($extraParameterValues)) {
                    $isUseExtraParameter = false;
                } else {
                    $values[$pos] = array_pop($extraParameterValues);
                }
            }

            if ( ! $isUseExtraParameter) {
                $values[$pos] = $this->findParameterDependValue($type['class'],
                    $type['name']);
                if (false === $values[$pos] && ! $type['optional']) {
                    if (empty($extraParameterValues)) {
                        throw new Exception("Need to provide a dependency or an extra parameter for '{$type['name']}'");
                    } else {
                        $isUseExtraParameter = true;
                        $values[$pos] = array_pop($extraParameterValues);
                    }
                }
            }
        }

        if ( ! empty($extraParameters)) {
            throw new Exception("Too many extra parameters");
        }

        return $values;
    }

    /**
     * @param $dependClass
     * @param $dependName
     * @return bool|mixed
     */
    protected function findParameterDependValue($dependClass, $dependName)
    {
        if ($dependClass && $this->container->has($dependClass)) {
            return $this->container->get($dependClass);
        } elseif ($this->isFindParameterName
            && $this->container->has($dependName)) {
            return $this->container->get($dependName);
        }

        return false;
    }
}