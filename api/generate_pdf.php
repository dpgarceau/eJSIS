<?php
/**
 * eJSIS PDF Generator
 * Generates a professional PDF report from submitted JSIS data
 */

require_once __DIR__ . '/vendor/autoload.php';

use TCPDF;

class EJSIS_PDF extends TCPDF {

    private $recordId;
    private $jsisType;

    public function setRecordInfo($recordId, $jsisType) {
        $this->recordId = $recordId;
        $this->jsisType = $jsisType;
    }

    // Custom Header
    public function Header() {
        // Logo
        $logoPath = dirname(__DIR__) . '/assets/ejsis_logo.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 6, 40, 0, 'PNG');
        }

        // Title
        $title = 'eJSIS Report';
        if ($this->jsisType === 'heatpump') {
            $title = 'Heat Pump Job Site Information Sheet';
        } elseif ($this->jsisType === 'ac') {
            $title = 'A/C Job Site Information Sheet';
        } elseif ($this->jsisType === 'gasfurnace') {
            $title = 'Gas Furnace Job Site Information Sheet';
        }

        $this->SetFont('helvetica', 'B', 14);
        $this->SetXY(55, 10);
        $this->Cell(0, 10, $title, 0, 0, 'L');

        // Line below header
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(180, 30, 30);
        $this->Line(10, 22, 200, 22);
    }

    // Custom Footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128);

        // Record ID on left
        $this->Cell(60, 10, 'Record ID: ' . $this->recordId, 0, 0, 'L');

        // Page number in center
        $this->Cell(70, 10, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');

        // Date on right
        $this->Cell(60, 10, 'Generated: ' . date('Y-m-d H:i'), 0, 0, 'R');
    }

    // Section Header
    public function sectionHeader($title) {
        $this->SetFont('helvetica', 'B', 11);
        $this->SetFillColor(180, 30, 30);
        $this->SetTextColor(255);
        $this->Cell(0, 7, ' ' . $title, 0, 1, 'L', true);
        $this->SetTextColor(0);
        $this->Ln(2);
    }

    // Data Row (only if value exists)
    public function dataRow($label, $value, $unit = '') {
        if (empty($value) && $value !== 0 && $value !== '0') {
            return false;
        }

        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(55, 5, $label . ':', 0, 0, 'R');
        $this->SetFont('helvetica', '', 9);
        $displayValue = $value . ($unit ? ' ' . $unit : '');
        $this->Cell(0, 5, $displayValue, 0, 1, 'L');
        return true;
    }

    // Two column data row
    public function dataRowTwoCol($label1, $value1, $label2, $value2, $unit1 = '', $unit2 = '') {
        $hasValue1 = !empty($value1) || $value1 === 0 || $value1 === '0';
        $hasValue2 = !empty($value2) || $value2 === 0 || $value2 === '0';

        if (!$hasValue1 && !$hasValue2) {
            return false;
        }

        if ($hasValue1) {
            $this->SetFont('helvetica', 'B', 9);
            $this->Cell(35, 5, $label1 . ':', 0, 0, 'R');
            $this->SetFont('helvetica', '', 9);
            $this->Cell(55, 5, $value1 . ($unit1 ? ' ' . $unit1 : ''), 0, 0, 'L');
        } else {
            $this->Cell(90, 5, '', 0, 0);
        }

        if ($hasValue2) {
            $this->SetFont('helvetica', 'B', 9);
            $this->Cell(35, 5, $label2 . ':', 0, 0, 'R');
            $this->SetFont('helvetica', '', 9);
            $this->Cell(0, 5, $value2 . ($unit2 ? ' ' . $unit2 : ''), 0, 1, 'L');
        } else {
            $this->Ln();
        }

        return true;
    }
}

class EJSIS_PDFGenerator {

    private $data;
    private $recordId;
    private $uploadDir;

    public function __construct($recordData, $recordId) {
        $this->data = $recordData;
        $this->recordId = $recordId;
        $this->uploadDir = __DIR__ . '/uploads/';
    }

    public function generate() {
        // Create PDF
        $pdf = new EJSIS_PDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $jsisType = $this->data['jsis_type'] ?? 'ac';
        $pdf->setRecordInfo($this->recordId, $jsisType);

        // Set document information
        $pdf->SetCreator('eJSIS');
        $pdf->SetAuthor($this->data['tech_name'] ?? 'Technician');
        $pdf->SetTitle('JSIS Report #' . $this->recordId);

        // Set margins
        $pdf->SetMargins(10, 28, 10);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);

        // Generate pages
        $this->generatePage1($pdf);
        $this->generatePage2($pdf);
        $this->generatePhotoPages($pdf);

        // Save to temp file
        $tempDir = __DIR__ . '/temp/';
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $filename = 'JSIS_' . $this->recordId . '_' . date('Ymd_His') . '.pdf';
        $filepath = $tempDir . $filename;

        $pdf->Output($filepath, 'F');

        return $filepath;
    }

    private function generatePage1($pdf) {
        $pdf->AddPage();
        $d = $this->data;

        // Service Info
        $pdf->sectionHeader('SERVICE INFORMATION');
        $pdf->dataRowTwoCol('Service Date', $d['service_date'] ?? '', 'Install Date', $d['install_date'] ?? '');
        $pdf->dataRow('JSIS Type', $this->formatJsisType($d['jsis_type'] ?? ''));
        if (($d['jsis_type'] ?? '') === 'heatpump' && !empty($d['heatpump_test_mode'])) {
            $pdf->dataRow('Test Mode', ucfirst($d['heatpump_test_mode']) . ' Mode');
        }
        $pdf->Ln(3);

        // Two column layout for contacts
        $startY = $pdf->GetY();

        // Left column - Homeowner
        $pdf->sectionHeader('HOMEOWNER');
        $pdf->dataRow('Name', $d['homeowner_name'] ?? '');
        $pdf->dataRow('Address', $d['homeowner_street'] ?? '');
        $address2 = trim(($d['homeowner_city'] ?? '') . ', ' . ($d['homeowner_state'] ?? '') . ' ' . ($d['homeowner_zip'] ?? ''));
        if ($address2 !== ', ') {
            $pdf->dataRow('', $address2);
        }
        $pdf->Ln(3);

        // Servicing Contractor
        $pdf->sectionHeader('SERVICING CONTRACTOR');
        $pdf->dataRow('Technician', $d['tech_name'] ?? '');
        $pdf->dataRow('Email', $d['tech_email'] ?? '');
        $pdf->dataRow('Mobile', $d['tech_mobile'] ?? '');
        $pdf->dataRow('Company', $d['company_name'] ?? '');
        $pdf->dataRow('Address', $d['company_street'] ?? '');
        $address3 = trim(($d['company_city'] ?? '') . ', ' . ($d['company_state'] ?? '') . ' ' . ($d['company_zip'] ?? ''));
        if ($address3 !== ', ') {
            $pdf->dataRow('', $address3);
        }
        $pdf->dataRow('Phone', $d['company_phone'] ?? '');
        $pdf->Ln(3);

        // Equipment
        $pdf->sectionHeader('EQUIPMENT');
        $pdf->dataRowTwoCol('Outdoor Model', $d['outdoor_model'] ?? '', 'Serial', $d['outdoor_serial'] ?? '');
        $pdf->dataRowTwoCol('Indoor Model', $d['indoor_model'] ?? '', 'Serial', $d['indoor_serial'] ?? '');
        if (!empty($d['coil_model']) || !empty($d['coil_serial'])) {
            $pdf->dataRowTwoCol('Coil Model', $d['coil_model'] ?? '', 'Serial', $d['coil_serial'] ?? '');
        }
        $pdf->Ln(3);

        // Problem & Actions
        $pdf->sectionHeader('PROBLEM & ACTIONS');
        if (!empty($d['problem_summary'])) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(55, 5, 'Problem Summary:', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(0, 5, $d['problem_summary'], 0, 'L');
            $pdf->Ln(2);
        }
        $pdf->dataRow('Current Fault Codes', $d['current_fault_codes'] ?? '');
        $pdf->dataRow('Fault Code History', $d['fault_code_history'] ?? '');
        if (!empty($d['corrective_actions'])) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(55, 5, 'Corrective Actions:', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(0, 5, $d['corrective_actions'], 0, 'L');
        }
        $pdf->Ln(3);

        // Electrical Data (if present)
        $hasElectrical = !empty($d['control_voltage']) || !empty($d['voltage_phase']);
        if ($hasElectrical) {
            $pdf->sectionHeader('ELECTRICAL DATA');
            $pdf->dataRowTwoCol('Control Voltage', $d['control_voltage'] ?? '', 'Voltage/Phase', $this->formatVoltagePhase($d['voltage_phase'] ?? ''), 'V', '');

            // Single phase
            if (!empty($d['supply_voltage_115'])) {
                $pdf->dataRow('Supply Voltage (115V)', $d['supply_voltage_115'], 'V');
            }
            if (!empty($d['supply_voltage_230_1ph'])) {
                $pdf->dataRow('Supply Voltage (230V)', $d['supply_voltage_230_1ph'], 'V');
            }

            // Compressor amps - single phase
            $pdf->dataRowTwoCol('Comp Start Amps', $d['comp_start_amps'] ?? '', 'Run Amps', $d['comp_run_amps'] ?? '', 'A', 'A');
            $pdf->dataRow('Comp Common Amps', $d['comp_common_amps'] ?? '', 'A');

            // Three phase voltages
            if (!empty($d['voltage_l1l2_230_3ph']) || !empty($d['voltage_l1l2_460_3ph'])) {
                $v1 = $d['voltage_l1l2_230_3ph'] ?? $d['voltage_l1l2_460_3ph'] ?? '';
                $v2 = $d['voltage_l2l3_230_3ph'] ?? $d['voltage_l2l3_460_3ph'] ?? '';
                $v3 = $d['voltage_l3l1_230_3ph'] ?? $d['voltage_l3l1_460_3ph'] ?? '';
                $pdf->dataRow('Voltage L1-L2 / L2-L3 / L3-L1', "$v1 / $v2 / $v3", 'V');
            }

            // Compressor amps - three phase
            if (!empty($d['comp_l1_amps'])) {
                $pdf->dataRow('Comp Amps L1 / L2 / L3', ($d['comp_l1_amps'] ?? '') . ' / ' . ($d['comp_l2_amps'] ?? '') . ' / ' . ($d['comp_l3_amps'] ?? ''), 'A');
            }

            // Fan amps
            $fanAmps = $d['fan_amps_115'] ?? $d['fan_amps_230_1ph'] ?? $d['fan_amps_230_3ph'] ?? $d['fan_amps_460_3ph'] ?? '';
            $pdf->dataRow('Condenser Fan Amps', $fanAmps, 'A');
            $pdf->Ln(3);
        }

        // Additional Info / Accessories
        $accessories = $this->getAccessoriesList();
        if (!empty($accessories)) {
            $pdf->sectionHeader('ADDITIONAL INFO & ACCESSORIES');

            if (!empty($d['communicating_system'])) {
                $pdf->dataRow('Communicating System', 'Yes');
                $pdf->dataRow('Outdoor Software', $d['software_outdoor'] ?? '');
                $pdf->dataRow('Indoor Software', $d['software_indoor'] ?? '');
                $pdf->dataRow('Thermostat Software', $d['software_thermostat'] ?? '');
            }
            if (!empty($d['zone_control_system'])) {
                $pdf->dataRow('Zone Control System', 'Yes');
            }

            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(55, 5, 'Accessories:', 0, 0, 'R');
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(0, 5, implode(', ', $accessories), 0, 'L');

            if (!empty($d['acc_other'])) {
                $pdf->dataRow('Other', $d['acc_other']);
            }
        }
    }

    private function generatePage2($pdf) {
        $pdf->AddPage();
        $d = $this->data;

        // Air & Airflow Data
        $pdf->sectionHeader('AIR & AIRFLOW DATA');
        $pdf->dataRowTwoCol('Airflow Direction', $this->formatAirflowDirection($d['airflow_direction'] ?? ''), 'Test Method', $this->formatTestMethod($d['airflow_test_method'] ?? ''));
        $pdf->dataRowTwoCol('Outdoor Temp', $d['outdoor_temp'] ?? '', 'Indoor DB', $d['indoor_db'] ?? '', '°F', '°F');
        $pdf->dataRow('Indoor WB', $d['indoor_wb'] ?? '', '°F');
        $pdf->Ln(2);

        // Static Pressure
        if (!empty($d['return_static']) || !empty($d['supply_static'])) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(0, 5, 'Static Pressure Method:', 0, 1, 'L');
            $pdf->dataRowTwoCol('Return Static', $d['return_static'] ?? '', 'Supply Static', $d['supply_static'] ?? '', 'in.w.c.', 'in.w.c.');
            $pdf->dataRow('Total ESP', $d['total_static'] ?? '', 'in.w.c.');
            $pdf->dataRowTwoCol('Return Temp', $d['return_temp_static'] ?? '', 'Supply DB', $d['supply_temp_static'] ?? '', '°F', '°F');
            $pdf->dataRowTwoCol('Supply WB', $d['supply_wb_static'] ?? '', 'CFM', $d['cfm_static'] ?? '', '°F', '');
        }

        // Temperature Rise
        if (!empty($d['return_temp_rise']) || !empty($d['supply_temp_rise'])) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(0, 5, 'Temperature Rise Method:', 0, 1, 'L');
            $pdf->dataRowTwoCol('Heating Voltage', $d['heating_voltage'] ?? '', 'Amperage', $d['heating_amperage'] ?? '', 'V', 'A');
            $pdf->dataRowTwoCol('Return Temp', $d['return_temp_rise'] ?? '', 'Supply DB', $d['supply_temp_rise'] ?? '', '°F', '°F');
            $pdf->dataRowTwoCol('Supply WB', $d['supply_wb_rise'] ?? '', 'Temp Rise', $d['measured_temp_rise'] ?? '', '°F', '°F');
            $pdf->dataRowTwoCol('Elevation', $d['elevation'] ?? '', 'Calc CFM', $d['calc_cfm_temprise'] ?? '', 'ft', '');
        }

        // Flowhood
        if (!empty($d['total_return_cfm']) || !empty($d['total_supply_cfm'])) {
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell(0, 5, 'Flowhood Method (' . ucfirst($d['flowhood_method'] ?? 'uncorrected') . '):', 0, 1, 'L');
            $pdf->dataRowTwoCol('Return CFM', $d['total_return_cfm'] ?? '', 'Supply CFM', $d['total_supply_cfm'] ?? '');
            $pdf->dataRowTwoCol('Return Temp', $d['return_temp_hood'] ?? '', 'Supply DB', $d['supply_temp_hood'] ?? '', '°F', '°F');
            $pdf->dataRow('Supply WB', $d['supply_wb_hood'] ?? '', '°F');
        }
        $pdf->Ln(5);

        // Refrigerant Data - Graphical Box
        $pdf->sectionHeader('REFRIGERANT DATA');
        $pdf->dataRow('Refrigerant Type', ($d['refrigerant_type'] ?? '') . (!empty($d['refrigerant_other']) ? ' (' . $d['refrigerant_other'] . ')' : ''));
        $pdf->Ln(3);

        // Draw refrigerant diagram
        $this->drawRefrigerantDiagram($pdf, $d);

        $pdf->Ln(5);

        // Line Set Details
        $hasLineset = !empty($d['lineset_length']) || !empty($d['vapor_size']) || !empty($d['liquid_size']);
        if ($hasLineset) {
            $pdf->sectionHeader('LINE SET DETAILS');
            $pdf->dataRowTwoCol('Total Length', $d['lineset_length'] ?? '', 'Vapor Size', $d['vapor_size'] ?? '', 'ft', 'in');
            $pdf->dataRowTwoCol('Liquid Size', $d['liquid_size'] ?? '', 'Position', $this->formatPosition($d['outdoor_position'] ?? ''), 'in', '');
            $pdf->dataRow('Vertical Separation', $d['vertical_separation'] ?? '', 'ft');
        }

        // Detailed Line Temps
        $hasLineTemps = !empty($d['comp_suction_temp']) || !empty($d['comp_discharge_temp']) ||
                        !empty($d['outdoor_inlet_temp']) || !empty($d['drier_inlet_temp']) ||
                        !empty($d['indoor_inlet_temp']);
        if ($hasLineTemps) {
            $pdf->Ln(3);
            $pdf->sectionHeader('DETAILED LINE TEMPERATURES');
            $pdf->dataRowTwoCol('Comp Suction', $d['comp_suction_temp'] ?? '', 'Comp Discharge', $d['comp_discharge_temp'] ?? '', '°F', '°F');
            $pdf->dataRowTwoCol('Outdoor Inlet', $d['outdoor_inlet_temp'] ?? '', 'Outdoor Discharge', $d['outdoor_discharge_temp'] ?? '', '°F', '°F');
            $pdf->dataRowTwoCol('Drier Inlet', $d['drier_inlet_temp'] ?? '', 'Drier Discharge', $d['drier_discharge_temp'] ?? '', '°F', '°F');
            $pdf->dataRowTwoCol('Indoor Inlet', $d['indoor_inlet_temp'] ?? '', 'Indoor Discharge', $d['indoor_discharge_temp'] ?? '', '°F', '°F');
        }
    }

    private function drawRefrigerantDiagram($pdf, $d) {
        $startX = 15;
        $startY = $pdf->GetY();

        // Box dimensions
        $boxWidth = 85;
        $boxHeight = 45;
        $gap = 10;

        // Low Side Box (Vapor)
        $pdf->SetDrawColor(0, 100, 180);
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($startX, $startY, $boxWidth, $boxHeight);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(0, 100, 180);
        $pdf->SetXY($startX + 2, $startY + 2);
        $pdf->Cell($boxWidth - 4, 6, 'VAPOR LINE (Low Side)', 0, 1, 'C');

        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($startX + 5, $startY + 10);
        $pdf->Cell(35, 5, 'Gauge Pressure:', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 5, ($d['vapor_pressure'] ?? '--') . ' psig', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($startX + 5, $startY + 16);
        $pdf->Cell(35, 5, 'Line Temp:', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 5, ($d['vapor_temp'] ?? '--') . ' °F', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($startX + 5, $startY + 22);
        $pdf->Cell(35, 5, 'Sat Temp:', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 5, ($d['vapor_sat_temp'] ?? '--') . ' °F', 0, 1, 'L');

        // Superheat calculation box
        $pdf->SetFillColor(230, 240, 250);
        $pdf->Rect($startX + 5, $startY + 30, $boxWidth - 10, 12, 'F');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(0, 100, 180);
        $pdf->SetXY($startX + 5, $startY + 32);
        $pdf->Cell($boxWidth - 10, 8, 'SUPERHEAT: ' . ($d['superheat'] ?? '--') . ' °F', 0, 0, 'C');

        // High Side Box (Liquid)
        $highX = $startX + $boxWidth + $gap;
        $pdf->SetDrawColor(180, 30, 30);
        $pdf->Rect($highX, $startY, $boxWidth, $boxHeight);

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(180, 30, 30);
        $pdf->SetXY($highX + 2, $startY + 2);
        $pdf->Cell($boxWidth - 4, 6, 'LIQUID LINE (High Side)', 0, 1, 'C');

        $pdf->SetTextColor(0);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($highX + 5, $startY + 10);
        $pdf->Cell(35, 5, 'Gauge Pressure:', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 5, ($d['liquid_pressure'] ?? '--') . ' psig', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($highX + 5, $startY + 16);
        $pdf->Cell(35, 5, 'Line Temp:', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 5, ($d['liquid_temp'] ?? '--') . ' °F', 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY($highX + 5, $startY + 22);
        $pdf->Cell(35, 5, 'Sat Temp:', 0, 0, 'R');
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(40, 5, ($d['liquid_sat_temp'] ?? '--') . ' °F', 0, 1, 'L');

        // Subcooling calculation box
        $pdf->SetFillColor(255, 235, 235);
        $pdf->Rect($highX + 5, $startY + 30, $boxWidth - 10, 12, 'F');
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(180, 30, 30);
        $pdf->SetXY($highX + 5, $startY + 32);
        $pdf->Cell($boxWidth - 10, 8, 'SUBCOOLING: ' . ($d['subcooling'] ?? '--') . ' °F', 0, 0, 'C');

        $pdf->SetTextColor(0);
        $pdf->SetY($startY + $boxHeight + 5);
    }

    private function generatePhotoPages($pdf) {
        $d = $this->data;
        $photos = [];

        // Collect photos
        if (!empty($d['photo_outdoor'])) {
            $photos[] = ['file' => $d['photo_outdoor'], 'label' => 'Outdoor Unit Nameplate'];
        }
        if (!empty($d['photo_indoor'])) {
            $photos[] = ['file' => $d['photo_indoor'], 'label' => 'Indoor Unit Nameplate'];
        }
        if (!empty($d['photo_additional'])) {
            $additional = json_decode($d['photo_additional'], true);
            if (is_array($additional)) {
                foreach ($additional as $i => $photo) {
                    $photos[] = ['file' => $photo, 'label' => 'Additional Photo ' . ($i + 1)];
                }
            }
        }

        // Generate photo pages
        foreach ($photos as $photo) {
            $filepath = $this->uploadDir . $photo['file'];
            if (file_exists($filepath)) {
                $pdf->AddPage();
                $pdf->sectionHeader($photo['label']);

                // Calculate image dimensions to fit page
                $maxWidth = 190;
                $maxHeight = 230;

                list($imgWidth, $imgHeight) = getimagesize($filepath);
                $ratio = min($maxWidth / $imgWidth, $maxHeight / $imgHeight);
                $newWidth = $imgWidth * $ratio;
                $newHeight = $imgHeight * $ratio;

                // Center image
                $x = (210 - $newWidth) / 2;
                $y = $pdf->GetY() + 5;

                $pdf->Image($filepath, $x, $y, $newWidth, $newHeight);
            }
        }
    }

    private function getAccessoriesList() {
        $d = $this->data;
        $list = [];

        if (!empty($d['acc_filter'])) {
            $list[] = 'Air Filter' . (!empty($d['acc_filter_type']) ? ' (' . $d['acc_filter_type'] . ')' : '');
        }
        if (!empty($d['acc_thermostat'])) {
            $list[] = 'Thermostat' . (!empty($d['acc_thermostat_model']) ? ' (' . $d['acc_thermostat_model'] . ')' : '');
        }
        if (!empty($d['acc_surge'])) $list[] = 'Surge Protector';
        if (!empty($d['acc_crankcase_heater'])) $list[] = 'Crankcase Heater';
        if (!empty($d['acc_hard_start'])) $list[] = 'Hard Start Kit';
        if (!empty($d['acc_filter_drier'])) $list[] = 'Filter Drier';
        if (!empty($d['acc_sound_blanket'])) $list[] = 'Compressor Sound Blanket';
        if (!empty($d['acc_low_ambient'])) $list[] = 'Low Ambient Kit';
        if (!empty($d['acc_time_delay'])) $list[] = 'Compressor Time Delay';
        if (!empty($d['acc_energy_mgmt'])) $list[] = 'Energy Management';
        if (!empty($d['acc_hot_gas_bypass'])) $list[] = 'Hot Gas Bypass';
        if (!empty($d['acc_hot_water_recovery'])) $list[] = 'Hot Water Recovery';
        if (!empty($d['acc_pump_down'])) $list[] = 'Pump Down Kit';

        return $list;
    }

    private function formatJsisType($type) {
        $types = [
            'ac' => 'Air Conditioning System',
            'heatpump' => 'Heat Pump System',
            'gasfurnace' => 'Gas Furnace'
        ];
        return $types[$type] ?? $type;
    }

    private function formatVoltagePhase($phase) {
        $phases = [
            '115-1ph' => '115V Single Phase',
            '230-1ph' => '208-230V Single Phase',
            '230-3ph' => '208-230V Three Phase',
            '460-3ph' => '460V Three Phase'
        ];
        return $phases[$phase] ?? $phase;
    }

    private function formatAirflowDirection($dir) {
        $dirs = [
            'upflow' => 'Upflow',
            'downflow' => 'Downflow',
            'horizontal-left' => 'Horizontal Left',
            'horizontal-right' => 'Horizontal Right'
        ];
        return $dirs[$dir] ?? $dir;
    }

    private function formatTestMethod($method) {
        $methods = [
            'static' => 'Static Pressure',
            'temprise' => 'Temperature Rise',
            'flowhood' => 'Flowhood'
        ];
        return $methods[$method] ?? $method;
    }

    private function formatPosition($pos) {
        $positions = [
            'same' => 'Same Level',
            'above' => 'Above Indoor',
            'below' => 'Below Indoor'
        ];
        return $positions[$pos] ?? $pos;
    }
}
