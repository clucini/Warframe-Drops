<html>
    <head></head>
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

$sql = "SELECT * 
            FROM mission_data
            WHERE Item IN (
                SELECT LongName
                FROM relic_data
                WHERE Item='".$_GET['item']."'
            )
            ORDER BY Droprate DESC";


$osql = "SELECT m.Planet, m.Mission, m.Mission_Type, m.Droprate, m.Item as Relic, r.Item, r.Droprate
        from mission_data m left join relic_data r on r.Longname=m.Item";
$result = $conn->query($sql);
echo($result)

if ($result->num_rows > 0) {
    echo("<table>");
    echo("<th>Planet</th><th>Mission</th><th>Mission Type</th><th>Droprate (%)</th><th>Relic</th>");
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["Planet"] . "</td><td>" . $row["Mission"] . "</td><td>" . $row["Mission_Type"] . "</td><td>" . $row["Droprate"] . "</td><td>" . $row["Relic"] . "</td></tr>";
    }
    echo("</table>");
} else {
    echo "0 results";
}

$conn->close();
?>

</body>
</html>