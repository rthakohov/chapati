<?php
	function handleRequest($connection, $request, $params) {
		if ($request == "POST") {
			$result = uploadImage($connection, $params["imageData"], $params["extension"]);
		} else if ($request == "GET") {
			$result = getImage($connection, $params["attachmentId"]);
		} else {
			return array("error" => "Invalid request");
		}

		if ($result["error"]) {
			return $result;
		} else {
			return array("data" => $result); 
		}
	}

	function uploadImage($connection, $imageData, $extension) {
		require_once 'database/attachments.php';

		$filename = substr(sha1(rand()), 0, 10);
		
		$result = file_put_contents("../../htdocs/img/$filename.$extension", base64_decode($imageData));

		if ($result) {
			$url = "http://localhost:8888/img/$filename.$extension";
			$query = mysqli_query($connection, "INSERT INTO attachments(url) VALUES('$url')");
			if (!mysqli_errno($connection)) {
				$id = mysqli_insert_id($connection);
				return array("id" => $id, "url" => $url);
			}
		}

		return array("error" => "Failed to upload image!");
	}

	function getImage($connection, $attachmentId) {
		require_once 'database/attachments.php';

		$attachment = getAttachment($connection, $attachmentId);
		if ($attachment) {
			return $attachment;
		} 

		return array("error" => "Failed to get image!");
	}
?>