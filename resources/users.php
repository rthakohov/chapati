<?php
	function handleRequest($connection, $request, $params) {
		if ($request == "POST") {
			return array("user" => registerUser($connection, $params["login"], $params["name"], $params["password"]));
		} else {
			return array("error" => "Invalid request");
		}
	}

	function registerUser($connection, $login, $name,  $password) {
		require_once 'database/users.php';
		if (findUser($connection, $login)) {
			return array("error" => "User with this login already exists!");
		}

		$hash = hashSSHA($password);
        $hashedPassword = $hash["encrypted"]; 
        $salt = $hash["salt"]; 

        $userId = addUser($connection, $login, $name, $hashedPassword, $salt);
        if ($userId) {
        	return array("id" => $userId, "login" => $login, "name" => $name);
        } else {
        	return array("error" => "Failed to register a new user");
        }
	}

	function hashSSHA($password) {
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
?>