<?php
function verifyCredentials($connection, $id, $token) {
	require_once 'database/sessions.php';
	$session = findSession($connection, $id);
	if ($session) {
		$lastActive = $session["lastActive"];
		if (time() - strtotime($lastActive) > 86400) {
			deleteSession($session["id"]);
			return false;
		}
		if ($session["token"] != $token) {
			return false;
		}
		updateSession($connection, $id);
		return true;
	} else {
		return false;
	}
}
?>