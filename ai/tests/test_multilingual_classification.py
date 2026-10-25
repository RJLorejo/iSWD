"""Regression tests for local-language and code-switched complaints.

The test phrases are a small, curated regression set, not a representative
estimate of real-world model accuracy. Expand it with reviewed SWD examples.
"""

import json
import unittest
from pathlib import Path

from ai.app.classification.classifier import ComplaintClassifier


class MultilingualComplaintClassificationTests(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.classifier = ComplaintClassifier()
        cls.test_data_path = (
            Path(__file__).resolve().parents[1]
            / "data"
            / "complaint_multilingual_test.json"
        )
        with cls.test_data_path.open(encoding="utf-8") as file:
            cls.test_cases = json.load(file)

    def test_curated_multilingual_examples(self):
        failures = []
        for case in self.test_cases:
            result = self.classifier.predict(case["text"])
            predicted = result["complaint_type"]
            if predicted != case["category"]:
                failures.append(
                    f"[{case.get('language_group', 'unspecified')}] "
                    f"expected {case['category']!r}, got {predicted!r}: "
                    f"{case['text']}"
                )
        self.assertFalse(
            failures,
            "Multilingual classification regressions:\n" + "\n".join(failures),
        )

    def test_english_complaints_remain_classifiable(self):
        examples = [
            ("There is a large water leak from the main pipe along the road.", "Mainline Leakage"),
            ("There is no water coming from any tap in our house.", "No Water"),
            ("The meter reading on the bill does not match the actual meter.", "Erroneous Meter Reading"),
            ("We would like to temporarily disconnect our water service.", "Temporary Disconnection"),
        ]
        for text, expected in examples:
            with self.subTest(text=text):
                result = self.classifier.predict(text)
                self.assertEqual(result["complaint_type"], expected)

    def test_training_examples_are_balanced_after_expansion(self):
        counts = self.classifier.category_counts
        self.assertEqual(len(counts), 15)
        self.assertGreaterEqual(min(counts.values()), 30)
        self.assertLessEqual(max(counts.values()) - min(counts.values()), 5)


if __name__ == "__main__":
    unittest.main()
