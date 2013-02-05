<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Model_FruitcakeEntry extends Orm\Model
{
	protected static $_table_name = 'fruitcake_entry';
	protected static $_primary_key = array('year', 'appID', 'steamID64');
	protected static $_properties = array('year', 'appID', 'steamID64', 'anonymous', 'count', 'source', 'timestamp');
}
