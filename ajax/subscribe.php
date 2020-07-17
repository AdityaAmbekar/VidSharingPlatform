<?php

require_once("../views/config.php");

if(isset($_POST['userTo']) && isset($_POST['userFrom'])){

    $userTo = $_POST['userTo'];
    $userFrom = $_POST['userFrom'];

    //check if user is subbed

    $query = $conn->prepare("SELECT * FROM Subscribers WHERE userTo = :userTo AND userFrom = :userFrom");

    $query->bindParam(":userTo", $userTo);
    $query->bindParam(":userFrom", $userFrom);

    $query->execute();

    if($query->rowCount() == 0){
        //if not subbed insert

        $query = $conn->prepare("INSERT INTO Subscribers(userTo, userFrom) VALUES(:userTo, :userFrom)");

        $query->bindParam(":userTo", $userTo);
        $query->bindParam(":userFrom", $userFrom);

        $query->execute();

    }
    else{
        //if subbed - delete

        $query = $conn->prepare("DELETE FROM Subscribers WHERE userTo = :userTo AND userFrom = :userFrom");

        $query->bindParam(":userTo", $userTo);
        $query->bindParam(":userFrom", $userFrom);

        $query->execute();

    }

}

else{
    echo "One or more not passed in subscribe";
}

?>