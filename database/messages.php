<?php
	function addMessage($connection, $senderLogin, $senderName, $text, $attachmentId) {
		$stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "INSERT INTO messages(sender_id, sender_login, sender_name, message_text, attachment_id) VALUES(?, ?, ?, ?, ?)")) {
            mysqli_stmt_bind_param($stmt, "isss", $senderLogin, $senderName, $text, $attachmentId);
            mysqli_stmt_execute($stmt);

            $error = mysqli_stmt_error($stmt);
            $id = mysqli_stmt_insert_id($stmt);

            $result = mysqli_query($connection, "SELECT * FROM users WHERE id = $id");

            mysqli_stmt_close($stmt);

            return $error || !$result ? false : mysqli_fetch_array($result);
        }
        return false;
	}

	function getMessages($connection, $count) {
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "SELECT * FROM messages ORDER BY id DESC LIMIT ?")) {
            mysqli_stmt_bind_param($stmt, "i", $count);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id, $senderLogin, $senderName, $text, $attachmentId, $tc);

            $messages = array();
            while (mysqli_stmt_fetch($stmt)) {
            	array_push($messages, createMessage($id, $senderLogin, $senderName, $text, $tc, $attachmentId));
            }
            mysqli_stmt_close($stmt);
            return $messages;
        }
        return false;
    }

	function getMessagesById($connection, $greaterThan) {
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "SELECT * FROM messages WHERE id > ? ORDER BY id DESC")) {
            mysqli_stmt_bind_param($stmt, "i", $greaterThan);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id, $senderLogin, $senderName, $text, $attachmentId, $tc);

            $messages = array();
            while (mysqli_stmt_fetch($stmt)) {
            	array_push($messages, createMessage($id, $senderLogin, $senderName, $text, $tc, $attachmentId));
            }
            mysqli_stmt_close($stmt);
            return $messages;
        }
        return false;
    }    

	function createMessage($id, $senderLogin, $senderName, $text, $tc, $attachmentId) {
		return array("id" => $id, "sender_login" => $senderLogin, "sender_name" => $senderName,
			"message_text" => $text, "attachment_id" => $attachmentId, "_tc" => $tc);
	}
?>