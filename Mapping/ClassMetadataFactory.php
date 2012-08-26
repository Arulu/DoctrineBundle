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

		if(!isset($this->connectionMetadata[$className]))
		{
			if(!preg_match("/(.*)Bundle/i", $className, $bundle))
			{
				// prefixed class, do something about it
				$connection = $this->em->getConfiguration()->getConnectionForNamespace($className);
			}
			else
			{
				$alias = explode("\\", $bundle[0]);
				$connection = $this->em->getConfiguration()->getConnectionFor(array_pop($alias));
			}


			$this->loadedMetadata[$className]->setConnection($connection['instance']);
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