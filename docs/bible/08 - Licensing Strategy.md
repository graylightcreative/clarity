# Licensing Strategy: NextGen Noise VST Plugin

## 1. Overview

The Mixing Mentor VST plugin requires a comprehensive licensing system to:
- Protect intellectual property (AI models, advice generation)
- Generate revenue (one-time purchases)
- Track activations (prevent unauthorized distribution)
- Provide trial period (14 days to evaluate)
- Support offline activation (for users without internet)

## 2. VST Plugin-Side Architecture (C++)

### 2.1 Machine Fingerprinting

**Purpose**: Generate unique hardware ID to prevent license sharing

**Implementation**:
```
Source/Licensing/MachineFingerprint.h
- GetHardwareID(): string
  - Windows: Combine CPU serial (WMI), MAC address, HDD serial
  - macOS: Combine IOKit hardware identifiers
  - Returns: SHA-256 hash of combined values
```

**VM Detection**:
- Detect Hyper-V, VMware, VirtualBox
- Flag virtual machines (warning to user, but allow activation)

**Data Flow**:
```
MachineFingerprint::GetHardwareID()
  -> Calls OS-specific APIs (WMI on Windows, IOKit on macOS)
  -> Returns consistent 64-char hex string
  -> Passed to LicenseManager for validation
```

### 2.2 Trial Period Management

**Purpose**: Allow 14-day free trial from first launch

**Implementation**:
```
Source/Licensing/TrialManager.h
- StartTrial(): void (saves start date)
- GetTrialDaysRemaining(): int
- IsTrialExpired(): bool
- GetTrialStartDate(): juce::Time
```

**Storage**:
- **Windows**: Registry (HKEY_CURRENT_USER\Software\NextGenNoise\Trial)
  - Store encrypted: `trial_start_date` (AES-256)
  - Key: machine fingerprint (prevents copying between machines)

- **macOS**: Keychain
  - Store encrypted: `trial_start_date`
  - Key: machine fingerprint

**Feature Locking**:
- If trial expired and no license: disable AI features
- Show "Purchase" dialog with link to nextgennoise.com/buy

### 2.3 Online Activation

**Purpose**: Validate license key and activate machine

**Endpoint**: `POST https://api.nextgennoise.com/api/v1/license/activate`

**Request**:
```json
{
  "license_key": "NG-XXXX-XXXX-XXXX",
  "hardware_id": "a1b2c3d4e5f6...",
  "product": "mixing_mentor",
  "plugin_version": "1.0.0"
}
```

**Response**:
```json
{
  "status": "success",
  "activation_token": "token_xyz...",
  "license_type": "standard",
  "max_activations": 2,
  "activations_remaining": 1,
  "expires_at": "2026-02-05T00:00:00Z"
}
```

**Error Handling**:
- Invalid license key: Show "License Invalid" dialog
- Max activations exceeded: Show "License activated on 2 other machines" dialog
- Network error: Retry with exponential backoff (1s, 2s, 4s, 8s, 16s)
- No internet: Fall back to offline activation or cached validation

### 2.4 Offline Activation

**Purpose**: Activate without internet connection

**Request File (.ngn_req)**:
```json
{
  "license_key": "NG-XXXX-XXXX-XXXX",
  "hardware_id": "a1b2c3d4e5f6...",
  "plugin_version": "1.0.0",
  "request_date": "2025-02-05T12:00:00Z"
}
```
- Signed with RSA private key (encrypted payload)
- User generates file in plugin UI → uploads to nextgennoise.com/activate → downloads .ngn_lic response

**License File (.ngn_lic)**:
```json
{
  "license_key": "NG-XXXX-XXXX-XXXX",
  "hardware_id": "a1b2c3d4e5f6...",
  "license_type": "standard",
  "expires_at": "2026-02-05T00:00:00Z",
  "signature": "RSA_SIGNATURE_HERE"
}
```
- Stored in: `%APPDATA%\NextGenNoise\` (Windows) or `~/Library/Application Support/NextGenNoise/` (macOS)
- Verified with RSA public key before use
- Valid for 365 days (re-validate annually)

### 2.5 License UI Dialogs

**Activation Dialog**:
- Input field: License key
- Toggle: Online vs. Offline mode
- Button: "Activate"
- Link: "Don't have a license? Buy here"

**Trial Warning Dialog** (shown at launch):
- "Trial: 3 days remaining"
- "Purchase to continue"
- Button: "Buy License" (opens nextgennoise.com/buy)

**License Expired Dialog**:
- "Your license has expired"
- "Renew" button or date to re-purchase

### 2.6 Anti-Piracy Measures

**Debugger Detection**:
- Windows: `IsDebuggerPresent()` check
- macOS: `sysctl("kern.bootargs")` to detect debugger flag
- If detected: Log warning, disable real-time analysis (still allow basic functions)

**Code Obfuscation**:
- Apply obfuscation to `LicenseManager.cpp` (rename variables, split logic)
- Consider using third-party obfuscator (e.g., LLVM passes)

**Periodic Re-validation**:
- Every 7 days: ping `api.nextgennoise.com/api/v1/license/status`
- If server returns invalid: lock features until valid
- Cache result locally for 7 days (allow offline use)

**Tamper Detection**:
- Store license file checksum (SHA-256 of contents)
- At startup: verify checksum matches
- If mismatch: invalidate license, show error

## 3. Licensing Integration Points

### 3.1 Plugin Processor Constructor

```cpp
MixingMentorAudioProcessor::MixingMentorAudioProcessor() {
  // 1. Check trial status
  if (TrialManager::isTrialExpired() && !LicenseManager::hasValidLicense()) {
    setProperty("license_status", "trial_expired");
  }

  // 2. Validate existing license
  LicenseManager::validate();

  // 3. Attempt online re-validation if 7 days have passed
  if (shouldRevalidate()) {
    LicenseManager::revalidateOnline();
  }
}
```

### 3.2 UI Status Display

```cpp
// In HeaderComponent.cpp
String licenseStatus = getLicenseStatus();
// "Licensed to: John Doe" or "Trial: 10 days remaining" or "Evaluation Mode"

if (isPurchaseDialog()) {
  showPurchaseButton();  // Links to nextgennoise.com/buy
}
```

## 4. Security Implementation

### 4.1 Encryption

- **AES-256**: Encrypt trial start date in registry/keychain
- **RSA-2048**: Sign offline activation request/response files
- **SHA-256**: Hash hardware ID, verify license file integrity

### 4.2 Error Handling

- **Network Timeout**: Retry with exponential backoff
- **Invalid License**: Show clear error, suggest purchase
- **Max Activations**: Allow user to deactivate old machine from portal
- **License Expired**: Remind user to renew

## 5. User Workflows

### 5.1 Trial User

1. First launch → LicenseManager detects no license
2. Show "Start 14-Day Trial" dialog
3. Click "Start Trial" → LicenseManager saves trial start date
4. User gets 14 days of full access
5. Day 13 → Show "2 days remaining" warning
6. Day 14 → Show "Trial Expired" dialog with purchase link

### 5.2 Purchased User (Online Activation)

1. User purchases on nextgennoise.com
2. Receives email with license key (e.g., "NG-XXXX-XXXX-XXXX")
3. Opens plugin, sees "Activate License" dialog
4. Enters license key, clicks "Activate Online"
5. Plugin sends request to `api.nextgennoise.com/activate`
6. Server validates key, increments activation count
7. Plugin receives activation token, saves to encrypted storage
8. User gets message: "License activated! Enjoy mixing."

### 5.3 Purchased User (Offline Activation)

1. User purchases, receives license key
2. Opens plugin, sees "Activate License" dialog
3. Clicks "Offline Mode" toggle
4. Plugin generates `.ngn_req` file
5. User uploads file to nextgennoise.com/activate
6. Server generates `.ngn_lic` response file
7. User downloads `.ngn_lic`, imports into plugin
8. Plugin validates signature, activates

### 5.4 Deactivating Machine

1. User has 2 machines, both activated
2. Buys new machine, tries to activate
3. Gets "Max activations (2) reached" error
4. Logs into nextgennoise.com/dashboard
5. Goes to "Licenses" tab, sees 3 activations:
   - "Studio Desktop (Windows) - Feb 5, 2025"
   - "Laptop (macOS) - Jan 28, 2025"
   - "New Machine (Windows) - Feb 5, 2025" (pending)
6. Clicks "Deactivate" on "Laptop"
7. Returns to plugin, clicks "Retry Activation"
8. Activation succeeds

## 6. API Endpoints (Backend)

See `09 - Web Infrastructure.md` for full API specifications.

Key endpoints:
- `POST /api/v1/license/activate` - Activate license
- `GET /api/v1/license/status` - Check license status
- `POST /api/v1/license/deactivate` - Deactivate machine

## 7. Testing Strategy

### 7.1 Unit Tests

```cpp
// Test machine fingerprinting
TEST(MachineFingerprint, ConsistentID) {
  string id1 = MachineFingerprint::GetHardwareID();
  string id2 = MachineFingerprint::GetHardwareID();
  EXPECT_EQ(id1, id2);  // Should be identical
}

// Test trial expiration
TEST(TrialManager, ExpirationLogic) {
  TrialManager::StartTrial();
  EXPECT_FALSE(TrialManager::IsTrialExpired());

  // Mock system clock 15 days forward
  EXPECT_TRUE(TrialManager::IsTrialExpired());
}

// Test license validation
TEST(LicenseManager, ValidateOfflineLicense) {
  // Load .ngn_lic file, verify signature
  EXPECT_TRUE(LicenseManager::ValidateOfflineFile("test.ngn_lic"));
}
```

### 7.2 Integration Tests

- Test online activation flow with mock server
- Test offline activation flow (generate/parse files)
- Test license expiration scenarios
- Test network failure handling (retry logic)

### 7.3 Manual Tests

- Test on Windows 10/11 VMs
- Test on macOS VMs (both Intel and Apple Silicon)
- Test debugger detection
- Test license file tampering detection

## 8. Legal & Compliance

- **EULA**: Users accept terms when activating license
- **Privacy**: Hardware ID is hashed, not stored with PII
- **Data Retention**: Activation logs kept for 1 year
- **GDPR**: Users can request data deletion via portal

## 9. Future Enhancements

- **Subscription Model**: Add monthly/yearly subscription option
- **Offline-Only**: Support perpetual offline licenses (no re-validation)
- **Site Licenses**: Support floating licenses (network-locked)
- **Demo Mode**: Limited feature set without license (5 minutes per use)
