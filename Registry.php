<?php

/*
 * This file is part of the Doctrine Bundle
 *
 * The code was originally distributed inside the Symfony framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Doctrine Project, Benjamin Eberlei <kontakt@beberlei.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Doctrine\Bundle\DoctrineBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;

/**
 * References all Doctrine connections and entity managers in a given Container.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Registry extends ManagerRegistry implements RegistryInterface
{
	protected $managers, $proxyInterfaceName;

    /**
     * Construct.
     *
     * @param ContainerInterface $container
     * @param array              $connections
     * @param array              $entityManagers
     * @param string             $defaultConnection
     * @param string             $defaultEntityManager
     */
    public function __construct(ContainerInterface $container, array $connections, array $entityManagers, $defaultConnection, $defaultEntityManager)
    {
        $this->setContainer($container);
        $this->managers = $entityManagers;
        $this->proxyInterfaceName = 'Doctrine\ORM\Proxy\Proxy';

		parent::__construct('ORM', $connections, $entityManagers, $defaultConnection, $defaultEntityManager, $this->proxyInterfaceName);
    }

    /**
     * Gets the default entity manager name.
     *
     * @return string The default entity manager name
     *
     * @deprecated
     */
    public function getDefaultEntityManagerName()
    {
        return $this->getDefaultManagerName();
    }

    /**
     * Gets a named entity manager.
     *
     * @param string $name The entity manager name (null for the default one)
     *
     * @return EntityManager
     *
     * @deprecated
     */
    public function getEntityManager($name = null)
    {
        return $this->getManager($name);
    }

    /**
     * Gets an array of all registered entity managers
     *
     * @return EntityManager[] an array of all EntityManager instances
     *
     * @deprecated
     */
    public function getEntityManagers()
    {
        return $this->getManagers();
    }

    /**
     * Resets a named entity manager.
     *
     * This method is useful when an entity manager has been closed
     * because of a rollbacked transaction AND when you think that
     * it makes sense to get a new one to replace the closed one.
     *
     * Be warned that you will get a brand new entity manager as
     * the existing one is not useable anymore. This means that any
     * other object with a dependency on this entity manager will
     * hold an obsolete reference. You can inject the registry instead
     * to avoid this problem.
     *
     * @param string $name The entity manager name (null for the default one)
     *
     * @return EntityManager
     */
    public function resetEntityManager($name = null)
    {
        $this->resetManager($name);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered entity managers.
     *
     * @param string $alias The alias
     *
     * @return string The full namespace
     */
    public function getEntityNamespace($alias)
    {
        return $this->getAliasNamespace($alias);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     *
     * This method looks for the alias in all registered entity managers.
     *
     * @param string $alias The alias
     *
     * @return string The full namespace
     *
     * @see Configuration::getEntityNamespace
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
    }

    /**
     * Gets all connection names.
     *
     * @return array An array of connection names
     */
    public function getEntityManagerNames()
    {
        return $this->getManagerNames();
    }

    /**
     * Gets the entity manager associated with a given class.
     *
     * @param string $class A Doctrine Entity class name
     *
     * @return EntityManager|null
     */
    public function getEntityManagerForClass($class)
    {
        return $this->getManagerForClass($class);
    }

	public function getManagerForClass($class)
    {
        // Check for namespace alias
        if (strpos($class, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $class);
            $class = $this->getAliasNamespace($namespaceAlias) . '\\' . $simpleClassName;
        }

        $proxyClass = new \ReflectionClass($class);
        if ($proxyClass->implementsInterface($this->proxyInterfaceName)) {
            $class = $proxyClass->getParentClass()->getName();
        }

		$compatibleManagers = [];
        foreach ($this->managers as $id) {
            $manager = $this->getService($id);

            if (!$manager->getMetadataFactory()->isTransient($class)) {
                $compatibleManagers[] = $manager;
            }
        }

		if(count($compatibleManagers) == 1)
			return $compatibleManagers[0];
		else if(!$this->getManager()->getMetadataFactory()->isTransient($class))
			return $this->getManager();
		else
			throw new \Exception("Class $class is available through various managers and not through the default one, so you should specify which entity manager use.");
    }
}
