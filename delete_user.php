<?php   
header('Content-Type: application/json');
require_once 'connect.php';

try{
    $stdId = isset($_POST['stdId']) ? $_POST['stdId'] : null;
    if (!$stdId || !is_numeric($stdId)) {
        echo json_encode(["success" => false, "message" => "Invalid student ID."]);
        exit;
    }

 
    $childTables = [
        'Attendance',
        'unitregistration',

    ];
    foreach ($childTables as $table) {
        $childStmt = $con->prepare("DELETE FROM $table WHERE stdId = ?");
        $childStmt->bind_param("i", $stdId);
        $childStmt->execute();
        $childStmt->close();
    }

    $stmt = $con->prepare("DELETE FROM Students WHERE stdId = ?");
    $stmt->bind_param("i", $stdId);

    if($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    $stmt->close();
} catch(Exception $e) {
    // Always return valid JSON
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
}
?>