<?php
	function handleRequest($connection, $request, $params) {
		if ($request == "POST") {
			$result = sendMessage($connection, $params["sender_login"], $params["sender_name"], $params["message_text"], $params["attachment_url"]);
		} else if ($request == "GET") {
			$result = getNewMessages($connection, $params["count"], $params["id"]);
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
	function getNewMessages($connection, $count, $id) {
		require_once 'database/messages.php';
		if ($count) {
			echo "here";
			$messages = getMessages($connection, $count);
		} else {
			$messages = getMessagesById($connection, $id);
		}
		if ($messages) {
			return $messages;
		} else {
			return array("error" => "Failed to get messages.");
		}
	}
?>