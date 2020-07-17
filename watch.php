<?php 
    require_once("./views/header.php");
    require_once("./views/classes/VideoPlayer.php");
    require_once("./views/classes/VideoInfoSection.php");
    require_once("./views/classes/Comments.php");
    require_once("./views/classes/CommentSection.php");

    if(!isset($_GET["id"])){

        echo "PAGE NOT FOUND";
        exit();
    }

    $video = new Video($conn, $_GET["id"], $userLoggedInObject);
    $video->incrementViewsCount();


?>
<script src="./assets/js/watchActions.js?2"></script>
<script src="./assets/js/videoPlayerActions.js?2" ></script>
<script src="./assets/js/commentActions.js?2" ></script>

<div class="watchLeftColumn">
<?php
    $videoPlayer = new VideoPlayer($video);
    echo $videoPlayer->create(true);

    $videoInfoSection = new VideoInfoSection($conn, $video, $userLoggedInObject);
    echo $videoInfoSection->create();

    $commentSection = new CommentSection($conn, $video, $userLoggedInObject);
    echo $commentSection->create();

?>
</div>

<div class = "suggestions">
    <?php
        $videoGrid = new VideoGrid($conn, $userLoggedInObject);

        echo $videoGrid->create(null, null, false);

    ?>
</div>

            
<?php require_once("./views/footer.php");?>