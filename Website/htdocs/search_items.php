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

$item = $_GET['item'];
$length = strlen($item);

$stmt = $conn->prepare("SELECT Item 
                        FROM relic_data
                        WHERE SUBSTR(Item, 1, $length) = ?
                        GROUP BY Item");
$stmt->bind_param('s',  $item);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
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
                    echo '<button class="dropdown-item" onclick="addItems(\''. $row . '\')">' . $row . '</button>';
                }
            echo '</div>';
        echo '</div>';
    }
} else {
    echo "<p>No Suggestions</p>";
}

$conn->close();
?>