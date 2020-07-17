
<?php


class VideoGrid{

    private $conn, $userLoggedInObject;
    private $largeMode = false;
    private $mediumMode = false;
    private $gridClass = "videoGrid";

    public function __construct($conn, $userLoggedInObject){

        $this->conn = $conn;
        $this->userLoggedInObject = $userLoggedInObject;

    }

    public function create($videos, $title, $showFilter){

        if($videos == null){

            $gridItems = $this->generateItems();
        }
        else{

            $gridItems = $this->generateItemsFromVideos($videos);

        }

        $header = "";

        if($title != null){

            $header = $this->createGridHeader($title, $showFilter);
        }

        return "$header
                <div class= '$this->gridClass'>
                    $gridItems
                </div>";

    }

    public function generateItems(){

        $query = $this->conn->prepare("SELECT * FROM Videos ORDER BY RAND() LIMIT 15");
        $query->execute();

        $elementsHtml = "";
        while($row = $query->fetch(PDO::FETCH_ASSOC)){

            $video = new Video($this->conn, $row, $this->userLoggedInObject);

            $item = new VideoGridItem($this->conn ,$video, $this->largeMode, $this->mediumMode);

            $elementsHtml .= $item->create();

        }

        return $elementsHtml;

    }

    public function generateItemsFromVideos($videos){

        $elementsHtml = "";

        foreach($videos as $video){

            $items = new VideoGridItem($this->conn ,$video, $this->largeMode, $this->mediumMode);
            $elementsHtml .= $items->create();

        }
        return $elementsHtml;
    }

    public function createGridHeader($title, $showFilter){

        $filter ="";

        if($showFilter){

            $link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            $urlArray = parse_url($link);

            $query = $urlArray["query"]; 
            parse_str($query, $params);

            unset($params["orderBy"]);

            $newQuery = http_build_query($params);

            $newUrl = basename($_SERVER["PHP_SELF"]). "?" . $newQuery;
            
            $filter = "<div class='right'>
                            <span>FILTERS: </span>
                            <a href='$newUrl&orderBy=uploadDate'>UploadDate</a>
                            <a href='$newUrl'>Most Viewed</a>
                        </div>";
        }

        $header = "<div class='videoGridHeader'>
                        <div class='left'>
                            $title
                        </div>
                        $filter
                    </div>";

        return $header;
    }

    public function createMedium($videos, $title, $showFilter){

        $this->gridClass .= " medium";
        $this->mediumMode = true;

        return $this->create($videos, $title, $showFilter);

    }

    public function createLarge($videos, $title, $showFilter){

        $this->gridClass .= " large";
        $this->largeMode = true;

        return $this->create($videos, $title, $showFilter);
    }

}

?>