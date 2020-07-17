<?php

require_once("./views/classes/VideoInfoControl.php");

class VideoInfoSection{

    private $conn, $video, $userLoggedInObject;

    public function __construct($conn, $video, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;
        $this->video = $video;
    }

    public function create(){

        return $this->createPrimaryInfo() . $this->createSecondaryInfo();       
    }
    
    private function createPrimaryInfo(){

        $title = $this->video->getTitle();
        $views = $this->video->getViews();
        $uploadDate = $this->video->getUploadDate();

        $videoInfoControl = new VideoInfoControl($this->video, $this->userLoggedInObject);
        $controls = $videoInfoControl->create();

        return "<div class = 'videoInfo'> 

                    <h1>$title </h1>
                    <div class='bottomSection'> 
                        <div class = 'left'>
                            <span class= 'viewsCount'>$views views</span>
                            <span class = 'date'> . $uploadDate</span>
                        </div>
                        <span class= 'likeDislike'>$controls</span>
                    </div>
                </div>";
    }

    private function createSecondaryInfo(){

        $description = $this->video->getDescription();
        $uploadedBy = $this->video->getUploadedBy();

        $userToObject = new User($this->conn, $uploadedBy);
        $subscriberCount = $userToObject->getSubscriberCount($userToObject);  //galat hai
        $profileButton =  ButtonProvider::createProfileButton($this->conn, $uploadedBy);

        if($uploadedBy == $this->userLoggedInObject->getUsername()){

            $actionButton = ButtonProvider::createEditVideoButton($this->video->getId()) ;
        }
        else{
            $actionButton = ButtonProvider::createSubscriberButton($this->conn, $userToObject, $this->userLoggedInObject);
        }

        return "<div class = 'secondaryInfo'>
        
                    <div class= 'topRow'>
                        $profileButton

                        <div class = 'uploadInfo'>
                            <span class = 'owner'>
                                <a href= 'profile.php?username=$uploadedBy'>
                                    $uploadedBy
                                </a>                 
                            </span>
                            <span class= 'subscriberCount'>
                                $subscriberCount subscribers
                            </span>
                        </div>
                        $actionButton
                    </div>

                    <div class='descriptionContainer'>
                        $description
                    </div>
                </div>";
        
    }
}

?>