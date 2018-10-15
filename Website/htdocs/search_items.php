<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbName = "warframe";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$length = strlen($_GET['item']);

$sql = "SELECT Item 
        FROM relic_data
        WHERE SUBSTR(Item, 1, $length) = '".$_GET['item']."'
        GROUP BY Item";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $arr[] = implode("",$row);
    }
    foreach($arr as $row) {
        $Items[explode(' ',$row)[0]][] = $row; 
    }
    foreach($Items as $item){
        $item_name = explode(' ',$item[0])[0];
        echo '<div class="btn-group" role="group">';
            echo '<button id="' . $item_name . '" type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' 
                . $item_name . 
            '</button>';
            echo '<div class="dropdown-menu" aria-labelledby="'. $item_name . '">';
                foreach($item as $row){
                    echo '<a class="dropdown-item" href="results.php?item=' . str_replace(" ", "+", $row) . '">' . $row . '</a>';
                }
            echo '</div>';
        echo '</div>';
    }
} else {
    echo "No suggestion";
}

$conn->close();
?>