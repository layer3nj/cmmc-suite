# Sample Data

This directory contains sample data exports that can be imported for demonstration purposes.

## 📁 Contents

### 1. `demo-customer.json`
**Sample customer data with assessment structure**
- Contains: Acme Corporation demo customer
- Includes: Sample assessment with findings
- Use for: Testing customer creation and assessment workflow
- Format: JSON

### 2. `demo-poam.csv`
**Sample POA&M items with realistic remediation plans**
- Contains: 8 POA&M items across CMMC/NIST controls
- Includes: Milestones, responsible parties, timelines
- Use for: Testing POA&M import and milestone tracking
- Format: CSV

### 3. `blank-checklist.csv`
**Empty assessment template for CMMC/NIST/STIG**
- Contains: Blank assessment form
- Includes: All required fields for evidence documentation
- Use for: Conducting assessments offline then importing
- Format: CSV

## 📥 How to Import

### Via Admin Panel (Coming Soon)
1. Log into the CMMC Compliance Suite
2. Navigate to **Admin** → **Import**
3. Select the file type and upload
4. Follow the import wizard

### Manual Import (Current Method)
Use the database import feature or copy-paste the data into forms.

## 📤 Creating Your Own Exports

To export data for backup or sharing:

1. Navigate to **Reports** → **Export Data**
2. Select the data types to export
3. Choose format (JSON, CSV)
4. Download the file

### Export Formats

**JSON Exports:**
- Full customer data with relationships
- Preserves all metadata
- Best for backups and migrations

**CSV Exports:**
- POA&M items for Excel
- Assessment findings for spreadsheets
- SPRS score breakdowns
- Control checklists

## 🎯 Demo Data Use Cases

### Training
- Import demo data to practice using the system
- Test assessment workflows
- Learn POA&M management

### Testing
- Verify installation is working correctly
- Test report generation
- Validate SPRS calculations

### Presentations
- Demonstrate features to stakeholders
- Show compliance dashboards with data
- Create demo assessments for proposals

## ⚠️ Important Notes

- Sample data is for **demonstration only**
- Do not use in production assessments
- Always review imported data before using
- Custom fields may not import correctly
- Backup before importing large datasets

## 📊 Data Structure Examples

### Customer JSON Structure
```json
{
  "name": "Company Name",
  "contact_email": "email@company.com",
  "active": 1
}
```

### POA&M CSV Structure
```csv
Control Code,Title,Weakness,Corrective Action,Status
3.5.3,MFA Required,Not implemented,Deploy MFA,open
```

### Assessment Findings Structure
```csv
Control Code,Status,Evidence,Severity
3.1.1,met,AD logs reviewed,low
```

## 🔐 Security Note

Sample data may contain:
- Fictitious company names
- Generic evidence descriptions
- Example security configurations
- Placeholder contact information

**Never use real security data in sample files!**
