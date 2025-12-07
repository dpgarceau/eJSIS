# JSIS Field Specifications

Detailed specifications for all data fields in the JSIS form.

## Section: Equipment Identification (Start JSIS)

| Field | ID | Type | Required | Validation | Notes |
|-------|-----|------|----------|------------|-------|
| Outdoor Unit Model # | outdoor-model | text | Yes | - | - |
| Outdoor Unit Serial # | outdoor-serial | text | Yes | - | - |
| Indoor Unit Model # | indoor-model | text | Yes | - | Air Handler or Furnace |
| Indoor Unit Serial # | indoor-serial | text | Yes | - | - |
| Airflow Direction | airflow-direction | select | Yes | Upflow, Downflow, Horizontal Left, Horizontal Right | - |
| Coil Model # | coil-model | text | No | - | If separate from air handler |
| Coil Serial # | coil-serial | text | No | - | - |

## Section: Contractor & Homeowner Info

### Contractor Information

| Field | ID | Type | Required | Validation | Format |
|-------|-----|------|----------|------------|--------|
| Technician Name | tech-name | text | Yes | - | - |
| Email | tech-email | email | Yes | Valid email | - |
| Mobile Number | tech-mobile | tel | Yes | - | - |
| Company Name | company-name | text | Yes | - | - |
| Street Address | company-street | text | Yes | - | - |
| City | company-city | text | Yes | - | - |
| State | company-state | text | Yes | 2 characters | Uppercase abbreviation |
| Zip Code | company-zip | text | Yes | 5 or 10 chars | 12345 or 12345-6789 |
| Company Phone | company-phone | tel | Yes | - | - |
| Submit To | submit-to | text | No | - | Email or name |
| Service Date | service-date | date | Yes | - | - |
| Install Date | install-date | date | No | - | - |

### Homeowner Information

| Field | ID | Type | Required | Validation | Format |
|-------|-----|------|----------|------------|--------|
| Homeowner Name | homeowner-name | text | Yes | - | - |
| Street Address | homeowner-street | text | Yes | - | - |
| City | homeowner-city | text | Yes | - | - |
| State | homeowner-state | text | Yes | 2 characters | Uppercase abbreviation |
| Zip Code | homeowner-zip | text | Yes | 5 or 10 chars | 12345 or 12345-6789 |

## Section: Air Temperatures & Airflow Data

### Air Temperatures

| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Outdoor Air Temperature | outdoor-temp | number | Yes | °F | 1 |
| Indoor Dry Bulb | indoor-db | number | Yes | °F | 1 |
| Indoor Wet Bulb | indoor-wb | number | No | °F | 1 |

### Airflow Data

**Test Method**: Dropdown selection determines required fields

#### Static Pressure Method
| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Supply Static Pressure | supply-static | number | Yes | in. w.c. | 2 |
| Return Static Pressure | return-static | number | Yes | in. w.c. | 2 |
| Total External Static | total-static | calculated | No | in. w.c. | 2 |
| Supply Temperature | supply-temp-static | number | Yes | °F | 1 |
| Return Temperature | return-temp-static | number | Yes | °F | 1 |
| Calculated CFM | calc-cfm-static | calculated | No | CFM | 0 |

#### Temperature Rise Method
| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Heating Element Voltage | heating-voltage | number | No | V | 1 |
| Heating Element Amperage | heating-amperage | number | No | A | 1 |
| Supply Temperature | supply-temp-rise | number | Yes | °F | 1 |
| Return Temperature | return-temp-rise | number | Yes | °F | 1 |
| Measured Temp Rise | measured-temp-rise | calculated | No | °F | 1 |
| Elevation | elevation | number | No | ft | 0 |
| Calculated CFM | calc-cfm-temprise | calculated | No | CFM | 0 |

#### Flowhood Method
| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Flowhood Test Method | flowhood-method | text | No | - | - |
| Total Supply CFM | total-supply-cfm | number | Yes | CFM | 0 |
| Total Return CFM | total-return-cfm | number | No | CFM | 0 |
| Supply Temperature | supply-temp-hood | number | No | °F | 1 |
| Return Temperature | return-temp-hood | number | No | °F | 1 |

## Section: Refrigerant Data

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
| Saturation Temp | liquid-sat-temp | calculated | No | °F | 1 |
| Subcooling | subcooling | calculated | No | °F | 1 |

### Vapor Line (Low Side)

| Field | ID | Type | Required | Unit | Decimals |
|-------|-----|------|----------|------|----------|
| Gauge Pressure | vapor-pressure | number | Yes | psig | 1 |
| Physical Temperature | vapor-temp | number | Yes | °F | 1 |
| Saturation Temp | vapor-sat-temp | calculated | No | °F | 1 |
| Superheat | superheat | calculated | No | °F | 1 |

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

## Section: Line Set Details

| Field | ID | Type | Required | Unit |
|-------|-----|------|----------|------|
| Total Length | lineset-length | number | Yes | ft |
| Vapor Line Size | vapor-size | text | Yes | in |
| Liquid Line Size | liquid-size | text | Yes | in |
| Vertical Separation | vertical-separation | number | No | ft |

## Section: Electrical Data

**Voltage/Phase**: Dropdown selection determines required fields

### 115V Single Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Supply Voltage L1-L2 | supply-voltage-115 | Yes | V | 1 |
| Neutral-L1 | neutral-l1-115 | No | V | 1 |
| Neutral-L2 | neutral-l2-115 | No | V | 1 |
| Compressor Amps | comp-amps-115 | Yes | A | 1 |
| Condenser Fan Amps | fan-amps-115 | No | A | 1 |

### 208-230V Single Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Supply Voltage L1-L2 | supply-voltage-230-1ph | Yes | V | 1 |
| Neutral-L1 | neutral-l1-230-1ph | No | V | 1 |
| Neutral-L2 | neutral-l2-230-1ph | No | V | 1 |
| Compressor Amps - Common | comp-common-230-1ph | Yes | A | 1 |
| Compressor Amps - Run | comp-run-230-1ph | Yes | A | 1 |
| Condenser Fan Amps | fan-amps-230-1ph | No | A | 1 |

### 208-230V Three Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Voltage L1-L2 | voltage-l1l2-230-3ph | Yes | V | 1 |
| Voltage L2-L3 | voltage-l2l3-230-3ph | Yes | V | 1 |
| Voltage L3-L1 | voltage-l3l1-230-3ph | Yes | V | 1 |
| Neutral-L1 | neutral-l1-230-3ph | No | V | 1 |
| Neutral-L2 | neutral-l2-230-3ph | No | V | 1 |
| Neutral-L3 | neutral-l3-230-3ph | No | V | 1 |
| Amps L1 | amps-l1-230-3ph | Yes | A | 1 |
| Amps L2 | amps-l2-230-3ph | Yes | A | 1 |
| Amps L3 | amps-l3-230-3ph | Yes | A | 1 |

### 460V Three Phase
| Field | ID | Required | Unit | Decimals |
|-------|-----|----------|------|----------|
| Voltage L1-L2 | voltage-l1l2-460-3ph | Yes | V | 1 |
| Voltage L2-L3 | voltage-l2l3-460-3ph | Yes | V | 1 |
| Voltage L3-L1 | voltage-l3l1-460-3ph | Yes | V | 1 |
| Neutral-L1 | neutral-l1-460-3ph | No | V | 1 |
| Neutral-L2 | neutral-l2-460-3ph | No | V | 1 |
| Neutral-L3 | neutral-l3-460-3ph | No | V | 1 |
| Amps L1 | amps-l1-460-3ph | Yes | A | 1 |
| Amps L2 | amps-l2-460-3ph | Yes | A | 1 |
| Amps L3 | amps-l3-460-3ph | Yes | A | 1 |

## Section: Problem & Actions

| Field | ID | Type | Required | Notes |
|-------|-----|------|----------|-------|
| Problem Summary | problem-summary | textarea | Yes | Description of issue |
| Corrective Actions | corrective-actions | textarea | No | Actions taken |

## Section: Accessories

| Field | ID | Type | Required | Notes |
|-------|-----|------|----------|-------|
| Thermostat | acc-thermostat | checkbox | * | At least one required |
| Air Filter | acc-filter | checkbox | * | - |
| Surge Protector | acc-surge | checkbox | * | - |
| Equipment Pad | acc-pad | checkbox | * | - |
| Disconnect Box | acc-disconnect | checkbox | * | - |
| Other | acc-other | textarea | No | Additional accessories |

\* At least one checkbox must be selected

## Section: Photos

| Field | ID | Type | Required | Notes |
|-------|-----|------|----------|-------|
| Outdoor Unit Nameplate | photo-outdoor | file | No | Image file |
| Indoor Unit Nameplate | photo-indoor | file | No | Image file |
| Additional Photos | photo-additional | file | No | Multiple files accepted |

## Calculations (To Be Implemented)

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
Total ESP = Supply Static + |Return Static|
```

### CFM (Static Pressure Method)
Uses manufacturer's air handler performance data tables based on ESP

### CFM (Temperature Rise Method)
```
CFM = (Watts × 3.41) / (Temp Rise × 1.08)
Where Watts = Voltage × Amperage
```

### Measured Temperature Rise
```
Measured Temp Rise = Supply Temp - Return Temp
```

## Data Storage Format

Form data is stored in localStorage as JSON:

```json
{
  "startJSIS": {
    "outdoor-model": "value",
    "outdoor-serial": "value",
    ...
  },
  "data1": { ... },
  "data2": { ... },
  ...
}
```

## Validation Rules

1. **Start JSIS** must be complete before other sections become active
2. **Required fields** must have non-empty values
3. **Conditional fields** (like "Other" refrigerant) become required when parent option is selected
4. **Dynamic sections** (Electrical, Airflow) have different required fields based on selection
5. **Accessories** require at least one checkbox checked
6. **Photos** are optional and don't block submission
7. **Detailed line temps** are all optional

## Button State Logic

- **Grey (Empty)**: No data entered or section disabled
- **Orange (Partial)**: Some but not all required fields filled
- **Green (Complete)**: All required fields filled
- **Submit**: Only enabled when all required sections are green
