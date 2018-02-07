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

class MethodResolve extends Resolve
{
    protected $class;
    protected $method;

    public function __construct($class, $method)
    {
        $this->class = $class;
        $this->method = new \ReflectionMethod($class, $method);
        $this->parameters = $this->method->getParameters();
    }

    public function getReturn($params = [])
    {
        if (empty($this->parameters)) {
            return $this->method->invoke($this->class);
        }
        $params = $this->completeParams($params);

        return $this->method->invokeArgs($this->class, $params);
    }
}