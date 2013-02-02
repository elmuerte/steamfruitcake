<?php
/**
 * SteamFruitcake
 * GNU Affero General Public License, version 3
 * Copyright 2013 Michiel Hendriks <elmuerte@drunksnipers.com>
 */

class Controller_Fruitcake extends Controller_AbstractFruitcake
{
	public function action_index()
	{
		try
		{
			$overview = Cache::get('scoreboard');
			Profiler::mark("Using cached scoreboard");
		}
		catch (\CacheNotFoundException $e)
		{
			Profiler::mark("Fetching scoreboard from database");
			$overview = DB::query('SELECT g.*, sum(e.count) AS totalCount, max(e.timestamp) AS lastVote FROM fruitcake_entry e LEFT JOIN games g ON g.appID = e.appID GROUP BY e.appID ORDER BY sum(e.count) DESC');
			$overview = $overview->as_object('Model_Fruitcake')->execute()->as_array();
			// cache for 1 minute
			Cache::set('scoreboard', $overview, 60);
		}

		$view = View::forge('fruitcake/index');

		$gameViews = array();
		foreach ($overview as $entry) {
			array_push($gameViews, $this->createScoreboardEntry($entry));
		}
		$view->scoreBoard = $gameViews;

		return Response::forge($view);
	}

	protected function createScoreboardEntry(Model_Fruitcake $entry) {
		$view = View::forge("fruitcake/fruitcake");
		$view->set('appID', $entry->appID);
		$view->set('name', $entry->name);
		$view->set('logo', $entry->logo);
		$view->set('storeLink', $entry->storeLink);
		$view->set('totalCount', $entry->totalCount);
		$view->set('lastVote', date("Y-m-d H:i Z", $entry->lastVote));
		return $view;
	}
}
