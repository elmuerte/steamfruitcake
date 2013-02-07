<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright ${year} Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_Fruitcake_Profile extends Controller_AbstractFruitcake {

	public function action_index(){
		$session = Session::instance();

		if ($session->get('steamID64', 0) == 0) {
			// not authenticated
			return Response::redirect('fruitcake/auth');
		}

		$steamUser = $session->get('steamUser', null);
		if ($steamUser == null) {
			$steamUser = new Model_SteamUser();
			$steamUser->steamID64 = $session->get('steamID64', 0);
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
			Message::error($e->getMessage());
		}

		if (!$steamUser->isPublic()) {
			Message::error('<h4>Non public profile!</h4>Unable to retrieve your game collection. Your profile needs to be temporarily public for Steam<em>Fruitcake</em>&trade; to gather information.');
		}

		$view = View::forge('fruitcake/profile/overview');
		$view->profileDetails = $this->getProfileDetails($steamUser);

		$games = array();
		/*
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

			array_push($games, $gameview);
		}
		*/
		$view->games = $games;

		$this->template->title = "Profile";
		$this->template->content = $view;
	}

	protected function getProfileDetails(Model_SteamUser $steamUser) {
		$details = View::forge('fruitcake/profile/details');
		$details->profile = $steamUser;
		$details->set('steamID', $steamUser->steamID);
		$details->set('realname', $steamUser->realname);
		$details->set('privacyState', $steamUser->privacyState);
		$details->set('avatarIcon', $steamUser->avatarIcon);
		$details->set('avatarMedium', $steamUser->avatarMedium);
		$details->set('avatarFull', $steamUser->avatarFull);
		$details->set('lastUpdate', strftime('%B %e, %Y %H:%m', $steamUser->lastUpdate));
		$details->set('timecreated', ($steamUser->timecreated > 0)?strftime('%B %e, %Y', $steamUser->timecreated):'unknown');
		return $details;
	}
}
