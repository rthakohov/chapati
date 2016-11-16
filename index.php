<?php
	require_once 'database/connect.php';
	require_once 'resources/messages.php';
	$connection = createConnection();
	$result = handleRequest($connection, "GET", array("id" => 115));

	echo json_encode($result);

	closeConnection($connection);
?>