<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once "db.php"; // your DB connection

$monitor = [];

// Fetch active batches
$sql = "
SELECT b.id AS batch_id, b.file_name, u.username AS cashier_name
FROM batches b
JOIN users u ON u.id = b.cashier_id
ORDER BY b.created_at DESC
";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $batch_id = $row['batch_id'];
    $file_name = $row['file_name'];
    $cashier_name = $row['cashier_name'];

    $file_path = __DIR__ . "/uploads/" . $file_name;

    $total = 0;
    $received = 0;
    $all_names = [];
    $pending_names = [];

    // Read all names from file
    if (file_exists($file_path)) {
        if (($handle = fopen($file_path, "r")) !== false) {
            $header = fgetcsv($handle); // skip header row
            while (($data = fgetcsv($handle)) !== false) {
                $name = trim($data[0] ?? '');
                if ($name !== '') {
                    $all_names[] = $name;
                }
            }
            fclose($handle);
        }
    }

    $total = count($all_names);

    // Fetch names already received in DB
    $stmt = $conn->prepare("SELECT receiver_name FROM batch_receivers WHERE batch_id = ? AND received_at IS NOT NULL");
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $received_names = [];
    while ($r = $res->fetch_assoc()) {
        $received_names[] = $r['receiver_name'];
    }
    $received = count($received_names);

    // Determine pending names
    $pending_names = array_values(array_diff($all_names, $received_names));
    $pending = count($pending_names);

    $percent = $total > 0 ? round(($received / $total) * 100) : 0;

    $monitor[] = [
        "batch_id" => $batch_id,
        "file_name" => $file_name,
        "cashier_name" => $cashier_name,
        "total" => $total,
        "received" => $received,
        "pending" => $pending,
        "pending_names" => $pending_names,
        "percent" => $percent
    ];
}

echo json_encode($monitor);
