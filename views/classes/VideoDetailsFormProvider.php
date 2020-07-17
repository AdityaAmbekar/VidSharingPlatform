<?php 

class VideoDetailsFormProvider{

    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function createUploadForm(){

        $fileInput = $this->createFileInput();
        $titleInput = $this->createTitleInput(null);
        $descriptionInput = $this->createDescriptionInput(null);
        $categoryInput = $this->createCategoriesInput(null);
        $privacyInput = $this->createPrivacyInput(null);
        $uploadButton = $this->createUploadButton();
        return "
                <form action= 'processing.php' method = 'POST' enctype='multipart/form-data'> 
                    $fileInput
                    $titleInput
                    $descriptionInput
                    $categoryInput
                    $privacyInput
                    $uploadButton
                </form>
                  ";
    }

    public function createEditDetailsForm($video){

        $titleInput = $this->createTitleInput($video->getTitle());
        $descriptionInput = $this->createDescriptionInput($video->getDescription());
        $categoryInput = $this->createCategoriesInput($video->getCategory());
        $privacyInput = $this->createPrivacyInput($video->getPrivacy());
        $saveButton = $this->createSaveButton();

        return "<form method = 'POST'> 
                    $titleInput
                    $descriptionInput
                    $categoryInput
                    $privacyInput
                    $saveButton
                </form>";
    }


    private function createFileInput(){

        return "<div class='form-group'>
                     <input type='file' class='form-control-file' name = 'fileInput' required>    
                 </div>";

    }

    private function createTitleInput($value){

        if($value == null) $value = ""; 

        return "<div class='form-group'>
                    <input class='form-control' type='text' placeholder='Title' name = 'titleInput' value='$value'>
                </div>";
    }

    private function createDescriptionInput($value){

        if($value == null) $value = "";

        return "<div class='form-group'>
                    <textarea class='form-control' placeholder = 'Description' name = 'descriptionInput' rows='3'>$value</textarea>
                </div>";
    }

    private function createPrivacyInput($value){

        if($value == null) $value = "";

        $privateSelected = ($value == 0) ? "selected='selected'" : "";
        $publicSelected = ($value == 1) ? "selected='selected'" : "";

        return "<div class='form-group'  >
                <select class='form-control' name = 'privacyInput'>
                    <option value = '-1'>Select Visibility Mode</option>
                    <option value = '0' $privateSelected>Private</option>
                    <option value = '1' $publicSelected>Public</option>
                </select>
                </div>";
    }

    private function createCategoriesInput($value){

        if($value == null) $value = "";
        
        $query = $this->conn->prepare("SELECT * FROM Categories");
            $query->execute();
            $html = "<div class='form-group'  >
                    <select class='form-control' name = 'categoryInput'>
                    <option value = '0'>Select Category</option>";


            while($row = $query->fetch(PDO::FETCH_ASSOC)) {  
                 //fetch-assoc return it in key value pair
                $id = $row["id"];
                $name = $row["name"];
                $selected = ($id == $value) ? "selected='selected'" : "";

                $html .= "<option $selected value = $id>$name</option>";     
            }

            $html .= "</select>
                        </div>";

            return $html;
    }

    private function createUploadButton(){

        return "<button type='submit' class= 'btn btn-primary' name = 'uploadButton'>Upload</button>";
    }

    private function createSaveButton(){

        return "<button type='submit' class= 'btn btn-primary' name = 'saveButton'>Save</button>";
    }
}

?>