<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Model_SteamGame extends Orm\Model
{
	protected static $_table_name = 'games';
	protected static $_primary_key = array('appID');
	protected static $_properties = array('appID', 'name', 'logo', 'storeLink', 'lastUpdate');
}
