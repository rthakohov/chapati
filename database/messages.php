<?php
function addMessage($connection, $senderLogin, $senderName, $text, $attachmentUrl, $mentions) {
  $stmt = mysqli_stmt_init($connection);
  if (mysqli_stmt_prepare($stmt, "INSERT INTO messages(senderLogin, senderName, messageText, attachmentUrl, mentions) VALUES(?, ?, ?, ?, ?)")) {
    mysqli_stmt_bind_param($stmt, "sssss", $senderLogin, $senderName, $text, $attachmentUrl, $mentions);
    mysqli_stmt_execute($stmt);

    $error = mysqli_stmt_error($stmt);
    $id = mysqli_stmt_insert_id($stmt);

    $result = mysqli_query($connection, "SELECT * FROM messages WHERE id = $id");

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
        mysqli_stmt_bind_result($stmt, $id, $senderLogin, $senderName, $text, $attachmentUrl, $mentions, $tc);

        $messages = array();
        while (mysqli_stmt_fetch($stmt)) {
           array_push($messages, createMessage($id, $senderLogin, $senderName, $text, $tc, $attachmentUrl, $mentions));
       }
       mysqli_stmt_close($stmt);
       return $messages;
   }
   return false;
}

function getMessagesById($connection, $start, $end) {
    $stmt = mysqli_stmt_init($connection);
    if ($start && $end) {
        $prepared = mysqli_stmt_prepare($stmt, "SELECT * FROM messages WHERE id >= ? AND id < ? ORDER BY id DESC");
    } else {
        $prepared = mysqli_stmt_prepare($stmt, "SELECT * FROM messages WHERE id >= ? ORDER BY id DESC");
    }
    if ($prepared) {
        if ($start && $end) {
            mysqli_stmt_bind_param($stmt, "ii", $start, $end);
        } else {
            mysqli_stmt_bind_param($stmt, "i", $start);
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $senderLogin, $senderName, $text, $attachmentUrl, $mentions, $tc);

        $messages = array();
        while (mysqli_stmt_fetch($stmt)) {
           array_push($messages, createMessage($id, $senderLogin, $senderName, $text, $tc, $attachmentUrl));
       }
       mysqli_stmt_close($stmt);
       return $messages;
   }
   return false;
}    

function createMessage($id, $senderLogin, $senderName, $text, $tc, $attachmentUrl, $mentions) {
  return array("id" => $id, "senderLogin" => $senderLogin, "senderName" => $senderName,
     "messageText" => $text, "attachmentUrl" => $attachmentUrl, "mentions" => $mentions, "timestamp" => $tc);
}
?>