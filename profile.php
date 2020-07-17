<?php
require_once("./views/header.php");
require_once("./views/classes/ProfileGenerator.php");

$profileUsername = "";
if(isset($_GET["username"])){
    $profileUsername =$_GET["username"];
}
else{
    echo "Channel not found";
    exit();
}

$profileGenerator = new ProfileGenerator($conn, $profileUsername, $userLoggedInObject);

echo $profileGenerator->create();
?>