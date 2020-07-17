<?php

    require_once("ButtonProvider.php");

    class CommentControl{

    private $conn, $comment, $userLoggedInObject;

    public function __construct($conn, $comment, $userLoggedInObject){

        $this->userLoggedInObject = $userLoggedInObject;
        $this->conn = $conn;
        $this->comment = $comment;
    }

    public function create(){
        
        $replyButton =  $this->createReplyButton();
        $likesCount = $this->createLikesCount();
        $likeButton = $this->createLikeButton();
        $dislikeButton = $this->createDislikeButton();
        $replySection = $this->createReplySection();

        return "<div class='controls'> 
                    $replyButton
                    $likeButton
                    $dislikeButton
                </div>
                $replySection";
    }
    
    private function createReplyButton(){

        $text = "REPLY";
        $action = "toggleReply(this)";

        return ButtonProvider::createButton($text, null, $action, null);

    }

    private function createLikesCount(){

        $text = $this->comment->getLikes();

        if($text == 0){ $text = "";}

        return "<span class='likesCount'>$text</span>";
    }

    private function createReplySection(){

        $postedBy = $this->userLoggedInObject->getUsername();
        $videoId = $this->comment->getVideoId();
        $commentId = $this->comment->getId();

        $profileButton = ButtonProvider::createProfileButton($this->conn, $postedBy);

        $cancelButtonAction = "toggleReply (this)";
        $cancelButton = ButtonProvider::createButton("Cancel", null, $cancelButtonAction, "cancelComment");

        $postButtonAction = "postComment(this, \"$postedBy\", $videoId, $commentId, \"repliesSection\")";
        $postButton = ButtonProvider::createButton("Reply", null, $postButtonAction, "postComment");

        //Get comment html
        return "<div class = 'commentForm hidden'>
                    $profileButton      
                        <textarea class = 'commentBodyClass' placeholder='Add a public comment...'></textarea>                 
                    $cancelButton
                    $postButton
                </div>";
    }


    private function createLikeButton(){

        $text = $this->comment->getLikes();
        $commentId = $this->comment->getId();
        $videoId = $this->comment->getVideoId();
        $action = "likeComment($commentId, this, $videoId)";
        $class = "likeButton";
        $imgSrc = "assets/images/icons/thumb-up.png";

        if($this->comment->wasLiked()){
            $imgSrc = "assets/images/icons/thumb-up-active.png";
        }

        return ButtonProvider::createButton($text, $imgSrc, $action, $class);
    }

    private function createDislikeButton(){

        $text = $this->comment->getDislikes();
        $commentId = $this->comment->getId();
        $videoId = $this->comment->getVideoId();
        $action = "dislikeComment($commentId, this, $videoId)";
        $class = "dislikeButton";
        $imgSrc = "assets/images/icons/thumb-down.png";

        if($this->comment->wasDisliked()){
            $imgSrc = "assets/images/icons/thumb-down-active.png";
        }

        return ButtonProvider::createButton($text, $imgSrc, $action, $class);
    }



       
    }

?>