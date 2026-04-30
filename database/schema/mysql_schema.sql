SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL DEFAULT 'operator',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `registration_number` varchar(50) NOT NULL,
  `brand` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `capacity_kg` decimal(10,2) DEFAULT NULL,
  `standard_fuel_rate_km_per_liter` decimal(8,2) DEFAULT NULL,
  `primary_driver_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','inactive','maintenance') NOT NULL DEFAULT 'active',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_registration_number_unique` (`registration_number`),
  KEY `vehicles_primary_driver_id_index` (`primary_driver_id`),
  KEY `vehicles_status_index` (`status`),
  KEY `vehicles_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `drivers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `driving_license_number` varchar(100) NOT NULL,
  `driving_license_expiry_date` date DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drivers_employee_code_unique` (`employee_code`),
  UNIQUE KEY `drivers_driving_license_number_unique` (`driving_license_number`),
  KEY `drivers_driving_license_expiry_date_index` (`driving_license_expiry_date`),
  KEY `drivers_status_index` (`status`),
  KEY `drivers_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_primary_driver_id_foreign` FOREIGN KEY (`primary_driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL;

CREATE TABLE IF NOT EXISTS `farms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `address` text,
  `phone` varchar(30) DEFAULT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `farms_farm_name_index` (`farm_name`),
  KEY `farms_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `vendors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `vendor_name` varchar(255) NOT NULL,
  `details` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vendors_vendor_name_unique` (`vendor_name`),
  KEY `vendors_status_index` (`status`),
  KEY `vendors_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `route_standards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `farm_id` bigint unsigned NOT NULL,
  `vendor_id` bigint unsigned NOT NULL,
  `company_oil_liters` decimal(8,2) NOT NULL,
  `standard_distance_km` decimal(10,2) NOT NULL,
  `notes` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `route_standards_farm_id_vendor_id_index` (`farm_id`,`vendor_id`),
  KEY `route_standards_status_index` (`status`),
  KEY `route_standards_deleted_at_index` (`deleted_at`),
  CONSTRAINT `route_standards_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `route_standards_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `oil_compensation_reasons` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reason_name` varchar(255) NOT NULL,
  `requires_details` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oil_compensation_reasons_reason_name_unique` (`reason_name`),
  KEY `oil_compensation_reasons_status_index` (`status`),
  KEY `oil_compensation_reasons_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `transport_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transport_date` date NOT NULL,
  `document_no` varchar(255) NOT NULL,
  `vehicle_id` bigint unsigned NOT NULL,
  `driver_id` bigint unsigned NOT NULL,
  `farm_id` bigint unsigned NOT NULL,
  `vendor_id` bigint unsigned NOT NULL,
  `route_standard_id` bigint unsigned NOT NULL,
  `food_weight_kg` decimal(10,2) NOT NULL DEFAULT '0.00',
  `odometer_start` decimal(12,2) NOT NULL,
  `odometer_end` decimal(12,2) NOT NULL,
  `actual_distance_km` decimal(10,2) NOT NULL,
  `standard_distance_km` decimal(10,2) NOT NULL,
  `company_oil_liters` decimal(8,2) NOT NULL,
  `oil_compensation_liters` decimal(8,2) NOT NULL DEFAULT '0.00',
  `oil_compensation_reason_id` bigint unsigned DEFAULT NULL,
  `oil_compensation_details` text,
  `approved_oil_liters` decimal(8,2) NOT NULL,
  `actual_oil_liters` decimal(8,2) NOT NULL DEFAULT '0.00',
  `oil_price_per_liter` decimal(8,2) NOT NULL DEFAULT '0.00',
  `total_oil_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `oil_difference_liters` decimal(8,2) NOT NULL DEFAULT '0.00',
  `oil_difference_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `distance_difference_km` decimal(10,2) NOT NULL DEFAULT '0.00',
  `average_fuel_rate_km_per_liter` decimal(8,2) NOT NULL DEFAULT '0.00',
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transport_jobs_document_no_unique` (`document_no`),
  KEY `transport_jobs_transport_date_index` (`transport_date`),
  KEY `transport_jobs_vehicle_id_transport_date_index` (`vehicle_id`,`transport_date`),
  KEY `transport_jobs_driver_id_transport_date_index` (`driver_id`,`transport_date`),
  KEY `transport_jobs_farm_id_transport_date_index` (`farm_id`,`transport_date`),
  KEY `transport_jobs_vendor_id_transport_date_index` (`vendor_id`,`transport_date`),
  KEY `transport_jobs_route_standard_id_index` (`route_standard_id`),
  KEY `transport_jobs_oil_compensation_reason_id_index` (`oil_compensation_reason_id`),
  KEY `transport_jobs_deleted_at_index` (`deleted_at`),
  CONSTRAINT `transport_jobs_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transport_jobs_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transport_jobs_farm_id_foreign` FOREIGN KEY (`farm_id`) REFERENCES `farms` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transport_jobs_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transport_jobs_route_standard_id_foreign` FOREIGN KEY (`route_standard_id`) REFERENCES `route_standards` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transport_jobs_oil_compensation_reason_id_foreign` FOREIGN KEY (`oil_compensation_reason_id`) REFERENCES `oil_compensation_reasons` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
