<?php 
    require_once("./views/config.php"); //header has config so sabko conn milega
    require_once("./views/classes/ButtonProvider.php"); 
    require_once("./views/classes/User.php");  
    require_once("./views/classes/Video.php"); 
    require_once("./views/classes/VideoGrid.php");
    require_once("./views/classes/VideoGridItem.php"); 
    require_once("./views/classes/SubscriptionsProvider.php"); 
    require_once("./views/classes/NavigationMenuProvider.php"); 
    
    $userNameLoggedIn = User::isLoggedIn() ? $_SESSION["userLoggedIn"] : "";
    $userLoggedInObject = new User($conn , $userNameLoggedIn);
?>   
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YouTube</title>
    <link rel='icon' type='image/png' sizes='16x16' href='assets/images/icons/YouTube.ico'>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css?1422585377">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="assets/js/commonAction.js"></script>
    <script src="assets/js/userActions.js"></script>

</head>
<body>
    
    <div id="pageContainer">
    
        <div id = "mastHeadContainer">    
            <button class = "navShowHide" id="watchButton">
                <img src="assets/images/icons/menu.png">
            </button>

            <a class = "logoContainer" href="index.php">
                 <img src="assets/images/icons/YouTube-Logo.png" alt="Site-logo" >   <!--alt makes if anything goes wrong toh name karega ke idher kya tha   -->
            </a>

            <div class = "searchBarContainer">

                <form action="search.php" method = "GET">

                    <input type="text" class = "searchBar" name="term" placeholder="Search">
                    <button class="searchButton">
                        <img src="assets/images/icons/search.png" title = "Search" >
                    </button>
                </form>

            </div>

            <div class="end">

                <a href="upload.php">
                    <img src="assets/images/icons/upload.png" alt="Create or add video" class="upload">
                </a>
                <?php

                    echo ButtonProvider::createUserProfileNavigationButton($conn, $userLoggedInObject->getUsername()); 
                ?>

            </div>

        </div>

        <div id= "sideNavContainer">    
            <?php

                $navigationProvider = new NavigationMenuProvider($conn, $userLoggedInObject);
                echo $navigationProvider->create();
            ?>
        </div>

            <div id = "mainSectionContainer" class="leftPadding"> <!-- need to change this class    -->

            <div id="mainContentContainer">
