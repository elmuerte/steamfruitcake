<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_AbstractFruitcake extends Controller_Template {

	public function before() {
		parent::before();
		$this->template->title = "";
		$this->template->steamID = false;
	}

	public function after($response) {
		// update menu when authenticated
		$session = Session::instance();
		$steamUser = $session->get('steamUser', null);
		if ($steamUser != null) {
			$this->template->steamID = $steamUser->steamID;
		}

		$response = parent::after($response);
		// ...
		return $response;
	}
}