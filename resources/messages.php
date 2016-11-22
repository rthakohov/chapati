<?php
	function handleRequest($connection, $request, $params) {
		if ($request == "POST") {
			$result = sendMessage($connection, $params["senderLogin"], $params["senderName"], $params["messageText"], $params["attachmentUrl"]);
		} else if ($request == "GET") {
			$result = getNewMessages($connection, $params["count"], $params["start"], $params["end"]);
		} else {
			return array("error" => "Invalid request");
		}

		if ($result["error"]) {
			return $result;
		} else {
			return array("data" => $result); 
		}
	}

	//TODO: Process the attachment URL
	function sendMessage($connection, $login, $name, $text, $attachmentUrl) {
		require_once 'database/messages.php';
		$message = addMessage($connection, $login, $name, htmlspecialchars($text), 0);
		if ($message) {
			return $message;
		} else {
			return array("error" => "Failed to send the message.");
		}
	}

	// Returns the last $count messages
	function getNewMessages($connection, $count, $start, $end) {
		require_once 'database/messages.php';
		if ($count) {
			$messages = getMessages($connection, $count);
		} else {
			$messages = getMessagesById($connection, $start, $end);
		}
		if ($messages) {
			return $messages;
		} else {
			return array("error" => "Failed to get messages.");
		}
	}
?>