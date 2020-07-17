<?php

session_start();  //to tell page we are using session
session_destroy();
header("Location: index.php");

?>