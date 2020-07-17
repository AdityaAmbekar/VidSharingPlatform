<?php

    require_once("./views/classes/ButtonProvider.php");

    class VideoInfoControl{

    private $video, $userLoggedInObject;

    public function __construct($video, $userLoggedInObject){

        $this->userLoggedInObject = $userLoggedInObject;
        $this->video = $video;
    }
    
    public function create(){
        
        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();

        return "<div class='controls'> 
                    $likeButton
                    $dislikeButton
                </div>";
    }

    private function createLikeButton(){

        $text = $this->video->getLikes();
        $videoId = $this->video->getId();
        $action = "likeVideo(this, $videoId)";
        $class = "likeButton";
        $imgSrc = "assets/images/icons/thumb-up.png";

        if($this->video->wasLiked()){
            $imgSrc = "assets/images/icons/thumb-up-active.png";
        }

        return ButtonProvider::createButton($text, $imgSrc, $action, $class);
    }

    private function createDislikeButton(){

        $text = $this->video->getDislikes();
        $videoId = $this->video->getId();
        $action = "dislikeVideo(this, $videoId)";
        $class = "dislikeButton";
        $imgSrc = "assets/images/icons/thumb-down.png";

        if($this->video->wasDisliked()){
            $imgSrc = "assets/images/icons/thumb-down-active.png";
        }

        return ButtonProvider::createButton($text, $imgSrc, $action, $class);
    }



       
    }

?>