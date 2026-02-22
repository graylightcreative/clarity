#!/usr/bin/env python3
"""
NGN Clarity - Feature Extractor
BRAIN_003: Extract 64-float fingerprint from normalized audio

The 64-Float Fingerprint (Audio DNA):
  ┌─ Spectral (15 features)
  │  ├─ Spectral Centroid (1)
  │  ├─ Spectral Flatness (1)
  │  ├─ MFCC Coefficients (13)
  │
  ├─ Dynamics (3 features)
  │  ├─ Crest Factor (peak/RMS)
  │  ├─ RMS Energy
  │  └─ Peak-to-Average Ratio
  │
  ├─ Temporal (2 features)
  │  ├─ Zero Crossing Rate
  │  └─ Onset Strength
  │
  ├─ Loudness (2 features)
  │  ├─ Integrated LUFS
  │  └─ Loudness Range (LRA)
  │
  └─ Padding (42 features for future expansion)

Total: 64 floats per audio sample
Format: NumPy array or CSV row

Output:
  /data/features/features.csv (all samples)
  /data/features/features.pkl (binary format)
"""

import os
import numpy as np
import librosa
import pandas as pd
from pathlib import Path
import json
import argparse
from tqdm import tqdm
import pickle

try:
    import pyloudnorm
except ImportError:
    print("Installing pyloudnorm...")
    os.system("pip install pyloudnorm")
    import pyloudnorm


class FeatureExtractor:
    def __init__(self, sr=48000, n_mfcc=13, base_dir="Python_Training"):
        """
        Initialize feature extractor.

        Args:
            sr: Sample rate (48000 Hz standard)
            n_mfcc: Number of MFCC coefficients (13 is standard)
            base_dir: Base directory for Python_Training
        """
        self.sr = sr
        self.n_mfcc = n_mfcc
        self.base_dir = Path(base_dir)
        self.processed_dir = self.base_dir / "data" / "processed"
        self.features_dir = self.base_dir / "data" / "features"
        self.features_dir.mkdir(parents=True, exist_ok=True)

        # Initialize loudness meter
        self.meter = pyloudnorm.Meter(sr=self.sr)

        # Feature column names
        self.feature_names = self._get_feature_names()

    def _get_feature_names(self):
        """Generate feature column names for the 64-float fingerprint."""
        names = []

        # Spectral features (15)
        names.append("spectral_centroid")
        names.append("spectral_flatness")
        for i in range(self.n_mfcc):
            names.append(f"mfcc_{i:02d}")

        # Dynamics features (3)
        names.append("crest_factor")
        names.append("rms_energy")
        names.append("peak_to_avg_ratio")

        # Temporal features (2)
        names.append("zero_crossing_rate")
        names.append("onset_strength")

        # Loudness features (2)
        names.append("integrated_lufs")
        names.append("loudness_range")

        # Padding for future features (42)
        for i in range(42):
            names.append(f"reserved_{i:02d}")

        return names

    def extract_spectral_features(self, audio):
        """Extract spectral features (15 floats)."""
        # Spectral centroid (brightness)
        centroid = librosa.feature.spectral_centroid(y=audio, sr=self.sr)[0]
        centroid_mean = np.mean(centroid)

        # Spectral flatness (noisy vs tonal)
        S = np.abs(librosa.stft(audio))
        flatness = np.mean(librosa.feature.spectral_flatness(S=S))

        # MFCC (13 coefficients)
        mfcc = librosa.feature.mfcc(y=audio, sr=self.sr, n_mfcc=self.n_mfcc)
        mfcc_mean = np.mean(mfcc, axis=1)

        features = [centroid_mean, flatness]
        features.extend(mfcc_mean)

        return np.array(features)

    def extract_dynamics_features(self, audio):
        """Extract dynamics features (3 floats)."""
        # RMS energy
        rms = librosa.feature.rms(y=audio)[0]
        rms_mean = np.mean(rms)

        # Peak level
        peak = np.max(np.abs(audio))

        # Crest factor (peak / RMS)
        if rms_mean > 0:
            crest_factor = peak / rms_mean
        else:
            crest_factor = 0.0

        # Peak-to-average ratio
        if np.mean(np.abs(audio)) > 0:
            peak_to_avg = peak / np.mean(np.abs(audio))
        else:
            peak_to_avg = 0.0

        return np.array([crest_factor, rms_mean, peak_to_avg])

    def extract_temporal_features(self, audio):
        """Extract temporal features (2 floats)."""
        # Zero crossing rate (noisy vs pitched)
        zcr = librosa.feature.zero_crossing_rate(audio)[0]
        zcr_mean = np.mean(zcr)

        # Onset strength (transient clarity)
        S = np.abs(librosa.stft(audio))
        onset_env = librosa.onset.onset_strength(S=S, sr=self.sr)
        onset_strength = np.mean(onset_env)

        return np.array([zcr_mean, onset_strength])

    def extract_loudness_features(self, audio):
        """Extract loudness features (2 floats)."""
        # Integrated loudness (LUFS)
        loudness = self.meter.integrated_loudness(audio)
        if loudness is None or np.isnan(loudness):
            loudness = -30.0  # Assume very quiet if unmeasurable

        # Loudness range (LRA) - difference between 10th and 90th percentile
        loudness_array = librosa.feature.melspectrogram(y=audio, sr=self.sr)
        loudness_array_db = librosa.power_to_db(loudness_array)
        loudness_values = np.mean(loudness_array_db, axis=0)

        if len(loudness_values) > 10:
            lra = np.percentile(loudness_values, 90) - np.percentile(loudness_values, 10)
        else:
            lra = 0.0

        return np.array([loudness, lra])

    def extract_features(self, audio_path):
        """
        Extract complete 64-float fingerprint from audio file.

        Args:
            audio_path: Path to normalized WAV file

        Returns:
            np.array: 64-float feature vector
        """
        try:
            # Load audio
            audio, sr = librosa.load(str(audio_path), sr=self.sr)

            # Extract feature groups
            spectral = self.extract_spectral_features(audio)  # 15
            dynamics = self.extract_dynamics_features(audio)  # 3
            temporal = self.extract_temporal_features(audio)  # 2
            loudness = self.extract_loudness_features(audio)  # 2

            # Combine features (22 total so far)
            features = np.concatenate([spectral, dynamics, temporal, loudness])

            # Pad to 64 floats with zeros
            if len(features) < 64:
                padding = np.zeros(64 - len(features))
                features = np.concatenate([features, padding])

            return features

        except Exception as e:
            print(f"  ✗ Error extracting features from {audio_path.name}: {e}")
            return None

    def extract_dataset(self):
        """
        Extract features from all processed audio files.
        Outputs to CSV and PKL formats.
        """
        print(f"\n🔍 Extracting 64-float fingerprints...")

        all_features = []
        all_metadata = []

        for subset in ["gold", "flawed"]:
            print(f"\n{subset.upper()} SET:")
            subset_dir = self.processed_dir / subset

            if not subset_dir.exists():
                print(f"  ⚠️  {subset_dir} not found, skipping")
                continue

            for instrument in ["drums", "vocals", "bass", "guitar", "keys"]:
                instrument_dir = subset_dir / instrument
                if not instrument_dir.exists():
                    continue

                wav_files = list(instrument_dir.glob("*.wav"))
                if not wav_files:
                    continue

                print(f"  {instrument} ({len(wav_files)} files):")

                with tqdm(wav_files, desc=f"  Extracting {instrument}") as pbar:
                    for wav_file in pbar:
                        features = self.extract_features(wav_file)

                        if features is not None:
                            all_features.append(features)
                            all_metadata.append({
                                "file": wav_file.name,
                                "subset": subset,
                                "instrument": instrument,
                                "path": str(wav_file),
                            })

        # Convert to DataFrame
        df = pd.DataFrame(all_features, columns=self.feature_names)

        # Add metadata columns
        for key in ["file", "subset", "instrument"]:
            df.insert(0, key, [m[key] for m in all_metadata])

        # Save to CSV
        csv_path = self.features_dir / "features.csv"
        df.to_csv(csv_path, index=False)
        print(f"\n✅ CSV saved: {csv_path}")

        # Save to PKL (binary format, faster loading)
        pkl_path = self.features_dir / "features.pkl"
        with open(pkl_path, "wb") as f:
            pickle.dump(df, f)
        print(f"✅ PKL saved: {pkl_path}")

        # Save metadata
        metadata = {
            "total_samples": len(all_features),
            "feature_count": 64,
            "feature_names": self.feature_names,
            "sample_rate": self.sr,
            "n_mfcc": self.n_mfcc,
            "breakdown": {
                "spectral": 15,
                "dynamics": 3,
                "temporal": 2,
                "loudness": 2,
                "reserved": 42,
            }
        }

        meta_path = self.features_dir / "feature_metadata.json"
        with open(meta_path, "w") as f:
            json.dump(metadata, f, indent=2)
        print(f"✅ Metadata saved: {meta_path}")

        print(f"\n📊 Feature Extraction Summary:")
        print(f"   Total samples: {len(all_features)}")
        print(f"   Features per sample: 64")
        print(f"   Total floats: {len(all_features) * 64:,}")

        return df

    def analyze_feature_distribution(self, df):
        """
        Analyze feature distributions to identify outliers.
        """
        print("\n📈 Analyzing feature distributions...")

        numeric_cols = self.feature_names
        stats = df[numeric_cols].describe()

        # Check for NaN values
        nan_count = df[numeric_cols].isna().sum().sum()
        if nan_count > 0:
            print(f"  ⚠️  {nan_count} NaN values found in features")

        # Check for infinite values
        inf_count = np.isinf(df[numeric_cols]).sum().sum()
        if inf_count > 0:
            print(f"  ⚠️  {inf_count} infinite values found in features")

        print(f"  ✓ Features analyzed")


def main():
    parser = argparse.ArgumentParser(description="NGN Clarity Feature Extractor")
    parser.add_argument("--extract", action="store_true", help="Extract features from processed audio")
    parser.add_argument("--analyze", action="store_true", help="Analyze feature distributions")
    parser.add_argument("--all", action="store_true", help="Run all steps")

    args = parser.parse_args()

    extractor = FeatureExtractor()

    if args.extract or args.all:
        df = extractor.extract_dataset()

    if args.analyze or args.all:
        if 'df' not in locals():
            df = pd.read_csv(extractor.features_dir / "features.csv")
        extractor.analyze_feature_distribution(df)

    print("\n✅ Feature extraction pipeline complete")


if __name__ == "__main__":
    main()
