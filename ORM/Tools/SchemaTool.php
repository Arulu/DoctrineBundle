<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\ORM\Tools;

use Doctrine\ORM\Tools\SchemaTool as BaseSchemaTool;

class SchemaTool extends BaseSchemaTool
{
	/**
	 * @var \Doctrine\Bundle\DoctrineBundle\ORM\EntityManager
	 */
	protected $blackEntityManager;

	/**
	 * {@inheritDoc}
	 */
	public function getUpdateSchemaSql(array $classes, $saveMode=false)
	{
		$fromSchema = $this->blackEntityManager->getConnection()->getSchemaManager()->createSchema();

		echo "\n\n\n";

		foreach($this->blackEntityManager->getAdditionConnections() as $connection)
		{
			/** @var $connection \Doctrine\DBAL\Connection  */
			$newSchema = $connection->getSchemaManager()->createSchema();
			$fromSchema = $fromSchema->copy($newSchema);
		}

		var_dump($fromSchema->getTablesArray());

		$toSchema = $this->getSchemaFromMetadata($classes);

		foreach($toSchema->getTables() as $table)
		{
			$table->setName(preg_replace("/(.*)\./i", "", $table->getName()));
		}

		var_dump($toSchema->getTablesArray());

		$comparator = new \Doctrine\DBAL\Schema\Comparator();
		$schemaDiff = $comparator->compare($fromSchema, $toSchema);

		if ($saveMode) {
			return $schemaDiff->toSaveSql($this->blackEntityManager->getConnection()->getDatabasePlatform());
		} else {
			return $schemaDiff->toSql($this->blackEntityManager->getConnection()->getDatabasePlatform());
		}
	}

	public function setBlackEntityManager($blackEntityManager)
	{
		$this->blackEntityManager = $blackEntityManager;
	}

	public function getBlackEntityManager()
	{
		return $this->blackEntityManager;
	}
}
