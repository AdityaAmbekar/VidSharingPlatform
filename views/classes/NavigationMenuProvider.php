<?php

class NavigationMenuProvider{

    private $conn, $userLoggedInObject;

    public function __construct($conn, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
    }

    public function create(){

        $menuHtml = $this->createNavItem("Home", "assets/images/icons/home.png", "index.php"); 
        $menuHtml .= $this->createNavItem("Trending", "assets/images/icons/trending.png", "trending.php");
        $menuHtml .= $this->createNavItem("Subscriptions", "assets/images/icons/subscriptions.png", "subscriptions.php"); 
        $menuHtml .= $this->createNavItem("Liked videos", "assets/images/icons/thumb-up-active.png", "likedVideos.php"); 

        if(User::isLoggedIn()){
            $menuHtml .= $this->createNavItem("Settings", "assets/images/icons/settings.png", "settings.php"); 
            $menuHtml .= $this->createNavItem("Logout", "assets/images/icons/logout.png", "logout.php"); 

            //subscriptions section

            $menuHtml .= $this->createSubscriptionsSection();
        }

        return "<div class='navigationItems'>
                    $menuHtml
                </div>";
    }

    private function createNavItem($text, $icon, $link){

        return "<div class='navigationItem'>
                    <a href='$link'>
                        <img src='$icon'>
                        <span>$text</span>
                    </a>
                </div>";
    }

    private function createSubscriptionsSection(){

        $subscriptions = $this->userLoggedInObject->getSubscriptions();

        $html = "<span class='heading'>SUBSCRIPTIONS</span>";

        foreach($subscriptions as $subs){

            $username = $subs->getUsername();
            $html .= $this->createNavItem($username, $subs->getProfilePic(), "profile.php?username=$username");
        }

        return $html;
    }
}


?>