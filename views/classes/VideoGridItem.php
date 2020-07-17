<?php
// require_once("views/config.php");

class VideoGridItem{

    private $conn, $video, $largeMode, $mediumMode;

    public function __construct($conn ,$video,  $largeMode, $mediumMode){

        $this->conn = $conn;
        $this->video = $video;
        $this->largeMode = $largeMode;
        $this->mediumMode = $mediumMode;
    }

    public function create(){

        $thumbnail = $this->createThumbnail();
        $details = $this->createDetails();
        $url = "watch.php?id=".$this->video->getId();

        return "<a href='$url'>
                    <div class='videoGridItem'>
                        $thumbnail
                        $details
                    </div>
                </a>";
         
    }

     private function createThumbnail(){

        $thumbnail = $this->video->getThumbnail();
        $duration = $this->video->getDuration();

        return "<div class = 'thumbnail'>
                    <img src='$thumbnail'>
                    <div class='duration'>
                        <span>$duration</span>
                    </div>
                </div>";
     }

     private function createDetails(){

        $title = $this->video->getTitle();
        $username = $this->video->getUploadedBy();
        $views = $this->video->getViews();
        $profileButton = $this->createProfileButton();
        $description = $this->createDescription();
        $timestamp = $this->video->getTimestamp();
        return "<div class='down'>
                    $profileButton
                    <div class='details'>
                        <h3 class='title'>$title</h3>
                        <span class='username'>$username</span>
                        <div class = 'stats'>
                            <span class = 'viewsCount'>$views views - </span>
                            <span class = 'timestamp'>$timestamp</span>
                        </div>
                        $description
                    </div>
                </div>";
     }

     private function createProfileButton(){

        if(!$this->mediumMode){
            return "";
        }
        else{
            $username = $this->video->getUploadedBy();
            $profileButton = ButtonProvider::createProfileButton($this->conn, $username);

            return "<div class='mediumProfile'>
                        $profileButton
                    </div>";
        }
     }

     private function createDescription(){

        if(!$this->largeMode){
            return "";
        }
        else{
            $description = $this->video->getDescription();
            $description = (strlen($description) > 350) ? substr($description, 0, 347) . "..." : $description;
            return "<span class='description'>$description</span>";
        }
     }
}

?>