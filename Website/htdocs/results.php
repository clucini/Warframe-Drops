<html>
    <head>
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
        <script src="/scripts.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    </head>
<body>
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

$Items=explode(",",$_GET['item']);
$String = implode("','", $Items);

$sql = "SELECT m.Planet as Planet, m.Mission as Mission, m.Mission_Type as Mission_Type, m.Rotation as Rotation, m.Droprate as m_Droprate, m.Item as m_Item, 
                r.Item as r_Item, r.Droprate as r_Droprate, round(m.Droprate * r.Droprate / 100, 2) as Combined_Droprate
            FROM mission_data m, relic_data r
            WHERE m.Item IN (
                SELECT r.LongName
                FROM relic_data
                WHERE r.Item IN ('$String')
            )";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo("<table class='table'>");
    echo("<th>Planet</th><th>Mission</th><th>Mission Type</th><th>Rotation</th><th>Relic Droprate (%)</th><th>Relic</th><th>Item Droprate (%)</th><th>Combined Droprate (%)</th>");
    while($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    foreach($rows as $row) {
        $missions[$row['Mission']][] = $row; 
    }
    foreach($missions as $mission) {
        foreach($mission as $row) {
            echo "<tr><td>" . $row["Planet"] . "</td><td>" . $row["Mission"] . "</td><td>" . $row["Mission_Type"] . "</td><td>" . $row["Rotation"] . "</td><td>" . $row["m_Droprate"] . "</td><td>" . $row["m_Item"] . "</td><td>" . $row["r_Droprate"] . "</td><td>" . $row["Combined_Droprate"] . "</td></tr>";
        }
    }

    echo("</table>");
} else {
    echo "0 results";
}

$conn->close();
?>


</body>
</html>