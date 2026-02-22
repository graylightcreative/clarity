# NGN Clarity - PHASE_1_A Checkpoint
## Next Step: Suno AI Data Generation

**Date**: February 11, 2025
**Status**: 🟡 PAUSED - Awaiting User Decision

---

## ✅ What We've Completed

### PHASE_0: Shell Infrastructure (COMPLETE)
- ✅ Git repository initialized and pushed to GitHub
- ✅ Vault secrets configured (GRAYLIGHT_HMAC_SECRET_KEY, FORGE_API_KEY)
- ✅ GitHub Actions CI/CD pipeline live
- ✅ CMake build system ready
- ✅ Complete technical documentation (docs/bible/)

### PHASE_1_A: Data Pipeline Setup (COMPLETE)
- ✅ `dataset_generator.py` - Organizes raw audio (BRAIN_001)
- ✅ `loudness_normalizer.py` - Normalizes to -18 LUFS (BRAIN_004)
- ✅ `feature_extractor.py` - Extracts 64-float fingerprints (BRAIN_003)
- ✅ `run_pipeline.py` - Orchestrates complete workflow
- ✅ `README.md` - Complete documentation

---

## 🎯 Current State: Directory Structure

```
clarity/
├── .env                              # Vault secrets (configured)
├── .github/workflows/build.yml       # CI/CD live
├── README.md
├── CMakeLists.txt                    # Build system ready
├── Source/
│   ├── Licensing/
│   │   ├── HMAC_Keys.h.in           # HMAC template
│   │   ├── HMACKeyManager.h/cpp      # ✅ Implemented
│   │   └── ActivationClient.h/cpp    # ✅ Implemented
│   └── ...
├── Python_Training/
│   ├── dataset_generator.py          # ✅ Ready
│   ├── loudness_normalizer.py        # ✅ Ready
│   ├── feature_extractor.py          # ✅ Ready
│   ├── run_pipeline.py               # ✅ Ready
│   ├── README.md                     # ✅ Complete
│   └── data/
│       ├── raw/
│       │   ├── gold/                 # ⏳ WAITING FOR DATA
│       │   │   ├── drums/
│       │   │   ├── vocals/
│       │   │   ├── bass/
│       │   │   ├── guitar/
│       │   │   └── keys/
│       │   └── flawed/               # ⏳ WAITING FOR SUNO AI
│       │       ├── drums/
│       │       ├── vocals/
│       │       ├── bass/
│       │       ├── guitar/
│       │       └── keys/
│       └── processed/                # Will be created by normalizer
│
└── docs/bible/
    ├── 00 - Index.md                 # ✅ Complete
    ├── 11 - Graylight G-Fleet Integration.md
    └── 12 - NGN Clarity API Specifications.md
```

---

## 🚦 NEXT STEP: Suno AI Data Generation

### Decision Point: Gold Set Source

**Option A: Use Existing Clarity Audio Library**
- ✅ If you have raw stems (drums, vocals, bass, guitar, keys)
- ✅ Faster start
- Action: Place in `/clarity/Python_Training/data/raw/gold/[instrument]/`

**Option B: Record Custom Metalcore/Hard Rock Samples**
- ✅ Higher quality, custom to our target sound
- ⏳ Takes 2-3 days
- Action: Record 5-10 professionally mixed reference tracks

**Option C: Use Public Stem Packs**
- ✅ Quick to acquire
- ✅ Royalty-free options available
- Action: Download and organize into gold/ directory

---

## 🤖 Then: Generate Flawed Set via Suno AI

### What Needs to Happen:

1. **Confirm Gold Set Source** (User Decision)
   - Which option above? A, B, or C?
   - If A: Where is Clarity library located?
   - If B: Timeline for recording?
   - If C: Which stem packs to use?

2. **Generate Suno AI Prompts** (Claude - Ready)
   - Create prompt list for 100+ flawed variations
   - 5 flaw types × 20 variations per instrument × 5 instruments
   - Examples:
     ```
     "Muddy kick drum with heavy low-end mud, no clarity"
     "Harsh snare with 3kHz peak, brittle sounding"
     "Over-compressed drums with no transient punch"
     ```

3. **Generate Audio via Suno AI** (User - Manual)
   - Feed prompts into Suno AI
   - Generate audio clips (WAV, 24-bit, 48kHz)
   - Download and organize into `/data/raw/flawed/[instrument]/`

4. **Run Pipeline** (Claude - Automated)
   - `python run_pipeline.py`
   - Normalizes all audio to -18 LUFS
   - Extracts 64-float fingerprints
   - Outputs `features.csv` (ready for model training)

---

## 📋 Decision Required From You:

**Question 1: Gold Set Source**
```
Which will you use for professional reference mixing samples?
  A) Existing Clarity audio library
  B) Record custom metalcore/hard rock samples
  C) Use public royalty-free stem packs
  D) Other (specify)
```

**Question 2: If A - Clarity Library**
```
Where are the raw stems located?
Example: /Users/brock/Documents/Clarity/stems/
```

**Question 3: Timeline**
```
When can you have:
  • Gold Set ready?
  • Flawed Set generated via Suno AI?
```

---

## 🔄 Once You Decide:

### Immediately After:
1. You provide Gold Set source
2. I generate Suno AI prompt list (5 minutes)
3. You feed prompts into Suno AI and generate audio
4. You download and organize into `/data/raw/flawed/`

### Then (Automated):
```bash
cd clarity/Python_Training
python run_pipeline.py
```

**Output**: `features.csv` with 1000 samples × 64 floats

**Next Phase**: PHASE_1_B (Model Training)
- Random Forest on 64-float features
- Predict correction vectors (EQ, compression, etc.)
- Export to ONNX

---

## ⏱️ Timeline Estimate

| Step | Duration | Who |
|------|----------|-----|
| **Gold Set Acquisition** | 1 day | You |
| **Suno AI Prompt Generation** | 5 min | Claude |
| **Suno AI Audio Generation** | 2-3 days | You (Suno) |
| **Feature Extraction** | 30 min | Claude (automated) |
| **Model Training (PHASE_1_B)** | 3-5 days | Claude |
| **C++ Integration (PHASE_1_D)** | 1 week | Claude |
| **Total PHASE_1** | **2-3 weeks** | |

---

## 📝 Checklist Before Proceeding:

- [ ] Gold Set source decided (A, B, C, or D)
- [ ] Gold Set location confirmed (if applicable)
- [ ] Timeline for audio acquisition understood
- [ ] Ready to feed Suno AI prompts to generate flawed variations
- [ ] Understand pipeline output (features.csv)

---

## 🚀 To Resume:

**Once you decide on Gold Set source**, message:

```
"Ready - Gold Set source is [A/B/C]: [location or details]"
```

Then I will:
1. Generate Suno AI prompt list
2. Provide exact steps for downloading and organizing
3. Stand ready to run pipeline once data is in place

---

## 📚 Reference Files

- **Setup Instructions**: `Python_Training/README.md`
- **Pipeline Orchestration**: `Python_Training/run_pipeline.py`
- **Architecture Overview**: `docs/bible/00 - Index.md`
- **Full Technical Specs**: `docs/bible/12 - NGN Clarity API Specifications.md`

---

**Status**: 🟡 AWAITING USER DECISION ON GOLD SET SOURCE

**Next Action**: Confirm Gold Set acquisition method + timeline
