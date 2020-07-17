<?php 
    require_once("./views/header.php");
    require_once("./views/classes/VideoUploadData.php");
    require_once("./views/classes/VideoProcessor.php");

    if(!isset($_POST["uploadButton"])){      //name of that upload button is written and makes sure direct processing pe nai aya / daalke
        echo "File Was Not Sent to Page";
        exit();
    }



    $videoUploadData = new VideoUploadData( $_FILES["fileInput"], 
                                            $_POST["titleInput"], 
                                            $_POST["descriptionInput"],
                                            $_POST["categoryInput"],
                                            $_POST["privacyInput"],
                                            $userLoggedInObject->getUsername());



    $videoProcessor = new VideoProcessor($conn);
    $wasSuccessful = $videoProcessor->upload($videoUploadData);
    


    if($wasSuccessful){
        // alert("file uploaded successfully!!!");

        echo "<div class='uploadSuccessfulContainer'>

                <div class='topSection'>
                    <img src= 'assets/images/icons/upload-successful.png'>
                </div>
                <div class='bottomSection'>Thanks for uploading :)</div>
            </div>";

        // header("Location: index.php");
    }
?>


<?php require_once("./views/footer.php");?>
