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
	protected $entityManager;

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

		$this->entityManager = $entityManager;
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
		$metadata = new ClassMetadata($className);
		$metadata->setMetadataFactory($this);

		return $metadata;
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
			$connection = $this->entityManager->getConfiguration()->getConnectionForNamespace($namespace);

            if(!is_null($connection))
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

	/**
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}
}
