# eJSIS Data Entry Form

Mobile-optimized HVAC service data collection form for field technicians.

## Overview

This is a comprehensive electronic job site inspection sheet (eJSIS) web form designed for HVAC technicians to collect detailed system diagnostic data on mobile devices. The form features auto-save functionality, conditional field display based on system configuration, and extensive diagnostic data capture capabilities.

## Current Status

**Version**: 2.0
**Current Sections**: 8 (consolidated)
**Auto-save**: Implemented (blur events)
**Mobile Optimized**: Yes
**Main File**: `ejsis.html`

## Features

- **Auto-save**: Data persists automatically in browser localStorage on field blur
- **Progressive validation**: Buttons change color based on completion (grey → orange → green)
- **Click-outside-to-close**: Tap outside any modal to save and close
- **Conditional fields**: Dynamic field display based on:
  - Refrigerant type selection
  - Electrical voltage/phase configuration
  - Airflow test method
  - Outdoor unit position (vertical separation)
- **Barcode scanning**: Scan serial numbers using device camera (Chrome Android, Safari iOS)
- **Geolocation**: Auto-fill homeowner address from GPS location
- **Persistent technician data**: Technician/company info survives form clears
- **PT Chart calculations**: Auto-calculate superheat/subcooling for R-22, R-410A, R-32, R-454B
- **Offline capable**: Works without internet, stores data locally
- **Mobile-first design**: Touch-friendly interface optimized for phone screens
- **No spinner arrows**: Number inputs hide up/down controls for better mobile UX
- **Decimal limiting**: Automatic formatting to 1 decimal place for measurements

## Data Sections (8 Total)

### 1. Equipment Identification (Start eJSIS)
- Outdoor unit model and serial (with barcode scan)
- Indoor unit model and serial (with barcode scan)
- Coil model and serial (optional, with barcode scan)
- Date of Service (defaults to today)
- Install Date (optional)

### 2. Contact Information
- **Servicing Contractor** (persistent across submissions):
  - Technician name, email, mobile
  - Company name, address, phone
- **Homeowner Information**:
  - Geolocation button for auto-fill address
  - Name and structured address

### 3. Air & Airflow Data
- Air Handler/Furnace airflow direction
- **Air Temperatures**:
  - Outdoor air temp
  - Indoor dry bulb / wet bulb
- **Airflow Measurement** (dynamic by test method):
  - Static Pressure: supply/return static, temps, calculated CFM
  - Temperature Rise: voltage, amperage, temps, elevation, calculated CFM
  - Flowhood: total supply/return CFM, temps

### 4. Refrigerant Data
- Refrigerant type (R-22, R-410A, R-32, R-454B, Other)
- **Liquid Line (High Side)**:
  - Gauge pressure, physical temp
  - Calculated: Saturation temp, Subcooling
- **Vapor Line (Low Side)**:
  - Gauge pressure, physical temp
  - Calculated: Saturation temp, Superheat
- **Line Temperatures** (optional):
  - Compressor suction/discharge
  - Outdoor coil inlet/discharge
  - Filter drier inlet/discharge
  - Indoor coil inlet/discharge

### 5. Line Set Details
- Total length, vapor line size, liquid line size
- Outdoor unit position (above/below/same level as indoor)
- Vertical separation (required if above/below)

### 6. Electrical Data
- **Control Voltage**: Control voltage reading
- **Line Voltage** (dynamic by voltage/phase):
  - 115V Single Phase: Supply voltage, neutral readings
  - 208-230V Single Phase: Supply voltage, neutral readings
  - 208-230V Three Phase: L1-L2-L3 voltages, neutral readings
  - 460V Three Phase: L1-L2-L3 voltages, neutral readings
- **Compressor** (dynamic by phase type):
  - Single Phase: Start (X), Run (Y), Common (Z) amps
  - Three Phase: L1, L2, L3 amps
- **Condenser Fan**: Universal fan amps field

### 7. Problem & Actions
- Problem summary (required)
- Corrective actions (optional)

### 8. Accessories
- Checkbox list: Thermostat, Air Filter, Surge Protector, Equipment Pad, Disconnect Box
- Other accessories (text field)

### Photos (Optional)
- Outdoor/Indoor unit nameplates
- Additional photos (multiple)

## Field Validation Logic

- **Start eJSIS must be complete** before other sections become active
- **Dynamic validation** based on:
  - Selected refrigerant type (Other enables manual entry for calculated fields)
  - Selected voltage/phase (different required fields)
  - Selected airflow test method (different required fields)
  - Outdoor unit position (vertical separation required if not same level)
- **Accessories**: At least one checkbox must be selected
- **Photos**: Optional, don't block submission

## Technical Details

### Storage
- **Form Data**: `localStorage` key `jsisFormData`
- **Technician Data**: `localStorage` key `jsisTechnicianData` (persistent)
- **Format**: JSON objects

### PT Chart Calculations
Supports automatic superheat/subcooling calculation for:
- R-22
- R-410A
- R-32
- R-454B

Uses linear interpolation between data points. "Other" refrigerant type enables manual entry.

### Browser Support
- Modern browsers with localStorage support
- **Barcode Scanning**: Chrome Android, Safari iOS 16.4+, Edge Android
- **Geolocation**: All modern mobile browsers (requires HTTPS or localhost)
- File upload with camera access on mobile

### Decimal Handling
- Temperatures, pressures, voltages: 1 decimal place
- Static pressures: 2 decimal places
- Auto-formats on blur

## Setup Instructions

### For Local Development

1. Clone the repository:
```bash
git clone https://github.com/yourusername/ejsis.git
cd ejsis
```

2. Open `ejsis.html` in a browser or use a local server:
```bash
# Python 3
python -m http.server 8000

# Node.js
npx http-server
```

3. Access at `http://localhost:8000/ejsis.html`

### For GitHub Pages

1. Push to GitHub
2. Go to Settings → Pages
3. Select branch and root folder
4. Access at `https://yourusername.github.io/ejsis/ejsis.html`

### For Production Deployment

The form is a single HTML file with embedded CSS and JavaScript:
- Upload `ejsis.html` to web server
- No build process required
- No dependencies
- HTTPS required for geolocation and camera features

## Next Steps

- [x] Complete section consolidation (11 → 8)
- [x] Implement subcooling/superheat calculations
- [ ] Implement CFM calculations
- [ ] Add backend integration for form submission
- [ ] Add email functionality
- [ ] Add PDF export
- [ ] Add data export to CSV
- [ ] Multi-language support

## File Structure

```
ejsis/
├── ejsis.html          # Main form (self-contained)
├── README.md           # This file
├── .gitignore          # Git ignore rules
└── docs_private/       # Private documentation (gitignored)
```

## Contributing

This is an internal project. For questions or issues, contact David.

## License

Internal use only.
