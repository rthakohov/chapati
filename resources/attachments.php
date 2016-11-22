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
		
		$result = file_put_contents("../../img/$filename.$extension", base64_decode($imageData));

		if ($result) {
			$query = mysqli_query($connection, "INSERT INTO attachments(url) VALUES('$filename.$extension')");
			if (!mysqli_errno($connection)) {
				$id = mysqli_insert_id($connection);
				return array("id" => $id, "url" => "$filename.$extension");
			}
		}

		return array("error" => "Failed to upload image!");
	}

	function getImage($connection, $attachmentId) {
		require_once 'database/attachments.php';

		$attachment = getAttachment($connection, $attachmentId);
		if ($attachment) {
			$from = "../../img/".$attachment["url"];
			$to = "../../htdocs/tmp/".$attachment["url"];
			if (copy("../../img/".$attachment["url"], "../../htdocs/tmp/".$attachment["url"])) {
				$attachment["url"] = "http://localhost:8888/tmp/".$attachment["url"];
				return $attachment;
			}
		} 

		return array("error" => "Failed to get image!");
	}
?>