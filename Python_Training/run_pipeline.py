#!/usr/bin/env python3
"""
NGN Clarity - PHASE_1_A Pipeline Orchestrator
Master script to run complete data ingestion and feature extraction

Pipeline:
  1. dataset_generator.py   → Organize raw audio into gold/flawed
  2. loudness_normalizer.py → Normalize to -18 LUFS
  3. feature_extractor.py   → Extract 64-float fingerprints
  4. Output → CSV ready for model training
"""

import subprocess
import sys
from pathlib import Path


def run_command(script_name, args=""):
    """Run a pipeline script."""
    print(f"\n{'='*70}")
    print(f"Running: {script_name}")
    print(f"{'='*70}")

    cmd = f"python {script_name} {args}"
    result = subprocess.run(cmd, shell=True, capture_output=False)

    if result.returncode != 0:
        print(f"❌ {script_name} failed with return code {result.returncode}")
        return False

    return True


def main():
    base_dir = Path("Python_Training")

    print("""
    ╔══════════════════════════════════════════════════════════════════╗
    ║         NGN CLARITY - PHASE_1_A PIPELINE ORCHESTRATOR            ║
    ║              Dataset Generation & Feature Extraction             ║
    ╚══════════════════════════════════════════════════════════════════╝
    """)

    scripts = [
        ("dataset_generator.py", "--all"),
        ("loudness_normalizer.py", "--all"),
        ("feature_extractor.py", "--all"),
    ]

    successful = 0
    failed = 0

    for script, args in scripts:
        if run_command(script, args):
            successful += 1
        else:
            failed += 1

    print(f"\n{'='*70}")
    print(f"PIPELINE COMPLETE")
    print(f"{'='*70}")
    print(f"Successful: {successful}/{len(scripts)}")
    print(f"Failed: {failed}/{len(scripts)}")

    if failed == 0:
        print("\n✅ All pipeline steps completed successfully!")
        print(f"\n📊 Output Files:")
        print(f"   • CSV features: Python_Training/data/features/features.csv")
        print(f"   • PKL features: Python_Training/data/features/features.pkl")
        print(f"\n🎯 Next Steps:")
        print(f"   1. Review features in Jupyter notebook")
        print(f"   2. Train Random Forest model on features.csv")
        print(f"   3. Export model to ONNX format")
        return 0
    else:
        print(f"\n❌ Pipeline incomplete - {failed} step(s) failed")
        print(f"   Check error messages above")
        return 1


if __name__ == "__main__":
    sys.exit(main())
