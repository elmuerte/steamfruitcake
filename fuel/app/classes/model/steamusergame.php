<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

define('GAME_SOURCE_UNKNOWN', 0);
define('GAME_SOURCE_LIBRARY', 1);
define('GAME_SOURCE_INVENTORY', 2);

/**
 * A game the user "owns"
*/
class Model_SteamUserGame extends Model {
	public $appID = 0;

	/**
	 * Loaded steam game instance
	 * @var Model_SteamGame
	 */
	protected $game = null;

	/**
	 * Location of this instance. This is a bit array.
	 */
	public $source = GAME_SOURCE_UNKNOWN;

	/**
	 * Map of inventory id to amount
	 */
	protected $inventory = array();

	public function inLibrary() {
		return ($this->source & GAME_SOURCE_LIBRARY) != 0;
	}

	public function inInventory() {
		return ($this->source & GAME_SOURCE_INVENTORY) != 0;
	}

	/**
	 * Get the game instance
	 * @return Model_SteamGame
	 */
	public function getGame() {
		if ($this->game == null) {
			$this->game = Model_SteamGame::find($this->appID);
		}
		return $this->game;
	}

	public function setGame(Model_SteamGame $game) {
		if (!$game)	{
			return;
		}
		if ($game->appID != $this->appID) {
			throw new Exception("Cannot set a game with a different appID");
		}
		$this->game = $game;
	}

	/**
	 * Clear the inventory data
	 */
	public function clearInventory() {
		$this->source = $this->source & ~GAME_SOURCE_INVENTORY;
		$this->inventory = array();
	}

	/**
	 * Register an inventory instance
	 * @param int $id
	 * @param int $amount
	 */
	public function addInventoryInstance($id, $amount) {
		if ((int) $amount <= 0) {
			return;
		}
		$this->source = $this->source | GAME_SOURCE_INVENTORY;
		$this->inventory[$id] = $amount;
	}

	/**
	 * Return the actual quantity
	 * @return unknown
	 */
	public function getQuantity() {
		$res = array_sum(array_values($this->inventory));
		if ($this->inLibrary())	{
			++$res;
		}
		return $res;
	}
}
