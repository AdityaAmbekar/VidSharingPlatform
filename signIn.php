
<?php 
    require_once("./views/config.php"); //header has config so sabko conn milega
    require_once("./views/classes/Account.php");
    require_once("./views/classes/Constants.php");
    require_once("./views/classes/FormSanitizer.php");

    $account = new Account($conn);

    if(isset($_POST["submitButton"])){    //checks that if submit button was pressed or not

        $username = FormSanitizer::formUsernameSanitizer($_POST["username"]);
        $password = FormSanitizer::formPasswordSanitizer($_POST["password"]);

        $wasSuccesful = $account->login($username, $password);

        if($wasSuccesful){
            //SUCESS ( set Session variable)
            $_SESSION["userLoggedIn"] = $username;

            //Redirect user to index page
            header("Location: index.php");    //error atay if we keep space between location and semicolon      
        }
    }

    function getInsertedValue($value){
        if(isset($_POST[$value])){
            echo $_POST[$value];
        }
    }
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>YOUTUBE CLONE</title>

    <link rel='icon' type='image/png' sizes='16x16' href='assets/images/icons/YouTube.ico'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/register.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>

<body>
    
    <div class="signInContainer">
         <div class="column">
             
            <div class="logo">
                <img src="assets/images/icons/YouTube-Logo.png" alt="Youtube-logo">
            </div>
            <div class ="header">
                <h4>SignIn To Continue</h4>
            </div>

            <div class="loginForm">

                <form action="signIn.php" method = "POST">

                    <?php echo $account->getError(Constants::$loginFailed);?>
                    <input type="text" name="username" placeholder= "Username" value = "<?php getInsertedValue('username');?>" autocomplete = "off" required>
                    <input type="password" name="password" placeholder= "Password" value = "<?php getInsertedValue('password');?>" autocomplete = "off" required>

                    <input type="submit" name="submitButton" value="LOGIN" class="submitButton" required>


                </form>

            
            </div>

            <a class = "signInMessage" href="signUp.php">Need an account? Sigup here</a>
         </div>
    </div>
</body>
</html>

