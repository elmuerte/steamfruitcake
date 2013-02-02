<?php

class Controller_fruitcake extends Controller
{
	public function action_index()
	{
		return Response::forge(View::forge('fruitcake/index'));
	}
}
