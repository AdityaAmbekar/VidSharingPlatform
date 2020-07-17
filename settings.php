<?php
require_once("views/header.php");
require_once("views/classes/Account.php");
require_once("views/classes/FormSanitizer.php");
require_once("views/classes/Constants.php");
require_once("views/classes/SettingsFormProvider.php");

if(!User::isLoggedIn()){
    header("Location: signIn.php");
}

$detailsMessage = "";
$passwordMessage = "";
$settingsFormProvider = new SettingsFormProvider();

if(isset($_POST["saveDetailsButton"])){

    $account = new Account($conn);
    $firstName = FormSanitizer::formStringSanitizer($_POST["firstName"]);
    $lastName = FormSanitizer::formStringSanitizer($_POST["lastName"]);
    $email = FormSanitizer::formStringSanitizer($_POST["email"]);

    if($account->updateDetails($firstName, $lastName, $email, $userLoggedInObject->getUsername())){

        $detailsMessage = "<div class='alert alert-success'>
                                <strong>SUCCESS!</strong>  
                            </div>";

    }
    else{

        $errorMessage = $account->getFirstError();

        if($errorMessage == ""){
            $errorMessage = "Something went wrong!";
        } 

        $detailsMessage = "<div class='alert alert-danger'>
                                <strong>ERROR!</strong>  
                            </div>";

    }
}

if(isset($_POST["savePasswordButton"])){
    

    $account = new Account($conn);
    $oldPassword = FormSanitizer::formPasswordSanitizer($_POST["oldPassword"]);
    $newPassword = FormSanitizer::formPasswordSanitizer($_POST["newPassword"]);
    $confirmPassword = FormSanitizer::formPasswordSanitizer($_POST["newPassword2"]);

    if($account->updatePassword($oldPassword, $newPassword, $confirmPassword, $userLoggedInObject->getUsername())){

        $passwordMessage = "<div class='alert alert-success'>
                                <strong>SUCCESS!</strong>  
                            </div>";

    }
    else{

        $errorMessage = $account->getFirstError();

        if($errorMessage == ""){
            $errorMessage = "Something went wrong!";
        } 

        $passwordMessage = "<div class='alert alert-danger'>
                                <strong>ERROR!</strong>  
                            </div>";
    }

}
?>

<div class="settingsContainer column">
    <div class="formSection">

        <div class="message">
            <?php echo $detailsMessage;?>
        </div>
        <?php
            echo $settingsFormProvider->createUserDetailsForm(
                isset($_POST["firstName"]) ? $_POST["firstName"] : $userLoggedInObject->getFirstName(),
                isset($_POST["lastName"]) ? $_POST["lastName"] : $userLoggedInObject->getLastName(),
                isset($_POST["email"]) ? $_POST["email"] : $userLoggedInObject->getEmail()
            );
            // echo $settingsFormProvider->createPasswordForm();
        ?>
    </div>

    <div class="formSection">

        <div class="message">
            <?php echo $passwordMessage;?>
        </div>
        <?php
            echo $settingsFormProvider->createPasswordForm();
        ?>
    </div>
</div>


<?php
require_once("views/footer.php");
?>
