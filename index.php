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

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Rochester Average Temperatures</title>
    <style>
        * {
            font-family: Calibri, Arial, sans-serif;   
        }
        
        .center {
            text-align: center;
        }
        
        #mycanvas {
            display: block;
            width: 960px;
            height: 480px;
            margin: auto;
        }
    </style>
    <script src="https://code.jquery.com/jquery-2.1.0.min.js"></script>
    <script>
        // Temperature data
        var data = <?php echo json_encode($data); ?>;
        //console.log(data);
        
        // Document ready
        document.addEventListener("DOMContentLoaded", function() {
            
            // Setup the canvas
            var c = document.getElementById("mycanvas");
            var ctx = c.getContext("2d");
            
            var width = Number(c.getAttribute("width"));
            var height = Number(c.getAttribute("height"));
            var baseline = 150;
            var startX = (width - (data.length)*2) / 2;
            var startY = (height - baseline);
            var scaleX = 2;
            var scaleY = 3;
            
            var currentMonth = "";
            var oddMonth = true;
            
            // Draw a line
            function drawLine(x1, y1, x2, y2) {
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.stroke();   
            }
            
            // Draw a point
            function drawPoint(x, y) {
                ctx.fillRect(x-1, y-1, 2, 2);
                //ctx.beginPath();
                //ctx.arc(x, y, 2, 0, 2*Math.PI);
                //ctx.fill();
            }
            
            // Loop through each data point and draw the lines
            for (var i = 0; i < data.length; i++) {
                var x = startX + i * scaleX;
                //console.log(data[i]);
                
                // Draw the background
                if (data[i]["month"] != currentMonth) {
                    currentMonth = data[i]["month"];
                    oddMonth = !oddMonth;
                    ctx.font = "14px Calibri";
                    ctx.fillStyle = "black";
                    ctx.textAlign = "center";
                    ctx.fillText(currentMonth.substr(0, 3), x + 30, height - 50);
                }
                if (oddMonth) {
                    ctx.strokeStyle = "#CCCCCC";
                    drawLine(x, 0, x, height - 67);
                }
                
                // Avgerage High
                ctx.fillStyle = "red";
                drawPoint(x, startY - Number(data[i]["average_high"]) * scaleY);
                //console.log("X: "+x+", Y:"+(startY - Number(data[i]["average_high"]) * scaleY));
                
                // Mean
                ctx.fillStyle = "black";
                drawPoint(x, startY - Number(data[i]["mean"]) * scaleY);
                
                // Average Low
                ctx.fillStyle = "blue";
                drawPoint(x, startY - Number(data[i]["average_low"]) * scaleY);
                
                // Record High
                ctx.fillStyle = "red";
                drawPoint(x, startY - Number(data[i]["record_high"]) * scaleY);
                
                // Record Low
                ctx.fillStyle = "blue";
                drawPoint(x, startY - Number(data[i]["record_low"]) * scaleY);
            }
            
            // Draw the grid
            ctx.strokeStyle = "#222222";
            ctx.font = "14px Calibri";
            ctx.fillStyle = "black";
            drawLine(startX - scaleX, height, startX - scaleX, 0);
            for (var i = -1; i < 6; i++) {
                var interval = 20;
                var aX = i * interval;
                drawLine(startX - scaleX, startY - (aX * scaleY), width - startX, startY - (aX * scaleY));
                ctx.fillText(""+aX, startX - 25, startY - (aX * scaleY));
            }
            
            // Label the axis
            ctx.font = "18px Calibri";
            ctx.fillStyle = "black";
            ctx.rotate(-Math.PI / 2);
            ctx.textAlign = "center";
            ctx.fillText("Temperature (F)", -height/2 , 50);
            ctx.rotate(-3 * (Math.PI / 2));
            ctx.fillText("Month", width/2, height - 15);
            
        });
        
    </script>
</head>
<body>
    <h1 class="center">Rochester Average Temperatures</h1>
    <canvas id="mycanvas" width="960" height="480"></canvas>
</body>
</html>