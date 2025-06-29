
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Garbage Collection Simulator</title>
    <style>
        canvas {
            border: 1px solid #000;
            background-color: #f0f0f0;
            margin: 20px;
        }
        #controls {
            margin: 20px;
        }
        button {
            margin: 5px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h2>Garbage Collection Simulator</h2>
    <div id="controls">
        <button onclick="initGraph()">Initialize Graph</button>
        <button onclick="markAndSweep()">Mark-and-Sweep</button>
        <button onclick="referenceCounting()">Reference Counting</button>
    </div>
    <canvas id="gcCanvas" width="800" height="600"></canvas>

    <script>
        const canvas = document.getElementById("gcCanvas");
        const ctx = canvas.getContext("2d");

        function initGraph() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#3498db";
            ctx.beginPath();
            ctx.arc(400, 300, 30, 0, 2 * Math.PI);
            ctx.fill();
            ctx.stroke();
        }

        function markAndSweep() {
            alert("Mark-and-Sweep not implemented yet.");
        }

        function referenceCounting() {
            alert("Reference Counting not implemented yet.");
        }
    </script>
</body>
</html>
