<?php
	// <config>
	session_start();
	$config = [
		'site_url' => 'http://otake',
		'db_host' => '127.0.0.1',
		'db_user' => 'root',
		'db_password' => '',
		'db_name' => 'otake'
	];
	// </config>
	// <db>
	require 'libs/rb.php';
	R::setup( 'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] ,
        $config['db_user'] , $config['db_password'] );
	if(!R::testConnection()) {
		die("No DB");
	}
	// </db>
	// <ip>
	if(isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}
	else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	// </ip>
?>