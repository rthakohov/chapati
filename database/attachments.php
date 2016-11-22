<?php
	function addAttachment($connection, $url) {
		$stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "INSERT INTO attachments(url) VALUES(?)")) {
            mysqli_stmt_bind_param($stmt, "s", $url);
            mysqli_stmt_execute($stmt);

            $error = mysqli_stmt_error($stmt);
            $id = mysqli_stmt_insert_id($stmt);

            $result = mysqli_query($connection, "SELECT * FROM attachments WHERE id = $id");

            mysqli_stmt_close($stmt);

            return $error || !$result ? false : mysqli_fetch_array($result, MYSQLI_ASSOC);
        }
        return false;
	}

	function getAttachment($connection, $id) {
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "SELECT * FROM attachments WHERE id = ?")) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $_id, $url);

            mysqli_stmt_fetch($stmt);

            mysqli_stmt_close($stmt);

            if ($id == $_id) {
            	return array("id" => $id, "url" => $url);
        	}
        }
        return false;
    }
?>