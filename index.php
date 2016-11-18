<?php
	require_once 'database/connect.php';
	$connection = createConnection();
	
	$urlComponents = parse_url($_SERVER["REQUEST_URI"]);
	$resource = explode("/", $urlComponents["path"])[3];

	if( file_exists("resources/{$resource}.php") ) {
        require_once "resources/{$resource}.php";
    } else {
        header("HTTP/1.0 404 Not Found");
        die();
    }

    if (!(($resource == "users" && $_SERVER["REQUEST_METHOD"] == "POST")
    	|| ($resource == "sessions" && $_SERVER["REQUEST_METHOD"] == "POST"))) {
    	if (!verifyCredentials($connection, $_REQEST["sessionId"], $_REQEST["token"])) {
	        header("HTTP/1.0 401 Unauthorized");
	        die();
    	}
    }

    $result = handleRequest($connection, $_SERVER["REQUEST_METHOD"], $_REQUEST);

    echo json_encode($result);

	closeConnection($connection);
?>