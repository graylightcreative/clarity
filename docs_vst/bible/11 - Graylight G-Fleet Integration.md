# Graylight G-Fleet Integration
## The Sovereign Infrastructure that Powers NGN Clarity

**Document Version**: 1.0
**Date**: February 11, 2025
**Status**: Active Architecture

---

## Executive Summary

NGN Clarity is integrated into Graylight Creative's **sovereign G-Fleet infrastructure**—a private, unified ecosystem that powers all Forge products (GoSiggy, MyIndiPro, and NGN Clarity). This integration eliminates technical debt through centralized services for security, identity, payments, and analytics.

**Key Principle**: One user, one identity, one data source across all Graylight products.

---

## What is the G-Fleet?

The G-Fleet is Graylight Creative's proprietary infrastructure layer consisting of four core services:

| Service | Purpose | Use in NGN Clarity |
|---------|---------|---|
| **Vault** | Secrets management, cryptographic keys, build-time secrets | HMAC-SHA256 keys for license activation |
| **Nexus** | Identity & SSO (Single Sign-On) | User authentication and session management |
| **Ledger** | Financial tracking, accounting, payment routing | 90/10 revenue split (Sovereign Infrastructure Fee) |
| **Pulse** | Telemetry, analytics, ML training data | A-OS improvement data collection |

### Why G-Fleet Instead of Third Parties?

1. **Data Sovereignty**: We own all data about our products and users
2. **Cost Efficiency**: Shared infrastructure scales across products
3. **Unified UX**: Users see one identity across all Graylight products
4. **Rapid Iteration**: Internal services iterate faster than external APIs
5. **Revenue Transparency**: All financial flows are visible and auditable
6. **AI Advantage**: Telemetry data directly trains our A-OS models

---

## 1. Vault: Secure Key Management

### Purpose
Vault stores secrets, cryptographic keys, and environment variables. It is the source of truth for all sensitive data used by NGN Clarity.

### HMAC-SHA256 Integration for Licensing

**What is HMAC-SHA256?**
HMAC-SHA256 is a cryptographic authentication mechanism. When NGN Clarity sends a license activation request to our backend, it includes:
- A message (JSON payload with hardware_id, license_key, timestamp)
- An HMAC signature (computed using a secret key only we know)

The server verifies the signature to ensure:
- The request wasn't tampered with in transit
- The request came from a legitimate NGN 2.0 installation

**Key Storage Strategy**

```
┌─────────────────────────────────────────┐
│     GitHub Actions Secret               │
│  (GRAYLIGHT_HMAC_SECRET_KEY)           │
└──────────────────┬──────────────────────┘
                   │
                   │ (during CI/CD build)
                   ▼
┌─────────────────────────────────────────┐
│   Obfuscated Byte Array in Binary       │
│ (Compiled into plugin executable)       │
│ Prevents memory scraping & reverse eng  │
└──────────────────┬──────────────────────┘
                   │
                   │ (at runtime)
                   ▼
┌─────────────────────────────────────────┐
│    License Activation Flow               │
│  (HMAC-SHA256 payload signing)          │
└─────────────────────────────────────────┘
```

**Build-Time Implementation**

1. **CI/CD Secret**: The HMAC key is stored in GitHub Actions Secrets as `GRAYLIGHT_HMAC_SECRET_KEY`
2. **Build Process**: During the CMake build (PHASE_0), the secret is injected into the C++ source
3. **Obfuscation**: The key is compiled as an obfuscated byte array, not as plaintext
4. **Runtime Usage**: At runtime, NGN 2.0 de-obfuscates and uses the key only when signing requests

**Why Not Fetch at Startup?**
- Startup fetches require network calls (adds latency, requires online activation)
- Hardcoded keys cannot be stolen from memory as easily (obfuscation adds layers)
- Offline activation relies on having the key available without network

**Implementation Details**
- See `Source/Licensing/OnlineActivation.cpp` for HMAC signing logic
- See `PHASE_2A (Licensing Integration)` for detailed task breakdown
- See **Document 12 - NGN Clarity API Specifications** for exact API payloads

---

## 2. Nexus: Identity & SSO

### Purpose
Nexus is Graylight's unified authentication system. A user creates one account and can log into all Graylight products (GoSiggy, MyIndiPro, NGN 2.0) with the same credentials.

### Architecture: NGN 2.0 as a Nexus Client

```
┌─────────────────────────┐
│   NGN 2.0 (Plugin)      │
│   (Web Backend Phase 7) │
└────────────┬────────────┘
             │
             │ /login or /register request
             │ (with email + password)
             ▼
┌─────────────────────────────────────────────────┐
│   Nexus API                                     │
│   (Identity Service)                            │
│                                                 │
│  https://nexus.graylightcreative.com/api/v1   │
└────────────┬────────────────────────────────────┘
             │
             │ JWT Token (user session)
             │ + User Profile Data
             ▼
┌─────────────────────────┐
│   NGN 2.0 Backend       │
│   (Stores JWT locally)  │
└────────────┬────────────┘
             │
             │ JWT attached to user requests
             │ (identifies user, authorizes actions)
             ▼
┌─────────────────────────┐
│   Other Systems         │
│   (Ledger, Pulse, etc.) │
└─────────────────────────┘
```

### Key Features

| Feature | Benefit |
|---------|---------|
| **JWT-Based Sessions** | Stateless, scalable authentication |
| **Unified User Database** | One email = one account across all products |
| **Profile Sync** | User data (name, email, preferences) shared |
| **Multi-Device Support** | Log in from plugin, web, mobile with same account |
| **Password Reset Flow** | Single password reset for all products |
| **Social Login** (future) | Support Google, GitHub, etc. once across all products |

### NGN Clarity Backend as a Nexus Client

The **NGN Clarity Backend (PHASE_7)** does NOT maintain a local user database. Instead:

1. **User Registration**: `POST /nexus/register`
   - Backend forwards user data to Nexus
   - Nexus creates account and returns JWT
   - Backend stores JWT in session/database

2. **User Login**: `POST /nexus/login`
   - Backend forwards credentials to Nexus
   - Nexus validates and returns JWT
   - Backend uses JWT for all subsequent requests

3. **JWT Validation**: All protected routes check Nexus JWT
   - Backend validates JWT signature (using Nexus public key)
   - JWT contains user ID, email, permissions

**Why This Matters**
- No password storage = no breach risk for NGN Clarity
- Nexus handles all identity logic (password resets, 2FA, etc.)
- Users are portable across all Graylight products
- Single logout logs user out of ALL products (if desired)

### Implementation Details
- See **Document 12 - NGN Clarity API Specifications** for Nexus endpoints
- See `PHASE_7 (Web Backend API)` Task WEB_001 for authentication setup
- See `PHASE_8 (Web Frontend)` Task UX_004 for login/register UI

---

## 3. Ledger: Financial Accounting & Revenue Split

### Purpose
Ledger tracks all financial transactions and enforces Graylight's revenue allocation mandate: **90% to NGN Forge account, 10% to Sovereign Reserve**.

### The 90/10 Mandate

**What It Means**
Every dollar earned by NGN Clarity is immediately split:

```
Sale: $29.99 (after Stripe fees)
  │
  ├─ 90% ($26.99) → NGN Forge Account
  │   (Product revenue, business operations)
  │
  └─ 10% ($3.00) → Sovereign Infrastructure Fee
      (Funds the G-Fleet: Vault, Nexus, Ledger, Pulse)
```

**Why This Exists**
- The G-Fleet costs money to run (servers, storage, traffic)
- Every Graylight product benefits from G-Fleet
- The 10% ensures infrastructure is funded automatically
- No product ever goes dark due to infrastructure costs
- Fair accounting: infrastructure costs are explicit, not hidden

**Ledger's Role**
Ledger is the automated accounting system that:
1. Receives Stripe webhook notifications (payment confirmations)
2. Calculates the 90/10 split
3. Records two ledger entries:
   - **NGN_REVENUE_90**: Product revenue account (90%)
   - **SOVEREIGN_INFRASTRUCTURE_FEE_10**: Infrastructure account (10%)
4. Generates monthly reports for finance/tax

### Implementation

**Stripe Webhook Integration (PHASE_7, WEB_004)**

When a customer completes a Stripe purchase:

```javascript
// Stripe sends payment_intent.succeeded webhook

Stripe Webhook → NGN Clarity Backend → Ledger API
                                    │
                                    ├─ Record: 90% to NGN_REVENUE_90
                                    └─ Record: 10% to SOVEREIGN_INFRASTRUCTURE_FEE_10
```

**Transaction Example**

```json
{
  "ledger_entries": [
    {
      "id": "LE_20250211_001",
      "date": "2025-02-11T14:23:00Z",
      "type": "PAYMENT",
      "source": "stripe_charge_ch123456",
      "description": "NGN Clarity License Purchase (User: john@example.com)",
      "amount_90": 26.99,
      "account_90": "NGN_REVENUE_90",
      "amount_10": 3.00,
      "account_10": "SOVEREIGN_INFRASTRUCTURE_FEE_10",
      "status": "posted"
    }
  ]
}
```

**Label Convention**
- Display in ledger as "Sovereign Infrastructure Fee" (not "Platform Fee" or "Service Fee")
- This distinction is important for accounting/tax purposes
- Finance team can easily identify and allocate costs

### Implementation Details
- See **Document 12 - NGN 2.0 API Specifications** for Ledger endpoints
- See `PHASE_7 (Web Backend API)` Task WEB_004 for Stripe webhook handler
- See `PHASE_9 (Database)` for transaction table schema

---

## 4. Pulse: Telemetry & Analytics

### Purpose
Pulse collects anonymized telemetry from NGN Clarity users to:
1. **Train the A-OS Brain**: Improve AI recommendations over time
2. **Understand Usage**: Which features are used most?
3. **Monitor Health**: Are users hitting errors? What latency?
4. **Improve UX**: Which advice items drive actual mixing improvements?

### What Data We Collect

**Telemetry Fields** (sent every 15 minutes of active session)

| Field | Example | Purpose |
|-------|---------|---------|
| `cpu_usage_avg` | 45.2 | Monitor plugin load on user systems |
| `buffer_size` | 256 | Understand latency requirements |
| `sample_rate` | 48000 | DAW configuration info |
| `daw_name` | "Studio One" | Which DAWs are popular? |
| `daw_version` | "6.5.30" | Which versions have issues? |
| `active_instrument_target` | "drums" | Which instruments users mix |
| `session_duration_ms` | 1800000 | How long users mix with Mentor |
| `advice_items_clicked` | 7 | Which advice is followed? |

### Privacy & Consent

**Opt-In by Default (Privacy First)**
- Telemetry is **OFF** by default
- Users must explicitly enable it
- No dark patterns; clear explanation required

**UI Implementation (PHASE_4, UX_003)**

Session Setup Component includes:

```
┌─────────────────────────────────────┐
│  Help The Mentor Learn              │
│  ────────────────────────────────   │
│                                     │
│ "Share usage data to help improve  │
│  the AI brain and recommendations  │
│  for everyone."                     │
│                                     │
│ ☐ Yes, share anonymized data       │
│ ☑ No, keep my data private         │
│                                     │
│     [Continue]                      │
└─────────────────────────────────────┘
```

**Why "Help the Mentor Learn"?**
- Transparent: Explains the benefit
- Positive framing: Helping, not surveilling
- Conversion driver: Users like being part of improving the product

### Data Flow

```
┌────────────────────┐
│  NGN 2.0 Plugin    │
│  (Session Active)  │
└─────────┬──────────┘
          │
          │ Every 15 minutes
          │ (if telemetry enabled)
          ▼
┌──────────────────────────────┐
│ Pulse Ingest Endpoint        │
│ POST /pulse/ingest           │
│ (Anonymized batch)           │
└──────────────┬───────────────┘
               │
               ├─ Deidentify (remove user ID, hash hardware ID)
               │
               ├─ Validate (schema, outliers, spam)
               │
               └─ Store in Data Lake (for ML training)
                  ↓
               A-OS Brain Training Pipeline
               ↓
               Improved AI Models
               ↓
               NGN 2.0 Gets Better
```

### Data Anonymization

Data sent to Pulse is **anonymized**:
- No user email or personal info
- Hardware ID is hashed (one-way transformation)
- Session ID is temporary, not linked to user
- No IP address or identifying headers
- Batch data (multiple entries) sent together

**Why Anonymized?**
- GDPR, CCPA, and privacy law compliance
- Users cannot be re-identified from telemetry alone
- Pulse team sees trends, not individual behavior
- Lower privacy risk = higher user trust

### Data Retention & Deletion

- **Retention**: Telemetry data retained for 2 years (for ML training)
- **User Deletion**: If user requests data deletion, telemetry is anonymized/deleted
- **Opt-Out**: Users can disable telemetry at any time; new data is not collected
- **Export**: Users can request a copy of their telemetry data

### Implementation Details
- See **Document 12 - NGN 2.0 API Specifications** for Pulse endpoints
- See `PHASE_5 (Beta Testing & Optimization)` Task TEST_004 for telemetry testing
- See `PHASE_4 (Tutorial & Onboarding)` Task UX_003 for consent UI

---

## Integration Flow: The Complete Picture

When a user first launches NGN 2.0:

```
1. VAULT (Secrets)
   └─ HMAC key ready (compiled at build)

2. NEXUS (Identity)
   └─ User logs in with email/password
   └─ Nexus returns JWT

3. NGN 2.0 Backend
   └─ Validates JWT with Nexus public key
   └─ Creates session record

4. LEDGER (Accounting)
   └─ User purchases license
   └─ Stripe webhook triggers
   └─ Ledger records 90/10 split

5. PULSE (Telemetry)
   └─ If user enabled telemetry
   └─ Every 15 mins: send anonymized usage data
   └─ Pulse stores for A-OS training

6. FUTURE PRODUCTS
   └─ New Graylight products use same Nexus
   └─ User can log in with same email
   └─ G-Fleet scales across product suite
```

---

## Critical Configuration Parameters

### Vault Secrets (Set in GitHub Actions)

| Secret | Example | Purpose |
|--------|---------|---------|
| `GRAYLIGHT_HMAC_SECRET_KEY` | (64 hex chars) | HMAC-SHA256 signing |
| `FORGE_API_KEY` | (32 alphanumeric) | Backend → Nexus authentication |

### Nexus Configuration (In NGN 2.0 Backend)

| Config | Value | Purpose |
|--------|-------|---------|
| `NEXUS_BASE_URL` | `https://nexus.graylightcreative.com/api/v1` | API endpoint |
| `NEXUS_JWT_PUBLIC_KEY` | (PEM format) | JWT signature verification |
| `FORGE_API_KEY` | (from Vault) | Client authentication |

### Ledger Configuration (In NGN 2.0 Backend)

| Config | Value | Purpose |
|--------|-------|---------|
| `LEDGER_BASE_URL` | `https://ledger.graylightcreative.com/api/v1` | API endpoint |
| `LEDGER_API_KEY` | (from Vault) | Authentication |
| `SPLIT_RATIO` | `0.90` / `0.10` | Revenue allocation |

### Pulse Configuration (In NGN 2.0 Plugin)

| Config | Value | Purpose |
|--------|-------|---------|
| `PULSE_INGEST_URL` | `https://pulse.graylightcreative.com/ingest` | Telemetry endpoint |
| `TELEMETRY_INTERVAL_MS` | `900000` | 15 minutes |
| `TELEMETRY_DEFAULT` | `false` | Opt-in (OFF by default) |

---

## Security Model

### Defense in Depth

1. **Vault**: Secrets never exposed to code; compiled at build-time
2. **Nexus**: JWT-based; tokens signed with private key; expire after 1 hour
3. **Ledger**: API authenticated with Forge API Key (sent via HTTPS only)
4. **Pulse**: Telemetry anonymized before transmission; sent via HTTPS; no user ID

### Threat Mitigations

| Threat | Mitigation |
|--------|-----------|
| **API Key Theft** | Keys stored in GitHub Actions Secrets, never in code |
| **HMAC Signature Replay** | Timestamp in payload; server rejects old timestamps |
| **JWT Forgery** | JWT signature verified with Nexus public key |
| **Telemetry Reidentification** | Data anonymized; hardware ID hashed; no email/names |
| **Network Interception** | All APIs use HTTPS/TLS 1.2+ |
| **Database Breach** | Passwords never stored locally; credential breach impacts Nexus only |

---

## Rollout Strategy

### Phase 1: Licensing (PHASE_2A)
- Implement Vault integration
- HMAC-SHA256 signing for activation requests
- Offline activation for air-gapped machines

### Phase 2: Web Backend (PHASE_7)
- Implement Nexus proxy authentication
- Stripe webhook → Ledger integration
- 90/10 revenue split automation

### Phase 3: Telemetry (PHASE_5)
- Implement Pulse ingest client
- Consent UI (UX_003)
- Test anonymization pipeline

### Phase 4: Monitoring (PHASE_11)
- Dashboard showing Ledger revenue splits
- Pulse analytics dashboard
- Nexus user growth metrics

---

## Operational Responsibilities

| Service | Owner | Monitoring |
|---------|-------|-----------|
| Vault | DevOps | GitHub Actions secret rotation |
| Nexus | Identity Team | User auth failures, JWT issues |
| Ledger | Finance/DevOps | Transaction posting, reconciliation |
| Pulse | Data/ML Team | Ingest latency, data quality |

---

## FAQ

**Q: Can we opt-out of the 90/10 split?**
A: No. This is a company mandate across all Forge products. It ensures infrastructure is funded fairly.

**Q: What if Nexus goes down?**
A: NGN 2.0 backend becomes unavailable (cannot authenticate users). Offline mode (license file) works. Plan for high availability.

**Q: Can users delete their Pulse data?**
A: Yes. Users can request deletion; the data is anonymized/removed. No Pulse data can be tied back to them after anonymization.

**Q: Is telemetry selling user data?**
A: No. Data is anonymized, never sold, and only used to improve our products. Privacy-first approach.

**Q: Can other products use G-Fleet?**
A: Yes. This is the entire point. Any Graylight product can integrate with these services.

---

## Next Document: API Specifications

For implementation details, see **Document 12 - NGN Clarity API Specifications**, which contains:
- Exact API endpoints for all G-Fleet services
- Request/response schemas
- Authentication methods
- Error handling
- Code examples

---

**Document Status**: Complete
**Next Review**: After PHASE_2A completion
**Maintained By**: Graylight Engineering Team
