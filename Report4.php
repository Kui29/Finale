<?php
// Create connection
require("connect.php");

// Query to select data from the failed_registrations view
$sql = "SELECT * FROM failed_registrations";
$result = $con->query($sql);

// Initialize arrays for storing data
$unitCodes = [];
$failedRegistrations = [];

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch data and store in arrays
    while ($row = $result->fetch_assoc()) {
        $unitCodes[] = $row["unitCode"];
        $failedRegistrations[] = $row["failed_registrations"];
    }
} else {
    echo "0 results";
}

// Close the connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Failed Registrations Report - Pie Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Failed Registrations Report - Pie Chart</h1>

    <!-- Canvas for the Pie Chart -->
    <canvas id="failedRegistrationsPieChart" width="400" height="400"></canvas>

    <script>
        // Get data from PHP and convert it to JSON
        const unitCodes = <?php echo json_encode($unitCodes); ?>;
        const failedRegistrations = <?php echo json_encode($failedRegistrations); ?>;

        // Set up the Pie chart
        const ctx = document.getElementById('failedRegistrationsPieChart').getContext('2d');
        const failedRegistrationsPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: unitCodes,  // Labels (unit codes)
                datasets: [{
                    label: 'Failed Registrations',
                    data: failedRegistrations,  // Data (failed registrations count)
                    backgroundColor: [
                        '#1f77b4', // Blue
                        '#ff7f0e', // Orange
                        '#2ca02c', // Green
                        '#d62728', // Red
                        '#9467bd', // Purple
                        '#8c564b'  // Brown
                    ],  // Colors that are easier to distinguish
                    borderColor: [
                        '#1f77b4', 
                        '#ff7f0e', 
                        '#2ca02c', 
                        '#d62728', 
                        '#9467bd', 
                        '#8c564b'
                    ],  // Border colors matching the segments
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,  // Make the chart responsive to window resizing
                plugins: {
                    legend: {
                        position: 'top',  // Display the legend at the top of the chart
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' failed registrations';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>