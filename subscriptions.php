<?php
require_once("./views/header.php");

if(!User::isLoggedIn()){
    header("Location: signIn.php");
}

$subscriptionsProvider = new SubscriptionsProvider($conn, $userLoggedInObject);
$videos = $subscriptionsProvider->getVideos();

$videoGrid = new VideoGrid($conn, $userLoggedInObject);
?>


<div class= "largeVideoGridContainer">

    <?php
        if(sizeof($videos) > 0){
            echo $videoGrid->createLarge($videos, "", false);
        }
        else{
            echo "NO SUBSCRIPTIONS VIDEOS TO SHOW";
        }
    ?>
</div>