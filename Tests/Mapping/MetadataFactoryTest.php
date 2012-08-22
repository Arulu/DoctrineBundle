<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Arulu\Bundle\DoctrineBundle\Tests;

use Arulu\Bundle\DoctrineBundle\Tests\TestCase;
use Arulu\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use Arulu\Bundle\DoctrineBundle\Mapping\ClassMetadataCollection;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class MetadataFactoryTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        if (!class_exists('Doctrine\\ORM\\Version')) {
            $this->markTestSkipped('Doctrine ORM is not available.');
        }
    }

    public function testFindNamespaceAndPathForMetadata()
    {
        $class = new ClassMetadataInfo(__CLASS__);
        $collection = new ClassMetadataCollection(array($class));

        $registry = $this->getMock('Doctrine\Common\Persistence\ManagerRegistry');
        $factory = new MetadataFactory($registry);

        $this->setExpectedException("RuntimeException", "Can't find base path for \"Arulu\Bundle\DoctrineBundle\Tests\MetadataFactoryTest");
        $factory->findNamespaceAndPathForMetadata($collection);
    }
}
