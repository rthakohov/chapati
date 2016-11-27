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

/**
 * @api {post} /images/:imageData:extension Upload a new image
 * @apiName UploadImage
 * @apiGroup Attachments
 *
 * @apiParam {String} data Base64-encoded image data.
 * @apiParam {String} extension image file extension (e.g. jpg).
 *
 * @apiSuccess {Object} data  Image information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": {
 	       "id" : 12,
		   "url" : "http://example.com/image.jpg"
	     }
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "Failed to upload image!"
 *     }
 */
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

/**
 * @api {get} /images/:imageId Get an image
 * @apiName GetImage
 * @apiGroup Attachments
 *
 * @apiParam {String} id Image unique id
 *
 * @apiSuccess {Object} data  Image information.
 *
 * @apiSuccessExample Success-Response:
 *     {
 *       "data": {
 	       "id" : 12,
		   "url" : "http://example.com/image.jpg"
	     }
 *     }
 *
 * @apiErrorExample Error-Response:
 *     {
 *       "error": "Failed to get image!"
 *     }
 */
function getImage($connection, $attachmentId) {
	require_once 'database/attachments.php';

	$attachment = getAttachment($connection, $attachmentId);
	if ($attachment) {
		return $attachment;
	} 

	return array("error" => "Failed to get image!");
}
?>