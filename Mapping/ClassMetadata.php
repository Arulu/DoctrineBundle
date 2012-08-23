<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\Mapping;

use Doctrine\ORM\Mapping\ClassMetadata as BaseClassMetadata;

class ClassMetadata extends BaseClassMetadata
{
	protected $database = null;

	public function setDatabase($database)
	{
		$this->database = $database;
	}

	public function getDatabase()
	{
		return $this->database;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQuotedTableName($platform)
	{
		$tableName = parent::getQuotedTableName($platform);

		if(!is_null($this->database))
			return $this->getDatabaseQuote($tableName);

		return $tableName;
	}

	private function getDatabaseQuote($tableName)
	{
		return $this->database . "." . preg_replace("/(.*)\.(.*)/i", "$2", $tableName);
	}

	/**
	 * Gets the (possibly quoted) name of the join table.
	 *
	 * @param AbstractPlatform $platform
	 * @return string
	 */
	public function getQuotedJoinTableName(array $assoc, $platform)
	{
		$tableName = parent::getQuotedJoinTableName($assoc, $platform);

		if(!is_null($this->database))
			return $this->getDatabaseQuote($tableName);

		return $tableName;
	}
}
