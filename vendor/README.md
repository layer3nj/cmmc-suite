# Vendor Directory

This directory is reserved for bundled third-party libraries.

## Current Status

**Empty by design** - No external dependencies required for basic installation.

## Future Libraries

When implementing full SAML support, bundle here:
- **OneLogin SAML PHP Toolkit** - For production SAML authentication
- **TCPDF or Dompdf** - For PDF report generation (optional)

## Why Bundled?

This application is designed for **zero-CLI deployment**:
- ✅ No composer required on the server
- ✅ No npm or build steps
- ✅ Just unzip and install via web browser

All necessary code is included in the core `app/` directory.

## Adding Libraries

To add a vendor library:

1. Download the library
2. Extract to `vendor/library-name/`
3. Update `app/Core/Autoloader.php` if needed
4. Test thoroughly

Example structure:
```
vendor/
├── onelogin/
│   └── php-saml/
│       └── src/
└── tecnickcom/
    └── tcpdf/
        └── tcpdf.php
```
