<?php require_once("./views/header.php");?>

<div class="videoSection">
    <?php

        $subscriptionsProvider = new SubscriptionsProvider($conn, $userLoggedInObject);
        $subscriptionVideo = $subscriptionsProvider->getVideos();

        $videoGrid = new VideoGrid($conn, $userLoggedInObject->getUsername());

        if(User::isLoggedIn() && sizeof($subscriptionVideo) > 0){

            echo $videoGrid->createMedium($subscriptionVideo,"Subscriptions",false);
        }

        echo $videoGrid->createMedium(null,"Recomended",false);
    ?>
</div>
          
<?php require_once("./views/footer.php");?>