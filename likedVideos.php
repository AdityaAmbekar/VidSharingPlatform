<?php
require_once("./views/header.php");
require_once("./views/classes/LikedVideosProvider.php");

if(!User::isLoggedIn()){
    header("Location: signIn.php");
}

$likedVideosProvider = new LikedVideosProvider($conn, $userLoggedInObject);
$videos = $likedVideosProvider->getVideos();

$videoGrid = new VideoGrid($conn, $userLoggedInObject);
?>

<div class= "largeVideoGridContainer">

    <?php
        if(sizeof($videos) > 0){
            echo $videoGrid->createLarge($videos, "Videos you liked", false);
        }
        else{
            echo "NO SUBSCRIPTIONS VIDEOS TO SHOW";
        }
    ?>
</div>
