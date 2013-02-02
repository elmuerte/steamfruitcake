<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright ${year} Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_fruitcake_profile extends Controller {

	public function action_index(){
		$session = Session::instance();

		if ($session->get('steamID', 0) == 0) {
			// not authenticated
			return Response::redirect('fruitcake/auth');
		}

		$steamUser = $session->get('steamUser', null);
		if ($steamUser == null) {
			$steamUser = new Model_SteamUser();
			$steamUser->steamID64 = $session->get('steamID', 0);
			$session->set('steamUser', $steamUser);
		}

		if (Input::post('forceUpdate') == '1') {
			$steamUser->lastUpdate = 0;
			$steamUser->lastLibraryUpdate = 0;
			$steamUser->lastInventoryUpdate = 0;
		}

		try {
			if ($steamUser->lastUpdate < 1) {
				Profiler::mark('Updating profile');
				$steamUser->updateProfile();
			}

			if ($steamUser->isPublic()) {
				// TODO: error handling
				if ($steamUser->lastLibraryUpdate < 1)
				{
					Profiler::mark('Updating library');
					$steamUser->updateLibrary();
				}
				if ($steamUser->lastInventoryUpdate < 1)
				{
					Profiler::mark('Updating inventory');
					$steamUser->updateInventory();
				}
			}
		}
		catch (Exception $e) {
			Debug::dump($e);
			return false;
		}

		$view = View::forge('fruitcake/profile/overview');
		$view->set('messages', '');
		$view->set('steamID', $steamUser->steamID);
		$view->set('privacyState', $steamUser->privacyState);
		$view->set('avatarIcon', $steamUser->avatarIcon);
		$view->set('lastUpdate', strftime('%c', $steamUser->lastUpdate));

		if (!$steamUser->isPublic()) {
			$view->set('messages', 'Cannot retrieve game collecion inventory. The steam profile is not public.');
		}

		$games = "";
		foreach ($steamUser->games as $userGame) {
			$game = $userGame->getGame();
			$gameview = View::forge('fruitcake/profile/game');
			$gameview->set('appID', $game->appID);
			$gameview->set('name', $game->name);
			$gameview->set('logo', $game->logo);
			$gameview->set('storeLink', $game->storeLink);
			$gameview->set('quantity', $userGame->getQuantity());
			$gameview->set('inCollection', $userGame->inLibrary());
			$gameview->set('inInventory', $userGame->inInventory());
			$games .= $gameview->render();
		}

		$view->set('games', $games, false);

		return Response::forge($view);
	}
}
