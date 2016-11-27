<?php
function handleRequest($connection, $request, $params) {
	if ($request == "POST") {
		$result = registerUser($connection, $params["login"], $params["name"], $params["password"]);
	} else {
		return array("error" => "Invalid request");
	}

	if ($result["error"]) {
		return $result;
	} else {
		return array("data" => $result); 
	}
}


/**
 * @api {post} /users/:login:name:password Register a new user
 * @apiName RegisterUser
 * @apiGroup User
 *
 * @apiParam {String} login User's login.
 * @apiParam {String} name User's name.
 * @apiParam {String} password User's password.
 *
 * @apiSuccess {Object} data  User information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": {
 	       "id" : 12,
		   "login" : "john",
		   "name" : "John Doe" 
	     }
 }
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "User with this login already exists!"
 *     }
 */
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