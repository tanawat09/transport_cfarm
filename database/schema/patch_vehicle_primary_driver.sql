SET @has_column := (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'transport_cfarm'
      AND TABLE_NAME = 'vehicles'
      AND COLUMN_NAME = 'primary_driver_id'
);
SET @sql := IF(
    @has_column = 0,
    'ALTER TABLE `vehicles` ADD COLUMN `primary_driver_id` BIGINT UNSIGNED NULL AFTER `standard_fuel_rate_km_per_liter`, ADD INDEX `vehicles_primary_driver_id_index` (`primary_driver_id`)',
    'SELECT ''primary_driver_id exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_fk := (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'transport_cfarm'
      AND TABLE_NAME = 'vehicles'
      AND CONSTRAINT_NAME = 'vehicles_primary_driver_id_foreign'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
    @has_fk = 0,
    'ALTER TABLE `vehicles` ADD CONSTRAINT `vehicles_primary_driver_id_foreign` FOREIGN KEY (`primary_driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL',
    'SELECT ''vehicles_primary_driver_id_foreign exists'''
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `vehicles` veh
JOIN `drivers` drv ON drv.`employee_code` = 'DRV001'
SET veh.`primary_driver_id` = drv.`id`
WHERE veh.`registration_number` = '70-1234';

UPDATE `vehicles` veh
JOIN `drivers` drv ON drv.`employee_code` = 'DRV002'
SET veh.`primary_driver_id` = drv.`id`
WHERE veh.`registration_number` = '71-5678';

SELECT veh.`registration_number`, drv.`employee_code`, drv.`full_name`
FROM `vehicles` veh
LEFT JOIN `drivers` drv ON drv.`id` = veh.`primary_driver_id`
ORDER BY veh.`id`;
