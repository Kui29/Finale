<?php
require_once 'connect.php';
//Checks if the lecturer is logged in. If not, shows a message and stops execution.
if (!isset($_SESSION['lecId'])) {
    echo '<p>You must be logged in as a lecturer to view QR codes.</p>';
    exit;
}
// Gets the lecturer's ID from the session
$lecId = $_SESSION['lecId'];
// Fetch all QR codes for this lecturer
$result = $con->prepare('SELECT qrlabel, qrphoto FROM qrcode WHERE lecId = ?');
$result->bind_param('s', $lecId);
$result->execute();
$res = $result->get_result();
$qrcodes = [];
while ($row = $res->fetch_assoc()) {
    // Extract unit code (first two words)
    $labelWords = preg_split('/\s+/', $row['qrlabel']);
    $unit = isset($labelWords[0], $labelWords[1]) ? $labelWords[0] . ' ' . $labelWords[1] : $row['qrlabel'];
    //Groups QR codes by unit
    $qrcodes[$unit][] = [
        'label' => $row['qrlabel'],
        'photo' => $row['qrphoto']
    ];
}
$result->close();
$con->close();
// Sort lectures for each unit by lecture number
foreach ($qrcodes as $unit => &$lectures) {
    usort($lectures, function($a, $b) {
        // Extract lecture number from label (e.g., 'Lecture 1')
        preg_match('/Lecture\s+(\d+)/i', $a['label'], $matchA);
        preg_match('/Lecture\s+(\d+)/i', $b['label'], $matchB);
        $numA = isset($matchA[1]) ? (int)$matchA[1] : 0;
        $numB = isset($matchB[1]) ? (int)$matchB[1] : 0;
        // Sorts lectures in ascending order based on the lecture number
        return $numA - $numB;
    });
}
//Unsets the reference to avoid accidental modification later.
unset($lectures);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Codes by Unit</title>
    <style>
        .unit-block {
            display: inline-block;
            margin: 20px;
            text-align: center;
            vertical-align: top;
            background: #f9f9f9;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 18px 20px 20px 20px;
            min-width: 220px;
        }
        .unit-block img {
            display: block;
            margin: 0 auto 12px auto;
        }
        .unit-btn {
            margin: 0 auto;
            display: block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        }
        .unit-btn:hover { background: #357a38; }
        .qr-list-container {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(6px);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .qr-list {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            max-width: 900px;
            margin: 40px auto 0 auto;
            justify-content: center;
        }
        .qr-img {
            width: 160px;
            height: 160px;
            border: 1px solid #ccc;
            background: #fff;
            margin-bottom: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .qr-img:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .zoom-modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .zoom-modal.active {
            display: flex;
        }
        .zoom-img {
            max-width: 80vw;
            max-height: 80vh;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            background: #fff;
            padding: 16px;
        }
        .close-modal {
            position: absolute;
            top: 30px;
            right: 40px;
            font-size: 2.5rem;
            color: #fff;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 2100;
        }
    </style>
    <script>
        function toggleQR(unit) {
            // Hide all containers
            document.querySelectorAll('.qr-list-container').forEach(function(el) { el.style.display = 'none'; });
            var qrContainer = document.getElementById('qr-list-container-' + unit.replace(/\s+/g, '_'));
            if (qrContainer.style.display === 'flex') {
                qrContainer.style.display = 'none';
            } else {
                qrContainer.style.display = 'flex';
            }
        }
        function showZoom(imgSrc) {
            var modal = document.getElementById('zoom-modal');
            var zoomImg = document.getElementById('zoom-img');
            zoomImg.src = imgSrc;
            modal.classList.add('active');
        }
        function closeZoom() {
            document.getElementById('zoom-modal').classList.remove('active');
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('zoom-modal').addEventListener('click', function(e) {
                if (e.target === this) closeZoom();
            });
        });
    </script>
</head>
<body>
    <h2>QR Codes by Unit</h2>
    <?php if (empty($qrcodes)): ?>
        <p>No QR codes found for your units.</p>
    <?php else: ?>
        <script>
        // Open a new page for the selected unit's QR codes
        function openUnitQR(unit) {
            window.open('qrDisplay.php?unit=' + encodeURIComponent(unit), '_blank');
        }
        </script>
        <?php foreach ($qrcodes as $unit => $photos): ?>
            <div class="unit-block">
                <img src = "liz.jpg" alt = "QR Logo">
                <button class="unit-btn" onclick="openUnitQR('<?php echo htmlspecialchars($unit); ?>')"><?php echo htmlspecialchars($unit); ?></button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php
    // Only show QR codes for the selected unit if ?unit= is set
    $selectedUnit = isset($_GET['unit']) ? $_GET['unit'] : null;
    if ($selectedUnit && isset($qrcodes[$selectedUnit])) {
        $photos = $qrcodes[$selectedUnit];
        echo '<div id="content" style="max-width:900px;margin:40px auto 0 auto;text-align:center;">';
        echo '<h2>' . htmlspecialchars($selectedUnit) . ' QR Codes</h2>';
        echo '<div id="qrNav" style="margin-bottom:20px;">';
        foreach ($photos as $idx => $photo) {
            echo '<img class="qr-img-nav" src="data:image/png;base64,' . htmlspecialchars($photo['photo']) . '" alt="QR Code for ' . htmlspecialchars($photo['label']) . '" style="width:120px;height:120px;margin:0 8px 12px 8px;cursor:pointer;border:2px solid #ccc;border-radius:8px;" onclick="showFullQR(' . $idx . ')">';
        }
        echo '</div>';
        // Fullscreen QR display
        echo '<div id="fullQRContainer" style="display:none;position:relative;">';
        echo '<button onclick="prevQR()" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:2rem;background:none;border:none;cursor:pointer;z-index:2;">&#8592;</button>';
        echo '<img id="fullQRImg" src="" alt="Full QR" style="max-width:80vw;max-height:70vh;display:block;margin:auto;box-shadow:0 8px 32px rgba(0,0,0,0.25);background:#fff;padding:16px;border-radius:12px;">';
        echo '<button onclick="nextQR()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:2rem;background:none;border:none;cursor:pointer;z-index:2;">&#8594;</button>';
        echo '<div id="qrLabel" style="margin-top:18px;font-size:1.2rem;color:#333;"></div>';
        echo '<button onclick="closeFullQR()" style="position:absolute;top:10px;right:20px;font-size:2rem;background:none;border:none;color:#333;cursor:pointer;">&times;</button>';
        echo '</div>';
        echo '</div>';
        // JS for navigation and fullscreen QR
        echo '<script>
        var qrPhotos = ' . json_encode($photos) . ';
        var currentIdx = 0;
        function showFullQR(idx) {
            currentIdx = idx;
            document.getElementById("fullQRImg").src = "data:image/png;base64," + qrPhotos[idx]["photo"];
            document.getElementById("qrLabel").textContent = qrPhotos[idx]["label"];
            document.getElementById("fullQRContainer").style.display = "block";
        }
        function closeFullQR() {
            document.getElementById("fullQRContainer").style.display = "none";
        }
        function prevQR() {
            if (currentIdx > 0) showFullQR(currentIdx - 1);
        }
        function nextQR() {
            if (currentIdx < qrPhotos.length - 1) showFullQR(currentIdx + 1);
        }
        </script>';
    } else if ($selectedUnit) {
        echo '<div style="text-align:center;margin-top:40px;color:red;">No QR codes found for this unit.</div>';
    }
    ?>
</body>
</html>

