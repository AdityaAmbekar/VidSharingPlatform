<?php
    require_once("../views/config.php");
    require_once("../views/classes/Video.php");
    require_once("../views/classes/User.php");

    $videoId = $_POST["videoId"];
    $username = $_SESSION["userLoggedIn"];
    $userLoggedInObject = new User($conn, $username);

    $video = new Video($conn, $videoId, $userLoggedInObject);

    echo $video->dislike();

?>