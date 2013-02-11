<?php

/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_AbstractFruitcake extends Controller_Template {

	public function before() {
		if (Input::is_ajax()) {
			$this->template = 'ajax-template';
			Messages::info("this is an ajax request");
		}

		parent::before();
		Config::load('fruitcake', true);

		$this->template->title = "";
		$this->template->steamID = false;
		$this->template->steamProfile = false;
		$this->template->messages = array();
	}

	public function after($response) {
		if (Input::is_ajax()) {
			$this->template->messages = Messages::get_xml();
		}
		else {
			$this->template->messages = Messages::get();
		}

		// update menu when authenticated
		$session = Session::instance();
		$steamUser = $session->get('steamUser', null);
		if ($steamUser != null) {
			$this->template->steamID = $steamUser->steamID;
			$this->template->steamProfile = $steamUser;
		}

		$response = parent::after($response);
		// ...
		if (Input::is_ajax()) {
			$response->set_header("Content-Type", "text/xml");
		}
		return $response;
	}
}