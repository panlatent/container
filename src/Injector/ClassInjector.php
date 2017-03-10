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
use ReflectionClass;
use ReflectionMethod;

/**
 * Class ClassInjector
 *
 * @package Panlatent\Container\Injector
 */
class ClassInjector extends Injector
{
    /**
     * 使用构造注入(如果存在构造函数)
     */
    const WITH_CONSTRUCTOR = 128;

    /**
     * 使用接口注入
     */
    const WITH_INTERFACE = 256;

    /**
     * 使用Setter注入
     */
    const WITH_SETTER = 512;

    /**
     * 使用注解方式注入Setter
     */
    const WITH_SETTER_ANNOTATE = 1024;

    /**
     * @var \ReflectionClass
     */
    protected $class;

    /**
     * @var object|null
     */
    protected $instance;

    /**
     * @var array
     */
    protected $parameterTypeCache = [];

    /**
     * @var array
     */
    protected $interfaces = [];

    /**
     * @var array
     */
    protected $setters = [];

    /**
     * @var bool
     */
    protected $isConstructor;

    /**
     * @var bool
     */
    protected $isInterface;

    /**
     * @var bool
     */
    protected $isSetter;

    /**
     * @var bool
     */
    protected $isSetterAnnotate;

    /**
     * ClassInjector constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param                                   $context
     */
    public function __construct(ContainerInterface $container, $context)
    {
        parent::__construct($container, $context);

        if (is_object($context)) {
            $this->instance = $context;
        }
        $this->class = new ReflectionClass($context);
        $this->withOption(static::WITH_CONSTRUCTOR);
    }

    /**
     * @return $this
     */
    public function withConstructor()
    {
        $this->isConstructor = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutConstructor()
    {
        $this->isConstructor = false;

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function withInterface($name)
    {
        if( ! array_search($name, $this->interfaces)) {
            $this->interfaces[] = $name;
        }

        return $this;
    }

    /**
     * @param $names
     * @return $this
     */
    public function withInterfaces($names)
    {
        $this->interfaces = array_merge($this->interfaces, $names);

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function withSetter($name)
    {
        if ( ! array_search($name, $this->setters)) {
            $this->setters[] = $name;
        }

        return $this;
    }

    /**
     * @param $names
     * @return $this
     */
    public function withSetters($names)
    {
        $this->setters = array_merge($this->setters, $names);

        return $this;
    }

    /**
     * @return int
     */
    public function getOption()
    {
        return parent::getOption() |
            $this->isConstructor * static::WITH_CONSTRUCTOR |
            $this->isInterface * static::WITH_INTERFACE |
            $this->isSetter * static::WITH_SETTER |
            $this->isSetterAnnotate * static::WITH_SETTER_ANNOTATE;
    }

    /**
     * @param $option
     * @return ClassInjector
     */
    public function setOption($option)
    {
        parent::setOption($option);

        $this->isConstructor = (($option & static::WITH_CONSTRUCTOR) ==
            static::WITH_CONSTRUCTOR);
        $this->isInterface = (($option & static::WITH_INTERFACE) ==
            static::WITH_INTERFACE);
        $this->isSetter = (($option & static::WITH_SETTER) ==
            static::WITH_SETTER);
        $this->isSetterAnnotate = (($option & static::WITH_SETTER_ANNOTATE) ==
            static::WITH_SETTER_ANNOTATE);
        
        return $this;
    }

    /**
     *
     */
    public function handle()
    {
        if ( ! is_object($this->context)) {
            if ($this->isConstructor) {
                $this->injectConstructor();
            } else {
                $this->instance = $this->class->newInstanceWithoutConstructor();
            }
        }

        if ($this->isInterface) {
            $interfaces = $this->class->getInterfaces();
            $interfaces = $this->filterInterfaces($interfaces);
            foreach ($interfaces as $interface) {
                $this->injectInterface($interface);
            }
        }

        if ($this->isSetter) {
            $this->injectSetter();
        }
    }

    /**
     * @return mixed
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param       $name
     * @param array $extraParameterValues
     * @return mixed
     */
    public function getReturn($name, $extraParameterValues = [])
    {
        if ( ! isset($this->parameterTypeCache[$name])) {
            return $this->injectMethod($this->class->getMethod($name));
        } elseif ( ! $this->parameterTypeCache[$name]) {
            return call_user_func([$this->instance, $name]);
        }

        $parameterTypes = $this->parameterTypeCache[$name];
        $dependValues = $this->getParameterDependValues($parameterTypes,
            $extraParameterValues);
        return call_user_func_array([$this->instance, $name], $dependValues);
    }

    /**
     *
     */
    protected function injectConstructor()
    {
        $parameterTypes = $this->getConstructorParameterTypes();
        if (false === $parameterTypes) {
            $this->instance = $this->class->newInstanceWithoutConstructor();
        } elseif (empty($parameterTypes)) {
            $this->instance = $this->class->newInstance();
        } else {
            $dependValues = $this->getParameterDependValues($parameterTypes);
            $this->instance = $this->class->newInstanceArgs($dependValues);
        }
    }

    /**
     * @param \ReflectionClass $interface
     */
    protected function injectInterface(ReflectionClass $interface)
    {
        $methods = $interface->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $method = $this->class->getMethod($method->getName());
            $this->injectMethod($method);
        }
    }

    /**
     *
     */
    protected function injectSetter()
    {
        // @TODO
    }

    /**
     * @param \ReflectionMethod $method
     * @param array             $extraParameterValues
     * @return mixed
     */
    protected function injectMethod(ReflectionMethod $method,
                                    $extraParameterValues = [])
    {
        if ( ! ($parameters = $method->getParameters())) {
            $this->parameterTypeCache[$method->getName()] = false;

            return $method->invoke($this->instance);
        }

        $parameterTypes = $this->getParameterTypes($parameters);
        $this->parameterTypeCache[$method->getName()] = $parameterTypes;
        $dependValues = $this->getParameterDependValues($parameterTypes,
            $extraParameterValues);

        return $method->invokeArgs($this->instance, $dependValues);
    }

    /**
     * @param \ReflectionClass[] $interfaces
     * @return array
     */
    protected function filterInterfaces($interfaces)
    {
        $passInterfaces = [];
        foreach ($interfaces as $interface) {
            foreach ($this->interfaces as $allowInterface) {
                if ($interface->getName() === $allowInterface ||
                    $interface->isSubclassOf($allowInterface)) {
                    $passInterfaces[] =  $interface;
                }
            }
        }

        return $passInterfaces;
    }

    /**
     *
     */
    protected function filterSetters()
    {

    }

    /**
     * @return array|bool
     */
    protected function getConstructorParameterTypes()
    {
        if ( ! ($constructor = $this->class->getConstructor())) {
            return false;
        } elseif ( ! ($parameters = $constructor->getParameters())) {
            return [];
        }

        return $this->getParameterTypes($parameters);
    }
}