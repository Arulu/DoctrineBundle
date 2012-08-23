<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\Mapping;

use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetadataFactory;

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

	/**
	 * {@inheritDoc}
	 */
	public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
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

		// inject database if exists
		$database = $this->em->getConfiguration()->getEntityDatabase($className);
		$this->loadedMetadata[$className]->setDatabase($database);

		return $this->loadedMetadata[$className];
	}
}
