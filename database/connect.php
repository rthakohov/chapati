<?php

$connection = null;

function createConnection() {
	require_once 'config.php';
	$GLOBALS['connection'] = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (!$GLOBALS['connection']) {
		die("Connection failed: " . mysqli_connect_error());
	}
	return $GLOBALS['connection'];
}

function closeConnection($connection) {
	mysqli_close(connection);
}
?>