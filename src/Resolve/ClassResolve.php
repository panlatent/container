<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container\Resolve;

use Panlatent\Container\Resolve;
use Panlatent\Container\ResolveException;
use ReflectionClass;

class ClassResolve extends Resolve
{
    protected $class;

    protected $constructor;

    public function __construct($className)
    {
        $this->class = new ReflectionClass($className);
        $this->setParametersByConstructor($this->class);
    }

    /**
     * Create a object.
     *
     * @param array $params
     * @return object
     * @throws ResolveException
     */
    public function getInstance($params = [])
    {
        if ( ! $this->constructor) {
            return $this->class->newInstanceWithoutConstructor();
        } elseif (empty($this->parameters)) {
            return $this->class->newInstance();
        }
        $params = $this->completeParams($params);

        return $this->class->newInstanceArgs($params);
    }

    /**
     * Sets reflection parameters from class constructor.
     *
     * @param ReflectionClass $class
     */
    private function setParametersByConstructor(ReflectionClass $class)
    {
        if (! ($constructor = $class->getConstructor())) {
            $this->constructor = false;
        } else {
            $this->parameters = $constructor->getParameters();
        }
    }
}