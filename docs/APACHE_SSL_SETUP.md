# Apache2 & SSL Setup Guide for CMMC Compliance Suite

This guide will walk you through setting up Apache2 with SSL for the CMMC Compliance Suite on Ubuntu/Debian.

## Prerequisites

- Ubuntu 20.04+ or Debian 11+ server
- Root or sudo access
- Domain name pointed to your server's IP address
- Ports 80 and 443 open in firewall

## Step 1: Install Apache2 and PHP

```bash
# Update package list
sudo apt update

# Install Apache2
sudo apt install -y apache2

# Install PHP 8.1 and required extensions
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring \
    php8.1-curl php8.1-zip php8.1-gd php8.1-intl php8.1-bcmath libapache2-mod-php8.1

# Verify PHP installation
php -v
```

## Step 2: Enable Required Apache Modules

```bash
# Enable mod_rewrite (for URL rewriting)
sudo a2enmod rewrite

# Enable mod_ssl (for HTTPS)
sudo a2enmod ssl

# Enable mod_headers (for security headers)
sudo a2enmod headers

# Restart Apache to apply changes
sudo systemctl restart apache2
```

## Step 3: Set Up Application Directory

```bash
# Create application directory (if not exists)
sudo mkdir -p /var/www/cmmc-suite

# Set proper ownership
sudo chown -R www-data:www-data /var/www/cmmc-suite

# Set proper permissions
sudo find /var/www/cmmc-suite -type d -exec chmod 755 {} \;
sudo find /var/www/cmmc-suite -type f -exec chmod 644 {} \;

# Make storage and config writable
sudo chmod -R 775 /var/www/cmmc-suite/storage
sudo chmod -R 775 /var/www/cmmc-suite/config
sudo chmod -R 775 /var/www/cmmc-suite/public/uploads
```

## Step 4: Copy Your Application Files

```bash
# If using git (recommended)
cd /var/www
sudo git clone https://github.com/your-org/cmmc-suite.git
sudo chown -R www-data:www-data cmmc-suite

# Or if copying from local development
# scp -r /path/to/local/cmmc-suite/* user@server:/var/www/cmmc-suite/
```

## Step 5: Configure Apache Virtual Host (HTTP Only - Initial Setup)

```bash
# Copy the provided Apache configuration
sudo cp /var/www/cmmc-suite/docs/apache-setup.conf /etc/apache2/sites-available/cmmc-suite.conf

# Edit the configuration file
sudo nano /etc/apache2/sites-available/cmmc-suite.conf

# IMPORTANT: Replace 'your-domain.com' with your actual domain name
# Example: compliance.yourcompany.com
```

**Edit these lines in the config file:**
```apache
ServerName your-domain.com          # Change to: compliance.yourcompany.com
ServerAlias www.your-domain.com     # Change to: www.compliance.yourcompany.com
ServerAdmin admin@your-domain.com   # Change to: admin@yourcompany.com
```

```bash
# Test Apache configuration
sudo apache2ctl configtest

# Should show "Syntax OK"

# Disable default site
sudo a2dissite 000-default.conf

# Enable your site
sudo a2ensite cmmc-suite.conf

# Reload Apache
sudo systemctl reload apache2
```

## Step 6: Install SSL Certificate with Let's Encrypt (Certbot)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-apache

# Run Certbot to obtain and install SSL certificate
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Follow the prompts:
# 1. Enter your email address
# 2. Agree to terms of service
# 3. Choose whether to redirect HTTP to HTTPS (recommended: Yes)
```

**Certbot will automatically:**
- Obtain SSL certificate from Let's Encrypt
- Modify your Apache configuration
- Set up automatic renewal
- Configure HTTPS redirect

## Step 7: Verify SSL Auto-Renewal

```bash
# Test the renewal process (dry run)
sudo certbot renew --dry-run

# Should show "Congratulations, all simulated renewals succeeded"

# Check renewal timer status
sudo systemctl status certbot.timer
```

Certificates will auto-renew before expiration (every 90 days).

## Step 8: Configure PHP Settings (Optional but Recommended)

```bash
# Edit PHP configuration
sudo nano /etc/php/8.1/apache2/php.ini

# Adjust these settings:
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M
max_execution_time = 300
date.timezone = America/New_York  # Set your timezone
```

```bash
# Restart Apache to apply PHP changes
sudo systemctl restart apache2
```

## Step 9: Set Up Database

```bash
# Install MySQL if not already installed
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

In MySQL prompt:
```sql
CREATE DATABASE cmmc_compliance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'cmmc_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON cmmc_compliance.* TO 'cmmc_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 10: Configure Application

```bash
# Navigate to your application
cd /var/www/cmmc-suite

# The application should now be accessible
# Visit: https://your-domain.com/install

# Follow the web-based installer to:
# 1. Test system requirements
# 2. Configure database connection
# 3. Run migrations
# 4. Create admin account
```

## Step 11: Security Hardening (Recommended)

### Hide Apache Version
```bash
sudo nano /etc/apache2/conf-available/security.conf

# Set these values:
ServerTokens Prod
ServerSignature Off
```

### Enable UFW Firewall
```bash
# Install UFW if not installed
sudo apt install -y ufw

# Allow SSH (IMPORTANT - do this first!)
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

### Set Up Fail2Ban (Protect Against Brute Force)
```bash
# Install Fail2Ban
sudo apt install -y fail2ban

# Create local configuration
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit configuration
sudo nano /etc/fail2ban/jail.local

# Enable Apache protection by finding and uncommenting:
[apache-auth]
enabled = true

[apache-badbots]
enabled = true

[apache-noscript]
enabled = true

# Start Fail2Ban
sudo systemctl start fail2ban
sudo systemctl enable fail2ban

# Check status
sudo fail2ban-client status
```

## Step 12: Set Up Automated Backups (Recommended)

```bash
# Create backup script
sudo nano /usr/local/bin/backup-cmmc.sh
```

Add this content:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/cmmc-suite"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u cmmc_user -p'your_password' cmmc_compliance | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/cmmc-suite/storage /var/www/cmmc-suite/config

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-cmmc.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e

# Add this line:
0 2 * * * /usr/local/bin/backup-cmmc.sh >> /var/log/cmmc-backup.log 2>&1
```

## Troubleshooting

### Apache Won't Start
```bash
# Check Apache error logs
sudo tail -f /var/log/apache2/error.log

# Check configuration syntax
sudo apache2ctl configtest

# Check if port 80/443 is already in use
sudo netstat -tulpn | grep :80
sudo netstat -tulpn | grep :443
```

### Permission Issues
```bash
# Reset permissions
sudo chown -R www-data:www-data /var/www/cmmc-suite
sudo chmod -R 755 /var/www/cmmc-suite
sudo chmod -R 775 /var/www/cmmc-suite/storage
sudo chmod -R 775 /var/www/cmmc-suite/config
sudo chmod -R 775 /var/www/cmmc-suite/public/uploads
```

### SSL Certificate Issues
```bash
# Check certificate status
sudo certbot certificates

# Force renewal
sudo certbot renew --force-renewal

# Check Apache SSL configuration
sudo apache2ctl -M | grep ssl
```

### PHP Issues
```bash
# Check PHP version
php -v

# Check loaded PHP modules
php -m

# Check PHP error log
sudo tail -f /var/log/apache2/cmmc-suite-error.log
```

### Database Connection Issues
```bash
# Test database connection
mysql -u cmmc_user -p -h localhost cmmc_compliance

# Check MySQL status
sudo systemctl status mysql

# View MySQL error log
sudo tail -f /var/log/mysql/error.log
```

## Monitoring and Maintenance

### Check Apache Status
```bash
sudo systemctl status apache2
```

### View Access Logs
```bash
sudo tail -f /var/log/apache2/cmmc-suite-access.log
```

### View Error Logs
```bash
sudo tail -f /var/log/apache2/cmmc-suite-error.log
```

### Restart Apache
```bash
sudo systemctl restart apache2
```

### Reload Apache (Graceful - No Downtime)
```bash
sudo systemctl reload apache2
```

## Performance Optimization (Optional)

### Enable Apache MPM Event (Better Performance)
```bash
# Disable prefork
sudo a2dismod php8.1
sudo a2dismod mpm_prefork

# Enable event and php-fpm
sudo a2enmod mpm_event
sudo a2enmod proxy_fcgi setenvif
sudo a2enconf php8.1-fpm

# Restart Apache
sudo systemctl restart apache2
```

### Enable OPcache
```bash
# Edit PHP-FPM configuration
sudo nano /etc/php/8.1/fpm/php.ini

# Find and set:
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

## Quick Reference Commands

```bash
# Restart all services
sudo systemctl restart apache2 mysql

# Check service status
sudo systemctl status apache2
sudo systemctl status mysql

# View live logs
sudo tail -f /var/log/apache2/cmmc-suite-error.log

# Test SSL certificate
sudo certbot certificates

# Force SSL renewal
sudo certbot renew

# Check disk space
df -h

# Check memory usage
free -h

# Check running processes
ps aux | grep apache
```

## Support

For issues specific to the CMMC Compliance Suite application, check:
- Application error logs: `/var/www/cmmc-suite/storage/logs/`
- PHP error log: `/var/log/apache2/cmmc-suite-error.log`
- Database: MySQL error log at `/var/log/mysql/error.log`

## Security Checklist

- [ ] SSL certificate installed and auto-renewal working
- [ ] HTTP redirects to HTTPS
- [ ] Firewall (UFW) enabled with only necessary ports
- [ ] Fail2Ban installed and configured
- [ ] Database has strong password
- [ ] File permissions set correctly (755 for directories, 644 for files)
- [ ] Sensitive directories blocked in Apache config
- [ ] Server tokens/signature disabled
- [ ] Regular backups configured
- [ ] PHP settings optimized
- [ ] Security headers enabled

Your CMMC Compliance Suite should now be securely accessible at https://your-domain.com
