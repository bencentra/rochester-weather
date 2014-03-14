<?php
// Database vars
$dbuser = "weatherman";
$dbpass = "9BEyLF9a3cbVhrKj";
$dbhost = "localhost";
$dbname = "data_visualization";
$pdo = null;

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
}
catch (PDOException $e) {
    die($e->getMessage());
}

// Get the weather data
$sql = "SELECT month, day, average_high, mean, average_low, record_high, record_low FROM rochester_weather";
$stmt = $pdo->query($sql);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Format the data
$data = array();
foreach ($result as $day) {
    $arr = array();
    foreach($day as $key => $val) {
        $arr[$key] = explode("°", utf8_encode($val))[0];  
    }
    $data[] = $arr;
}

echo "<pre>";
echo json_encode($data);
echo "</pre>";

?>