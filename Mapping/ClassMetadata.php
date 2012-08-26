<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata as BaseClassMetadata;
use Doctrine\DBAL\Connection;

class ClassMetadata extends BaseClassMetadata
{
	/**
	 * @var Connection
	 */
	protected $connection;

	public function setConnection(Connection $database)
	{
		$this->connection = $database;
	}

	public function hasConnection(Connection $connection)
	{
		return $this->connection === $connection;
	}

	public function getConnection()
	{
		return $this->connection;
	}

	public function getTableName()
	{
		return $this->connection->getDatabase() . "." . $this->table['name'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQuotedTableName($platform)
	{
		$tableName = parent::getQuotedTableName($platform);

		return $this->getDatabaseQuote($tableName);
	}

	private function getDatabaseQuote($tableName)
	{
		return $this->connection->getDatabase() . "." . preg_replace("/(.*)\.(.*)/i", "$2", $tableName);
	}

	/**
	 * Gets the (possibly quoted) name of the join table.
	 *
	 * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
	 * @return string
	 */
	public function getQuotedJoinTableName(array $assoc, $platform)
	{
		$tableName = parent::getQuotedJoinTableName($assoc, $platform);

		return $this->getDatabaseQuote($tableName);
	}
}
