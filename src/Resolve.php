<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Panlatent\Container;

use ReflectionParameter;

abstract class Resolve
{
    /**
     * @var ReflectionParameter[]
     */
    protected $parameters = [];

    /**
     * Gets required inject classes.
     *
     * @return array
     */
    public function getRequiredClasses()
    {
        $classes = [];
        foreach ($this->parameters as $parameter) {
            if (! $parameter->isOptional()) {
                if (($class = $parameter->getClass()->getName())) {
                    $classes[] = $class;
                }
            }
        }

        return $classes;
    }

    protected function completeParams($params)
    {
        if (count($this->parameters) > count($params)) {
            $params = array_merge($params, $this->getParameterDefaultValues(count($params)));
        }
        if (count($this->parameters) > count($params)) {
            // $this->parameters[count($params)]->getDeclaringClass();
            throw new ResolveException("Missing parameter " . count($params) .
                " " . $this->parameters[count($params)]->getName() . " ".
                "for: " . $this->parameters[count($params)]->getDeclaringFunction()->getName());
        }

        return $params;
    }

    /**
     * Gets default values of the remaining parameters.
     *
     * @param int $start
     * @return array
     * @throws ResolveException
     */
    protected function getParameterDefaultValues($start)
    {
        $values = [];
        for ($i = $start; $i < count($this->parameters); ++$i) {
            $parameter = $this->parameters[$i];
            if (! $parameter->isOptional()) {
                throw new ResolveException(
                    "Missing parameter {$parameter->getPosition()} {$parameter->getName()} ".
                    "for: " . $parameter->getDeclaringFunction()->getName());
            }
            $values[] = $this->parameters[$i]->getDefaultValue();
        }

        return $values;
    }
}