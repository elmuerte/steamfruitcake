<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_AbstractFruitcake extends Controller_Template {

	public function before()
	{
		parent::before();
		$this->template->title = "SteamFruitcake";
	}

	public function after($response)
	{
		$response = parent::after($response);
		// ...
		return $response;
	}
}