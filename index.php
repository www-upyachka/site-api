<?php
	ini_set("display_errors", 0);
	date_default_timezone_set('Europe/Moscow');
	error_reporting(E_ALL);
	header("content-type: application/json; charset=utf-8");
	header("Access-Control-Allow-Origin: *");
	header("Is-Shit: False");
	require "core/includes/includes.php";
	$user = new user;
	$api = new api;
	if(isset($_GET['access_token']))
	{
		if($user->tokenIsValid($_GET['access_token']))
		{
			$tokenInfo = $user->tokenByCode($_GET['access_token']);
			$GLOBALS['username'] = $tokenInfo[0]['for_user'];
			$GLOBALS['currentUserInfo'] = $user->info($tokenInfo[0]['for_user']);
		}
	}
	if(!isset($_GET['method']))
	{
		echo $api->error_no_method();
	}
	elseif(preg_match("/macintosh/iu", $_SERVER['HTTP_USER_AGENT']) && !preg_match("/firefox/iu", $_SERVER['HTTP_USER_AGENT'])) {
		echo $api->error("Излечитесь от педерастии пожалста");
	}
	else
	{
		$method = $_GET['method'];
		echo $api->start_method($method);
	}
?>