<?php
    require_once("../views/config.php");
    require_once("../views/classes/Comments.php");
    require_once("../views/classes/User.php");

    $videoId = $_POST["videoId"];
    $commentId = $_POST["commentId"];
    $username = $_SESSION["userLoggedIn"];
    
    $userLoggedInObject = new User($conn, $username);

    $comment = new Comments($conn, $commentId, $userLoggedInObject ,$videoId);

    echo $comment->getReplies();

?>
