<?php
require_once("./views/header.php");
require_once("./views/classes/TrendingProvider.php");

$trendingProvider = new TrendingProvider($conn, $userLoggedInObject);
$videos = $trendingProvider->getVideos();

$videoGrid = new VideoGrid($conn, $userLoggedInObject);
?>

<div class= "largeVideoGridContainer">

    <?php
        if(sizeof($videos) > 0){
            echo $videoGrid->createLarge($videos, "", false);
        }
        else{
            echo "NO TRENDING VIDEOS TO SHOW";
        }
    ?>
</div>