# CMMC Compliance Suite - Deployment Checklist

## ✅ Pre-Deployment Verification

### Required Files Present
- [x] public/index.php (front controller)
- [x] public/.htaccess (Apache config)
- [x] public/assets/css/app.css
- [x] public/assets/js/app.js
- [x] app/Core/* (10 core classes)
- [x] app/Controllers/* (13 controllers)
- [x] app/Middleware/AuthMiddleware.php
- [x] app/Services/* (2 services)
- [x] app/Views/install/index.php
- [x] app/Views/auth/login.php
- [x] app/Views/dashboard/index.php
- [x] app/Views/layout/app.php
- [x] database/migrations/* (11 migrations)
- [x] database/seeds/* (4 seed files)
- [x] docs/README_DEPLOY.html
- [x] docs/nginx.conf.sample

### Required Directories
- [x] config/ (must be writable - 770)
- [x] storage/ (must be writable - 770)
- [x] storage/uploads/
- [x] storage/logs/
- [x] storage/cache/
- [x] storage/sessions/
- [x] vendor/ (for future SAML lib)

## 📋 Deployment Steps

### 1. Download & Extract
```bash
cd /var/www/html
unzip cmmc-suite.zip
cd cmmc-suite
```

### 2. Set Permissions
```bash
# Set ownership (replace www-data with your web server user)
sudo chown -R www-data:www-data .

# Make config and storage writable
sudo chmod 770 config/
sudo chmod -R 770 storage/
```

### 3. Create Database
**MySQL:**
```sql
CREATE DATABASE cmmc_compliance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cmmc_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON cmmc_compliance.* TO 'cmmc_user'@'localhost';
FLUSH PRIVILEGES;
```

**PostgreSQL:**
```sql
CREATE DATABASE cmmc_compliance;
CREATE USER cmmc_user WITH ENCRYPTED PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE cmmc_compliance TO cmmc_user;
```

### 4. Web Server Configuration
- **Apache:** Included .htaccess should work automatically
- **nginx:** Use docs/nginx.conf.sample

### 5. Run Installer
Visit: `http://your-domain.com/install`

The installer will:
1. ✅ Check PHP requirements
2. ✅ Test database connection
3. ✅ Run all migrations
4. ✅ Seed control catalogs
5. ✅ Configure SAML (optional)
6. ✅ Create admin user
7. ✅ Generate config/.env.php
8. ✅ Lock installer

### 6. Post-Installation
- [ ] Log in with admin credentials
- [ ] Create first customer
- [ ] Review dashboard
- [ ] Configure SAML (if using Microsoft Entra)
- [ ] Set up integrations (Autotask/ITGlue)
- [ ] Create first assessment

## 🔐 Security Checklist

- [ ] HTTPS enabled and enforced
- [ ] Database password is strong
- [ ] config/.env.php is not web-accessible
- [ ] storage/ is not web-accessible
- [ ] File upload directory has proper permissions
- [ ] Session directory is writable by web server only
- [ ] mod_rewrite enabled (Apache) or proper nginx config
- [ ] PHP display_errors is OFF in production
- [ ] All passwords use Argon2ID hashing
- [ ] CSRF protection enabled on all forms

## 🧪 Testing Checklist

- [ ] Installer completes without errors
- [ ] Login page loads
- [ ] Can log in with admin account
- [ ] Dashboard displays (even without customer)
- [ ] Can create a customer
- [ ] Can switch customers
- [ ] Can view controls (CMMC/NIST/STIG)
- [ ] Can create an assessment
- [ ] SPRS scoring calculates correctly
- [ ] POA&M auto-generation works
- [ ] File upload works
- [ ] Reports export to CSV
- [ ] Audit log records actions
- [ ] SAML login works (if configured)

## 📊 What Gets Seeded

The installer automatically loads:
- ✅ **50+ CMMC 2.0 controls** (ML1-ML3)
- ✅ **75+ NIST SP 800-171 practices** (all 14 families)
- ✅ **30+ DISA STIG references** (multiple baselines)
- ✅ **40+ control mappings** (CMMC↔NIST↔STIG)

## 🚨 Common Issues

### "500 Internal Server Error"
- Check web server error logs
- Verify config/ and storage/ are writable
- Ensure all PHP extensions are installed

### "Database connection failed"
- Verify database exists
- Check credentials
- Confirm database server is running
- Check firewall rules

### "CSRF token validation failed"
- Clear browser cache
- Enable cookies
- Check session storage is writable

### Installer shows errors
- Check all 13 controllers exist in app/Controllers/
- Verify all migrations in database/migrations/
- Ensure all seed files in database/seeds/

## 📚 Documentation

- **Full Guide:** `docs/README_DEPLOY.html`
- **nginx Config:** `docs/nginx.conf.sample`
- **Sample Data:** `sample-data/README.md`

## ✨ Features Available After Install

✅ Multi-tenant customer management
✅ CMMC 2.0 compliance tracking (ML1-ML3)
✅ NIST SP 800-171 (110 practices)
✅ DISA STIG references
✅ Assessment workflow
✅ SPRS score calculator
✅ POA&M management with milestones
✅ Document/evidence storage
✅ Microsoft Entra SAML SSO
✅ Autotask & ITGlue integration stubs
✅ Role-based access control
✅ Complete audit logging
✅ CSV/PDF exports
✅ Responsive dashboard

## 🎯 Next Steps After Deployment

1. **Add More Customers:** Multi-tenant from day one
2. **Run Assessments:** Track compliance posture
3. **Generate POA&M:** Auto-create from gaps
4. **Configure Integrations:** Sync customer data
5. **Set Up SAML:** Enable SSO for your team
6. **Export Reports:** Share with auditors

---

**Support:** All actions are logged to `storage/logs/` and `audit_log` table.

**Version:** 1.0.0 | **Branch:** claude/compliance-tracking-app-011CUW2K31jMn7PmibmunLe1
