# eJSIS Deployment Guide

## Overview

Deployment details for the eJSIS (Electronic Job Site Information Sheet) application.

---

## Current Deployment

**Version**: 2.2
**Status**: Live Testing - PDF/Email pending setup
**URL**: https://ejsis.isstrckr.com
**Repository**: https://github.com/dpgarceau/eJSIS

### Architecture
- Single HTML file with embedded CSS and JavaScript
- PHP backend for form submission, photo uploads, PDF generation, and email
- MySQL database for data storage
- Protected photo storage with .htaccess
- TCPDF for PDF generation
- PHPMailer for email delivery

---

## Server Structure

```
ejsis.isstrckr.com/
├── ejsis.html                      # Main form
├── assets/
│   └── ejsis_logo.png              # Logo
├── api/
│   ├── jsis_dbconfig.php           # Database credentials (not in repo)
│   ├── jsis_dbconfig.example.php   # DB config template
│   ├── jsis_emailconfig.php        # Email credentials (not in repo)
│   ├── jsis_emailconfig.example.php # Email config template
│   ├── submit.php                  # JSON form submission + PDF/email trigger
│   ├── upload_photo.php            # Photo upload handler
│   ├── generate_pdf.php            # PDF generator (TCPDF)
│   ├── send_email.php              # Email sender (PHPMailer)
│   ├── composer.json               # PHP dependencies
│   ├── vendor/                     # Composer packages (not in repo)
│   ├── uploads/
│   │   └── .htaccess               # Denies direct access
│   └── temp/                       # Temporary PDFs (deleted after send)
├── database-schema.md
├── field-specs.md
└── README.md
```

---

## Database

### Connection Details
| Item | Value |
|------|-------|
| Database | ejsis_data |
| User | ejsis_dbacess |
| Table | jsis_records |
| Columns | 120 |

### Schema
Full SQL schema is in `database-schema.md`. Key sections:
- Equipment Identification (9 columns)
- Contact Information (14 columns)
- Air & Airflow Data (24 columns)
- Refrigerant Data (17 columns)
- Line Set Details (5 columns)
- Electrical Data (17 columns)
- Problem & Actions (4 columns)
- Additional Info (21 columns)
- Photos (4 columns)
- Metadata (5 columns)

---

## API Endpoints

### POST /api/submit.php
Submit completed form data as JSON.

**Request:**
```json
{
  "startJSIS": { "jsis-type": "ac", "outdoor-model": "...", ... },
  "data1": { "tech-name": "...", ... },
  "data2": { ... },
  "data3": { ... },
  "data4": { ... },
  "data5": { ... },
  "data6": { ... },
  "data7": { ... },
  "data8": { "photo-outdoor": "filename.jpg", ... }
}
```

**Response:**
```json
{
  "success": true,
  "record_id": 123,
  "message": "JSIS record saved successfully"
}
```

### POST /api/upload_photo.php
Upload photo file (multipart/form-data).

**Form Data:**
- `photo` - The image file
- `record_id` - Unique record identifier
- `photo_type` - "outdoor", "indoor", or "additional"

**Response:**
```json
{
  "success": true,
  "filename": "jsis_1234567890_outdoor.jpg",
  "photo_type": "outdoor",
  "size": 245678
}
```

---

## Deployment Checklist

### Initial Setup (COMPLETED)
- [x] Upload files via git pull
- [x] Create jsis_dbconfig.php with credentials
- [x] Import database schema to phpMyAdmin
- [x] Configure HTTPS
- [x] Test form loads correctly
- [x] Test logo displays
- [x] Test form submission to database

### PDF/Email Setup (NEXT STEPS)
- [ ] Pull latest changes: `git pull origin main`
- [ ] Install Composer dependencies: `cd api && composer install`
- [ ] Create email config: `cp jsis_emailconfig.example.php jsis_emailconfig.php`
- [ ] Edit jsis_emailconfig.php with email settings
- [ ] Set temp folder permissions: `chmod 755 temp`
- [ ] Test form submission with PDF email

### Functionality Testing
- [x] Test form submission flow
- [ ] Test photo upload with PDF
- [ ] Test barcode scanning
- [ ] Test geolocation
- [x] Verify data appears in database
- [ ] Verify PDF is generated correctly
- [ ] Verify email is received (tech + support CC)
- [ ] Test on mobile device (iOS)
- [ ] Test on mobile device (Android)

---

## Updating the Server

```bash
cd /path/to/ejsis
git pull origin main
```

The `jsis_dbconfig.php` file is git-ignored, so credentials won't be overwritten.

---

## Photo Storage

Photos are stored in `api/uploads/` with naming convention:
```
{record_id}_{type}.{extension}

Examples:
jsis_1701234567890_outdoor.jpg
jsis_1701234567890_indoor.png
jsis_1701234567890_additional_1701234567891.jpg
```

The `.htaccess` file blocks all direct web access. Photos are accessed server-side only (for PDF generation).

---

## Security Notes

1. **Database credentials**: Stored in `jsis_dbconfig.php` (git-ignored)
2. **Photo uploads**: Protected by .htaccess, validated MIME types, 10MB limit
3. **Input sanitization**: All database inputs use PDO prepared statements
4. **File validation**: Only JPG, PNG, GIF, WebP accepted

---

## Future Enhancements

- [x] PDF generation from submitted records (implemented, pending testing)
- [x] Email notifications on submission (implemented, pending testing)
- [ ] Admin dashboard for viewing submissions
- [ ] Export to CSV
- [ ] User authentication
- [ ] Regenerate PDF for existing records

---

## Contacts

| Role | Name | Contact |
|------|------|---------|
| Developer | | |
| Project Owner | David | |

---

## Change Log

| Date | Version | Changes |
|------|---------|---------|
| 2024-12-07 | 2.2 | Added PDF generation (TCPDF) and email sending (PHPMailer) |
| 2024-12-07 | 2.1 | Added API backend, photo uploads, database integration |
| 2024-XX-XX | 2.0 | Major overhaul - 8 sections, PT charts, barcode scanning, geolocation |
