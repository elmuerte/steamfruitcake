<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

define('OPENID_SITE', $_SERVER['SERVER_NAME']);
define('STEAM_OPENID_URL', 'http://steamcommunity.com/openid');
define('STEAM_OPENID_IDENTITY_REGEX', '#^http(s)?://steamcommunity.com/openid/id/([0-9]+)#i');

class Controller_Fruitcake_Auth extends Controller_AbstractFruitcake {

	public function action_index(){
		$session = Session::instance();

		// check for OpenID callback
		if (Input::param('openid_mode', null) != null) {
			Package::load('openid');
			$openid = new LightOpenID(OPENID_SITE);
			if($openid->mode == 'cancel') {
				Messages::error('Authentication cancelled.');
			} else {
				if ($openid->validate()) {
					if (preg_match(STEAM_OPENID_IDENTITY_REGEX, $openid->identity, $vars)) {
						$session->set('steamID64', $vars[2]);
					}
				}
			}
		}

		if ($session->get('steamID64', 0) == 0) {
			// present login form
			$this->template->content = View::forge('fruitcake/auth/login-form');
			return;
		}
		return Response::redirect('fruitcake/profile');
	}

	public function post_index() {
		Package::load('openid');
		$openid = new LightOpenID(OPENID_SITE);
		$openid->identity = STEAM_OPENID_URL;
		return Response::redirect($openid->authUrl());
	}

	public function action_logout() {
		$this->template->title = "Log out";
		$this->template->content = View::forge('fruitcake/auth/logout');
	}

	public function post_logout() {
		$session = Session::instance();
		$session->destroy();
		$this->template->title = "Logged out";
		$this->template->content = View::forge('fruitcake/auth/loggedout');
	}
}
