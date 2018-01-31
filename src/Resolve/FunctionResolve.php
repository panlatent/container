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
use ReflectionFunction;

class FunctionResolve extends Resolve
{
    protected $function;

    public function __construct($function)
    {
        $this->function = new ReflectionFunction($function);
        $this->parameters = $this->function->getParameters();
    }

    public function getReturn($params = [])
    {
        if (empty($this->parameters)) {
            return $this->function->invoke();
        }
        $params = $this->completeParams($params);

        return $this->function->invokeArgs($params);
    }
}