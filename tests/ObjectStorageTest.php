<?php
/**
 * Container - A depend inject container
 *
 * @author  panlatent@gmail.com
 * @link    https://github.com/panlatent/container
 * @license https://opensource.org/licenses/MIT
 */

namespace Tests;

use Tests\_data\Fruit;
use Tests\_data\Pear;
use Tests\_data\Plant;
use Panlatent\Container\ObjectStorage;

class ObjectStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $objects = [new Fruit(), new Pear(), new Plant()];
        $storage = new ObjectStorage($objects);
        $this->assertSame($objects[0], $storage->find(Fruit::class));

        $this->assertFalse($storage->find('Apple'));
    }

    public function testFindInstanceof()
    {
        $objects = [new Fruit(), new Pear(), new Plant()];
        $storage = new ObjectStorage($objects);
        $this->assertSame($objects[0], $storage->findInstanceOf(Fruit::class));
        $this->assertSame($objects[0], $storage->findInstanceOf(Plant::class));

        $this->assertFalse($storage->find('Apple'));
    }
}
