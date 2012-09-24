<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\ORM;

use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class QuoteStrategy extends DefaultQuoteStrategy
{
	private function getDatabaseQuote($tableName, ClassMetadata $class)
	{
		return $class->getConnection()->getDatabase() . "." . $tableName;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTableName(ClassMetadata $class, AbstractPlatform $platform)
	{
		$table = parent::getTableName($class, $platform);

		return $this->getDatabaseQuote($table, $class);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getJoinTableName(array $association, ClassMetadata $class, AbstractPlatform $platform)
	{
		$table = parent::getJoinTableName($association, $class, $platform);

		return $this->getDatabaseQuote($table, $class);
	}
}
