<?php

class Model_SteamGame extends Orm\Model
{
	protected static $_table_name = 'games';
	protected static $_primary_key = array('appID');
	protected static $_properties = array('appID', 'name', 'logo', 'storeLink', 'lastUpdate');
}
