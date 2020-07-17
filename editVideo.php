<?php 
require_once("./views/header.php");
require_once("views/classes/VideoPlayer.php");
require_once("views/classes/VideoDetailsFormProvider.php");
require_once("views/classes/VideoUploadData.php");
require_once("views/classes/SelectThumbnail.php");

if(!User::isLoggedIn()){

    header("Location: signIn.php");
}

if(!isset($_GET["videoId"])){
    echo "No video selected";
    exit(); 
}

$videoId =$_GET["videoId"];
$video = new Video($conn, $videoId, $userLoggedInObject);

if($video->getUploadedBy() != $userLoggedInObject->getUsername()){

    echo "Not your video";
    exit(); 
}
$detailsMessage = "";

if(isset($_POST["saveButton"])){

    $videoData = new VideoUploadData(
        null,
        $_POST["titleInput"],
        $_POST["descriptionInput"],
        $_POST["categoryInput"],
        $_POST["privacyInput"],
        $userLoggedInObject->getUsername()
    );

    if($videoData->updateDetails($conn, $videoId)){

        $detailsMessage = "<div class='alert alert-success'>
                                <strong>SUCCESS!</strong>  
                            </div>";
        $video = new Video($conn, $videoId, $userLoggedInObject);
    }
    else{
         
        $detailsMessage = "<div class='alert alert-danger'>
                                <strong>ERROR!</strong>  
                            </div>";
    }
}
?>
<script src="assets/js/editVideoActions.js"></script>
<div class= "editVideoContainer column">

    <div classs="message"><?php echo $detailsMessage?></div>

    <div class="topSection">
        <?php
            $videoPlayer = new VideoPlayer($video); 
            echo $videoPlayer->create(false);

            $selectThumbnail = new SelectThumbnail($conn, $video); 
            echo $selectThumbnail->create();
        ?>
    </div>
    <div class="bottomSection">
        <?php
            $formProvider = new VideoDetailsFormProvider($conn);
            echo $formProvider->createEditDetailsForm($video);
        ?>
    </div>
</div>