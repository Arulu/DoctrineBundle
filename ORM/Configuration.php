<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\ORM;

use Doctrine\ORM\Configuration as BaseConfiguration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Bundle\DoctrineBundle\ORM\QuoteStrategy;

class Configuration extends BaseConfiguration implements ContainerAwareInterface
{
	public function setConnections($connections)
	{
		$this->_attributes['connections'] = $connections;
	}

	public function setConnectionMap($connectionMap)
	{
		$this->_attributes['connection_map'] = $connectionMap;
	}

	public function getConnections()
	{
		return $this->_attributes['connections'];
	}

	public function getConnectionFor($entityAlias)
	{
		return isset($this->_attributes['connection_map'][$entityAlias]) ? $this->_attributes['connection_map'][$entityAlias] : null;
	}

	public function getConnectionForNamespace($className)
	{
		if(isset($this->_attributes['connection_class_map'][$className]))
			return $this->_attributes['connection_class_map'][$className];

		foreach(array_keys($this->_attributes['connection_map']) as $name)
		{
			if(preg_match("/" .addslashes($name)."(.*)/i", $className))
			{
				$connection = $this->_attributes['connection_map'][$name];
				$this->_attributes['connection_class_map'][$className] = $connection;

				return $connection;
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->_attributes['container'] = $container;
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer()
	{
		return $this->_attributes['container'];
	}

	/**
	 * Get quote strategy.
	 *
	 * @since 2.3
	 * @return Doctrine\ORM\Mapping\QuoteStrategy
	 */
	public function getQuoteStrategy()
	{
		if ( ! isset($this->_attributes['quoteStrategy'])) {
			$this->_attributes['quoteStrategy'] = new QuoteStrategy();
		}

		return $this->_attributes['quoteStrategy'];
	}
}
