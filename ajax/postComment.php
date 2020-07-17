<?php

require_once("../views/config.php");
require_once("../views/classes/User.php");
require_once("../views/classes/Comments.php");

if(isset($_POST['commentText']) && isset($_POST['postedBy']) && isset($_POST['videoId']) && isset($_POST['responseTo'])){

    $userLoggedInObject = new User($conn, $_SESSION['userLoggedIn']);

   $query = $conn->prepare("INSERT INTO Comments(postedBy, videoId, responseTo, body) VALUES(:postedBy, :videoId, :responseTo, :body)");

   $query->bindParam(":postedBy", $postedBy);
   $query->bindParam(":videoId", $videoId);
   $query->bindParam(":responseTo", $responseTo);
   $query->bindParam(":body", $commentText);

   $postedBy = $_POST['postedBy'];
   $videoId = $_POST['videoId'];
   $responseTo = $_POST['responseTo'];
   $commentText = $_POST['commentText'];

   $query->execute();

   //return newcomment html
   $newComment = new Comments($conn, $conn->lastInsertId(), $userLoggedInObject, $videoId);



   echo $newComment->create();
}

else{
    echo "One or more not passed in subscribe";
}

?>