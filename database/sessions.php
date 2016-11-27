<?php
function addSession($connection, $userId, $token) {
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, "INSERT INTO sessions(userId, token) VALUES(?, ?)")) {
        mysqli_stmt_bind_param($stmt, "is", $userId, $token);
        mysqli_stmt_execute($stmt);

        $error = mysqli_stmt_error($stmt);
        $id = mysqli_stmt_insert_id($stmt);

        mysqli_stmt_close($stmt);

        return $error ? false : $id;
    }
    return false;
}

    //Do I need this?
function findSession($connection, $id) {
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, "SELECT * FROM sessions WHERE id = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $_id, $userId, $token, $lastActive);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($_id == $id) {
            return array("id" => $_id, "userId" => $userId, "token" => $token, "lastActive" => $lastActive);
        }
    }
    return false;
}

function findSessionByUserId($connection, $userId) {
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, "SELECT * FROM sessions WHERE userId = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $_userId, $token, $lastActive);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if ($_userId == $userId) {
            return array("id" => $id, "userId" => $userId, "token" => $token, "lastActive" => $lastActive);
        }
    }
    return false;
}

function deleteSession($connection, $id) {
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, "DELETE FROM sessions WHERE id = ?")) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $error = mysqli_stmt_error($stmt);

        mysqli_stmt_close($stmt);

        return $error ? false : true;
    }
    return false;
}

    // Updates the last_active parameter of the session with the current time
function updateSession($connection, $id) {
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, "UPDATE sessions SET lastActive=now() WHERE id=?")) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
    }
}
?>