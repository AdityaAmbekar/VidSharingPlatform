<?php

require_once("../views/config.php");

if(isset($_POST['videoId']) && isset($_POST['thumbnailId'])){

    $videoId = $_POST['videoId'];
    $thumbnailId = $_POST['thumbnailId'];

    $query = $conn->prepare("UPDATE Thumbnails SET selected = 0 WHERE videoId=:videoId");
    $query->bindParam(":videoId", $videoId);
    $query->execute();

    $query = $conn->prepare("UPDATE Thumbnails SET selected = 1 WHERE id=:thumbnailId");
    $query->bindParam(":thumbnailId", $thumbnailId);
    $query->execute();
}

else{
    echo "One or more not passed in update thumbnail";
}

?>