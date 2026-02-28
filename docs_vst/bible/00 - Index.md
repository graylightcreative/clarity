# NGN Clarity - Complete Technical Bible

## Overview
NGN Clarity is a VST3 plugin that analyzes mixing tracks against AI-learned targets and teaches users how to fix issues using DAW stock plugins. This bible documents the complete technical architecture, implementation roadmap, and integration with Graylight Creative's sovereign G-Fleet infrastructure.

## Document Index

### Core Architecture
- **00 - Index.md** (this file) — Navigation and project overview
- **01 - Project Overview & Roadmap.md** — High-level project goals, timeline, and phase breakdown
- **02 - Audio Processing Architecture.md** — DSP, ONNX Runtime, and AI inference pipeline
- **03 - GUI & User Experience.md** — UI framework, components, and user workflows
- **04 - Tutorial System & Onboarding.md** — Interactive learning system and mentor workflows
- **05 - Testing & Quality Assurance.md** — Testing strategy, coverage, and beta process

### Infrastructure & Deployment
- **06 - Licensing Strategy.md** — Machine fingerprinting, trial management, and activation flows
- **07 - Installer & Distribution.md** — Windows/macOS installers, code signing, and auto-updates
- **08 - Licensing Strategy.md** — Anti-piracy measures and offline activation
- **09 - Web Infrastructure.md** — Backend API, frontend portal, and database schema
- **10 - aapanel Deployment & Subscription Setup.md** — Server configuration, backups, and cron jobs

### G-Fleet Integration (NEW)
- **11 - Graylight G-Fleet Integration.md** — Overview of Vault, Nexus, Ledger, and Pulse integration
- **12 - NGN 2.0 API Specifications.md** — Exact endpoints, request/response formats, and implementation details

## Project Status
- **Version**: 0.1.0
- **Status**: Planning Phase (PHASE_0 - Project Setup & Infrastructure)
- **Timeline**: 26 weeks (~6 months)
- **Total Phases**: 12
- **Total Tasks**: 150+

## Phase Breakdown

### Original Core Phases (Weeks 1-17)
| Phase | Name | Duration | Status |
|-------|------|----------|--------|
| PHASE_0 | Project Setup & Infrastructure | 1 week | Pending |
| PHASE_1 | DSP Core & AI Inference | 4 weeks | Pending |
| PHASE_2 | GUI & Visualization | 3 weeks | Pending |
| PHASE_2A | Licensing Integration | 2 weeks | Pending |
| PHASE_3 | Audio Analysis Engine | 2 weeks | Pending |
| PHASE_4 | Tutorial & Onboarding | 3 weeks | Pending |
| PHASE_5 | Beta Testing & Optimization | 2 weeks | Pending |

### G-Fleet & Web Infrastructure Phases (Weeks 12-26)
| Phase | Name | Duration | Status |
|-------|------|----------|--------|
| PHASE_9 | Database (PostgreSQL, Redis) | 1 week | Pending |
| PHASE_7 | Web Backend API | 3 weeks | Pending |
| PHASE_8 | Web Frontend (Customer Portal) | 2 weeks | Pending |
| PHASE_10 | Installer & Distribution | 1 week | Pending |
| PHASE_11 | Email, Admin, Monitoring, Security | 1 week | Pending |

## Key Technologies

### Plugin Development
- **Language**: C++17
- **Framework**: JUCE 7.0+
- **DSP**: ONNX Runtime (AI inference)
- **Build System**: CMake 3.25+
- **DAW Target**: Studio One Pro 6.5+

### Web Infrastructure
- **Backend**: Node.js 18+ (Express.js)
- **Frontend**: React 18+ or Vue 3+
- **Database**: PostgreSQL 14+
- **Cache**: Redis 7+
- **Server**: aapanel (Nginx, PM2)

### G-Fleet Services
- **Vault**: Secrets management and HMAC-SHA256 key storage
- **Nexus**: Identity/SSO for unified user experience
- **Ledger**: Financial tracking and 90/10 split mandate
- **Pulse**: Telemetry and analytics for A-OS training

## Critical Path Dependencies

Before starting implementation:

### Pre-PHASE_0
- [ ] Graylight Infrastructure access (Vault, Nexus, Ledger, Pulse)
- [ ] GitHub Actions setup for CI/CD
- [ ] JUCE 7.0+ installed locally

### Pre-PHASE_2A (Licensing)
- [ ] Windows Authenticode code signing certificate
- [ ] macOS Developer ID certificate
- [ ] Vault integration complete

### Pre-PHASE_7 (Web Backend)
- [ ] Domain registration (nextgennoise.com or similar)
- [ ] AWS/DigitalOcean account
- [ ] Stripe merchant account
- [ ] Nexus tenant configuration
- [ ] Ledger account setup

### Pre-PHASE_9 (Database)
- [ ] PostgreSQL 14+ instance provisioned
- [ ] Redis 7+ instance provisioned
- [ ] aapanel server configured

## Quick Navigation

**For Implementers**: Start with PHASE_0 (Project Setup & Infrastructure), then proceed sequentially through PHASE_1-5 (core plugin), integrating PHASE_2A (licensing) in parallel with PHASE_3.

**For Infrastructure**: Jump to Section G-Fleet Integration (Docs 11-12) for API specifications and integration details.

**For Release Planning**: See PHASE_10 (Installer & Distribution) and PHASE_11 (Email, Admin, Monitoring).

---

**Last Updated**: February 11, 2025
**Next Update**: After PHASE_0 completion
