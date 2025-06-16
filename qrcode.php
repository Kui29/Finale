<?php
    require_once 'connect.php';
    // Handle POST request for saving QR code
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qrlabel']) && isset($_POST['qrphoto'])) {
        $qrlabel = $_POST['qrlabel'];
        $qrphoto = $_POST['qrphoto'];
        //ternary operator to check if lecId is set in the session, otherwise set it to null
        $lecId = isset($_SESSION['lecId']) ? $_SESSION['lecId'] : null;
        // Only store base64 image data, not the full data URL
        if (strpos($qrphoto, 'base64,') !== false) {
            $qrphoto = explode('base64,', $qrphoto, 2)[1];
        }
        // Prepare SQL statement to insert QR code data
        $stmt = $con->prepare('INSERT INTO qrcode (qrlabel, qrphoto, lecId) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $qrlabel, $qrphoto, $lecId);
        // Execute and respond
        if ($stmt->execute()) {
            echo 'QR code saved to database.';
        } else {
            http_response_code(500);
            echo 'Database error: ' . $stmt->error;
        }
        $stmt->close(); // Close statement
        $con->close(); // Close connection
        exit; // Stop further execution
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Set character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive design -->
    <title>QR Code Generator</title> <!-- Page title -->
    <style>
        /* Container for the generated QR code image */
        #qrcode {
            width: 160px;
            height: 160px;
            margin-top: 20px;
        }
        /* Show the label helper when the input is focused or hovered */
        #text:focus + #label-helper,
        #text:hover + #label-helper {
            display: block !important; /* element will start on a new line and take up full width available
                                          any other css style will be overridden by !important */
        }
    </style>
</head>
<body>
    <h1>QR Code Generator</h1> <!-- Main heading -->
    Enter QR Code Label: <input type="text" id="text" placeholder="e.g., CMT 105 Lecture 7"> <!-- Input for label -->
        <button onclick="generateQRCode()">Generate QR Code</button> <!-- Button to generate QR -->
    <div id="label-helper" style="color:#888;font-size:14px;margin-bottom:10px;display:none;">Label must be exactly 4 words separated by single spaces</div>

    <!-- Container for QR Code -->
    <div id="qrcode"></div>
    <button id="downloadBtn" style="display:none; margin-top:10px;">Download QR Code</button> <!-- Download button -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script> <!-- QR code library -->
    <script>
        let lastQRCodeCanvas = null; // Store last QR code canvas
        // Clear QR code when typing a new entry
        document.getElementById('text').addEventListener('input', function() {
            document.getElementById('qrcode').innerHTML = '';
            document.getElementById('downloadBtn').style.display = 'none';
        });
        function isValidLabel(label) {
            // Must have exactly 4 words separated by a single space, no leading/trailing spaces, no double spaces
            return /^([A-Za-z0-9]+\s[A-Za-z0-9]+\s[A-Za-z0-9]+\s[A-Za-z0-9]+)$/.test(label.trim());
        }
        function generateQRCode() {
            // Get the trimmed value from the label input field
            var text = document.getElementById('text').value.trim(); 
            // Validate the label format: must be exactly 4 words separated by a single space
            if (!isValidLabel(text)) {
                alert('Label must contain exactly 4 words separated by a single space (e.g., "CMT 105 Lecture 7").');
                return;
            }
            // Get the QR code container div
            var qrcodeDiv = document.getElementById("qrcode"); 
            // Clear any previous QR code or label from the container
            qrcodeDiv.innerHTML = ""; 
            // Create a new QRCode object and render it in the container
            var qrcode = new QRCode(qrcodeDiv, {
                text: text, // The label text to encode in the QR code
                width: 160, // Width of the QR code in pixels
                height: 160, // Height of the QR code in pixels
                colorDark : "#000000", // Color of the QR code modules
                colorLight : "#ffffff", // Background color of the QR code
                correctLevel : QRCode.CorrectLevel.H // Error correction level (high)
            });
            // Wait a short time for the QR code to render, then show the download button
            setTimeout(function() {
                // Try to get the generated QR code as a canvas or image element
                let img = qrcodeDiv.querySelector('img');
                let canvas = qrcodeDiv.querySelector('canvas');
                // Store the last QR code element for possible download
                lastQRCodeCanvas = canvas || img;
                // Show the download button
                document.getElementById('downloadBtn').style.display = 'inline-block';
            }, 500);
            // Display the label text below the QR code for user reference
            var labelElement = document.createElement('p');
            labelElement.textContent = text;
            qrcodeDiv.appendChild(labelElement);
        }
        // Download and save QR code
        document.getElementById('downloadBtn').onclick = function() {
            var text = document.getElementById('text').value.trim(); // Get label
            if (!isValidLabel(text)) {
                alert('Label must contain exactly 4 words separated by a single space (e.g., "CMT 105 Lecture 7").');
                return;
            }
            var qrcodeDiv = document.getElementById('qrcode');
            let img = qrcodeDiv.querySelector('img');
            let canvas = qrcodeDiv.querySelector('canvas');
            let dataUrl = "";
            if (canvas) {
                dataUrl = canvas.toDataURL("image/png"); // Get PNG from canvas
            } else if (img) {
                dataUrl = img.src; // Get PNG from img
            } else {
                alert('No QR code found to download.');
                return;
            }
            var a = document.createElement('a'); // Create download link
            a.href = dataUrl;
            a.download = text + '.png';
            document.body.appendChild(a);
            a.click(); // Trigger download
            document.body.removeChild(a);
            var formData = new FormData(); // Prepare form data for POST
            formData.append('qrlabel', text);
            formData.append('qrphoto', dataUrl);
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(msg => { /* Optionally show a message */ })
            .catch(err => { /* Optionally handle error */ });
        };
        // Show label helper on focus
        document.getElementById('text').addEventListener('focus', function() {
            document.getElementById('label-helper').style.display = 'block';
        });
        // Hide label helper on blur
        document.getElementById('text').addEventListener('blur', function() {
            document.getElementById('label-helper').style.display = 'none';
        });
    </script>
</body>
</html>