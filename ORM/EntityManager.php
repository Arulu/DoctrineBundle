<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\ORM;

use Doctrine\ORM\EntityManager as BaseEntityManager;
use Doctrine\ORM\Configuration as BaseConfiguration;
use Doctrine\Common\EventManager;
use Doctrine\ORM\ORMException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;

class EntityManager extends BaseEntityManager
{
	protected $connections = array();

	/**
	 * {@inheritDoc}
	 */
	public static function create($conn, BaseConfiguration $config, EventManager $eventManager = null)
	{
		if ( ! $config->getMetadataDriverImpl()) {
			throw ORMException::missingMappingDriverImpl();
		}

		switch (true) {
			case (is_array($conn)):
				$conn = \Doctrine\DBAL\DriverManager::getConnection(
					$conn, $config, ($eventManager ?: new EventManager())
				);
				break;

			case ($conn instanceof Connection):
				if ($eventManager !== null && $conn->getEventManager() !== $eventManager) {
					throw ORMException::mismatchedEventManager();
				}
				break;

			default:
				throw new \InvalidArgumentException("Invalid argument: " . $conn);
		}

		return new EntityManager($conn, $config, $conn->getEventManager());
	}

	public function createTranslatableQuery($dql = "", $locale = null)
	{
		$query = $this->createQuery($dql)
			->setHint(
				Query::HINT_CUSTOM_OUTPUT_WALKER,
				'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
			);

		// locale
		if($locale !== null)
		{
			$query->setHint(
				TranslatableListener::HINT_TRANSLATABLE_LOCALE,
				$locale
			);
		}

		return $query;
	}

	public function setConnections($connections)
	{
		$this->connections = $connections;
	}

	public function hasConnection(Connection $connectionOrigin)
	{
		foreach($this->connections as $connection)
		{
			if($connectionOrigin === $connection)
				return true;
		}

		return false;
	}
}
