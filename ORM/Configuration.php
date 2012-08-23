<?php

/*
 * Copyright (c) 2012 Arulu Inversiones SL
 * Todos los derechos reservados
 */

namespace Doctrine\Bundle\DoctrineBundle\ORM;

use Doctrine\ORM\Configuration as BaseConfiguration;

class Configuration extends BaseConfiguration
{
	protected $entityDatabases = array();

	public function setEntityDatabases($entityDatabases)
	{
		$this->_attributes['entityDatabases'] = $entityDatabases;
	}

	public function getEntityDatabase($entityAlias)
	{
		if(isset($this->entityDatabases[$entityAlias]))
			return $this->entityDatabases[$entityAlias];

		preg_match("/(.*)Bundle/i", $entityAlias, $bundle);

		$class = explode("\\", $bundle[0]);

		$this->entityDatabases[$entityAlias] = $this->_attributes['entityDatabases'][array_pop($class)];

		return $this->entityDatabases[$entityAlias];
	}
}
