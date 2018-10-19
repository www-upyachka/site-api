<?php
	session_start();
	header("Access-Control-Allow-Origin: *");
	$config = [
		'db_host' => '127.0.0.1',
		'db_user' => 'koteradue',
		'db_password' => '101',
		'db_name' => 'bigdirty',
	];

	require 'libs/rb.php';
	require 'libs/Parsedown.php';
	require 'libs/jevix.php';

	$parsedown = new Parsedown();
	$qevix = new Jevix();

	R::setup( 'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] ,
		$config['db_user'] , $config['db_password'] );
	if(!R::testConnection()) {
		die("No DB");
	}

	if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	$GLOBALS['ip'] = $ip;

	function generateRandomKey()
	{
		return md5(rand(0, 1000000000));
	}

	include_once "core/classes/api.class.php";
	include_once "core/classes/user.class.php";
	include_once "core/classes/invite.class.php";
	include_once "core/classes/sub.class.php";
	include_once "core/classes/modlog.class.php";
	include_once "core/classes/post.class.php";
	include_once "core/classes/comment.class.php";
	include_once "core/classes/ban.class.php";
	include_once "core/classes/karma.class.php";
	include_once "core/classes/parser.class.php";
	include_once "core/classes/report.class.php";
	$parser = new parser();
?>
