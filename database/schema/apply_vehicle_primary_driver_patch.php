<?php

$mysqli = mysqli_init();
$mysqli->real_connect('192.168.7.3', 'itadmin', 'Noipalee@09', 'transport_cfarm', 3308);
$mysqli->set_charset('utf8mb4');

function scalar(mysqli $mysqli, string $sql): int
{
    $result = $mysqli->query($sql);
    if (!$result) {
        throw new RuntimeException($mysqli->error);
    }

    $row = $result->fetch_row();
    return (int) ($row[0] ?? 0);
}

if (scalar($mysqli, "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'transport_cfarm' AND TABLE_NAME = 'vehicles' AND COLUMN_NAME = 'primary_driver_id'") === 0) {
    if (!$mysqli->query("ALTER TABLE `vehicles` ADD COLUMN `primary_driver_id` BIGINT UNSIGNED NULL AFTER `standard_fuel_rate_km_per_liter`, ADD INDEX `vehicles_primary_driver_id_index` (`primary_driver_id`)") ) {
        throw new RuntimeException($mysqli->error);
    }
    echo "Added column primary_driver_id\n";
} else {
    echo "Column primary_driver_id already exists\n";
}

if (scalar($mysqli, "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = 'transport_cfarm' AND TABLE_NAME = 'vehicles' AND CONSTRAINT_NAME = 'vehicles_primary_driver_id_foreign' AND CONSTRAINT_TYPE = 'FOREIGN KEY'") === 0) {
    if (!$mysqli->query("ALTER TABLE `vehicles` ADD CONSTRAINT `vehicles_primary_driver_id_foreign` FOREIGN KEY (`primary_driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL")) {
        throw new RuntimeException($mysqli->error);
    }
    echo "Added foreign key vehicles_primary_driver_id_foreign\n";
} else {
    echo "Foreign key vehicles_primary_driver_id_foreign already exists\n";
}

$updates = [
    "UPDATE `vehicles` veh JOIN `drivers` drv ON drv.`employee_code` = 'DRV001' SET veh.`primary_driver_id` = drv.`id` WHERE veh.`registration_number` = '70-1234'",
    "UPDATE `vehicles` veh JOIN `drivers` drv ON drv.`employee_code` = 'DRV002' SET veh.`primary_driver_id` = drv.`id` WHERE veh.`registration_number` = '71-5678'",
];

foreach ($updates as $sql) {
    if (!$mysqli->query($sql)) {
        throw new RuntimeException($mysqli->error);
    }
}

echo "Updated sample vehicle-driver assignments\n";

$result = $mysqli->query("SELECT veh.`registration_number`, COALESCE(drv.`employee_code`, '-') AS employee_code, COALESCE(drv.`full_name`, '-') AS full_name FROM `vehicles` veh LEFT JOIN `drivers` drv ON drv.`id` = veh.`primary_driver_id` ORDER BY veh.`id`");
if (!$result) {
    throw new RuntimeException($mysqli->error);
}

while ($row = $result->fetch_assoc()) {
    echo $row['registration_number'] . ' => ' . $row['employee_code'] . ' / ' . $row['full_name'] . PHP_EOL;
}
