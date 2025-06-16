<?php
// Database connection
require("connect.php");
// Query to get successful registrations
$sql = "SELECT unitCode, COUNT(*) AS successful_registrations 
        FROM UnitRegistration 
        WHERE registrationStatus = 'Successful' 
        GROUP BY unitCode";
$result = $con->query($sql);

$unitCodes = [];
$successfulRegistrations = [];

while ($row = $result->fetch_assoc()) {
    $unitCodes[] = $row['unitCode'];
    $successfulRegistrations[] = $row['successful_registrations'];
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Successful Registrations Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Successful Registrations Report</h1>

    <!-- Canvas for the bar chart -->
    <canvas id="barChart" width="400" height="200"></canvas>

    <script>
        const ctx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($unitCodes); ?>, // X-axis labels (unit codes)
                datasets: [{
                    label: 'Successful Registrations',
                    data: <?php echo json_encode($successfulRegistrations); ?>, // Y-axis data (registration counts)
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Set the interval (step size) for the y-axis to 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

