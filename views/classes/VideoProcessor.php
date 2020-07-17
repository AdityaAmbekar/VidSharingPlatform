<?php

    class VideoProcessor{
    
        private $conn;
        private $sizeLimit = 500000000;   //500Mb
        private $allowedTypes = array("mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg");
        private $ffmpegPath = "./ffmpeg/bin/ffmpeg ";
        private $ffprobePath = "./ffmpeg/bin/ffprobe";

        public function __construct($conn){

            $this->conn = $conn;
        }

        public function upload($videoUploadData){

            $targetDirectory = "uploads/videos/";
            $videoData = $videoUploadData->getVideoDataArray();

            $tempFilePath = $targetDirectory . uniqid() . basename($videoData["name"]);   //makes sure ki even in same video ka naam ebtered it will help in differentiating between them
            $tempFilePath = str_replace(" ", "_", $tempFilePath);   //uploads/videos/5e8608f217bf5Apple_Store_Screensaver_-_Imgur.mp4

            $isValidData = $this->processData($videoData, $tempFilePath);

            if(!$isValidData) {
                return false;
            }

            if(move_uploaded_file($videoData["tmp_name"], $tempFilePath)){
                
                $finalFilePath =  $targetDirectory . uniqid() . ".mp4";

                if(!$this->convertVideoToMp4($tempFilePath, $finalFilePath)){

                    echo "Video Converting Error! Upload failed";
                    return false;
                }     

                //this is put above this condition becoz if conversion is failed dnt insert into table

                if(!$this->deleteFile($tempFilePath)){

                    return false;
                }

                if(!$this->insertVideoData($videoUploadData, $finalFilePath)){

                    echo "Insert query failed";
                    return false;
                }

                if(!$this->generateThumbnail($finalFilePath)){

                    echo "Couldn't generate thumbnail";
                    return false;
                }

                return true;
             
            }
        }

        private function processData($videoData, $tempFilePath){

            $videoType = pathinfo($tempFilePath, PATHINFO_EXTENSION);   //inbuilt function gives extension of file

            if(!$this->isValidSize($videoData)){
                echo "File too Large !! Can't be greater than " . $sizeLimit . "bytes";
                return false;
            }

            else if(!$this->isValidType($videoType)){
                echo "Invalid File Types";
                return false;
            }

            else if($this->hasError($videoData)){
                echo "ERROR CODE: " . $videoData["error"];
                return false;
            }

            return true;

        }

        private function isValidSize($videoData){
            return $videoData["size"] <= $this->sizeLimit;
        }

        private function isValidType($videoType){
            $lowerCase = strtolower($videoType);
            return in_array($lowerCase, $this->allowedTypes);
        }

        private function hasError($videoData){
            return $videoData["error"] != 0;
        }

        private function insertVideoData($uploadData, $finalFilePath){

            $query = $this->conn->prepare("INSERT INTO Videos(title, uploadedBy, description, privacy, category, filePath) 
                                            VALUES(:title, :uploadedBy, :description, :privacy, :category, :filePath )");

            $query->bindParam(":title", $uploadData->title);
            $query->bindParam(":uploadedBy", $uploadData->uploadedBy);                
            $query->bindParam(":description", $uploadData->description);
            $query->bindParam(":privacy", $uploadData->privacy);
            $query->bindParam(":category", $uploadData->category);
            $query->bindParam(":filePath", $finalFilePath);

            return $query->execute();


        }

        public function convertVideoToMp4($tempFilePath, $finalFilePath){


            $convertCommand = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1";   //2>&1 enables error to if there            
            $outputLog =  array();

            exec($convertCommand, $outputLog, $returnCode);

            if($returnCode != 0){
                //it failed
                foreach($outputLog as $line){
                    echo $line . "<br>";                //will print the error on individual line
                }
                return false;
            }
            return true;

        }

        private function deleteFile($tempFilePath){

            if(!unlink($tempFilePath)){              //unlink is a php function which deletes the path you give it
                echo "Could not delete file";
                return false;
            }
            return true;
 
        }

        public function generateThumbnail($finalFilePath){

            $thumbnailSize = "1280x720";           //Youtube now uses 1280x720 earlier it was 230x118
            $numThumbnails = 3;
            $pathToThumbnails = "./uploads/videos/thumbnails/";

            $duration = $this->getVideoDuration($finalFilePath);

            $videoId = $this->conn->lastInsertId();       //built in function gives last inserted id
            $this->updateDuration($duration, $videoId);

            for($num =1 ; $num <= $numThumbnails; $num++){

                $imageName = uniqid() . ".jpeg";
                $interval = ($duration * 0.8) / $numThumbnails * $num ;
                $fullThumbnailPath = $pathToThumbnails . $videoId . "-" . $imageName;

                $convertCommand = "$this->ffmpegPath -i  $finalFilePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1";   //2>&1 enables error to if there  
                // $convertCommand = "$this->ffmpegPath -ss 3 -i $finalFilePath -vf 'select=gt(scene\,0.4)' -frames:v 5 -vsync vfr -vf fps=fps=1/600 $fullThumbnailPath  2>&1";          
                $outputLog =  array();

                exec($convertCommand, $outputLog, $returnCode);

                if($returnCode != 0){
                    //it failed
                    foreach($outputLog as $line){
                        echo $line . "<br>";                //will print the error on individual line
                    }
                }
                
                $query = $this->conn->prepare("INSERT INTO Thumbnails(videoId, filePath, selected) VALUES (:videoId, :filePath, :selected)");

                $query->bindParam(":videoId", $videoId);
                $query->bindParam(":filePath", $fullThumbnailPath);
                $query->bindParam(":selected", $selected);

                $selected = $num == 1 ? 1 : 0;

                $success = $query->execute();

                if(!$success){

                    echo "Error in generating thumbnail !!";
                    return false;
                }
            }

            return true;
        }

        private function getVideoDuration($finalFilePath){

            return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $finalFilePath");   //it is same like exec but no out put command and error mssgs is given just 
        }

        private function updateDuration($duration, $id){

            $hours = floor($duration / 3600);
            $minutes = floor(($duration - ($hours*3600)) / 60);  
            $secs = floor($duration % 60);

            
            $hours = ($hours < 1) ? "" : $hours . ":";
            $minutes = ($minutes < 10 ) ? "0" . $minutes . ":" : $minutes . ":" ;
            $secs = ($secs < 10) ? "0" . $secs : $secs;

            $formattedDuration = $hours . $minutes . $secs ;

            // echo "$formattedDuration" . "<br><br><br>";
            
            $query = $this->conn->prepare("UPDATE Videos SET duration = :duration WHERE id = :videoId");

            $query->bindParam(":duration", $formattedDuration);
            $query->bindParam(":videoId", $id);
            $query->execute();

        }
    }

?>