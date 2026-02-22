# NGN Clarity - Python Training Pipeline
## PHASE_1_A: Dataset Generation & Feature Extraction

**Mission**: Build training data and extract the 64-float audio DNA fingerprints

---

## 📊 Pipeline Overview

```
1. Data Ingestion
   ├─ Gold Set (Clarity library + professional references)
   └─ Flawed Set (Suno AI synthetic variations)
           ↓
2. Loudness Normalization
   └─ All audio normalized to -18 LUFS (gain-independent)
           ↓
3. Feature Extraction
   └─ Extract 64-float fingerprint (DNA) from each sample
           ↓
4. Output
   └─ CSV/PKL ready for model training
```

---

## 🚀 Quick Start

### Install Dependencies

```bash
pip install librosa soundfile numpy pandas scikit-learn pyloudnorm tqdm
```

### Run Complete Pipeline

```bash
python run_pipeline.py
```

Or run individual steps:

```bash
# Step 1: Initialize directory structure
python dataset_generator.py --all

# Step 2: Normalize to -18 LUFS
python loudness_normalizer.py --all

# Step 3: Extract features
python feature_extractor.py --all
```

---

## 📁 Directory Structure

### Input: Raw Audio

```
Python_Training/data/raw/
├── gold/               # Professional references
│   ├── drums/
│   │   ├── kick_001.wav
│   │   ├── kick_002.wav
│   │   └── ...
│   ├── vocals/
│   ├── bass/
│   ├── guitar/
│   └── keys/
│
└── flawed/             # Intentionally flawed (from Suno AI)
    ├── drums/
    │   ├── kick_flawed_muddy_001.wav
    │   ├── kick_flawed_harsh_001.wav
    │   └── ...
    ├── vocals/
    ├── bass/
    ├── guitar/
    └── keys/
```

### Processing: Normalized Audio

```
Python_Training/data/processed/
├── gold/               # Normalized to -18 LUFS
│   ├── drums/
│   ├── vocals/
│   └── ...
│
└── flawed/             # Normalized to -18 LUFS
    ├── drums/
    ├── vocals/
    └── ...
```

### Output: Features

```
Python_Training/data/features/
├── features.csv              # 64-column CSV (all samples)
├── features.pkl              # Binary format (faster)
├── feature_metadata.json     # Feature descriptions
└── normalization_log.json    # Loudness measurements
```

---

## 🧬 The 64-Float Fingerprint

Each audio sample is reduced to a 64-float vector representing its "DNA":

| Category | Features | Count | Description |
|----------|----------|-------|-------------|
| **Spectral** | Centroid, Flatness, MFCC (0-12) | 15 | Tonal quality, brightness, timbre |
| **Dynamics** | Crest Factor, RMS, Peak-to-Avg | 3 | Punch, compression, life |
| **Temporal** | Zero Crossing, Onset Strength | 2 | Transient clarity, definition |
| **Loudness** | Integrated LUFS, LRA | 2 | Calibrated loudness measures |
| **Reserved** | Future expansion | 42 | For model improvements |
| | | **64** | **Total** |

### Why These Features?

- **Spectral**: Defines mud, harshness, tone quality
- **Dynamics**: Defines life vs over-compression
- **Temporal**: Defines punch and transient clarity
- **Loudness**: Calibrated at -18 LUFS (broadcast standard)

---

## 🤖 Suno AI Generation Strategy

### Target: 100 samples per instrument category

**Instruments** (5 categories):
- Drums (kick, snare, toms)
- Vocals (lead, harmonies)
- Bass
- Guitar (rhythm, lead)
- Keys

**Flaw Variations** (per category):

| Instrument | Flaw Types |
|------------|-----------|
| **Drums** | Muddy, Harsh (3kHz), Over-compressed, Thin, Sloppy timing |
| **Vocals** | Nasal, Boxy, Over-sibilant, Muddy, Over-compressed |
| **Bass** | Undefined, Boomy, Thin, Muddy, Distorted |
| **Guitar** | Muddy, Harsh, Thin, Boxed, Over-compressed |
| **Keys** | Thin, Dark, Harsh, Over-compressed, Ringy |

**Generation Process**:
1. Use Suno AI to generate 20+ variations per flaw type
2. Select best 5 per flaw
3. Place in `/data/raw/flawed/[instrument]/`
4. Dataset generator auto-organizes by filename

---

## 📋 Script Reference

### BRAIN_001: `dataset_generator.py`

**Purpose**: Organize raw audio files

```bash
python dataset_generator.py --init           # Create directory structure
python dataset_generator.py --import-gold    # Import from Clarity library
python dataset_generator.py --prepare-suno   # Generate Suno AI prompts
python dataset_generator.py --organize-flawed # Organize downloaded files
python dataset_generator.py --manifest       # Generate dataset manifest
python dataset_generator.py --validate       # Validate structure
python dataset_generator.py --all            # Run all steps
```

**Output**:
- Directory structure (`/data/raw/gold`, `/data/raw/flawed`)
- `suno_generation_config.json` - Prompts for Suno AI
- `manifest.json` - Dataset statistics

---

### BRAIN_004: `loudness_normalizer.py`

**Purpose**: Normalize all audio to -18 LUFS

Why -18 LUFS?
- Professional streaming standard
- Gain-independent feature extraction
- Prevents model from learning based on level

```bash
python loudness_normalizer.py --create-dirs  # Create /data/processed
python loudness_normalizer.py --normalize    # Normalize all audio
python loudness_normalizer.py --verify       # Verify LUFS levels
python loudness_normalizer.py --all          # Run all steps
```

**Output**:
- Normalized WAV files in `/data/processed/`
- `normalization_log.json` - Before/after LUFS levels

---

### BRAIN_003: `feature_extractor.py`

**Purpose**: Extract 64-float fingerprints

```bash
python feature_extractor.py --extract   # Extract features from all audio
python feature_extractor.py --analyze   # Analyze feature distributions
python feature_extractor.py --all       # Run all steps
```

**Output**:
- `features.csv` - All samples with 64-float features
- `features.pkl` - Binary format (faster loading)
- `feature_metadata.json` - Feature column descriptions

**CSV Format**:
```
file,subset,instrument,spectral_centroid,spectral_flatness,mfcc_00,...,mfcc_12,crest_factor,rms_energy,peak_to_avg_ratio,zero_crossing_rate,onset_strength,integrated_lufs,loudness_range,reserved_00,...,reserved_41
kick_001.wav,gold,drums,1234.5,0.45,-120.3,...,-45.2,2.1,0.08,3.5,0.032,0.42,-18.0,4.2,0.0,...,0.0
kick_flawed_001.wav,flawed,drums,987.2,0.32,-125.1,...,-52.1,1.5,0.05,2.8,0.018,0.31,-18.0,3.1,0.0,...,0.0
...
```

---

## 🔄 Workflow

### Phase 1A: Data Generation (This Week)

1. **Prepare Gold Set**
   - Use existing Clarity audio library
   - Or record professional mixing references
   - Extract STEMS (drums, vocals, bass, guitar, keys separately)

2. **Generate Flawed Set via Suno AI**
   - Run `dataset_generator.py --prepare-suno`
   - Get prompts from `suno_generation_config.json`
   - Use Suno AI to generate 100+ flawed variations per category
   - Download and place in `/data/raw/flawed/`

3. **Normalize & Extract**
   - Run `run_pipeline.py`
   - Waits for `/data/raw/gold/` and `/data/raw/flawed/` to be populated
   - Outputs `features.csv` ready for training

### Phase 1B: Model Training (Next Week)

- Random Forest Regressor on 64-float features
- Predict correction vectors (EQ, compression, etc.)
- Export to ONNX for C++ runtime

---

## 📊 Expected Output

After running complete pipeline:

```
✅ Dataset Summary
   Gold samples:   500 (100 per instrument × 5 categories)
   Flawed samples: 500 (100 per instrument × 5 categories)
   Total samples:  1000
   Total floats:   64,000 (1000 × 64)

✅ Feature Statistics
   Samples with valid features: 1000
   NaN values: 0
   Infinite values: 0
   Ready for training: ✓

✅ Output Files
   features.csv (5 MB)
   features.pkl (8 MB)
```

---

## 🧪 Testing

### Quick Test (5 samples)

```bash
# Create small test set
mkdir -p data/raw/{gold,flawed}/{drums,vocals,bass,guitar,keys}

# Add a few test WAV files to each directory
# Then run pipeline
python run_pipeline.py
```

---

## 🔧 Troubleshooting

### "No gold samples found"
- Ensure audio library is in `Assets/Targets/`
- Or manually place in `Python_Training/data/raw/gold/`

### "Clipping detected" warnings
- Some audio may be too loud
- Normalizer will reduce gain automatically
- Check `normalization_log.json` for details

### "Could not measure loudness"
- Audio file may be corrupted or too short
- Normalizer falls back to -30 LUFS assumption
- Consider re-generating or using different source

### NaN values in features
- May indicate very short audio clips
- Or audio with extreme dynamics
- Inspect problematic files individually

---

## 📈 Next Steps

Once `features.csv` is ready:

1. **Load in Jupyter**
   ```python
   import pandas as pd
   df = pd.read_csv('Python_Training/data/features/features.csv')
   df.shape  # Should be (1000, 67) - 3 metadata + 64 features
   ```

2. **Prepare for training**
   ```python
   X = df.iloc[:, 3:]  # 64 feature columns
   y = df['flawed_type']  # Or correction vector labels
   ```

3. **Train Random Forest**
   - 500 estimators
   - 8 max depth
   - 80% train / 20% test split

4. **Export to ONNX**
   - sklearn-onnx library
   - Load in C++ with ONNX Runtime
   - <5ms inference latency target

---

## 📚 References

- **Librosa**: Audio analysis library
  - [Spectral features](https://librosa.org/doc/main/feature.html)
  - [MFCC](https://en.wikipedia.org/wiki/Mel-frequency_cepstral_coefficient)

- **pyloudnorm**: Loudness normalization (ITU-R BS.1770-4)
  - [-18 LUFS standard](https://en.wikipedia.org/wiki/LUFS)

- **Suno AI**: Music generation
  - [Suno.ai](https://www.suno.ai)

---

## 🎬 Ready?

```bash
python run_pipeline.py
```

**Result**: `features.csv` with 64-float DNA fingerprints for 1000 audio samples

**Status**: 🟢 Ready for Phase 1B (Model Training)

---

**Maintained By**: Graylight Creative - Software Foundry
**Last Updated**: February 11, 2025
