<?php

ob_start();  //Turns on ouput buffering
session_start();
date_default_timezone_set('Asia/Kolkata');


try { 
    $conn = new PDO("mysql:dbname=YOUTUBE_CLONE; host = localhost", "root", "password"); 
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

}

catch(PDOException $e) {
    echo "Connection Failed" . $e->getMessage();
}
?>