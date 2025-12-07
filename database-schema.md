# JSIS Database Schema

SQL schema for MySQL/MariaDB (phpMyAdmin compatible) to store all JSIS form data.

## Table Creation SQL

```sql
-- JSIS Records Table
-- Stores all job site information sheet data

CREATE TABLE IF NOT EXISTS `jsis_records` (
    -- Primary Key & Metadata
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `submitted_at` TIMESTAMP NULL,
    `status` ENUM('draft', 'submitted', 'reviewed', 'archived') DEFAULT 'draft',

    -- ============================================
    -- SECTION: Equipment Identification (Start JSIS)
    -- ============================================
    `jsis_type` ENUM('ac', 'heatpump', 'gasfurnace') NOT NULL,
    `outdoor_model` VARCHAR(100) NULL,
    `outdoor_serial` VARCHAR(100) NULL,
    `indoor_model` VARCHAR(100) NULL,
    `indoor_serial` VARCHAR(100) NULL,
    `coil_model` VARCHAR(100) NULL,
    `coil_serial` VARCHAR(100) NULL,
    `service_date` DATE NULL,
    `install_date` DATE NULL,

    -- ============================================
    -- SECTION: Contact Information (Data 1)
    -- ============================================
    -- Servicing Contractor
    `tech_name` VARCHAR(100) NULL,
    `tech_email` VARCHAR(255) NULL,
    `tech_mobile` VARCHAR(20) NULL,
    `company_name` VARCHAR(150) NULL,
    `company_street` VARCHAR(255) NULL,
    `company_city` VARCHAR(100) NULL,
    `company_state` CHAR(2) NULL,
    `company_zip` VARCHAR(10) NULL,
    `company_phone` VARCHAR(20) NULL,

    -- Homeowner Information
    `homeowner_name` VARCHAR(100) NULL,
    `homeowner_street` VARCHAR(255) NULL,
    `homeowner_city` VARCHAR(100) NULL,
    `homeowner_state` CHAR(2) NULL,
    `homeowner_zip` VARCHAR(10) NULL,

    -- ============================================
    -- SECTION: Air & Airflow Data (Data 2)
    -- ============================================
    -- Air Temperatures
    `airflow_direction` ENUM('upflow', 'downflow', 'horizontal-left', 'horizontal-right') NULL,
    `outdoor_temp` DECIMAL(5,1) NULL,
    `indoor_db` DECIMAL(5,1) NULL,
    `indoor_wb` DECIMAL(5,1) NULL,
    `airflow_test_method` ENUM('static', 'temprise', 'flowhood') NULL,

    -- Static Pressure Method
    `return_static` DECIMAL(5,2) NULL,
    `supply_static` DECIMAL(5,2) NULL,
    `total_static` DECIMAL(5,2) NULL,
    `return_temp_static` DECIMAL(5,1) NULL,
    `supply_temp_static` DECIMAL(5,1) NULL,
    `supply_wb_static` DECIMAL(5,1) NULL,
    `cfm_static` INT UNSIGNED NULL,

    -- Temperature Rise Method
    `heating_voltage` DECIMAL(6,1) NULL,
    `heating_amperage` DECIMAL(6,1) NULL,
    `return_temp_rise` DECIMAL(5,1) NULL,
    `supply_temp_rise` DECIMAL(5,1) NULL,
    `supply_wb_rise` DECIMAL(5,1) NULL,
    `measured_temp_rise` DECIMAL(5,1) NULL,
    `elevation` INT UNSIGNED NULL,
    `calc_cfm_temprise` INT UNSIGNED NULL,

    -- Flowhood Method
    `flowhood_method` ENUM('uncorrected', 'corrected') NULL,
    `total_return_cfm` INT UNSIGNED NULL,
    `total_supply_cfm` INT UNSIGNED NULL,
    `return_temp_hood` DECIMAL(5,1) NULL,
    `supply_temp_hood` DECIMAL(5,1) NULL,
    `supply_wb_hood` DECIMAL(5,1) NULL,

    -- ============================================
    -- SECTION: Refrigerant Data (Data 3)
    -- ============================================
    -- Basic Refrigerant Info
    `refrigerant_type` ENUM('R-22', 'R-410A', 'R-32', 'R-454B', 'Other') NULL,
    `refrigerant_other` VARCHAR(50) NULL,
    `heatpump_test_mode` ENUM('cooling', 'heating') NULL,

    -- Liquid Line (High Side)
    `liquid_pressure` DECIMAL(6,1) NULL,
    `liquid_temp` DECIMAL(5,1) NULL,
    `liquid_sat_temp` DECIMAL(5,1) NULL,
    `subcooling` DECIMAL(5,1) NULL,

    -- Vapor Line (Low Side)
    `vapor_pressure` DECIMAL(6,1) NULL,
    `vapor_temp` DECIMAL(5,1) NULL,
    `vapor_sat_temp` DECIMAL(5,1) NULL,
    `superheat` DECIMAL(5,1) NULL,

    -- Detailed Line Temperatures (Compressor)
    `comp_suction_temp` DECIMAL(5,1) NULL,
    `comp_discharge_temp` DECIMAL(5,1) NULL,

    -- Detailed Line Temperatures (Outdoor Unit Coil)
    `outdoor_inlet_temp` DECIMAL(5,1) NULL,
    `outdoor_discharge_temp` DECIMAL(5,1) NULL,

    -- Detailed Line Temperatures (Filter Drier)
    `drier_inlet_temp` DECIMAL(5,1) NULL,
    `drier_discharge_temp` DECIMAL(5,1) NULL,

    -- Detailed Line Temperatures (Indoor Unit Coil)
    `indoor_inlet_temp` DECIMAL(5,1) NULL,
    `indoor_discharge_temp` DECIMAL(5,1) NULL,

    -- ============================================
    -- SECTION: Line Set Details (Data 4)
    -- ============================================
    `lineset_length` DECIMAL(6,1) NULL,
    `vapor_size` VARCHAR(20) NULL,
    `liquid_size` VARCHAR(20) NULL,
    `outdoor_position` ENUM('same', 'above', 'below') NULL,
    `vertical_separation` DECIMAL(5,1) NULL,

    -- ============================================
    -- SECTION: Electrical Data (Data 5)
    -- ============================================
    `control_voltage` DECIMAL(5,1) NULL,
    `voltage_phase` ENUM('115-1ph', '230-1ph', '230-3ph', '460-3ph') NULL,

    -- 115V / 230V Single Phase
    `supply_voltage_115` DECIMAL(5,1) NULL,
    `supply_voltage_230_1ph` DECIMAL(5,1) NULL,
    `comp_start_amps` DECIMAL(6,1) NULL,
    `comp_run_amps` DECIMAL(6,1) NULL,
    `comp_common_amps` DECIMAL(6,1) NULL,
    `fan_amps_115` DECIMAL(5,1) NULL,
    `fan_amps_230_1ph` DECIMAL(5,1) NULL,

    -- 230V Three Phase
    `voltage_l1l2_230_3ph` DECIMAL(5,1) NULL,
    `voltage_l2l3_230_3ph` DECIMAL(5,1) NULL,
    `voltage_l3l1_230_3ph` DECIMAL(5,1) NULL,

    -- 460V Three Phase
    `voltage_l1l2_460_3ph` DECIMAL(5,1) NULL,
    `voltage_l2l3_460_3ph` DECIMAL(5,1) NULL,
    `voltage_l3l1_460_3ph` DECIMAL(5,1) NULL,

    -- Three Phase Compressor Amps (shared)
    `comp_l1_amps` DECIMAL(6,1) NULL,
    `comp_l2_amps` DECIMAL(6,1) NULL,
    `comp_l3_amps` DECIMAL(6,1) NULL,
    `fan_amps_230_3ph` DECIMAL(5,1) NULL,
    `fan_amps_460_3ph` DECIMAL(5,1) NULL,

    -- ============================================
    -- SECTION: Problem & Actions (Data 6)
    -- ============================================
    `problem_summary` TEXT NULL,
    `current_fault_codes` VARCHAR(255) NULL,
    `fault_code_history` VARCHAR(255) NULL,
    `corrective_actions` TEXT NULL,

    -- ============================================
    -- SECTION: Additional Info (Data 7)
    -- ============================================
    -- System Type
    `communicating_system` TINYINT(1) DEFAULT 0,
    `software_outdoor` VARCHAR(50) NULL,
    `software_indoor` VARCHAR(50) NULL,
    `software_thermostat` VARCHAR(50) NULL,
    `zone_control_system` TINYINT(1) DEFAULT 0,

    -- Accessories
    `acc_filter` TINYINT(1) DEFAULT 0,
    `acc_filter_type` VARCHAR(100) NULL,
    `acc_sound_blanket` TINYINT(1) DEFAULT 0,
    `acc_time_delay` TINYINT(1) DEFAULT 0,
    `acc_crankcase_heater` TINYINT(1) DEFAULT 0,
    `acc_energy_mgmt` TINYINT(1) DEFAULT 0,
    `acc_filter_drier` TINYINT(1) DEFAULT 0,
    `acc_hard_start` TINYINT(1) DEFAULT 0,
    `acc_hot_gas_bypass` TINYINT(1) DEFAULT 0,
    `acc_hot_water_recovery` TINYINT(1) DEFAULT 0,
    `acc_low_ambient` TINYINT(1) DEFAULT 0,
    `acc_pump_down` TINYINT(1) DEFAULT 0,
    `acc_surge` TINYINT(1) DEFAULT 0,
    `acc_thermostat` TINYINT(1) DEFAULT 0,
    `acc_thermostat_model` VARCHAR(100) NULL,
    `acc_other_check` TINYINT(1) DEFAULT 0,
    `acc_other` TEXT NULL,

    -- ============================================
    -- SECTION: Photos (Data 8)
    -- ============================================
    -- Photo file paths/references (actual files stored in filesystem or blob storage)
    `photo_outdoor` VARCHAR(500) NULL,
    `outdoor_photo_na` TINYINT(1) DEFAULT 0,
    `photo_indoor` VARCHAR(500) NULL,
    `photo_additional` TEXT NULL,  -- JSON array of file paths for multiple photos

    -- ============================================
    -- INDEXES
    -- ============================================
    INDEX `idx_jsis_type` (`jsis_type`),
    INDEX `idx_service_date` (`service_date`),
    INDEX `idx_tech_name` (`tech_name`),
    INDEX `idx_company_name` (`company_name`),
    INDEX `idx_homeowner_name` (`homeowner_name`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Quick Copy SQL (No Comments)

```sql
CREATE TABLE IF NOT EXISTS `jsis_records` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `submitted_at` TIMESTAMP NULL,
    `status` ENUM('draft', 'submitted', 'reviewed', 'archived') DEFAULT 'draft',
    `jsis_type` ENUM('ac', 'heatpump', 'gasfurnace') NOT NULL,
    `outdoor_model` VARCHAR(100) NULL,
    `outdoor_serial` VARCHAR(100) NULL,
    `indoor_model` VARCHAR(100) NULL,
    `indoor_serial` VARCHAR(100) NULL,
    `coil_model` VARCHAR(100) NULL,
    `coil_serial` VARCHAR(100) NULL,
    `service_date` DATE NULL,
    `install_date` DATE NULL,
    `tech_name` VARCHAR(100) NULL,
    `tech_email` VARCHAR(255) NULL,
    `tech_mobile` VARCHAR(20) NULL,
    `company_name` VARCHAR(150) NULL,
    `company_street` VARCHAR(255) NULL,
    `company_city` VARCHAR(100) NULL,
    `company_state` CHAR(2) NULL,
    `company_zip` VARCHAR(10) NULL,
    `company_phone` VARCHAR(20) NULL,
    `homeowner_name` VARCHAR(100) NULL,
    `homeowner_street` VARCHAR(255) NULL,
    `homeowner_city` VARCHAR(100) NULL,
    `homeowner_state` CHAR(2) NULL,
    `homeowner_zip` VARCHAR(10) NULL,
    `airflow_direction` ENUM('upflow', 'downflow', 'horizontal-left', 'horizontal-right') NULL,
    `outdoor_temp` DECIMAL(5,1) NULL,
    `indoor_db` DECIMAL(5,1) NULL,
    `indoor_wb` DECIMAL(5,1) NULL,
    `airflow_test_method` ENUM('static', 'temprise', 'flowhood') NULL,
    `return_static` DECIMAL(5,2) NULL,
    `supply_static` DECIMAL(5,2) NULL,
    `total_static` DECIMAL(5,2) NULL,
    `return_temp_static` DECIMAL(5,1) NULL,
    `supply_temp_static` DECIMAL(5,1) NULL,
    `supply_wb_static` DECIMAL(5,1) NULL,
    `cfm_static` INT UNSIGNED NULL,
    `heating_voltage` DECIMAL(6,1) NULL,
    `heating_amperage` DECIMAL(6,1) NULL,
    `return_temp_rise` DECIMAL(5,1) NULL,
    `supply_temp_rise` DECIMAL(5,1) NULL,
    `supply_wb_rise` DECIMAL(5,1) NULL,
    `measured_temp_rise` DECIMAL(5,1) NULL,
    `elevation` INT UNSIGNED NULL,
    `calc_cfm_temprise` INT UNSIGNED NULL,
    `flowhood_method` ENUM('uncorrected', 'corrected') NULL,
    `total_return_cfm` INT UNSIGNED NULL,
    `total_supply_cfm` INT UNSIGNED NULL,
    `return_temp_hood` DECIMAL(5,1) NULL,
    `supply_temp_hood` DECIMAL(5,1) NULL,
    `supply_wb_hood` DECIMAL(5,1) NULL,
    `refrigerant_type` ENUM('R-22', 'R-410A', 'R-32', 'R-454B', 'Other') NULL,
    `refrigerant_other` VARCHAR(50) NULL,
    `heatpump_test_mode` ENUM('cooling', 'heating') NULL,
    `liquid_pressure` DECIMAL(6,1) NULL,
    `liquid_temp` DECIMAL(5,1) NULL,
    `liquid_sat_temp` DECIMAL(5,1) NULL,
    `subcooling` DECIMAL(5,1) NULL,
    `vapor_pressure` DECIMAL(6,1) NULL,
    `vapor_temp` DECIMAL(5,1) NULL,
    `vapor_sat_temp` DECIMAL(5,1) NULL,
    `superheat` DECIMAL(5,1) NULL,
    `comp_suction_temp` DECIMAL(5,1) NULL,
    `comp_discharge_temp` DECIMAL(5,1) NULL,
    `outdoor_inlet_temp` DECIMAL(5,1) NULL,
    `outdoor_discharge_temp` DECIMAL(5,1) NULL,
    `drier_inlet_temp` DECIMAL(5,1) NULL,
    `drier_discharge_temp` DECIMAL(5,1) NULL,
    `indoor_inlet_temp` DECIMAL(5,1) NULL,
    `indoor_discharge_temp` DECIMAL(5,1) NULL,
    `lineset_length` DECIMAL(6,1) NULL,
    `vapor_size` VARCHAR(20) NULL,
    `liquid_size` VARCHAR(20) NULL,
    `outdoor_position` ENUM('same', 'above', 'below') NULL,
    `vertical_separation` DECIMAL(5,1) NULL,
    `control_voltage` DECIMAL(5,1) NULL,
    `voltage_phase` ENUM('115-1ph', '230-1ph', '230-3ph', '460-3ph') NULL,
    `supply_voltage_115` DECIMAL(5,1) NULL,
    `supply_voltage_230_1ph` DECIMAL(5,1) NULL,
    `comp_start_amps` DECIMAL(6,1) NULL,
    `comp_run_amps` DECIMAL(6,1) NULL,
    `comp_common_amps` DECIMAL(6,1) NULL,
    `fan_amps_115` DECIMAL(5,1) NULL,
    `fan_amps_230_1ph` DECIMAL(5,1) NULL,
    `voltage_l1l2_230_3ph` DECIMAL(5,1) NULL,
    `voltage_l2l3_230_3ph` DECIMAL(5,1) NULL,
    `voltage_l3l1_230_3ph` DECIMAL(5,1) NULL,
    `voltage_l1l2_460_3ph` DECIMAL(5,1) NULL,
    `voltage_l2l3_460_3ph` DECIMAL(5,1) NULL,
    `voltage_l3l1_460_3ph` DECIMAL(5,1) NULL,
    `comp_l1_amps` DECIMAL(6,1) NULL,
    `comp_l2_amps` DECIMAL(6,1) NULL,
    `comp_l3_amps` DECIMAL(6,1) NULL,
    `fan_amps_230_3ph` DECIMAL(5,1) NULL,
    `fan_amps_460_3ph` DECIMAL(5,1) NULL,
    `problem_summary` TEXT NULL,
    `current_fault_codes` VARCHAR(255) NULL,
    `fault_code_history` VARCHAR(255) NULL,
    `corrective_actions` TEXT NULL,
    `communicating_system` TINYINT(1) DEFAULT 0,
    `software_outdoor` VARCHAR(50) NULL,
    `software_indoor` VARCHAR(50) NULL,
    `software_thermostat` VARCHAR(50) NULL,
    `zone_control_system` TINYINT(1) DEFAULT 0,
    `acc_filter` TINYINT(1) DEFAULT 0,
    `acc_filter_type` VARCHAR(100) NULL,
    `acc_sound_blanket` TINYINT(1) DEFAULT 0,
    `acc_time_delay` TINYINT(1) DEFAULT 0,
    `acc_crankcase_heater` TINYINT(1) DEFAULT 0,
    `acc_energy_mgmt` TINYINT(1) DEFAULT 0,
    `acc_filter_drier` TINYINT(1) DEFAULT 0,
    `acc_hard_start` TINYINT(1) DEFAULT 0,
    `acc_hot_gas_bypass` TINYINT(1) DEFAULT 0,
    `acc_hot_water_recovery` TINYINT(1) DEFAULT 0,
    `acc_low_ambient` TINYINT(1) DEFAULT 0,
    `acc_pump_down` TINYINT(1) DEFAULT 0,
    `acc_surge` TINYINT(1) DEFAULT 0,
    `acc_thermostat` TINYINT(1) DEFAULT 0,
    `acc_thermostat_model` VARCHAR(100) NULL,
    `acc_other_check` TINYINT(1) DEFAULT 0,
    `acc_other` TEXT NULL,
    `photo_outdoor` VARCHAR(500) NULL,
    `outdoor_photo_na` TINYINT(1) DEFAULT 0,
    `photo_indoor` VARCHAR(500) NULL,
    `photo_additional` TEXT NULL,
    INDEX `idx_jsis_type` (`jsis_type`),
    INDEX `idx_service_date` (`service_date`),
    INDEX `idx_tech_name` (`tech_name`),
    INDEX `idx_company_name` (`company_name`),
    INDEX `idx_homeowner_name` (`homeowner_name`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Column Summary

| Section | Column Count |
|---------|--------------|
| Metadata | 5 |
| Equipment Identification | 9 |
| Contact Information | 14 |
| Air & Airflow Data | 24 |
| Refrigerant Data | 17 |
| Line Set Details | 5 |
| Electrical Data | 17 |
| Problem & Actions | 4 |
| Additional Info | 21 |
| Photos | 4 |
| **Total** | **120** |

## Notes

1. **Photos**: File paths are stored as VARCHAR/TEXT. Actual image files should be stored in filesystem or cloud storage (S3, etc.)

2. **Checkboxes**: Stored as `TINYINT(1)` where 0 = unchecked, 1 = checked

3. **ENUM fields**: Match the form dropdown options. Values use lowercase with hyphens replaced by underscores where needed.

4. **Calculated fields**: Stored in database for historical record, even though they can be recalculated from source values.

5. **NULL handling**: Most fields allow NULL since the form has conditional logic and not all fields apply to all JSIS types.

6. **Character set**: UTF-8 (utf8mb4) for full Unicode support including special characters.
