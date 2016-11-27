<?php
function handleRequest($connection, $request, $params) {
	if ($request == "POST") {
		$result = startSession($connection, $params["login"], $params["password"]);
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
 * @api {post} /sessions/:login:password Start a new session for a user
 * @apiName StartSession
 * @apiGroup Sessions
 *
 * @apiParam {String} login User's login.
 * @apiParam {String} password User's password.
 *
 * @apiSuccess {Object} data  Session information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": {
 *	       "id" : 12,
 *		   "token" : "fsafa54faa654fas4fas",
 *		   "user" : {
 *	 	       "id" : 12,
 *			   "login" : "john",
 *			   "name" : "John Doe" 
 *		   }
 *	     }
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "Wrong login or password!"
 */   
function startSession($connection, $login,  $password) {
	require_once 'database/sessions.php';
	require_once 'database/users.php';
	$user = findUser($connection, $login);
	$salt = $user['salt'];
	$hashedPassword = $user['password'];
	$hash = checkhashSSHA($salt, $password);
	if (!$user || $hash != $hashedPassword) {
		return array("error" => "Wrong login or password!");
	}

	$oldSession = findSessionByUserId($connection, $user["id"]);
	if ($oldSession) {
		deleteSession($connection, $oldSession["id"]);
	}

	$token = sha1(rand());
	$sessionId = addSession($connection, $user["id"], $token);
	unset($user["password"]);
	unset($user["salt"]);
	if ($sessionId) {
		return array("id" => $sessionId, "token" => $token,
			"user" => $user);
	} else {
		return array("error" => "Failed to log in!");
	}
}

function checkhashSSHA($salt, $password) {
	return base64_encode(sha1($password . $salt, true) . $salt);
}
?>