#!/usr/bin/env python3
"""
NGN Clarity - Dataset Generator
BRAIN_001: Data ingestion and organization pipeline

Purpose:
  Organize raw WAV files from Clarity library and Suno AI generation
  into structured /data/raw/gold and /data/raw/flawed directories.

Structure:
  /data/raw/gold/
    ├── drums/
    │   ├── kick_001.wav
    │   └── ...
    ├── vocals/
    ├── bass/
    ├── guitar/
    └── keys/

  /data/raw/flawed/
    ├── drums/
    │   ├── kick_flawed_001.wav (muddy, harsh, compressed)
    │   └── ...
    └── ...

Target: 100 samples per category (500 total)
"""

import os
import shutil
from pathlib import Path
import argparse
import json
from datetime import datetime

# Instrument categories
INSTRUMENTS = {
    "drums": {
        "sub_categories": ["kick", "snare", "toms", "room"],
        "target_samples": 100,
    },
    "vocals": {
        "sub_categories": ["lead", "harmonies", "backing"],
        "target_samples": 100,
    },
    "bass": {
        "sub_categories": ["bass"],
        "target_samples": 100,
    },
    "guitar": {
        "sub_categories": ["rhythm", "lead", "acoustic"],
        "target_samples": 100,
    },
    "keys": {
        "sub_categories": ["keys"],
        "target_samples": 100,
    },
}


class DatasetGenerator:
    def __init__(self, base_dir="Python_Training"):
        self.base_dir = Path(base_dir)
        self.data_dir = self.base_dir / "data"
        self.raw_dir = self.data_dir / "raw"
        self.gold_dir = self.raw_dir / "gold"
        self.flawed_dir = self.raw_dir / "flawed"
        self.metadata = {
            "created": datetime.now().isoformat(),
            "instruments": {},
            "statistics": {}
        }

    def create_directory_structure(self):
        """Create the directory structure for gold and flawed sets."""
        print("📁 Creating directory structure...")

        # Create root directories
        for directory in [self.gold_dir, self.flawed_dir]:
            directory.mkdir(parents=True, exist_ok=True)
            print(f"  ✓ {directory}")

        # Create instrument subdirectories
        for instrument in INSTRUMENTS.keys():
            gold_inst = self.gold_dir / instrument
            flawed_inst = self.flawed_dir / instrument

            gold_inst.mkdir(parents=True, exist_ok=True)
            flawed_inst.mkdir(parents=True, exist_ok=True)

            print(f"  ✓ {gold_inst}")
            print(f"  ✓ {flawed_inst}")

            self.metadata["instruments"][instrument] = {
                "target_samples": INSTRUMENTS[instrument]["target_samples"],
                "gold_count": 0,
                "flawed_count": 0,
                "sub_categories": INSTRUMENTS[instrument]["sub_categories"],
            }

    def import_gold_references(self, source_dir=None):
        """
        Import existing Clarity audio library as Gold references.

        Args:
            source_dir: Path to Clarity audio library
        """
        print("\n🎵 Importing Gold references from Clarity library...")

        if source_dir is None:
            source_dir = Path("Assets/Targets")  # Default location

        if not source_dir.exists():
            print(f"  ⚠️  Source directory not found: {source_dir}")
            print(f"  Please place Clarity audio files in: {source_dir}")
            return False

        total_imported = 0

        for instrument in INSTRUMENTS.keys():
            # Look for files in source matching instrument pattern
            instrument_files = list(source_dir.glob(f"*{instrument}*/*.wav"))
            instrument_files += list(source_dir.glob(f"**/*{instrument}*.wav"))

            if not instrument_files:
                print(f"  ⚠️  No {instrument} files found in {source_dir}")
                continue

            dest_dir = self.gold_dir / instrument
            imported_count = 0

            for src_file in instrument_files:
                try:
                    dest_file = dest_dir / src_file.name
                    shutil.copy2(src_file, dest_file)
                    imported_count += 1
                    total_imported += 1
                except Exception as e:
                    print(f"  ✗ Failed to import {src_file.name}: {e}")

            if imported_count > 0:
                self.metadata["instruments"][instrument]["gold_count"] = imported_count
                print(f"  ✓ {instrument}: {imported_count} files imported")

        print(f"\n✅ Total Gold references imported: {total_imported}")
        return True

    def prepare_for_suno_generation(self):
        """
        Generate prompts and organization guide for Suno AI generation.
        Creates a structured list for generating Flawed variations.
        """
        print("\n🤖 Preparing Suno AI prompt structure...")

        suno_config = {
            "platform": "Suno AI",
            "purpose": "Generate intentionally flawed audio variations for training",
            "target_per_category": 100,
            "generation_instructions": {},
        }

        # Define flaw patterns for each instrument
        flaw_patterns = {
            "drums": {
                "muddy": "Drums with heavy low-end mud, no clarity",
                "harsh": "Drums with harsh 3kHz peak, brittle snare",
                "compressed": "Over-compressed drums, no transient punch",
                "thin": "Thin drums lacking low-end weight",
                "delayed": "Drums with bad timing, sloppy timing",
            },
            "vocals": {
                "nasal": "Nasal vocals with harsh 2-3kHz peak",
                "boxy": "Boxy sounding vocals, small room tone",
                "sibilant": "Over-sibilant with harsh 'S' sounds",
                "muddy": "Muddy vocals with excess low mids",
                "compressed": "Over-compressed vocals, lost dynamics",
            },
            "bass": {
                "undefined": "Undefined bass, indistinct pitch",
                "boomy": "Boomy bass with excessive sub-bass",
                "thin": "Thin bass lacking punch and weight",
                "muddy": "Muddy bass without clarity",
                "distorted": "Distorted bass, unnatural saturation",
            },
            "guitar": {
                "muddy": "Muddy guitar lacking clarity and definition",
                "harsh": "Harsh guitar with 4-5kHz peak",
                "thin": "Thin guitar lacking body and warmth",
                "boxed": "Boxy guitar with resonant frequency buildup",
                "compressed": "Over-compressed guitar, lost dynamics",
            },
            "keys": {
                "thin": "Thin keys lacking body and presence",
                "dark": "Dark keys with too much low-end",
                "harsh": "Harsh keys with brittle high-end",
                "compressed": "Over-compressed keys, no dynamics",
                "ringy": "Ringy keys with ringing resonances",
            },
        }

        for instrument, flaws in flaw_patterns.items():
            suno_config["generation_instructions"][instrument] = {
                "target_count": 100,
                "variations": flaws,
                "format": "WAV, 24-bit, 48kHz",
                "duration": "30-60 seconds",
                "quality": "Studio quality audio",
            }

        # Save Suno configuration
        suno_config_path = self.base_dir / "suno_generation_config.json"
        with open(suno_config_path, "w") as f:
            json.dump(suno_config, f, indent=2)

        print(f"  ✓ Suno AI configuration saved: {suno_config_path}")
        print(f"\nGeneration targets per instrument:")
        for instrument, config in suno_config["generation_instructions"].items():
            print(f"  • {instrument}: {config['target_count']} samples")
            print(f"    Variations: {list(config['variations'].keys())}")

        return suno_config

    def organize_flawed_imports(self, source_dir=None):
        """
        Organize imported Suno AI generated flawed variations.

        Args:
            source_dir: Path to folder containing Suno AI generated files
        """
        print("\n🎵 Organizing flawed variations from Suno AI...")

        if source_dir is None:
            source_dir = Path("downloads/suno_flawed")  # Default Suno export location

        if not source_dir.exists():
            print(f"  ⚠️  Source directory not found: {source_dir}")
            print(f"  Place Suno AI exports in: {source_dir}")
            return False

        total_organized = 0

        # Organize by instrument patterns in filenames
        for instrument in INSTRUMENTS.keys():
            instrument_files = list(source_dir.glob(f"*{instrument}*"))
            if not instrument_files:
                instrument_files = list(source_dir.glob(f"**/*{instrument}*"))

            dest_dir = self.flawed_dir / instrument
            organized_count = 0

            for src_file in instrument_files:
                if src_file.suffix.lower() == ".wav":
                    try:
                        dest_file = dest_dir / src_file.name
                        shutil.copy2(src_file, dest_file)
                        organized_count += 1
                        total_organized += 1
                    except Exception as e:
                        print(f"  ✗ Failed to organize {src_file.name}: {e}")

            if organized_count > 0:
                self.metadata["instruments"][instrument]["flawed_count"] = organized_count
                print(f"  ✓ {instrument}: {organized_count} flawed variations organized")

        print(f"\n✅ Total flawed variations organized: {total_organized}")
        return True

    def generate_manifest(self):
        """
        Generate a manifest file documenting the dataset.
        """
        print("\n📋 Generating dataset manifest...")

        # Count actual files
        for instrument in INSTRUMENTS.keys():
            gold_count = len(list((self.gold_dir / instrument).glob("*.wav")))
            flawed_count = len(list((self.flawed_dir / instrument).glob("*.wav")))

            self.metadata["instruments"][instrument]["gold_count"] = gold_count
            self.metadata["instruments"][instrument]["flawed_count"] = flawed_count

        # Summary statistics
        total_gold = sum(inst["gold_count"] for inst in self.metadata["instruments"].values())
        total_flawed = sum(inst["flawed_count"] for inst in self.metadata["instruments"].values())
        total_samples = total_gold + total_flawed

        self.metadata["statistics"] = {
            "total_gold_samples": total_gold,
            "total_flawed_samples": total_flawed,
            "total_samples": total_samples,
            "target_total": sum(inst["target_samples"] for inst in INSTRUMENTS.values()) * 2,
            "completion_percent": (total_samples / (sum(inst["target_samples"] for inst in INSTRUMENTS.values()) * 2)) * 100,
        }

        # Save manifest
        manifest_path = self.data_dir / "manifest.json"
        with open(manifest_path, "w") as f:
            json.dump(self.metadata, f, indent=2)

        print(f"  ✓ Manifest saved: {manifest_path}")
        print(f"\n📊 Dataset Summary:")
        print(f"  Gold samples:   {total_gold}")
        print(f"  Flawed samples: {total_flawed}")
        print(f"  Total samples:  {total_samples}")
        print(f"  Target total:   {self.metadata['statistics']['target_total']}")
        print(f"  Completion:     {self.metadata['statistics']['completion_percent']:.1f}%")

        return self.metadata

    def validate_dataset(self):
        """
        Validate that dataset structure is correct.
        """
        print("\n✔️  Validating dataset structure...")

        valid = True
        for instrument in INSTRUMENTS.keys():
            gold_dir = self.gold_dir / instrument
            flawed_dir = self.flawed_dir / instrument

            if not gold_dir.exists() or not flawed_dir.exists():
                print(f"  ✗ Missing directories for {instrument}")
                valid = False
                continue

            gold_files = list(gold_dir.glob("*.wav"))
            flawed_files = list(flawed_dir.glob("*.wav"))

            if len(gold_files) == 0:
                print(f"  ⚠️  No gold samples for {instrument} (expected: {INSTRUMENTS[instrument]['target_samples']})")

            if len(flawed_files) == 0:
                print(f"  ⚠️  No flawed samples for {instrument} (expected: {INSTRUMENTS[instrument]['target_samples']})")

            print(f"  ✓ {instrument}: {len(gold_files)} gold, {len(flawed_files)} flawed")

        return valid


def main():
    parser = argparse.ArgumentParser(description="NGN Clarity Dataset Generator")
    parser.add_argument("--init", action="store_true", help="Initialize directory structure")
    parser.add_argument("--import-gold", action="store_true", help="Import Gold references from Clarity library")
    parser.add_argument("--prepare-suno", action="store_true", help="Prepare Suno AI generation config")
    parser.add_argument("--organize-flawed", action="store_true", help="Organize flawed variations from Suno AI")
    parser.add_argument("--manifest", action="store_true", help="Generate dataset manifest")
    parser.add_argument("--validate", action="store_true", help="Validate dataset structure")
    parser.add_argument("--all", action="store_true", help="Run all steps")

    args = parser.parse_args()

    gen = DatasetGenerator()

    if args.init or args.all:
        gen.create_directory_structure()

    if args.import_gold or args.all:
        gen.import_gold_references()

    if args.prepare_suno or args.all:
        gen.prepare_for_suno_generation()

    if args.organize_flawed or args.all:
        gen.organize_flawed_imports()

    if args.manifest or args.all:
        gen.generate_manifest()

    if args.validate or args.all:
        gen.validate_dataset()

    print("\n✅ Dataset generation pipeline ready")


if __name__ == "__main__":
    main()
