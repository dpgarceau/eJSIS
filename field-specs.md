# JSIS Field Specifications

Detailed specifications for all data fields in the JSIS form.

## Section: Equipment Identification (Start JSIS)

| Field | ID | Type | Required | Validation | Notes |
|-------|-----|------|----------|------------|-------|
| JSIS Type | jsis-type | select | Yes | AC, Heat Pump, Gas Furnace | Determines form fields shown |
| Outdoor Unit Model # | outdoor-model | text | Yes* | - | *Not required for Gas Furnace |
| Outdoor Unit Serial # | outdoor-serial | text | Yes* | - | Has barcode scanner |
| Indoor Unit Model # | indoor-model | text | Yes* | - | Air Handler or Furnace |
| Indoor Unit Serial # | indoor-serial | text | Yes* | - | Has barcode scanner |
| Coil Model # | coil-model | text | No | - | If separate from air handler |
| Coil Serial # | coil-serial | text | No | - | Has barcode scanner |
| Date of Service | service-date | date | Yes* | - | - |
| Install Date | install-date | date | No | - | Optional |

*Gas Furnace Only: Only JSIS Type is required (placeholder for future implementation)

## Section: Contact Information (Data 1)

### Servicing Contractor

| Field | ID | Type | Required | Validation | Format |
|-------|-----|------|----------|------------|--------|
| Technician Name | tech-name | text | Yes | - | - |
| Email | tech-email | email | Yes | Valid email | - |
| Technician Mobile | tech-mobile | tel | Yes | - | - |
| Company Name | company-name | text | Yes | - | - |
| Street Address | company-street | text | Yes | - | - |
| City | company-city | text | Yes | - | - |
| State | company-state | text | Yes | 2 characters max | Uppercase abbreviation |
| Zip Code | company-zip | text | Yes | 10 chars max | 12345 or 12345-6789 |
| Company Phone | company-phone | tel | Yes | - | - |

### Homeowner Information

| Field | ID | Type | Required | Validation | Format |
|-------|-----|------|----------|------------|--------|
| Homeowner Name | homeowner-name | text | Yes | - | - |
| Street Address | homeowner-street | text | Yes | - | Geolocation available |
| City | homeowner-city | text | Yes | - | - |
| State | homeowner-state | text | Yes | 2 characters max | Uppercase abbreviation |
| Zip Code | homeowner-zip | text | Yes | 10 chars max | 12345 or 12345-6789 |

## Section: Air & Airflow Data (Data 2)

### Air Temperatures

| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Air Handler/Furnace Air Flow Direction | airflow-direction | select | Yes | - | Upflow, Downflow, Horizontal Left, Horizontal Right |
| Outdoor Air Temperature | outdoor-temp | number | Yes | °F | 1 |
| Indoor Dry Bulb | indoor-db | number | Yes | °F | 1 |
| Indoor Wet Bulb | indoor-wb | number | No | °F | 1 |
| Airflow Test Method | airflow-test-method | select | Yes | - | Static Pressure, Temperature Rise, Flowhood |

### Static Pressure Method

| Field | ID | Type | Required | Unit | Decimals | Notes |
|-------|-----|------|----------|------|----------|-------|
| Return Static Pressure | return-static | number | Yes | in. w.c. | 2 | Negative value (e.g. -0.20) |
| Supply Static Pressure | supply-static | number | Yes | in. w.c. | 2 | Positive value (e.g. 0.45) |
| Total External Static | total-static | calculated | Auto | in. w.c. | 2 | Supply − Return |
| Return Temperature at Plenum | return-temp-static | number | Yes | °F | 1 | - |
| Supply Dry Bulb Temp at Plenum | supply-temp-static | number | Yes | °F | 1 | - |
| Supply Wet Bulb Temp at Plenum | supply-wb-static | number | No | °F | 1 | - |
| CFM | cfm-static | number | No | CFM | 0 | Manual entry |

### Temperature Rise Method

| Field | ID | Type | Required | Unit | Decimals | Notes |
|-------|-----|------|----------|------|----------|-------|
| Heating Element Voltage | heating-voltage | number | No | V | 1 | For CFM calculation |
| Heating Element Amperage | heating-amperage | number | No | A | 1 | For CFM calculation |
| Return Temperature at Plenum | return-temp-rise | number | Yes | °F | 1 | - |
| Supply Dry Bulb Temp at Plenum | supply-temp-rise | number | Yes | °F | 1 | - |
| Supply Wet Bulb Temp at Plenum | supply-wb-rise | number | No | °F | 1 | - |
| Measured Temp Rise | measured-temp-rise | calculated | Auto | °F | 1 | Supply − Return |
| Elevation | elevation | number | No | ft | 0 | For altitude correction |
| Calculated CFM | calc-cfm-temprise | calculated | Auto | CFM | 0 | See formula below |

### Flowhood Method

| Field | ID | Type | Required | Unit | Notes |
|-------|-----|------|----------|------|-------|
| Flowhood Test Method | flowhood-method | select | No | - | Uncorrected, Corrected |
| Total Return CFM | total-return-cfm | number | Either* | CFM | - |
| Total Supply CFM | total-supply-cfm | number | Either* | CFM | - |
| Return Temperature at Plenum | return-temp-hood | number | No | °F | - |
| Supply Dry Bulb Temp at Plenum | supply-temp-hood | number | No | °F | - |
| Supply Wet Bulb Temp at Plenum | supply-wb-hood | number | No | °F | - |

*Either Return CFM OR Supply CFM must be filled to complete section

## Section: Refrigerant Data (Data 3)

### Basic Refrigerant Info

| Field | ID | Type | Required | Options |
|-------|-----|------|----------|---------|
| Refrigerant Type | refrigerant-type | select | Yes | R-22, R-410A, R-32, R-454B, Other |
| Other Type | refrigerant-other | text | Conditional | If "Other" selected |

### Liquid Line (High Side)

| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Gauge Pressure | liquid-pressure | number | Yes | psig | 1 |
| Physical Temperature | liquid-temp | number | Yes | °F | 1 |
| Saturation Temp | liquid-sat-temp | calculated | Auto | °F | 1 |
| Subcooling | subcooling | calculated | Auto | °F | 1 |

### Vapor Line (Low Side)

| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Gauge Pressure | vapor-pressure | number | Yes | psig | 1 |
| Physical Temperature | vapor-temp | number | Yes | °F | 1 |
| Saturation Temp | vapor-sat-temp | calculated | Auto | °F | 1 |
| Superheat | superheat | calculated | Auto | °F | 1 |

### Detailed Line Temperatures (All Optional)

#### Compressor
| Field | ID | Type | Unit | Decimals |
|-------|-----|------|------|----------|
| Suction Line Temp | comp-suction-temp | number | °F | 1 |
| Discharge Line Temp | comp-discharge-temp | number | °F | 1 |

#### Outdoor Unit Coil
| Field | ID | Type | Unit | Decimals |
|-------|-----|------|------|----------|
| Inlet Line Temp | outdoor-inlet-temp | number | °F | 1 |
| Discharge Line Temp | outdoor-discharge-temp | number | °F | 1 |

#### Filter Drier
| Field | ID | Type | Unit | Decimals |
|-------|-----|------|------|----------|
| Inlet Line Temp | drier-inlet-temp | number | °F | 1 |
| Discharge Line Temp | drier-discharge-temp | number | °F | 1 |

#### Indoor Unit Coil
| Field | ID | Type | Unit | Decimals |
|-------|-----|------|------|----------|
| Inlet Line Temp | indoor-inlet-temp | number | °F | 1 |
| Discharge Line Temp | indoor-discharge-temp | number | °F | 1 |

## Section: Line Set Details (Data 4)

| Field | ID | Type | Required | Unit | Notes |
|-------|-----|------|----------|------|-------|
| Total Length | lineset-length | number | Yes | ft | - |
| Vapor Line Size | vapor-size | text | Yes | in | - |
| Liquid Line Size | liquid-size | text | Yes | in | - |
| Outdoor Unit Position | outdoor-position | select | Yes | - | Same Level, Above, Below |
| Vertical Separation | vertical-separation | number | Conditional | ft | Required if Above/Below |

## Section: Electrical Data (Data 5)

### Base Fields

| Field | ID | Type | Required | Unit |
|-------|-----|------|----------|------|
| Control Voltage | control-voltage | number | Yes | V |
| Voltage/Phase | voltage-phase | select | Yes | - |

**Voltage/Phase Options**: 115V 1-Phase, 208-230V 1-Phase, 208-230V 3-Phase, 460V 3-Phase

### 115V Single Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Supply Voltage | supply-voltage-115 | Yes | V | 1 |
| Compressor Start Amps | comp-start-amps | Yes | A | 1 |
| Compressor Run Amps | comp-run-amps | Yes | A | 1 |
| Compressor Common Amps | comp-common-amps | Yes | A | 1 |
| Condenser Fan Amps | fan-amps-115 | No | A | 1 |

### 208-230V Single Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Supply Voltage | supply-voltage-230-1ph | Yes | V | 1 |
| Compressor Start Amps | comp-start-amps | Yes | A | 1 |
| Compressor Run Amps | comp-run-amps | Yes | A | 1 |
| Compressor Common Amps | comp-common-amps | Yes | A | 1 |
| Condenser Fan Amps | fan-amps-230-1ph | No | A | 1 |

### 208-230V Three Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Voltage L1-L2 | voltage-l1l2-230-3ph | Yes | V | 1 |
| Voltage L2-L3 | voltage-l2l3-230-3ph | Yes | V | 1 |
| Voltage L3-L1 | voltage-l3l1-230-3ph | Yes | V | 1 |
| Compressor Amps L1 | comp-l1-amps | Yes | A | 1 |
| Compressor Amps L2 | comp-l2-amps | Yes | A | 1 |
| Compressor Amps L3 | comp-l3-amps | Yes | A | 1 |
| Condenser Fan Amps | fan-amps-230-3ph | No | A | 1 |

### 460V Three Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Voltage L1-L2 | voltage-l1l2-460-3ph | Yes | V | 1 |
| Voltage L2-L3 | voltage-l2l3-460-3ph | Yes | V | 1 |
| Voltage L3-L1 | voltage-l3l1-460-3ph | Yes | V | 1 |
| Compressor Amps L1 | comp-l1-amps | Yes | A | 1 |
| Compressor Amps L2 | comp-l2-amps | Yes | A | 1 |
| Compressor Amps L3 | comp-l3-amps | Yes | A | 1 |
| Condenser Fan Amps | fan-amps-460-3ph | No | A | 1 |

## Section: Problem & Actions (Data 6)

| Field | ID | Type | Required | Notes |
|-------|-----|------|----------|-------|
| Problem Summary | problem-summary | textarea | Yes | Description of issue |
| Current Alarm/Fault Codes | current-fault-codes | text | No | Current codes displayed |
| Alarm/Fault Code History | fault-code-history | text | No | Historical codes |
| Corrective Actions | corrective-actions | textarea | No | Actions taken |

## Section: Additional Info (Data 7)

Validation: At least one checkbox must be selected.

### System Type

| Field | ID | Type | Required | Notes |
|-------|-----|------|----------|-------|
| Communicating System | communicating-system | checkbox | No | Shows software version fields when checked |
| Outdoor Unit Software Version | software-outdoor | text | No | Only shown if Communicating System checked |
| Indoor Unit Software Version | software-indoor | text | No | Only shown if Communicating System checked |
| Thermostat Software Version | software-thermostat | text | No | Only shown if Communicating System checked |
| Zone Control System | zone-control-system | checkbox | No | Shows note about all zones open |

### Accessories (Alphabetical)

| Field | ID | Type | Notes |
|-------|-----|------|-------|
| Air Filter | acc-filter | checkbox | Shows filter type field when checked |
| Filter Type | acc-filter-type | text | Only shown if Air Filter checked |
| Compressor Sound Blanket | acc-sound-blanket | checkbox | - |
| Compressor Time Delay | acc-time-delay | checkbox | - |
| Crankcase Heater | acc-crankcase-heater | checkbox | - |
| Energy Management (TVA etc.) | acc-energy-mgmt | checkbox | - |
| Filter Drier | acc-filter-drier | checkbox | - |
| Hard Start Kit | acc-hard-start | checkbox | - |
| Hot Gas Bypass | acc-hot-gas-bypass | checkbox | - |
| Hot Water Recovery | acc-hot-water-recovery | checkbox | - |
| Low Ambient Kit | acc-low-ambient | checkbox | - |
| Pump Down Kit | acc-pump-down | checkbox | - |
| Surge Protector | acc-surge | checkbox | - |
| Thermostat | acc-thermostat | checkbox | Shows brand/model field when checked |
| Thermostat Brand/Model | acc-thermostat-model | text | Only shown if Thermostat checked |
| Other Accessories | acc-other-check | checkbox | - |
| Other Details | acc-other | textarea | Always visible, for listing other items |

## Section: Photos (Data 8)

| Field | ID | Type | Required | Notes |
|-------|-----|------|----------|-------|
| Outdoor Unit Nameplate | photo-outdoor | file | Yes* | Image file, camera capture enabled |
| Bypass Photo Requirement | outdoor-photo-na | checkbox | No | If checked, photo not required |
| Indoor Unit Nameplate | photo-indoor | file | No | Image file |
| Additional Photos | photo-additional | file | No | Multiple files accepted |

*Required unless bypass checkbox is checked

## Calculations

### Subcooling
```
Subcooling = Saturation Temp (from liquid pressure) - Liquid Line Physical Temp
```

### Superheat
```
Superheat = Vapor Line Physical Temp - Saturation Temp (from vapor pressure)
```

### Total External Static Pressure
```
Total ESP = Supply Static - Return Static
Example: 0.45 - (-0.20) = 0.65
```

### Measured Temperature Rise
```
Measured Temp Rise = Supply Temp - Return Temp
```

### CFM (Temperature Rise Method)
```
CFM = (Volts × Amps × 3.412) / (Altitude Factor × ΔT)

Where:
- Volts = Measured Heating Element Voltage
- Amps = Measured Heating Element Amperage
- ΔT = Temperature Rise (Supply - Return)
- Altitude Factor = Based on elevation (see table below)
```

### Altitude Factor Table

| Elevation (ft) | Factor |
|----------------|--------|
| 0 (Sea Level) | 1.00 |
| 1,000 | 1.04 |
| 2,000 | 1.08 |
| 3,000 | 1.12 |
| 4,000 | 1.16 |
| 5,000 | 1.20 |
| 6,000 | 1.25 |
| 7,000 | 1.30 |
| 8,000 | 1.35 |
| 9,000 | 1.40 |
| 10,000 | 1.45 |

*Values between intervals are linearly interpolated*

## Data Storage Format

Form data is stored in localStorage as JSON:

```json
{
  "startJSIS": {
    "jsis-type": "ac",
    "outdoor-model": "value",
    "outdoor-serial": "value",
    ...
  },
  "data1": { ... },
  "data2": { ... },
  "data3": { ... },
  "data4": { ... },
  "data5": { ... },
  "data6": { ... },
  "data7": { ... },
  "data8": { ... }
}
```

## Validation Rules

1. **Start JSIS** must be complete before other sections become active
2. **JSIS Type** determines which equipment fields are required:
   - AC/Heat Pump: All equipment fields required
   - Gas Furnace: Only type selection required (placeholder)
3. **Required fields** must have non-empty values
4. **Conditional fields** (like "Other" refrigerant, vertical separation) become required when parent option is selected
5. **Dynamic sections** (Electrical, Airflow) have different required fields based on dropdown selection
6. **Flowhood method** requires either Return CFM OR Supply CFM (not both)
7. **Additional Info** requires at least one checkbox checked
8. **Photos** - Outdoor nameplate required unless bypass checkbox is checked
9. **Detailed line temps** are all optional

## Button State Logic

- **Grey (Empty/Disabled)**: No data entered or section not yet available
- **Orange (Partial)**: Some but not all required fields filled
- **Green (Complete)**: All required fields filled
- **Submit**: Only enabled when all required sections are green
