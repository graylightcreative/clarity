#!/usr/bin/env python3
"""
NGN Clarity - Loudness Normalizer
BRAIN_004: Normalize all audio to -18 LUFS for gain-independent analysis

Purpose:
  Ensure all audio samples are normalized to -18 LUFS (integrated loudness)
  so that feature extraction is independent of level/gain.

Why -18 LUFS:
  - Professional mixing standard for broadcast and streaming
  - Prevents feature extraction from being fooled by level differences
  - Ensures the model learns about tonal quality, not just volume

Output:
  /data/processed/gold/
  /data/processed/flawed/
  (Same structure as /data/raw/, but with normalized audio)
"""

import os
import numpy as np
import librosa
import soundfile as sf
from pathlib import Path
import json
import argparse
from tqdm import tqdm

try:
    import pyloudnorm
except ImportError:
    print("Installing pyloudnorm...")
    os.system("pip install pyloudnorm")
    import pyloudnorm


class LoudnessNormalizer:
    def __init__(self, target_lufs=-18.0, base_dir="Python_Training"):
        """
        Initialize loudness normalizer.

        Args:
            target_lufs: Target integrated loudness in LUFS (-18.0 is standard)
            base_dir: Base directory for Python_Training
        """
        self.target_lufs = target_lufs
        self.base_dir = Path(base_dir)
        self.raw_dir = self.base_dir / "data" / "raw"
        self.processed_dir = self.base_dir / "data" / "processed"

        # Initialize loudness meter (ITU-R BS.1770-4 standard)
        self.meter = pyloudnorm.Meter(sr=48000)  # Assumes 48kHz sample rate

    def create_output_structure(self):
        """Create /data/processed directory structure matching /data/raw."""
        print("📁 Creating processed data directories...")

        for subset in ["gold", "flawed"]:
            for instrument in ["drums", "vocals", "bass", "guitar", "keys"]:
                output_dir = self.processed_dir / subset / instrument
                output_dir.mkdir(parents=True, exist_ok=True)
                print(f"  ✓ {output_dir}")

    def normalize_file(self, input_path, output_path):
        """
        Normalize a single audio file to target LUFS.

        Args:
            input_path: Path to input WAV file
            output_path: Path to output normalized WAV file

        Returns:
            dict: Normalization metadata (original LUFS, gain applied, etc.)
        """
        try:
            # Load audio at 48kHz
            audio, sr = librosa.load(str(input_path), sr=48000)

            # Measure loudness
            loudness = self.meter.integrated_loudness(audio)

            # Handle cases where loudness measurement fails
            if loudness is None or np.isnan(loudness):
                print(f"    ⚠️  Could not measure loudness: {input_path.name}")
                # Fall back to simple RMS normalization
                loudness = -30.0  # Assume very quiet

            # Normalize to target LUFS
            normalized_audio = pyloudnorm.normalize(audio, loudness, self.target_lufs)

            # Prevent clipping
            max_val = np.max(np.abs(normalized_audio))
            if max_val > 0.99:
                print(f"    ⚠️  Clipping detected, reducing gain: {input_path.name}")
                normalized_audio = normalized_audio * (0.99 / max_val)

            # Save normalized audio
            sf.write(str(output_path), normalized_audio, sr, subtype="PCM_24")

            metadata = {
                "file": input_path.name,
                "original_lufs": float(loudness) if loudness is not None else -30.0,
                "target_lufs": self.target_lufs,
                "gain_applied_db": float(self.target_lufs - (loudness if loudness is not None else -30.0)),
                "peak_level": float(max_val),
                "clipped": bool(max_val > 0.99),
            }

            return metadata

        except Exception as e:
            print(f"    ✗ Error normalizing {input_path.name}: {e}")
            return None

    def normalize_dataset(self):
        """
        Normalize all audio files in /data/raw to /data/processed.
        """
        print(f"\n🔊 Normalizing audio to {self.target_lufs} LUFS...")

        normalization_log = []

        for subset in ["gold", "flawed"]:
            print(f"\n{subset.upper()} SET:")
            subset_dir = self.raw_dir / subset

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

                with tqdm(wav_files, desc=f"  Normalizing {instrument}") as pbar:
                    for input_file in pbar:
                        output_file = self.processed_dir / subset / instrument / input_file.name

                        metadata = self.normalize_file(input_file, output_file)

                        if metadata:
                            normalization_log.append(metadata)
                            pbar.set_postfix({
                                "lufs": f"{metadata['target_lufs']:.1f}",
                                "gain": f"{metadata['gain_applied_db']:.1f}dB"
                            })

        # Save normalization log
        log_path = self.processed_dir / "normalization_log.json"
        with open(log_path, "w") as f:
            json.dump(normalization_log, f, indent=2)

        print(f"\n✅ Normalization complete")
        print(f"   Files processed: {len(normalization_log)}")
        print(f"   Log saved: {log_path}")

        return normalization_log

    def verify_normalization(self):
        """
        Verify that processed files are at target LUFS.
        """
        print("\n✔️  Verifying normalization...")

        for subset in ["gold", "flawed"]:
            subset_dir = self.processed_dir / subset

            if not subset_dir.exists():
                continue

            for instrument in ["drums", "vocals", "bass", "guitar", "keys"]:
                instrument_dir = subset_dir / instrument
                wav_files = list(instrument_dir.glob("*.wav"))

                if not wav_files:
                    continue

                loudnesses = []
                for wav_file in wav_files:
                    audio, sr = librosa.load(str(wav_file), sr=48000)
                    loudness = self.meter.integrated_loudness(audio)
                    if loudness is not None:
                        loudnesses.append(loudness)

                if loudnesses:
                    avg_loudness = np.mean(loudnesses)
                    std_loudness = np.std(loudnesses)
                    print(f"  {subset}/{instrument}: "
                          f"{avg_loudness:.1f}±{std_loudness:.1f} LUFS "
                          f"(n={len(loudnesses)})")


def main():
    parser = argparse.ArgumentParser(description="NGN Clarity Loudness Normalizer")
    parser.add_argument("--create-dirs", action="store_true", help="Create output directories")
    parser.add_argument("--normalize", action="store_true", help="Normalize audio files")
    parser.add_argument("--verify", action="store_true", help="Verify normalization")
    parser.add_argument("--target-lufs", type=float, default=-18.0, help="Target loudness in LUFS")
    parser.add_argument("--all", action="store_true", help="Run all steps")

    args = parser.parse_args()

    normalizer = LoudnessNormalizer(target_lufs=args.target_lufs)

    if args.create_dirs or args.all:
        normalizer.create_output_structure()

    if args.normalize or args.all:
        normalizer.normalize_dataset()

    if args.verify or args.all:
        normalizer.verify_normalization()

    print("\n✅ Loudness normalization pipeline complete")


if __name__ == "__main__":
    main()
