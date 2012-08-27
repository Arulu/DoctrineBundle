<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\Mapping;

use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;

class ClassMetadataFactory extends BaseClassMetadataFactory
{
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 * @var array
	 */
	protected $loadedMetadata = array();

	protected $connectionMetadata = array();

	/**
	 * {@inheritDoc}
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
		parent::setEntityManager($entityManager);

		$this->em = $entityManager;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getLoadedMetadata()
	{
		return $this->loadedMetadata;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function newClassMetadataInstance($className)
	{
		return new ClassMetadata($className);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMetadataFor($className)
	{
		$this->loadedMetadata[$className] = parent::getMetadataFor($className);

		$namespace = $this->loadedMetadata[$className]->namespace;

		if(!isset($this->connectionMetadata[$namespace]))
		{
			$connection = $this->em->getConfiguration()->getConnectionForNamespace($namespace);

			$this->loadedMetadata[$className]->setConnection($connection);
			$this->connectionMetadata[$className] = true;
		}

		return $this->loadedMetadata[$className];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMetadataForConnection(Connection $connection)
	{
		$metadatas = parent::getAllMetadata();

		$metadatasConnection = array();

		// look for specific metadata in this connection
		foreach($metadatas as $metadata)
		{
			if($metadata->hasConnection($connection))
			{
				$metadatasConnection[] = $metadata;
			}
		}

		return $metadatasConnection;
	}
}
