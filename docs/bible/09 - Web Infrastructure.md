# Web Infrastructure: NextGen Noise Platform

## 1. Overview

The NextGen Noise web platform consists of:
1. **Backend API**: RESTful API for authentication, licensing, payments, analytics
2. **Frontend Portal**: Customer-facing website for purchases, account management, downloads
3. **Database**: PostgreSQL for persistent data, Redis for caching
4. **Email**: Transactional email service for confirmations and notifications
5. **Admin Dashboard**: Internal tool for managing users, licenses, analytics

## 2. Technology Stack

### 2.1 Backend API

**Option A: Node.js (Express.js)** - Recommended for rapid development
```
- Runtime: Node.js 18+
- Framework: Express.js 4.18+
- ORM: Prisma or TypeORM
- Testing: Jest, Supertest
- Auth: JWT (jsonwebtoken), bcrypt
- Payment: stripe package
- Database: PostgreSQL, Redis
- Logging: winston or pino
- Error Tracking: Sentry
```

**Option B: Python (FastAPI)** - Alternative for data science teams
```
- Runtime: Python 3.10+
- Framework: FastAPI
- ORM: SQLAlchemy
- Testing: pytest
- Auth: PyJWT, passlib
- Database: PostgreSQL, redis-py
- Logging: structlog
- Error Tracking: Sentry
```

**Option C: Go (Gin)** - High performance alternative
```
- Runtime: Go 1.19+
- Framework: Gin-gonic
- ORM: GORM
- Testing: testify
- Database: pgx (PostgreSQL driver)
```

### 2.2 Frontend Portal

```
- Framework: Next.js 13+ (React) or Nuxt 3 (Vue)
- CSS: TailwindCSS 3+
- State Management: Zustand or Pinia
- Form Validation: Zod or Yup
- HTTP Client: Axios or Fetch API
- UI Components: Headless UI, Radix UI
- Payment: Stripe.js, @stripe/react-stripe-js
- Hosting: Vercel or Netlify
- Domain: nextgennoise.com
```

### 2.3 Database

```
- Primary: PostgreSQL 14+
  - Connection pooling: pgBouncer or PgPool
  - Backups: AWS RDS automated backups
  - Read replicas: For horizontal scaling

- Cache: Redis 7+
  - Session storage
  - JWT token invalidation
  - License validation caching (5-min TTL)
  - Rate limit counters
```

### 2.4 Infrastructure

```
- Hosting: AWS (EC2 + RDS) or DigitalOcean
- CDN: CloudFlare
- SSL: Let's Encrypt (automated with certbot)
- Monitoring: Datadog, New Relic, or Grafana + Prometheus
- Error Tracking: Sentry
- Log Aggregation: Elasticsearch + Kibana or Loggly
- CI/CD: GitHub Actions
- Email: smtp.com (transactional emails via SMTP)
- Email Marketing: Mailchimp (newsletters, automation, segments)
```

## 3. API Specification

### 3.1 Base URL

```
https://api.nextgennoise.com/api/v1
```

### 3.2 Authentication Endpoints

#### Register User
```
POST /auth/register
Content-Type: application/json

Request:
{
  "email": "user@example.com",
  "password": "securePassword123",
  "first_name": "John",
  "last_name": "Doe"
}

Response (201 Created):
{
  "id": "uuid-here",
  "email": "user@example.com",
  "first_name": "John",
  "last_name": "Doe",
  "email_verified": false,
  "created_at": "2025-02-05T12:00:00Z"
}

Error (400 Bad Request):
{
  "error": "email_already_exists"
}
```

#### Login
```
POST /auth/login
Content-Type: application/json

Request:
{
  "email": "user@example.com",
  "password": "securePassword123"
}

Response (200 OK):
{
  "access_token": "eyJhbGciOiJIUzI1NiIs...",
  "refresh_token": "eyJhbGciOiJIUzI1NiIs...",
  "expires_in": 3600
}

Error (401 Unauthorized):
{
  "error": "invalid_credentials"
}
```

#### Logout
```
POST /auth/logout
Authorization: Bearer <token>

Response (200 OK):
{
  "message": "logged_out"
}
```

#### Refresh Token
```
POST /auth/refresh
Content-Type: application/json

Request:
{
  "refresh_token": "eyJhbGciOiJIUzI1NiIs..."
}

Response (200 OK):
{
  "access_token": "eyJhbGciOiJIUzI1NiIs...",
  "expires_in": 3600
}
```

#### Forgot Password
```
POST /auth/forgot-password
Content-Type: application/json

Request:
{
  "email": "user@example.com"
}

Response (200 OK):
{
  "message": "reset_email_sent"
}
```

#### Reset Password
```
POST /auth/reset-password
Content-Type: application/json

Request:
{
  "token": "reset_token_here",
  "new_password": "newSecurePassword123"
}

Response (200 OK):
{
  "message": "password_reset"
}
```

### 3.3 User Profile Endpoints

#### Get Profile
```
GET /user/profile
Authorization: Bearer <token>

Response (200 OK):
{
  "id": "uuid-here",
  "email": "user@example.com",
  "first_name": "John",
  "last_name": "Doe",
  "email_verified": true,
  "created_at": "2025-02-05T12:00:00Z",
  "stripe_customer_id": "cus_xyz..."
}
```

#### Update Profile
```
PUT /user/profile
Authorization: Bearer <token>
Content-Type: application/json

Request:
{
  "first_name": "Jane",
  "last_name": "Smith"
}

Response (200 OK):
{ updated profile object }
```

### 3.4 License Endpoints

#### Activate License
```
POST /license/activate
Authorization: Bearer <token>
Content-Type: application/json

Request:
{
  "license_key": "NG-XXXX-XXXX-XXXX",
  "hardware_id": "a1b2c3d4e5f6...",
  "machine_name": "Studio Desktop",
  "os": "Windows 11"
}

Response (200 OK):
{
  "status": "activated",
  "activation_token": "token_xyz...",
  "license_type": "standard",
  "max_activations": 2,
  "activations_remaining": 1,
  "expires_at": "2026-02-05T00:00:00Z"
}

Error (400 Bad Request):
{
  "error": "license_invalid"
}

Error (400 Bad Request):
{
  "error": "max_activations_reached",
  "activations": [
    {
      "id": "act_uuid",
      "machine_name": "Laptop",
      "os": "macOS 13",
      "activated_at": "2025-01-28T10:00:00Z"
    },
    {
      "id": "act_uuid",
      "machine_name": "Studio Desktop",
      "os": "Windows 11",
      "activated_at": "2025-02-01T14:00:00Z"
    }
  ]
}
```

#### Deactivate Machine
```
POST /license/deactivate
Authorization: Bearer <token>
Content-Type: application/json

Request:
{
  "activation_id": "act_uuid"
}

Response (200 OK):
{
  "status": "deactivated",
  "message": "machine_deactivated"
}
```

#### Check License Status
```
GET /license/status
Authorization: Bearer <token>

Response (200 OK):
{
  "status": "active",
  "license_key": "NG-XXXX-XXXX-XXXX",
  "license_type": "standard",
  "expires_at": "2026-02-05T00:00:00Z",
  "activations": [
    {
      "id": "act_uuid",
      "machine_name": "Studio Desktop",
      "os": "Windows 11",
      "activated_at": "2025-02-01T14:00:00Z",
      "last_validated": "2025-02-05T12:00:00Z"
    }
  ]
}
```

#### List User's Licenses
```
GET /license/list
Authorization: Bearer <token>

Response (200 OK):
{
  "licenses": [
    {
      "id": "lic_uuid",
      "license_key": "NG-XXXX-XXXX-XXXX",
      "product": "mixing_mentor",
      "license_type": "standard",
      "created_at": "2025-02-01T00:00:00Z",
      "expires_at": "2026-02-05T00:00:00Z",
      "activations_count": 1,
      "max_activations": 2
    }
  ]
}
```

### 3.5 Purchase Endpoints

#### Create Checkout Session
```
POST /purchase/create-checkout
Authorization: Bearer <token>
Content-Type: application/json

Request:
{
  "plan": "standard",
  "license_type": "perpetual"
}

Response (200 OK):
{
  "session_id": "cs_test_xyz...",
  "checkout_url": "https://checkout.stripe.com/pay/cs_test_xyz..."
}
```

#### Get Purchase History
```
GET /purchase/history
Authorization: Bearer <token>

Response (200 OK):
{
  "purchases": [
    {
      "id": "pur_uuid",
      "amount": 2999,
      "currency": "USD",
      "status": "completed",
      "created_at": "2025-02-01T14:00:00Z",
      "license_key": "NG-XXXX-XXXX-XXXX"
    }
  ]
}
```

#### Stripe Webhook (Internal)
```
POST /purchase/webhook
X-Stripe-Signature: <signature>
Content-Type: application/json

Events:
- payment_intent.succeeded
  → Create license
  → Send license email

- payment_intent.payment_failed
  → Log failure
  → Notify user (optional)

- charge.refunded
  → Revoke license
  → Send revocation email
```

### 3.6 Analytics Endpoints

#### Send Telemetry
```
POST /analytics/telemetry
Authorization: Bearer <token>
Content-Type: application/json

Request:
{
  "event_type": "session_start",
  "daw_type": "Studio One Pro",
  "os": "Windows 11",
  "plugin_version": "1.0.0",
  "session_duration_secs": 3600,
  "features_used": ["ghost_needle", "target_curve", "text_feed"],
  "crash_report": null,
  "custom_data": {}
}

Response (201 Created):
{
  "status": "recorded"
}
```

#### Get Analytics Dashboard (Admin)
```
GET /analytics/dashboard?period=30d
Authorization: Bearer <admin_token>

Response (200 OK):
{
  "period": "30d",
  "metrics": {
    "total_users": 150,
    "active_licenses": 120,
    "total_revenue": 239880,
    "daily_active_users": 45,
    "average_session_duration": 2400,
    "most_used_features": [
      "text_feed": 95,
      "ghost_needle": 88,
      "target_curve": 72
    ]
  },
  "crashes": [
    {
      "os": "Windows 11",
      "version": "1.0.0",
      "count": 2,
      "error": "NullPointerException in AnalysisEngine"
    }
  ]
}
```

### 3.7 Support Endpoints

#### Create Support Ticket
```
POST /support/ticket
Authorization: Bearer <token>
Content-Type: application/json

Request:
{
  "subject": "Plugin crashes on Snare track",
  "message": "When I add the plugin to my snare track and analyze...",
  "category": "bug"
}

Response (201 Created):
{
  "id": "tkt_uuid",
  "status": "open",
  "created_at": "2025-02-05T12:00:00Z"
}
```

#### List Support Tickets
```
GET /support/tickets
Authorization: Bearer <token>

Response (200 OK):
{
  "tickets": [
    {
      "id": "tkt_uuid",
      "subject": "Plugin crashes on Snare track",
      "status": "open",
      "created_at": "2025-02-05T12:00:00Z"
    }
  ]
}
```

### 3.8 Download Endpoints

#### Get Latest Plugin Version
```
GET /downloads/latest?platform=windows
Authorization: Bearer <token>

Response (200 OK):
{
  "version": "1.0.0",
  "platform": "windows",
  "download_url": "https://cdn.nextgennoise.com/vst/1.0.0/MixingMentor-1.0.0-windows.exe",
  "file_size": 52428800,
  "checksum_sha256": "abc123...",
  "release_notes": "Initial release..."
}
```

#### List Available Versions
```
GET /downloads/versions
Authorization: Bearer <token>

Response (200 OK):
{
  "versions": [
    {
      "version": "1.0.0",
      "release_date": "2025-02-05T00:00:00Z",
      "windows_url": "https://cdn.nextgennoise.com/vst/1.0.0/MixingMentor-1.0.0-windows.exe",
      "macos_url": "https://cdn.nextgennoise.com/vst/1.0.0/MixingMentor-1.0.0-macos.pkg"
    }
  ]
}
```

## 4. Database Schema

See `progress.json` PHASE_9 for complete schema definition.

Key tables:
- `users` - User accounts
- `licenses` - License records
- `activations` - Machine activations
- `purchases` - Purchase transactions
- `telemetry` - Usage analytics
- `support_tickets` - Customer support

## 5. Frontend Pages

### 5.1 Public Pages

#### Homepage (`/`)
- Hero section with demo video
- Feature highlights (Ghost Needle, AI Mentor, etc.)
- Pricing section
- FAQ
- Footer with links

#### Pricing (`/pricing`)
- Plan comparison table
- "Buy Now" button for each plan
- FAQ section

#### Documentation (`/docs`)
- Installation guide
- Quick start tutorial
- Feature reference
- Troubleshooting FAQ

#### Legal
- `/privacy` - Privacy policy (GDPR-compliant)
- `/terms` - Terms of service

### 5.2 Authenticated Pages

#### Dashboard (`/dashboard`)
- User overview (welcome, active licenses)
- Recent activity
- Support ticket count

#### Licenses (`/dashboard/licenses`)
- List of active licenses
- List of machine activations
- "Deactivate" buttons
- Expiration dates

#### Downloads (`/dashboard/downloads`)
- Windows installer download links
- macOS installer download links
- Version history

#### Support (`/dashboard/support`)
- Create new ticket form
- List of user's tickets
- Ticket detail view

#### Settings (`/dashboard/settings`)
- Email address
- Password change
- Download personal data (GDPR)
- Delete account (GDPR)

### 5.3 Payment Pages

#### Checkout (`/checkout?session_id=...`)
- Stripe embedded payment form
- Order summary
- "Pay" button

#### Success (`/checkout/success`)
- Order confirmation
- License key display (copyable)
- "Download Plugin" button
- "View Account" link

#### Error (`/checkout/error`)
- Error message
- Retry button
- Support link

## 6. Deployment

### 6.1 Backend Deployment (AWS)

```bash
# Create EC2 instance
aws ec2 run-instances \
  --image-id ami-0c55b159cbfafe1f0 \
  --instance-type t3.medium

# Setup RDS PostgreSQL
aws rds create-db-instance \
  --db-instance-identifier nextgen-noise-prod \
  --db-instance-class db.t3.micro \
  --engine postgres \
  --master-username postgres

# Setup ElastiCache Redis
aws elasticache create-cache-cluster \
  --cache-cluster-id nextgen-noise-redis \
  --engine redis \
  --cache-node-type cache.t3.micro
```

### 6.2 Frontend Deployment (Vercel)

```bash
# Connect GitHub repo to Vercel
# Automatic deployments on push to main
# Custom domain: nextgennoise.com
# SSL: Automatic with Let's Encrypt
```

### 6.3 CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/deploy.yml
name: Deploy Backend
on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - run: npm install
      - run: npm test
      - run: npm run lint

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - run: npm install
      - run: npm run build
      - run: npm run migrate
      - uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.EC2_HOST }}
          username: ec2-user
          key: ${{ secrets.EC2_KEY }}
          script: |
            cd /app
            git pull origin main
            npm install --production
            npm run start
```

## 7. Security Considerations

- **HTTPS Only**: All endpoints require TLS 1.2+
- **Rate Limiting**: 20 requests/minute per IP
- **CSRF Protection**: Use CSRF tokens for state-changing requests
- **SQL Injection Prevention**: Use parameterized queries
- **XSS Prevention**: Escape all user input
- **Authentication**: JWT tokens with 1-hour expiration
- **Password Hashing**: bcrypt with 10+ rounds
- **Secrets Management**: Use AWS Secrets Manager for API keys

## 8. Monitoring & Logging

- **Sentry**: Capture errors and exceptions
- **Datadog**: Monitor CPU, memory, disk usage
- **CloudWatch**: Log API requests and responses
- **Uptime Monitoring**: Pingdom or StatusPage

## 9. Email Service Integration

### smtp.com Setup (Transactional Emails)

```
1. Login to existing smtp.com account
2. Configure SMTP credentials in backend:
   - Host: smtp.com
   - Port: 587 (TLS) or 465 (SSL)
   - Username: your_username
   - Password: your_app_password

3. Setup sender email: noreply@nextgennoise.com
4. Test SMTP connection with test email
5. Verify email delivery

Backend Configuration (Node.js example):
- Use nodemailer with smtp.com configuration
- Create email templates (welcome, verification, license key, password reset, etc.)
- Queue system for reliable delivery (Bull or similar)
- Bounce/complaint handling

Email Templates:
- Welcome email (after registration)
- Email verification (confirm address)
- License key delivery (after purchase)
- Password reset (forgot password flow)
- Trial expiration warning (3 days before expiry)
- Support ticket confirmation
- Update notifications
```

### Mailchimp Setup (Email Marketing & Automation)

```
1. Login to existing Mailchimp account
2. Create audience/list for NextGen Noise users
3. Setup automation workflows:
   - Welcome series (3 emails over 7 days)
   - License expiration reminder (30 days before)
   - Feature announcements (monthly digest)
   - Abandoned checkout recovery (if applicable)

4. Configure API integration:
   - Get API key from account settings
   - Setup webhook for sync events

5. Segment users by:
   - License type (trial vs. paid)
   - Purchase status
   - Feature usage (via custom tags)
   - DAW preference

Integration:
- Sync newly registered users to Mailchimp list
- Tag users based on license status
- Trigger abandoned cart recovery (if applicable)
- Monitor campaign performance (open rates, clicks)
- A/B test subject lines and content

Use Cases:
- Welcome series (product education)
- Feature updates (new genres, improvements)
- Seasonal promotions (Black Friday, etc.)
- License renewal reminders
- Support resources and best practices
```

## 10. Future Enhancements

- **Subscription Model**: Monthly/yearly subscription plans
- **Affiliate Program**: Partner commission system
- **Custom Training**: Genre-specific AI model training service
- **API Access**: For DAW developers to integrate Mixing Mentor
- **Mobile App**: Companion app for iOS/Android
