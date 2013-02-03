<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright ${year} Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_Fruitcake_Report extends Controller_AbstractFruitcake {

	public function get_index() {
		return Response::redirect('fruitcake/profile');
	}

	public function post_index() {
		$session = Session::instance();

		if ($session->get('steamID64', 0) == 0) {
			// not authenticated
			return Response::redirect('fruitcake/auth');
		}

		$steamUser = $session->get('steamUser', null);
		if ($steamUser == null) {
			// get profile
			return Response::redirect('fruitcake/profile');
		}

		$appID = (int) Input::param('appID', 0);

		if ($appID == 0) {
			throw new Exception("Invalid appID provided.");
		}

		$userGame = $steamUser->getGame($appID);
		if ($userGame == null) {
			throw new Exception("Provided app ID not part of your collection.");
		}

		if (Input::param('confirm', 0) == 1) {
			return $this->registerCake($steamUser, $userGame);
		}
		else {
			return $this->confirmPage($steamUser, $userGame);
		}
	}

	protected function confirmPage($steamUser, $userGame) {
		$game = $userGame->getGame();

		$view = View::forge('fruitcake/report/confirm');
		$view->set('appID', $game->appID);
		$view->set('name', $game->name);
		$view->set('logo', $game->logo);
		$view->set('storeLink', $game->storeLink);
		$view->set('quantity', $userGame->getQuantity());
		$view->set('inCollection', $userGame->inLibrary());
		$view->set('inInventory', $userGame->inInventory());

		$this->template->content = $view;
	}

	protected function registerCake($steamUser, $userGame) {
		$key = array('appID' => $userGame->appID, 'steamID64' => $steamUser->steamID64);
		$fruitcakeEntry = Model_FruitcakeEntry::find($key);
		if ($fruitcakeEntry == null) {
			$fruitcakeEntry = Model_FruitcakeEntry::forge($key);
		}

		if ((int) $fruitcakeEntry->count < $userGame->getQuantity()) {
			$fruitcakeEntry->count = $userGame->getQuantity();
			$fruitcakeEntry->timestamp = time();
			$fruitcakeEntry->save();
		}
		else {
			// ...
		}

		$game = $userGame->getGame();

		$view = View::forge('fruitcake/report/confirmed');
		$view->set('appID', $game->appID);
		$view->set('name', $game->name);
		$view->set('logo', $game->logo);
		$view->set('storeLink', $game->storeLink);
		$view->set('quantity', $userGame->getQuantity());
		$view->set('inCollection', $userGame->inLibrary());
		$view->set('inInventory', $userGame->inInventory());

		$this->template->content = $view;
	}
}
