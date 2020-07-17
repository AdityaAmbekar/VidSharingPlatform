<?php

class CommentSection{

    private $conn, $video, $userLoggedInObject;

    public function __construct($conn, $video, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
        $this->video = $video;
    }

    public function create(){

        if($this->userLoggedInObject->getUsername() == ""){

            return $this->createNormal();
        }

        return $this->createCommentSection();
    }

    private function createCommentSection(){

        $numberOfComments = $this->video->getNumberOfComments();
        $postedBy = $this->userLoggedInObject->getUsername();
        $videoId = $this->video->getId();

        $profileButton = ButtonProvider::createProfileButton($this->conn, $postedBy);
        $profilePic = $this->userLoggedInObject->getProfilePic(); 
        $commentAction = "postComment(this, \"$postedBy\", $videoId, 0, \"comments\")";

        $commentButton = ButtonProvider::createButton("Comment", null, $commentAction, "postComment");
        
        $comments = $this->video->getComments();
        $commentItems = "";

        foreach($comments as $comment){

            $commentItems .= $comment->create();
        }

        return "<div class='commentSection'>
        
                    <div class='header'>
                        <span class= 'commentCount'>$numberOfComments  Comments</span>

                        <div class = 'commentForm'>
                            $profileButton
                       
                                <textarea class = 'commentBodyClass' placeholder='Add a public comment...'></textarea>
                            
                            $commentButton
                        </div>
                    </div>

                    <div class = 'comments'>
                        $commentItems
                    </div>
                </div>";
    }

    private function createComment(){
        
    }

    private function createNormal(){

        return "<div> 
                    PLEASE LOGIN TO VIEW COMMENTS
                </div>";
    }

}

?>