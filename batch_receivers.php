// After inserting/updating a batch
$allCashiers = $conn->query("SELECT id FROM cashiers")->fetch_all(MYSQLI_ASSOC);

foreach ($allCashiers as $c) {
    $cashierId = (int)$c['id'];

    // Check if row already exists
    $check = $conn->prepare("SELECT id FROM batch_receivers WHERE batch_id = ? AND cashier_id = ?");
    $check->bind_param("ii", $batch_id, $cashierId);
    $check->execute();
    $res = $check->get_result();

    if (!$res->fetch_assoc()) {
        $insertReceiver = $conn->prepare("INSERT INTO batch_receivers (batch_id, cashier_id) VALUES (?, ?)");
        $insertReceiver->bind_param("ii", $batch_id, $cashierId);
        $insertReceiver->execute();
        $insertReceiver->close();
    }

    $check->close();
}
