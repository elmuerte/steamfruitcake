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
		$this->template->steamProfile = false;
		$this->template->messages = array();
	}

	public function after($response) {
		$this->template->messages = Message::get();

		// update menu when authenticated
		$session = Session::instance();
		$steamUser = $session->get('steamUser', null);
		if ($steamUser != null) {
			$this->template->steamID = $steamUser->steamID;
			$this->template->steamProfile = $steamUser;
		}

		$response = parent::after($response);
		// ...
		return $response;
	}
}