<?php
	function addUser($connection, $login, $name, $password, $salt) {
		$stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "INSERT INTO users(login, name, password, salt) VALUES(?, ?, ?, ?)")) {
            mysqli_stmt_bind_param($stmt, "ssss", $login, $name, $password, $salt);
            mysqli_stmt_execute($stmt);

            $error = mysqli_stmt_error($stmt);
            $id = mysqli_stmt_insert_id($stmt);

            mysqli_stmt_close($stmt);

            return $error ? false : $id;
        }
        return false;
	}

    function findUser($connection, $login) {
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "SELECT * FROM users WHERE login = ?")) {
            mysqli_stmt_bind_param($stmt, "s", $login);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id, $_login, $name, $password, $salt);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($_login == $login) {
                return createUser($id, $_login, $name, $password, $salt);
            }
        }
        return false;
    }

    function findUserById($connection, $id) {
        $stmt = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($stmt, "SELECT * FROM users WHERE id = ?")) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $_id, $login, $name, $password, $salt);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);

            if ($_id == $id) {
                return createUser($id, $_login, $name, $password, $salt);
            }
        }
        return false;
    }

    function createUser($id, $login, $name, $password, $salt) {
        return array("id" => $id, "login" => $login, "name" => $name, 
                    "password" => $password, "salt" => $salt);
    }
?>