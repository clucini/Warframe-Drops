<html>
    <head>
        <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/jquery.dataTables.min.js"></script>
        <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
        <title>Warframe Grind Assistant</title>
        <link rel="icon" href="favicon.ico">
    </head>
    <body>
        <div class="container-fluid" style="max-width:75%">
            <div class = "row mt-5">
                <div class="col-md-12">
                    <table class='table' id='tab'>
                        <thead>
                            <th>Planet</th><th>Mission</th><th>Mission Type</th><th>Rotation</th><th>Relic Droprate (%)</th><th>Relic(s)</th><th>Item(s)</th><th>Item Droprate (%)</th><th>Combined Droprate (%)</th>
                        </thead>
                        <tbody>
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
                            $Missions = [];
                            $Items=explode(",",$_GET['item']);
                            $String = implode("','", $Items);
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
                                } else {
                                    echo "<p>This item is vaulted</p>";
                                }
                            }
                            foreach($Missions as $mission)
                            {
                                foreach($mission as $row)
                                {
                                    echo "<tr><td>" . $row["Planet"] . "</td><td>" . $row["Mission"] . "</td><td>" . $row["Mission_Type"] . "</td><td>" . $row["Rotation"] . "</td><td>" . $row["m_Droprate"] . "</td><td>" . implode(', ', $row['Relic']) . "</td><td>" . implode(', ', $row['Item']) . "</td><td>" . $row["r_Droprate"] . "</td><td>" . $row["Combined_Droprate"] . "</td></tr>";
                                }
                            }
                            $conn->close();
                            ?>

                        
                        </tbody>
                    </table>
                    <script>
                        $(document).ready(function() {
                            $('#tab').DataTable({
                                "autoWidth": false,
                                "iDisplayLength": 25
                            });
                        } );
                    </script>
                </div>
            </div>
        </div>
    </body>
</html>