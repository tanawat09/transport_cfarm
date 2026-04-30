<?php

$mysqli = mysqli_init();
$mysqli->real_connect('192.168.7.3', 'itadmin', 'Noipalee@09', 'transport_cfarm', 3308);
$mysqli->set_charset('utf8mb4');

function scalar(mysqli $mysqli, string $sql): int
{
    $result = $mysqli->query($sql);
    if (! $result) {
        throw new RuntimeException($mysqli->error);
    }

    $row = $result->fetch_row();
    return (int) ($row[0] ?? 0);
}

if (scalar($mysqli, "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'transport_cfarm' AND TABLE_NAME = 'transport_jobs' AND COLUMN_NAME = 'oil_difference_amount'") === 0) {
    if (! $mysqli->query("ALTER TABLE `transport_jobs` ADD COLUMN `oil_difference_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `oil_difference_liters`")) {
        throw new RuntimeException($mysqli->error);
    }
    echo "Added column oil_difference_amount\n";
} else {
    echo "Column oil_difference_amount already exists\n";
}

if (! $mysqli->query("UPDATE `transport_jobs` SET `oil_difference_amount` = ROUND(`oil_difference_liters` * `oil_price_per_liter`, 2)")) {
    throw new RuntimeException($mysqli->error);
}

echo "Updated oil_difference_amount for existing transport jobs\n";

$result = $mysqli->query("SELECT `document_no`, `oil_difference_liters`, `oil_price_per_liter`, `oil_difference_amount` FROM `transport_jobs` ORDER BY `id` DESC LIMIT 5");
if (! $result) {
    throw new RuntimeException($mysqli->error);
}

while ($row = $result->fetch_assoc()) {
    echo $row['document_no'] . ' => liters: ' . $row['oil_difference_liters'] . ', price: ' . $row['oil_price_per_liter'] . ', amount: ' . $row['oil_difference_amount'] . PHP_EOL;
}
