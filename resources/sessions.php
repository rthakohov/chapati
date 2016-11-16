<?php
	function handleRequest($connection, $request, $params) {
		if ($request == "POST") {
			return array("session" => startSession($connection, $params["login"], $params["password"]));
		} else {
			return array("error" => "Invalid request");
		}
	}

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
		if ($sessionId) {
            return array("id" => $sessionId, "user_id" => $user["id"], "token" => $token);
		} else {
			return array("error" => "Failed to log in!");
		}
	}

	function verifyCredentials($connection, $id, $token) {
		require_once 'database/sessions.php';

		$session = findSession($connection, $id);
		if ($session) {
			$lastActive = $session["last_active"];
			if (time() - strtotime($lastActive) > 86400) {
				deleteSession($session["id"]);
				return false;
			}
			updateSession($connection, $id);
			return true;
		} else {
			return false;
		}
	}

	function checkhashSSHA($salt, $password) {
        return base64_encode(sha1($password . $salt, true) . $salt);
    }
?>