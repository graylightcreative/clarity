# aapanel Deployment & Subscription Setup

## 1. Overview

This document covers deploying the NextGen Noise web platform on aapanel (aaPanel) with email via smtp.com and Mailchimp integration, plus cron jobs for subscription management and recurring tasks.

## 2. aapanel Setup

### 2.1 Initial aapanel Installation

**Prerequisites**:
- Linux server (Ubuntu 18.04+, CentOS 7+, etc.)
- aapanel account created at aapanel.com
- Root or sudo access to server

**Installation Steps**:

```bash
# Download and install aapanel
wget -O install.sh http://www.aapanel.com/install/install_panel.sh
bash install.sh

# Follow on-screen prompts and save your credentials
# Access aapanel at: http://your_server_ip:7888
# Default username: admin
# Default password: [shown in terminal, save this!]
```

### 2.2 aapanel Configuration

**Basic Setup in aapanel Dashboard**:

1. **System Security**:
   - Change default login password
   - Enable 2FA (two-factor authentication)
   - Configure firewall rules (open 7888, 80, 443)
   - Whitelist your IP address

2. **Install Required Software**:
   - Node.js (18+ LTS recommended)
   - PostgreSQL 14+
   - Redis 7+
   - Apache (reverse proxy) OR Nginx (if preferred)
   - Git

   ```bash
   # Via aapanel UI:
   # - Click "App Store"
   # - Install each application
   # - Or use terminal:
   apt update
   apt install nodejs npm postgresql redis-server nginx git curl
   ```

3. **SSL Certificates**:
   - Install Let's Encrypt
   - Auto-renew certificates via aapanel
   - Add domain: nextgennoise.com
   - Enable SSL (free Let's Encrypt)

### 2.3 Database Setup

**PostgreSQL Configuration**:

```bash
# Access PostgreSQL via aapanel or terminal
sudo -u postgres psql

# Create database
CREATE DATABASE nextgennoise_prod;

# Create user with password
CREATE USER nextgennoise_user WITH PASSWORD 'strong_db_password_here';

# Grant permissions
GRANT ALL PRIVILEGES ON DATABASE nextgennoise_prod TO nextgennoise_user;
ALTER ROLE nextgennoise_user SET client_encoding TO 'utf8';
ALTER ROLE nextgennoise_user SET default_transaction_isolation TO 'read committed';
ALTER ROLE nextgennoise_user SET default_transaction_deferrable TO on;
ALTER ROLE nextgennoise_user SET timezone TO 'UTC';

# Exit psql
\q
```

**Backup Configuration**:

```bash
# Create backup directory
mkdir -p /backup/postgresql
chmod 700 /backup/postgresql

# Create backup script
cat > /usr/local/bin/backup_postgresql.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backup/postgresql"
DB_NAME="nextgennoise_prod"
DB_USER="nextgennoise_user"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/nextgennoise_${TIMESTAMP}.sql.gz"

# Create backup
sudo -u postgres pg_dump $DB_NAME | gzip > $BACKUP_FILE

# Keep only last 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Backup completed: $BACKUP_FILE"
EOF

chmod +x /usr/local/bin/backup_postgresql.sh
```

### 2.4 Redis Setup

**Redis Configuration for aapanel**:

```bash
# Edit Redis config
sudo nano /etc/redis/redis.conf

# Key settings:
maxmemory 256mb                    # Set memory limit
maxmemory-policy allkeys-lru       # Evict LRU keys when limit reached
requirepass your_redis_password    # Set password

# Restart Redis
sudo systemctl restart redis-server
```

### 2.5 Node.js Application Setup

**Deploy Application**:

```bash
# Create application directory
mkdir -p /home/nextgennoise
cd /home/nextgennoise

# Clone repository (or upload your code)
git clone https://github.com/yourusername/nextgennoise-api.git .

# Install dependencies
npm install --production

# Create .env file with credentials
cat > /home/nextgennoise/.env << 'EOF'
# Server
NODE_ENV=production
PORT=3001
DOMAIN=api.nextgennoise.com

# Database
DB_HOST=localhost
DB_PORT=5432
DB_NAME=nextgennoise_prod
DB_USER=nextgennoise_user
DB_PASSWORD=strong_db_password_here

# Redis
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=your_redis_password

# SMTP (smtp.com)
SMTP_HOST=smtp.com
SMTP_PORT=587
SMTP_USER=your_username
SMTP_PASS=your_app_password
SMTP_FROM=noreply@nextgennoise.com

# Mailchimp
MAILCHIMP_API_KEY=your_mailchimp_api_key
MAILCHIMP_AUDIENCE_ID=your_audience_id

# Stripe
STRIPE_SECRET_KEY=sk_live_xxxxxxxxxxxx
STRIPE_PUBLIC_KEY=pk_live_xxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxx

# JWT
JWT_SECRET=very_long_random_secret_key_here

# Sentry
SENTRY_DSN=https://xxxxx@xxxxx.ingest.sentry.io/xxxxx
EOF

# Set proper permissions
chown -R nobody:nogroup /home/nextgennoise
chmod 600 /home/nextgennoise/.env
```

### 2.6 Web Server Configuration (Apache or Nginx)

#### Option A: Apache (Recommended for aapanel)

**Reverse Proxy Setup with Apache**:

```bash
# Enable required modules
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Create Apache config
cat > /etc/apache2/sites-available/nextgennoise.conf << 'EOF'
<VirtualHost *:80>
    ServerName api.nextgennoise.com
    ServerAdmin admin@nextgennoise.com

    # Redirect HTTP to HTTPS
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteRule ^/?(.*) https://%{SERVER_NAME}/$1 [R=301,L]
</VirtualHost>

<VirtualHost *:443>
    ServerName api.nextgennoise.com
    ServerAdmin admin@nextgennoise.com

    # SSL Certificates (via Let's Encrypt in aapanel)
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/nextgennoise.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/nextgennoise.com/privkey.pem

    # SSL Security Headers
    SSLProtocol TLSv1.2 TLSv1.3
    SSLCipherSuite HIGH:!aNULL:!MD5
    Header always set Strict-Transport-Security "max-age=31536000"

    # Reverse Proxy to Node.js
    ProxyPreserveHost On
    ProxyPass / http://127.0.0.1:3001/
    ProxyPassReverse / http://127.0.0.1:3001/

    # Forward headers
    ProxyAddHeaders On
    RequestHeader set X-Forwarded-Proto "https"
    RequestHeader set X-Forwarded-For "%{REMOTE_ADDR}s"
</VirtualHost>
EOF

# Enable site
sudo a2ensite nextgennoise

# Test Apache config
sudo apache2ctl configtest

# Restart Apache
sudo systemctl restart apache2
```

#### Option B: Nginx (Alternative)

**Reverse Proxy Setup with Nginx**:

```bash
# Create Nginx config
cat > /etc/nginx/sites-available/nextgennoise << 'EOF'
upstream nextgennoise_api {
    server 127.0.0.1:3001;
}

server {
    listen 80;
    server_name api.nextgennoise.com;

    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name api.nextgennoise.com;

    # SSL Certificates (via Let's Encrypt in aapanel)
    ssl_certificate /etc/letsencrypt/live/nextgennoise.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/nextgennoise.com/privkey.pem;

    # SSL Security Headers
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    add_header Strict-Transport-Security "max-age=31536000" always;

    # Application
    location / {
        proxy_pass http://nextgennoise_api;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
EOF

# Enable site
sudo ln -s /etc/nginx/sites-available/nextgennoise /etc/nginx/sites-enabled/

# Test Nginx config
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 2.7 Process Manager (PM2)

**Setup PM2 to Keep App Running**:

```bash
# Install PM2 globally
npm install -g pm2

# Start application with PM2
cd /home/nextgennoise
pm2 start app.js --name "nextgennoise-api"

# Configure startup
pm2 startup
pm2 save

# Monitor status
pm2 status
pm2 logs nextgennoise-api
```

## 3. Cron Jobs for Subscription & Recurring Tasks

### 3.1 Daily Database Backup

```bash
# Add to crontab
crontab -e

# Daily backup at 2 AM
0 2 * * * /usr/local/bin/backup_postgresql.sh

# Weekly backup (keep for 90 days)
0 3 * * 0 /usr/local/bin/backup_postgresql.sh -weekly
```

### 3.2 License Expiration Checks (Daily)

**Create Script**:

```bash
cat > /usr/local/bin/check_license_expiry.js << 'EOF'
#!/usr/bin/env node

require('dotenv').config();
const db = require('./db');
const email = require('./email');

async function checkExpiredLicenses() {
  try {
    // Find licenses expiring in 7 days
    const expiringLicenses = await db.query(`
      SELECT u.email, l.license_key, l.expires_at
      FROM licenses l
      JOIN users u ON l.user_id = u.id
      WHERE l.expires_at BETWEEN NOW() AND NOW() + INTERVAL 7 days
      AND NOT EXISTS (
        SELECT 1 FROM expiry_reminder_sent
        WHERE license_id = l.id
        AND sent_at > NOW() - INTERVAL 1 day
      )
    `);

    for (const license of expiringLicenses) {
      // Send expiration warning email
      await email.send({
        to: license.email,
        template: 'license_expiring_7days',
        data: {
          license_key: license.license_key,
          expiry_date: license.expires_at
        }
      });

      // Mark reminder as sent
      await db.query(
        'INSERT INTO expiry_reminder_sent (license_id) VALUES ($1)',
        [license.id]
      );
    }

    console.log(`Sent ${expiringLicenses.length} expiry reminders`);
  } catch (error) {
    console.error('License expiry check failed:', error);
    // Send alert via Sentry
    Sentry.captureException(error);
  }
}

checkExpiredLicenses().then(() => process.exit(0));
EOF

chmod +x /usr/local/bin/check_license_expiry.js
```

**Add to Crontab**:

```bash
crontab -e

# Run daily at 8 AM
0 8 * * * cd /home/nextgennoise && node /usr/local/bin/check_license_expiry.js
```

### 3.3 Trial Expiration Checks (Daily)

```bash
cat > /usr/local/bin/check_trial_expiry.js << 'EOF'
#!/usr/bin/env node

require('dotenv').config();
const db = require('./db');
const email = require('./email');

async function checkExpiredTrials() {
  try {
    // Find trials expiring today
    const expiredTrials = await db.query(`
      SELECT u.email, u.first_name,
             (EXTRACT(DAY FROM (NOW() - trial_start_date))) as days_since_start
      FROM trial_users t
      JOIN users u ON t.user_id = u.id
      WHERE EXTRACT(DAY FROM (NOW() - trial_start_date)) >= 14
      AND NOT EXISTS (
        SELECT 1 FROM licenses WHERE user_id = u.id
      )
      AND NOT EXISTS (
        SELECT 1 FROM trial_expiry_sent
        WHERE user_id = u.id
        AND sent_at > NOW() - INTERVAL 1 day
      )
    `);

    for (const trial of expiredTrials) {
      // Send "please purchase" email
      await email.send({
        to: trial.email,
        template: 'trial_expired',
        data: { first_name: trial.first_name }
      });

      await db.query(
        'INSERT INTO trial_expiry_sent (user_id) VALUES ($1)',
        [trial.user_id]
      );
    }

    console.log(`Processed ${expiredTrials.length} trial expirations`);
  } catch (error) {
    console.error('Trial expiry check failed:', error);
    Sentry.captureException(error);
  }
}

checkExpiredTrials().then(() => process.exit(0));
EOF

chmod +x /usr/local/bin/check_trial_expiry.js

# Add to crontab
# 0 9 * * * cd /home/nextgennoise && node /usr/local/bin/check_trial_expiry.js
```

### 3.4 Mailchimp Audience Sync (Daily)

```bash
cat > /usr/local/bin/sync_mailchimp.js << 'EOF'
#!/usr/bin/env node

require('dotenv').config();
const db = require('./db');
const mailchimp = require('@mailchimp/mailchimp_marketing');

mailchimp.setConfig({
  apiKey: process.env.MAILCHIMP_API_KEY,
  server: process.env.MAILCHIMP_SERVER_PREFIX
});

async function syncMailchimp() {
  try {
    // Get all users
    const users = await db.query(`
      SELECT u.id, u.email, u.first_name, u.last_name,
             CASE WHEN l.id IS NOT NULL THEN 'paid' ELSE 'trial' END as status
      FROM users u
      LEFT JOIN licenses l ON u.id = l.user_id AND l.revoked = FALSE
      WHERE u.created_at > NOW() - INTERVAL 1 day
    `);

    for (const user of users) {
      // Check if user exists in Mailchimp
      try {
        await mailchimp.lists.setListMember(
          process.env.MAILCHIMP_AUDIENCE_ID,
          user.email,
          {
            email_address: user.email,
            status: 'subscribed',
            merge_fields: {
              FNAME: user.first_name,
              LNAME: user.last_name
            },
            tags: [user.status === 'paid' ? 'paid_customer' : 'trial_user']
          }
        );
      } catch (error) {
        if (error.status === 404) {
          // User not in list, add them
          await mailchimp.lists.addListMember(
            process.env.MAILCHIMP_AUDIENCE_ID,
            {
              email_address: user.email,
              status: 'subscribed',
              merge_fields: {
                FNAME: user.first_name,
                LNAME: user.last_name
              },
              tags: [user.status === 'paid' ? 'paid_customer' : 'trial_user']
            }
          );
        } else {
          throw error;
        }
      }
    }

    console.log(`Synced ${users.length} users to Mailchimp`);
  } catch (error) {
    console.error('Mailchimp sync failed:', error);
    Sentry.captureException(error);
  }
}

syncMailchimp().then(() => process.exit(0));
EOF

chmod +x /usr/local/bin/sync_mailchimp.js

# Add to crontab
# 0 12 * * * cd /home/nextgennoise && node /usr/local/bin/sync_mailchimp.js
```

### 3.5 Analytics Aggregation (Daily)

```bash
cat > /usr/local/bin/aggregate_analytics.js << 'EOF'
#!/usr/bin/env node

require('dotenv').config();
const db = require('./db');

async function aggregateAnalytics() {
  try {
    // Aggregate telemetry into daily summaries
    await db.query(`
      INSERT INTO analytics_daily (day, metric_name, value)
      SELECT
        DATE(created_at) as day,
        event_type,
        COUNT(*) as value
      FROM telemetry
      WHERE created_at > NOW() - INTERVAL 1 day
      GROUP BY DATE(created_at), event_type
      ON CONFLICT (day, metric_name) DO UPDATE SET value = EXCLUDED.value
    `);

    // Calculate active users
    await db.query(`
      INSERT INTO analytics_daily (day, metric_name, value)
      SELECT
        DATE(created_at) as day,
        'active_users',
        COUNT(DISTINCT license_id)
      FROM telemetry
      WHERE created_at > NOW() - INTERVAL 1 day
      GROUP BY DATE(created_at)
      ON CONFLICT (day, metric_name) DO UPDATE SET value = EXCLUDED.value
    `);

    console.log('Analytics aggregation completed');
  } catch (error) {
    console.error('Analytics aggregation failed:', error);
    Sentry.captureException(error);
  }
}

aggregateAnalytics().then(() => process.exit(0));
EOF

chmod +x /usr/local/bin/aggregate_analytics.js

# Add to crontab
# 0 1 * * * cd /home/nextgennoise && node /usr/local/bin/aggregate_analytics.js
```

### 3.6 Complete Crontab Example

```bash
# Edit crontab
crontab -e

# Add all jobs:
# Format: minute hour day month weekday command

# Daily database backup at 2 AM
0 2 * * * /usr/local/bin/backup_postgresql.sh

# Daily license expiry check at 8 AM
0 8 * * * cd /home/nextgennoise && node /usr/local/bin/check_license_expiry.js >> /var/log/nextgennoise_cron.log 2>&1

# Daily trial expiry check at 9 AM
0 9 * * * cd /home/nextgennoise && node /usr/local/bin/check_trial_expiry.js >> /var/log/nextgennoise_cron.log 2>&1

# Daily analytics aggregation at 1 AM
0 1 * * * cd /home/nextgennoise && node /usr/local/bin/aggregate_analytics.js >> /var/log/nextgennoise_cron.log 2>&1

# Daily Mailchimp sync at 12 PM (noon)
0 12 * * * cd /home/nextgennoise && node /usr/local/bin/sync_mailchimp.js >> /var/log/nextgennoise_cron.log 2>&1

# Weekly full site backup (Sunday at 3 AM)
0 3 * * 0 /usr/local/bin/backup_full_site.sh

# Monthly cleanup of old logs (1st of month at 4 AM)
0 4 1 * * find /var/log/nextgennoise* -mtime +30 -delete
```

## 4. smtp.com Email Configuration

### 4.1 SMTP Credentials in .env

```bash
# SMTP Settings
SMTP_HOST=smtp.com
SMTP_PORT=587              # TLS port (can also use 465 for SSL)
SMTP_USER=your_smtp_username
SMTP_PASS=your_app_password
SMTP_FROM_EMAIL=noreply@nextgennoise.com
SMTP_FROM_NAME=NextGen Noise
```

### 4.2 Node.js Nodemailer Configuration

```javascript
// config/email.js
const nodemailer = require('nodemailer');
require('dotenv').config();

const transporter = nodemailer.createTransport({
  host: process.env.SMTP_HOST,
  port: process.env.SMTP_PORT,
  secure: false,  // true for 465, false for 587
  auth: {
    user: process.env.SMTP_USER,
    pass: process.env.SMTP_PASS
  }
});

// Queue system for reliability
const Queue = require('bull');
const emailQueue = new Queue('email', {
  redis: {
    host: process.env.REDIS_HOST,
    port: process.env.REDIS_PORT,
    password: process.env.REDIS_PASSWORD
  }
});

// Process queue
emailQueue.process(async (job) => {
  const { to, template, data } = job.data;

  const html = await renderTemplate(template, data);

  await transporter.sendMail({
    from: `${process.env.SMTP_FROM_NAME} <${process.env.SMTP_FROM_EMAIL}>`,
    to: to,
    subject: getSubject(template, data),
    html: html
  });
});

// Send email (queue for async processing)
async function sendEmail(to, template, data) {
  await emailQueue.add({ to, template, data }, {
    attempts: 3,
    backoff: {
      type: 'exponential',
      delay: 2000
    }
  });
}

module.exports = { sendEmail, transporter };
```

## 5. Monitoring & Maintenance

### 5.1 Health Check Script

```bash
cat > /usr/local/bin/health_check.sh << 'EOF'
#!/bin/bash

# Check application
curl -f http://localhost:3001/api/health > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo "Application is down!"
  # Restart PM2 app
  pm2 restart nextgennoise-api
  # Send alert
  curl -X POST https://hooks.slack.com/services/YOUR/WEBHOOK/URL \
    -H 'Content-Type: application/json' \
    -d '{"text":"NextGen Noise API is down!"}'
fi

# Check PostgreSQL
pg_isready -h localhost -U nextgennoise_user > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo "Database is down!"
  # Send alert to Slack
fi

# Check Redis
redis-cli -h localhost ping > /dev/null 2>&1
if [ $? -ne 0 ]; then
  echo "Redis is down!"
fi

echo "Health check completed at $(date)"
EOF

chmod +x /usr/local/bin/health_check.sh

# Add to crontab
# */5 * * * * /usr/local/bin/health_check.sh >> /var/log/health_check.log 2>&1
```

### 5.2 View Logs in aapanel

```bash
# Application logs
tail -f /home/nextgennoise/logs/app.log

# Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# PostgreSQL logs
tail -f /var/log/postgresql/postgresql.log

# PM2 logs
pm2 logs nextgennoise-api
```

## 6. Security Hardening

### 6.1 Firewall Rules (via aapanel)

```bash
# Allow only necessary ports
ufw enable
ufw allow 22/tcp      # SSH
ufw allow 80/tcp      # HTTP
ufw allow 443/tcp     # HTTPS
ufw allow 7888/tcp    # aapanel (restrict to your IP)
ufw deny incoming
ufw allow outgoing
```

### 6.2 Fail2Ban Configuration

```bash
# Install fail2ban
apt install fail2ban

# Create local config
cat > /etc/fail2ban/jail.local << 'EOF'
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[recidive]
enabled = true
EOF

# Restart fail2ban
systemctl restart fail2ban
```

### 6.3 SSL Renewal

```bash
# Automatic renewal via aapanel
# Or manually:
certbot renew --quiet

# Add to crontab
# 0 3 * * * /usr/bin/certbot renew --quiet
```

## 7. Deployment Checklist

- [ ] aapanel installed and secured
- [ ] PostgreSQL configured with backup strategy
- [ ] Redis configured with password
- [ ] Node.js application deployed
- [ ] .env configured with all secrets
- [ ] Nginx reverse proxy configured
- [ ] SSL certificates installed (Let's Encrypt)
- [ ] PM2 configured for auto-restart
- [ ] All cron jobs configured
- [ ] Mailchimp API integrated
- [ ] smtp.com SMTP credentials tested
- [ ] Firewall rules configured
- [ ] Monitoring and logging configured
- [ ] Backups tested (restore from backup)
- [ ] Application tested in production

## 8. Troubleshooting

### Application won't start
```bash
pm2 logs nextgennoise-api
npm start  # Test manually
```

### Database connection error
```bash
# Test PostgreSQL connection
psql -h localhost -U nextgennoise_user -d nextgennoise_prod

# Check .env file
cat /home/nextgennoise/.env | grep DB_
```

### Email not sending
```bash
# Test SMTP connection
telnet smtp.com 587

# Check email queue
redis-cli llen bull:email:jobs:active
```

### High CPU/Memory
```bash
# Check processes
top -u nobody

# PM2 monitoring
pm2 monit

# Kill old processes
pm2 restart nextgennoise-api
```
