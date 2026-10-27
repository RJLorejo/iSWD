"""Evaluate the classifier against the curated multilingual regression set.

This is a small regression check, not a claim of production accuracy.
Run from the repository root: python ai/evaluate_multilingual.py
"""

import json
from collections import defaultdict
from pathlib import Path

from ai.app.classification.classifier import ComplaintClassifier


def main():
    data_path = (
        Path(__file__).resolve().parent
        / "data"
        / "complaint_multilingual_test.json"
    )
    with data_path.open(encoding="utf-8") as file:
        cases = json.load(file)

    classifier = ComplaintClassifier()
    grouped = defaultdict(lambda: {"correct": 0, "total": 0})
    wrong = []

    for case in cases:
        result = classifier.predict(case["text"])
        predicted = result["complaint_type"]
        group = case.get("language_group", "Unspecified")
        grouped[group]["total"] += 1
        if predicted == case["category"]:
            grouped[group]["correct"] += 1
        else:
            wrong.append((case, predicted))

    total_correct = sum(item["correct"] for item in grouped.values())
    total = sum(item["total"] for item in grouped.values())
    print(f"Curated multilingual regression set: {total_correct}/{total} ({total_correct / total:.1%})")
    print("This score describes only this small curated set, not real-world accuracy.")
    for group, scores in sorted(grouped.items()):
        accuracy = scores["correct"] / scores["total"] if scores["total"] else 0
        print(f"{group}: {scores['correct']}/{scores['total']} ({accuracy:.1%})")
    if wrong:
        print("\nMisclassified examples:")
        for case, predicted in wrong:
            print(f"- [{case.get('language_group', 'Unspecified')}] expected {case['category']!r}; got {predicted!r}: {case['text']}")


if __name__ == "__main__":
    main()
