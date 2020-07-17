<?php

class ButtonProvider{

    public static $signedInFunction = "notSignedIn()";


    public static function createLink($link){

        return User::isLoggedIn() ? $link : ButtonProvider::$signedInFunction;
    }

    public static function createButton($text, $imgSrc, $action, $class){

        $image = ($imgSrc == null) ? "" : "<img src = '$imgSrc'>";

        $action = ButtonProvider::createLink($action);

        return "<button class= '$class' onclick ='$action'>
                    $image
                    <span class= 'text'>$text</span>
                </button>";

    }

    public static function createHyperlinkButton($text, $imgSrc, $href, $class){

        $image = ($imgSrc == null) ? "" : "<img src = '$imgSrc'>";

        return "<a href = '$href'>
                    <button class= '$class'>
                        $image
                        <span class= 'text'>$text</span>
                    </button>
                </a>";

    }
    
    public static function createProfileButton($conn, $username){

        $userObject = new User($conn, $username);
        $profilePic = $userObject->getProfilePic();

        $link = "profile.php?username=$username";

        return " <a href= '$link'>
                    <img src = '$profilePic' class= 'profilePic'>
                </a>";

    }

    public static function createEditVideoButton($videoId){

        $href = "editVideo.php?videoId=$videoId";

        $button = ButtonProvider::createHyperlinkButton("EDIT VIDEO", null, $href, "edit button");

        return "<div class= 'editVideoButtonContainer'>
                    $button
                </div>";

    }

    public function createSubscriberButton($conn, $userToObject, $userLoggedInObject){

        $userTo = $userToObject->getUsername();
        $userLoggedIn = $userLoggedInObject->getUsername();

        $isSubscribedTo = $userLoggedInObject->isSubscribedTo($userToObject->getUsername());
        $buttonText = $isSubscribedTo ? "SUBSCRIBED" : "SUBSCRIBE" ;

        $buttonClass = $isSubscribedTo ? "unsubscribe button" : "subscribe button";
        $action = "subscribe(\"$userTo\", \"$userLoggedIn\", this)";                       //as string bhejnay thats why

        $button = ButtonProvider::createButton($buttonText, null, $action, $buttonClass);

        return "<div class='subscribeButtonContainer'>
                    $button
                </div>";
    }

    public static function createUserProfileNavigationButton($conn, $username){

        if(User::isLoggedIn()){

            return ButtonProvider::createProfileButton($conn, $username);
        }
        else{

            return "<a href='signIn.php'>
                        <span class='signInLink'>SIGN IN</span>
                    </a>";
        }
    }
}

?>