<?php

class Model_FruitcakeEntry extends Orm\Model
{
	protected static $_table_name = 'fruitcake_entry';
	protected static $_primary_key = array('appID', 'steamID64');
	protected static $_properties = array('appID', 'steamID64', 'count', 'timestamp');
}
