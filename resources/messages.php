<?php
function handleRequest($connection, $request, $params) {
	if ($request == "POST") {
		$result = sendMessage($connection, $params["senderLogin"], $params["senderName"], $params["messageText"],
			$params["attachmentUrl"]);
	} else if ($request == "GET") {
		if ($params["lastLoadedId"]) {
			$result = waitForNewMessages($connection, $params["lastLoadedId"], $params["timeout"]);
		} else {
			$result = getNewMessages($connection, $params["count"], $params["start"], $params["end"]);
		}
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
 * @api {post} /messages/:login:name:text:attachmentUrl Send a new message
 * @apiName SendMessage
 * @apiGroup Messages
 *
 * @apiParam {String} senderLogin Login of the user sending the message
 * @apiParam {String} senderName Name of the user sending the message
 * @apiParam {String} text Message text
 * @apiParam {String} attachmentUrl (optional) url of the image attached to the message
 *
 * @apiSuccess {Object} data  Message information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": {
 *	       "senderLogin" : "john",
 *	       "senderName" : "John Doe",
 *	       "text" : "Hi! How are you?",
 *		   "attachmentUrl" : "http://example.com/image.jpg"
 *	     }
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "Failed to send the message!"
 *     }
 */
function sendMessage($connection, $login, $name, $text, $attachmentUrl) {
	require_once 'database/messages.php';
	$message = addMessage($connection, $login, $name, htmlspecialchars($text), $attachmentUrl);
	if ($message) {
		return $message;
	} else {
		return array("error" => "Failed to send the message.");
	}
}

/**
 * @api {get} /messages/:count:start:end Get messages
 * @apiName GetMessages
 * @apiGroup Messages
 *
 * @apiParam {Numnber} count (optional) Number of messages to return. If this is specified, returns count last messages.
 * @apiParam {Number} start (optional) The ID of the first message to return
 * @apiParam {Number} end (optional) The ID of the last message to return
 * @apiParam {String} attachmentUrl (optional) url of the image attached to the message
 *
 * @apiSuccess {Object} data  Message information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": [
 *	       0: {"senderLogin" : "john",
 *	       	"senderName" : "John Doe",
 *	       	"text" : "Hi! How are you?",
 *		   	"attachmentUrl" : "http://example.com/image.jpg"
 *		   }
 *	     ]
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "Failed to get messages!"
 *     }
 */    
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

/**
 * @api {get} /messages/:lastLoadedId:timeout Return when new messages are available
 * @apiName GetMessages
 * @apiGroup Messages
 *
 * @apiDescription This methods waits for new messages and returns when new ones are available
 *
 * @apiParam {Numnber} lastLoadedId The ID of the last loaded message (all the newer messages will be returned).
 * @apiParam {Number} timeout (optional) The time after which the method will return even if no messages are available.
 *
 * @apiSuccess {Object} data  Message information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": [
 *	       0: {"senderLogin" : "john",
 *	       	"senderName" : "John Doe",
 *	       	"text" : "Hi! How are you?",
 *		   	"attachmentUrl" : "http://example.com/image.jpg"
 *		   }
 *	     ]
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "Failed to get messages!"
 *     }
 */   
function waitForNewMessages($connection, $lastLoadedId, $timeout) {
	require_once 'database/messages.php';
	if (!$timeout) {
		$timeout = 60;
	}
	while ($timeout) {
		$messages = getMessagesById($connection, $lastLoadedId);
		if ($messages) {
			return $messages;
		}
		sleep(1);
		$timeout--;
	}
	return array("error" => "Failed to get messages.");
}
?>