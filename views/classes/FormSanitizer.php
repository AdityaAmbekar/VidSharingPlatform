<?php

class FormSanitizer {

    public static function formStringSanitizer($inputText){

        $inputText = strip_tags($inputText);
        $inputText = trim($inputText);
        $inputText = strtolower($inputText);
        $inputText = ucfirst($inputText);

        return $inputText;
    }

    public static function formUsernameSanitizer($inputText){

        $inputText = strip_tags($inputText);
        $inputText = str_replace(" ", "", $inputText);

        return $inputText;
    }

    public static function formEmailSanitizer($inputText){

        $inputText = strip_tags($inputText);
        $inputText = trim($inputText);

        return $inputText;
    }

    public static function formPasswordSanitizer($inputText){

        $inputText = strip_tags($inputText);

        return $inputText;
    }

}

?>