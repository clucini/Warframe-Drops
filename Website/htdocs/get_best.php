<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbName = "warframe";



try {
    $conn = new mysqli($servername, $username, $password, $dbName);
    
    $Items=explode(",",$_GET['item']);

    echo("<table>");
    echo("<th>Planet</th><th>Mission</th><th>Mission Type</th><th>Rotation</th><th>Relic Droprate (%)</th><th>Relic(s)</th><th>Item(s)</th><th>Item Droprate (%)</th><th>Combined Droprate (%)</th>");

    $Missions = [];
    $Best_Mission = "";
    $best_Score = 0;

    foreach($Items as $item){
        $stmt = $conn->prepare("SELECT m.Planet as Planet, m.Mission as Mission, m.Mission_Type as Mission_Type, m.Rotation as Rotation, m.Droprate as m_Droprate, m.Item as Relic, 
                                r.Item as Item, r.Droprate as r_Droprate, round(m.Droprate * r.Droprate / 100, 2) as Combined_Droprate
                                FROM mission_data m, relic_data r
                                WHERE m.Item IN (
                                    SELECT r.LongName
                                    FROM relic_data
                                    WHERE r.Item=?
                                )");
        $stmt->bind_param('s',  $item);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows > 0) {
            while($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }
            foreach($rows as $row) {
                $temp[$row['Mission']][] = $row; 
            }
            foreach($temp as $mission) {
                foreach($mission as $row) {
                    if(array_key_exists($row['Mission'],$Missions)){
                        if(array_key_exists($row['Rotation'],$Missions[$row['Mission']])){
                            
                            if(in_array($row['Relic'], $Missions[$row['Mission']][$row['Rotation']]['Relic'])==FALSE){
                                array_push($Missions[$row['Mission']][$row['Rotation']]['Relic'], $row['Relic']);
                                $Missions[$row['Mission']][$row['Rotation']]['m_Droprate'] += $row['m_Droprate'];                            
                                $Missions[$row['Mission']][$row['Rotation']]['Combined_Droprate'] += $row['Combined_Droprate'];    
                            }
                            if(in_array($row['Item'], $Missions[$row['Mission']][$row['Rotation']]['Item'])==FALSE){
                                array_push($Missions[$row['Mission']][$row['Rotation']]['Item'], $row['Item']);
                            }
                        }
                        else {
                            $Missions[$row['Mission']][$row['Rotation']] = $row;
                            $Missions[$row['Mission']][$row['Rotation']]['Relic'] = explode(",",$row['Relic']);
                            $Missions[$row['Mission']][$row['Rotation']]['Item'] = explode(",",$row['Item']);
                        }
                    }
                    else {
                        $Missions[$row['Mission']][$row['Rotation']] = $row;
                        $Missions[$row['Mission']][$row['Rotation']]['Relic'] = explode(",",$row['Relic']);
                        $Missions[$row['Mission']][$row['Rotation']]['Item'] = explode(",",$row['Item']);                        
                    }
                }
            }
            foreach($Missions as $m=>$mission) {
                $m_total = 0;
                foreach($mission as $r=>$rotation){
                    if($r == 'A') {
                        $m_total += $rotation['Combined_Droprate'] * 2;
                    }
                    else {
                        $m_total += $rotation['Combined_Droprate'];
                    }
                }
                if ($m_total > $best_Score){
                    $Best_Mission = $m;
                    $best_Score = $m_total;
                }
            }
        } else {
            echo "<p>This item is vaulted</p>";
        }
    }
    foreach($Missions[$Best_Mission] as $row) {
        echo "<tr><td>" . $row["Planet"] . "</td><td>" . $row["Mission"] . "</td><td>" . $row["Mission_Type"] . "</td><td>" . $row["Rotation"] . "</td><td>" . $row["m_Droprate"] . "</td><td>" . implode(', ', $row['Relic']) . "</td><td>" . implode(', ', $row['Item']) . "</td><td>" . $row["r_Droprate"] . "</td><td>" . $row["Combined_Droprate"] . "</td></tr>";
    }
    $conn->close();
    echo '</table>';
}
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }

?>