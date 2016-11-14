<?php
	require_once 'database/connect.php';
	require_once 'database/users.php';
	require_once 'database/sessions.php';
	require_once 'database/messages.php';

	$connection = createConnection();

	$result = getMessagesById($connection, 115);

	echo json_encode($result);

	closeConnection($connection);
?>