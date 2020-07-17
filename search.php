<?php
require_once("views/header.php");
require_once("views/classes/SearchResultsProvider.php");

if(!isset($_GET["term"]) || $_GET["term"] == "") {

    // echo "Enter the search term";
    header("Location: index.php");
    exit();   //makes sure agar naiye toh dnt load this page
}
$term = $_GET["term"];

//also now we need to see ki it is by default orederedBy views

if(!isset($_GET["orderBy"]) || $_GET["orderBy"]== "views"){
    $orderBy = "views";
}
else{
    $orderBy = "uploadDate";   //these terms should match to db ke columns name
}

$searchResultsProvider = new SearchResultsProvider($conn, $userLoggedInObject);

$videos = $searchResultsProvider->getVideos($term, $orderBy);

$videoGrid = new VideoGrid($conn, $userLoggedInObject);
?>

<div class = "largeVideoGridContainer">

    <?php

        if(sizeof($videos) > 0){
            echo $videoGrid->createLarge($videos, " ", true);   //left header tha sizeof($videos). " results found"
        }
        else{
            echo "No results Found";
        }
    ?>
</div>


<?php
require_once("views/footer.php");
?>