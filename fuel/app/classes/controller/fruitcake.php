<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright ${year} Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_fruitcake extends Controller
{
	public function action_index()
	{
		return Response::forge(View::forge('fruitcake/index'));
	}
}
