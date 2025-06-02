<?php
// Database configuration
$servername = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'the seeds';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle different actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'getYears') {
    try {
        $sql = "SELECT DISTINCT YEAR(p.payment_time) AS year 
                FROM payment p
                JOIN registration_class rc ON p.payment_id = rc.payment_id
                ORDER BY year DESC";
        $result = $conn->query($sql);
        
        if ($result === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Query failed: ' . $conn->error]);
            exit;
        }

        $years = [];
        while ($row = $result->fetch_assoc()) {
            $years[] = (int)$row['year']; // Ensure year is an integer
        }

        // Debug: Log the years fetched
        error_log("Years fetched: " . implode(', ', $years));

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'years' => $years]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error fetching years: ' . $e->getMessage()]);
    }
    $conn->close();
    exit;
}

// Fetch payment data
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? str_pad((int)$_GET['month'], 2, '0', STR_PAD_LEFT) : date('m');
$yearMonth = "$year-$month";
$start_date = date("Y-m-01 00:00:00", strtotime($yearMonth));
$end_date = date("Y-m-t 23:59:59", strtotime($yearMonth));

// Debug: Log the date range
error_log("Fetching payments for year: $year, month: $month, Start: $start_date, End: $end_date");

$sql = "SELECT 
            p.payment_id, 
            pr.parent_name, 
            p.payment_method, 
            p.payment_time, 
            p.payment_total_amount 
        FROM payment p
        JOIN credit_cards cc ON p.credit_card_id = cc.credit_card_id
        JOIN parent pr ON cc.parent_id = pr.parent_id
        JOIN registration_class rc ON p.payment_id = rc.payment_id
        WHERE p.payment_time BETWEEN ? AND ? 
        ORDER BY p.payment_id ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'payment_id' => (int)$row['payment_id'],
        'parent_name' => $row['parent_name'] ?? 'Unknown',
        'payment_method' => $row['payment_method'] ?? 'N/A',
        'payment_time' => $row['payment_time'] ?? 'N/A',
        'payment_total_amount' => number_format((float)$row['payment_total_amount'], 2, '.', '')
    ];
}

// Debug: Log the number of records fetched
error_log("Records fetched: " . count($data));

header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$conn->close();
?>