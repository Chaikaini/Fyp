<?php
// Database configuration
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'the seeds';

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'getYears') {
    try {
        $sql = "SELECT DISTINCT YEAR(child_register_date) AS year 
                FROM child 
                ORDER BY year DESC";
        $stmt = $conn->query($sql);
        
        $years = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $years[] = (int)$row['year']; // Convert to integer
        }

        // Debug: Log the years fetched
        error_log("Years fetched: " . implode(', ', $years));

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'years' => $years]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error fetching years: ' . $e->getMessage()]);
    }
    $conn = null;
    exit;
}

// Fetch child data
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? str_pad((int)$_GET['month'], 2, '0', STR_PAD_LEFT) : date('m');
$yearMonth = "$year-$month";
$start_date = date("Y-m-01 00:00:00", strtotime($yearMonth));
$end_date = date("Y-m-t 23:59:59", strtotime($yearMonth));

// Debug: Log the date range
error_log("Fetching children for year: $year, month: $month, Start: $start_date, End: $end_date");

try {
    $sql = "SELECT 
                child_id, 
                child_name, 
                child_gender, 
                child_kidNumber, 
                child_birthday, 
                child_register_date 
            FROM child 
            WHERE child_register_date BETWEEN ? AND ? 
            ORDER BY child_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$start_date, $end_date]);

    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = [
            'child_id' => (int)$row['child_id'],
            'child_name' => $row['child_name'] ?? 'Unknown',
            'child_gender' => $row['child_gender'] ?? 'N/A',
            'child_kidNumber' => $row['child_kidNumber'] ?? 'N/A',
            'child_birthday' => $row['child_birthday'] ?? 'N/A',
            'child_register_date' => $row['child_register_date'] ?? 'N/A'
        ];
    }

    // Debug: Log the number of records fetched
    error_log("Records fetched: " . count($data));

    header('Content-Type', 'application/json');
    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Query error: ' . $e->getMessage()]);
}

$conn = null;
?>