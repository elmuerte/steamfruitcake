<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

define('BASE_URL_ID', 'http://steamcommunity.com/id/');
define('BASE_URL_PROFILE', 'http://steamcommunity.com/profiles/');
define('BASE_URL_INVENTORY_LOGO', 'http://cdn.steamcommunity.com/economy/image/');
define('BASE_URL_APP', 'http://store.steampowered.com/app/');

define('STEAM_DATA_PROFILE', 'profile');
define('STEAM_DATA_LIBRARY', 'library');
define('STEAM_DATA_INVENTORY', 'inventory');

/**
 * Exception thrown by the Model_SteamUser class
*/
class SteamUserException extends Exception {
}

/**
 * Steam user profile
 */
class Model_SteamUser extends Model {
	public $steamID64;
	public $steamID;
	public $privacyState;
	public $visibilityState;
	public $avatarIcon;

	public $lastUpdate = 0;
	public $lastLibraryUpdate = 0;
	public $lastInventoryUpdate = 0;

	/**
	 * Map of appID -> Model_SteamUserGame instance
	 */
	public $games = array();

	/**
	 * Return true if the profile is public
	*/
	public function isPublic() {
		return $this->privacyState == 'public';
	}

	/**
	 * Get the game entry with a given appID
	 * @param unknown $appID
	 * @return NULL|Model_SteamUserGame:
	 */
	public function getGame($appID) {
		if (!isset($this->games[$appID])) {
			return null;
		}
		return $this->games[$appID];
	}

	/**
	 * Update cached steam profile data
	 * @throws Exception
	 */
	public function updateProfile()	{
		if (!$this->steamID64) {
			throw new SteamUserException("No 64bit steam id");
		}

		$data = $this->retrieveSteamData(STEAM_DATA_PROFILE);
		if ($data === false) {
			throw new SteamUserException("Unable to retrieve data from Steam.");
		}
		$data = Format::forge($data, 'xml')->to_array();
		if (!is_numeric($data['steamID64'])) {
			throw new SteamUserException("Steam returned invalid data");
		}
		$this->steamID64 = $data['steamID64'];
		$this->steamID = $data['steamID'];
		$this->privacyState = $data['privacyState'];
		$this->visibilityState = $data['visibilityState'];
		$this->avatarIcon = $data['avatarIcon'];
		$this->lastUpdate = time();
	}

	/**
	 * Update the game collection
	 * @throws Exception
	 */
	public function updateLibrary() {
		if (!$this->steamID64) {
			throw new SteamUserException("No 64bit steam id");
		}

		foreach ($this->games as $game)	{
			if ($game->inLibrary())	{
				$game->source = $game->source & ~GAME_SOURCE_LIBRARY;
			}
			if (!$game->inInventory()) {
				unset($this->games[$game->appID]);
			}
		}

		$data = $this->retrieveSteamData(STEAM_DATA_LIBRARY);

		if ($data === false) {
			throw new SteamUserException("Unable to retrieve game library data from Steam.");
		}
		$data = Format::forge($data, 'xml')->to_array();
		if (isset($data['error'])) {
			throw new SteamUserException($data['error']);
		}

		if (isset($data['games']) && isset($data['games']['game']))	{
			foreach ($data['games']['game'] as $gameEntry) {
				$this->updateLibraryEntry($gameEntry);
			}
		}

		$this->lastLibraryUpdate = time();
	}

	/**
	 * Process a single entry
	 * @param array $gameEntry
	 */
	protected function updateLibraryEntry(array $gameEntry) {
		if (!isset($gameEntry['appID'])) {
			return;
		}
		$appID = (int) $gameEntry['appID'];
		if ($appID == 0) {
			return;
		}

		$entry = null;
		if (isset($this->games[$appID])) {
			$entry = $this->games[$appID];
		}
		else {
			$entry = new Model_SteamUserGame();
			$entry->appID = $appID;
			$this->games[$appID] = $entry;
		}

		$entry->source = $entry->source | GAME_SOURCE_LIBRARY;
		if ($entry->getGame() == null) {
			$steamGame = Model_SteamGame::forge($gameEntry);
			$steamGame->lastUpdate = time();
			$steamGame->save();
		}
	}

	public function updateInventory()
	{
		if (!$this->steamID64) {
			throw new SteamUserException("No 64bit steam id");
		}

		foreach ($this->games as $game)
		{
			if ($game->inInventory()) {
				$game->clearInventory();
			}
			if (!$game->inLibrary()) {
				unset($this->games[$game->appID]);
			}
		}

		$data = $this->retrieveSteamData(STEAM_DATA_INVENTORY);

		if ($data === false) {
			throw new SteamUserException("Unable to retrieve game collection data from Steam.");
		}

		$data = Format::forge($data, 'json')->to_array();

		if (!isset($data['success']) || !$data['success']) {
			throw new SteamUserException("Failed to retrieve steam inventory.");
		}

		foreach ($data['rgInventory'] as $invId => $invEntry) {
			$descId = $invEntry['classid'].'_'.$invEntry['instanceid'];
			$this->processInventoryEntry($invEntry, $data['rgDescriptions'][$descId]);
		}

		$this->lastInventoryUpdate = time();
	}

	protected function processInventoryEntry($invEntry, $descEntry) {
		if ((int) $descEntry['tradable'] != 1) {
			// must be tradeable to be a gift
			return;
		}

		if (isset($descEntry['actions'])) {
			foreach ($descEntry['actions'] as $act)	{
				if (preg_match('#^http(s)?://store.steampowered.com/app/([0-9]+)#i', $act['link'], $vars)) {
					$this->updateInventoryEntry($invEntry, (int) $vars[2], $descEntry['name'], BASE_URL_INVENTORY_LOGO.$descEntry['icon_url']);
					return;
				}
			}
		}

		if (isset($descEntry['descriptions'])) {
			foreach ($descEntry['descriptions'] as $desc) {
				if (preg_match_all('#http(s)?://store.steampowered.com/app/([0-9]+)#i', $desc['value'], $vars)) {
					foreach ($vars[2] as $appID) {
						$this->updateInventoryEntry($invEntry, (int) $appID, null, null);
					}
				}
			}
		}
	}

	protected function updateInventoryEntry($invEntry, $appID, $name, $logo) {
		if ($appID == 0) {
			return;
		}

		$entry = null;
		if (isset($this->games[$appID])) {
			$entry = $this->games[$appID];
		}
		else {
			$entry = new Model_SteamUserGame();
			$entry->appID = $appID;
			$this->games[$appID] = $entry;
		}

		$entry->addInventoryInstance($invEntry['id'], (int) $invEntry['amount']);

		if ($entry->getGame() == null) {
			$gameEntry['appID'] = $appID;
			$gameEntry['name'] = $name;
			$gameEntry['logo'] = $logo;
			$gameEntry['storeLink'] = BASE_URL_APP.$appID;
			$steamGame = Model_SteamGame::forge($gameEntry);
			$steamGame->lastUpdate = time();
			$steamGame->save();
		}
	}

	protected function retrieveSteamData($type) {
		if (Fuel::$env == \Fuel::DEVELOPMENT) {
			Profiler::console('Retrieving local data '.$type.' for '.$this->steamID64);
			return File::read(DOCROOT.'cache/'.$this->steamID64.'.'.$type, true);
		}

		$url = '';
		switch ($type) {
			case STEAM_DATA_PROFILE:
				$url = BASE_URL_PROFILE.$this->steamID64.'?xml=1';
				break;
			case STEAM_DATA_LIBRARY:
				$url = BASE_URL_PROFILE.$this->steamID64.'/games/?xml=1';
				break;
			case STEAM_DATA_INVENTORY:
				$url = BASE_URL_PROFILE.$this->steamID64.'/inventory/json/753/1';
				break;
			default:
				throw new SteamUserException('Unknown steam data type requested: '.$type);
		}

		Profiler::console('Retrieving data from '.$url);
		$curl = Request::forge($url, 'curl');
		$curl->set_option(CURLOPT_CONNECTTIMEOUT, 30);
		$curl->set_option(CURLOPT_REFERER, 'http://steamfruitcake.com');
		$curl->set_option(CURLOPT_USERAGENT, 'SteamFruitcake/1.0 (http://steamfruitcake.com)');
		$curl->execute();
		$response = $curl->response();
		if ($response->status != 200) {
			throw new SteamUserException('Failed to retrieve Steam data. Response code: '.$response->status);
		}
		return $response->body();
	}
}
