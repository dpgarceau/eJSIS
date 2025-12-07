# eJSIS Data Entry Form

Mobile-optimized HVAC service data collection form for field technicians.

## Overview

This is a comprehensive electronic job site inspection sheet (eJSIS) web form designed for HVAC technicians to collect detailed system diagnostic data on mobile devices. The form features auto-save functionality, conditional field display based on system configuration, and extensive diagnostic data capture capabilities.

## Current Status

**Version**: 2.1
**Current Sections**: 8 (consolidated)
**Backend**: PHP/MySQL
**Main File**: `ejsis.html`

## Features

- **JSIS Type Selection**: AC System, Heat Pump System, or Gas Furnace (determines required fields)
- **Auto-save**: Data persists automatically in browser localStorage on field blur
- **Progressive validation**: Buttons change color based on completion (grey → orange → green)
- **Click-outside-to-close**: Tap outside any modal to save and close
- **Conditional fields**: Dynamic field display based on:
  - JSIS type (AC/Heat Pump/Gas Furnace)
  - Heat pump test mode (cooling/heating)
  - Refrigerant type selection
  - Electrical voltage/phase configuration
  - Airflow test method
  - Outdoor unit position (vertical separation)
- **Barcode scanning**: Scan serial numbers using device camera (Chrome Android, Safari iOS)
- **Geolocation**: Auto-fill homeowner address from GPS location
- **Persistent technician data**: Technician/company info survives form clears
- **PT Chart calculations**: Auto-calculate superheat/subcooling for R-22, R-410A, R-32, R-454B
- **CFM calculations**: Temperature rise method with altitude correction
- **Photo uploads**: Secure photo storage with .htaccess protection
- **Database submission**: JSON submission to MySQL backend
- **Mobile-first design**: Touch-friendly interface optimized for phone screens

## Data Sections (8 Total)

### 1. Equipment Identification (Start eJSIS)
- **JSIS Type selector**: AC, Heat Pump, or Gas Furnace
- Outdoor unit model and serial (with barcode scan)
- Indoor unit model and serial (with barcode scan)
- Coil model and serial (optional, with barcode scan)
- Date of Service, Install Date

### 2. Contact Information
- **Servicing Contractor** (persistent across submissions):
  - Technician name, email, mobile
  - Company name, address, phone
- **Homeowner Information**:
  - Geolocation button for auto-fill address
  - Name and structured address

### 3. Air & Airflow Data
- Air Handler/Furnace airflow direction
- **Air Temperatures**: Outdoor air, Indoor dry bulb / wet bulb
- **Airflow Measurement** (dynamic by test method):
  - Static Pressure: return/supply static, temps at plenum (dry bulb & wet bulb), manual CFM
  - Temperature Rise: voltage, amperage, temps, elevation, calculated CFM
  - Flowhood: total supply/return CFM, temps

### 4. Refrigerant Data
- Refrigerant type (R-22, R-410A, R-32, R-454B, Other)
- **Heat Pump Test Mode**: Cooling or Heating (only for heat pump systems)
- **Liquid Line (High Side)**: Pressure, temp, calculated saturation temp & subcooling
- **Vapor Line (Low Side)**: Pressure, temp, calculated saturation temp & superheat
- **Detailed Line Temps** (optional): Compressor, outdoor coil, filter drier, indoor coil

### 5. Line Set Details
- Total length, vapor line size, liquid line size
- Outdoor unit position (above/below/same level)
- Vertical separation (conditional)

### 6. Electrical Data
- Control voltage
- **Line Voltage** (dynamic by voltage/phase):
  - 115V Single Phase
  - 208-230V Single Phase
  - 208-230V Three Phase
  - 460V Three Phase
- Compressor amps, condenser fan amps

### 7. Problem & Actions
- Problem summary (required)
- Current Alarm/Fault Codes
- Alarm/Fault Code History
- Corrective actions

### 8. Additional Info & Photos
- **System Type**: Communicating system (with software versions), Zone control system
- **Accessories**: Air filter (with type), thermostat (with brand/model), surge protector, crankcase heater, hard start kit, filter drier, compressor sound blanket, low ambient kit, time delay, energy management, hot gas bypass, hot water recovery, pump down kit, other
- **Photos**: Outdoor/Indoor unit nameplates, additional photos

## File Structure

```
ejsis/
├── ejsis.html                    # Main form (self-contained HTML/CSS/JS)
├── assets/
│   └── ejsis_logo.png            # Logo image
├── api/
│   ├── jsis_dbconfig.example.php # Database config template
│   ├── jsis_dbconfig.php         # Actual config (git-ignored)
│   ├── submit.php                # Form submission endpoint
│   ├── upload_photo.php          # Photo upload endpoint
│   └── uploads/
│       └── .htaccess             # Blocks direct file access
├── README.md                     # This file
├── field-specs.md                # Complete field specifications
├── database-schema.md            # MySQL table schema
└── .gitignore                    # Git ignore rules
```

## Setup Instructions

### Server Deployment

1. Clone or pull the repository:
```bash
git clone https://github.com/dpgarceau/eJSIS.git
cd eJSIS
```

2. Configure database credentials:
```bash
cd api
cp jsis_dbconfig.example.php jsis_dbconfig.php
# Edit jsis_dbconfig.php with your MySQL credentials
```

3. Create database table:
   - Import SQL from `database-schema.md` into phpMyAdmin

4. Ensure HTTPS is configured (required for geolocation/camera)

5. Set proper permissions on uploads folder:
```bash
chmod 755 api/uploads
```

### For Local Development

1. Use a local PHP server:
```bash
php -S localhost:8000
```

2. Access at `http://localhost:8000/ejsis.html`

Note: Barcode scanning and geolocation require HTTPS in production.

## Technical Details

### Storage
- **Form Data**: `localStorage` key `jsisFormData`
- **Technician Data**: `localStorage` key `jsisTechnicianData` (persistent)
- **Database**: MySQL with 120 columns (see `database-schema.md`)
- **Photos**: Filesystem storage in `api/uploads/` (protected by .htaccess)

### API Endpoints
- `POST api/submit.php` - Submit form data as JSON
- `POST api/upload_photo.php` - Upload photos (multipart/form-data)

### Browser Support
- Modern browsers with localStorage support
- **Barcode Scanning**: Chrome Android, Safari iOS 16.4+, Edge Android
- **Geolocation**: All modern mobile browsers (requires HTTPS)
- File upload with camera access on mobile

## Documentation

- `field-specs.md` - Complete field specifications with IDs, types, validation
- `database-schema.md` - MySQL table creation SQL with all 120 columns

## Contributing

This is an internal project. For questions or issues, contact David.

## License

Internal use only.
