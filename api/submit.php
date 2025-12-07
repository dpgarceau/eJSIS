<?php
/**
 * eJSIS Form Submission Handler
 * Receives JSON form data and inserts into database
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Load database config
require_once __DIR__ . '/jsis_dbconfig.php';

// Get JSON input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

// Map form field IDs to database columns
function mapFieldToColumn($fieldId) {
    // Convert kebab-case to snake_case
    return str_replace('-', '_', $fieldId);
}

// Build the insert data from form sections
$insertData = [];

// Section mappings - form section => array of field IDs
$sections = [
    'startJSIS' => [
        'jsis-type', 'outdoor-model', 'outdoor-serial', 'indoor-model', 'indoor-serial',
        'coil-model', 'coil-serial', 'service-date', 'install-date'
    ],
    'data1' => [
        'tech-name', 'tech-email', 'tech-mobile', 'company-name', 'company-street',
        'company-city', 'company-state', 'company-zip', 'company-phone',
        'homeowner-name', 'homeowner-street', 'homeowner-city', 'homeowner-state', 'homeowner-zip'
    ],
    'data2' => [
        'airflow-direction', 'outdoor-temp', 'indoor-db', 'indoor-wb', 'airflow-test-method',
        'return-static', 'supply-static', 'total-static', 'return-temp-static',
        'supply-temp-static', 'supply-wb-static', 'cfm-static',
        'heating-voltage', 'heating-amperage', 'return-temp-rise', 'supply-temp-rise',
        'supply-wb-rise', 'measured-temp-rise', 'elevation', 'calc-cfm-temprise',
        'flowhood-method', 'total-return-cfm', 'total-supply-cfm',
        'return-temp-hood', 'supply-temp-hood', 'supply-wb-hood'
    ],
    'data3' => [
        'refrigerant-type', 'refrigerant-other', 'heatpump-test-mode',
        'liquid-pressure', 'liquid-temp', 'liquid-sat-temp', 'subcooling',
        'vapor-pressure', 'vapor-temp', 'vapor-sat-temp', 'superheat',
        'comp-suction-temp', 'comp-discharge-temp',
        'outdoor-inlet-temp', 'outdoor-discharge-temp',
        'drier-inlet-temp', 'drier-discharge-temp',
        'indoor-inlet-temp', 'indoor-discharge-temp'
    ],
    'data4' => [
        'lineset-length', 'vapor-size', 'liquid-size', 'outdoor-position', 'vertical-separation'
    ],
    'data5' => [
        'control-voltage', 'voltage-phase',
        'supply-voltage-115', 'supply-voltage-230-1ph',
        'comp-start-amps', 'comp-run-amps', 'comp-common-amps',
        'fan-amps-115', 'fan-amps-230-1ph',
        'voltage-l1l2-230-3ph', 'voltage-l2l3-230-3ph', 'voltage-l3l1-230-3ph',
        'voltage-l1l2-460-3ph', 'voltage-l2l3-460-3ph', 'voltage-l3l1-460-3ph',
        'comp-l1-amps', 'comp-l2-amps', 'comp-l3-amps',
        'fan-amps-230-3ph', 'fan-amps-460-3ph'
    ],
    'data6' => [
        'problem-summary', 'current-fault-codes', 'fault-code-history', 'corrective-actions'
    ],
    'data7' => [
        'communicating-system', 'software-outdoor', 'software-indoor', 'software-thermostat',
        'zone-control-system',
        'acc-filter', 'acc-filter-type', 'acc-sound-blanket', 'acc-time-delay',
        'acc-crankcase-heater', 'acc-energy-mgmt', 'acc-filter-drier', 'acc-hard-start',
        'acc-hot-gas-bypass', 'acc-hot-water-recovery', 'acc-low-ambient', 'acc-pump-down',
        'acc-surge', 'acc-thermostat', 'acc-thermostat-model', 'acc-other-check', 'acc-other'
    ],
    'data8' => [
        'photo-outdoor', 'outdoor-photo-na', 'photo-indoor', 'photo-additional'
    ]
];

// Process each section
foreach ($sections as $sectionKey => $fields) {
    if (!isset($data[$sectionKey])) {
        continue;
    }

    $sectionData = $data[$sectionKey];

    foreach ($fields as $fieldId) {
        if (isset($sectionData[$fieldId])) {
            $column = mapFieldToColumn($fieldId);
            $value = $sectionData[$fieldId];

            // Handle boolean values (checkboxes)
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            }

            // Handle empty strings as NULL
            if ($value === '') {
                $value = null;
            }

            $insertData[$column] = $value;
        }
    }
}

// Add metadata
$insertData['status'] = 'submitted';
$insertData['submitted_at'] = date('Y-m-d H:i:s');

// Validate required field
if (empty($insertData['jsis_type'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'JSIS type is required']);
    exit;
}

try {
    $pdo = getDbConnection();

    // Build INSERT query
    $columns = array_keys($insertData);
    $placeholders = array_map(function($col) { return ':' . $col; }, $columns);

    $sql = "INSERT INTO jsis_records (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")";

    $stmt = $pdo->prepare($sql);

    // Bind values
    foreach ($insertData as $column => $value) {
        $stmt->bindValue(':' . $column, $value);
    }

    $stmt->execute();
    $recordId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'record_id' => $recordId,
        'message' => 'JSIS record saved successfully'
    ]);

} catch (Exception $e) {
    error_log("JSIS submit error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
