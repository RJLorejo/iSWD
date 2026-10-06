from collections import Counter
from pathlib import Path
import json
import sys

from sklearn.metrics import (
    accuracy_score,
    classification_report,
    confusion_matrix,
    f1_score,
    precision_score,
    recall_score,
)


AI_DIR = Path(__file__).resolve().parent

if str(AI_DIR) not in sys.path:
    sys.path.insert(0, str(AI_DIR))

from app.classification.classifier import ComplaintClassifier


TEST_PATH = (
    AI_DIR
    / "data"
    / "complaint_external_test.json"
)


def load_test_data():
    if not TEST_PATH.exists():
        raise FileNotFoundError(
            f"External test dataset not found: {TEST_PATH}"
        )

    with open(
        TEST_PATH,
        "r",
        encoding="utf-8",
    ) as file:
        data = json.load(file)

    if not isinstance(data, list) or not data:
        raise ValueError(
            "External test dataset must be a non-empty list."
        )

    cleaned = []

    for index, item in enumerate(data, start=1):
        if not isinstance(item, dict):
            raise ValueError(
                f"Test item {index} must be an object."
            )

        text = str(
            item.get("text", "")
        ).strip()

        category = str(
            item.get("category", "")
        ).strip()

        if not text or not category:
            raise ValueError(
                f"Test item {index} must contain "
                "'text' and 'category'."
            )

        cleaned.append({
            "text": text,
            "category": category,
        })

    return cleaned


def main():
    test_data = load_test_data()

    classifier = ComplaintClassifier()

    expected = []
    predicted = []

    results = []

    print("=" * 72)
    print("EXTERNAL COMPLAINT CLASSIFIER EVALUATION")
    print("=" * 72)

    print(
        f"\nExternal test samples: {len(test_data)}"
    )

    category_counts = Counter(
        item["category"]
        for item in test_data
    )

    print(
        f"Number of categories: {len(category_counts)}"
    )

    print("\nExternal category distribution:")

    for category in sorted(category_counts):
        print(
            f"  {category}: {category_counts[category]}"
        )

    for item in test_data:
        result = classifier.predict(
            item["text"]
        )

        actual = item["category"]
        prediction = result["complaint_type"]

        expected.append(actual)
        predicted.append(prediction)

        results.append({
            "text": item["text"],
            "actual": actual,
            "predicted": prediction,
            "confidence":
                result["confidence_percentage"],
            "confidence_level":
                result["confidence_level"],
            "gap":
                result["confidence_gap_percentage"],
            "ambiguous":
                result["ambiguous"],
        })

    categories = sorted(
        set(expected) | set(predicted)
    )

    accuracy = accuracy_score(
        expected,
        predicted,
    )

    macro_precision = precision_score(
        expected,
        predicted,
        average="macro",
        zero_division=0,
    )

    macro_recall = recall_score(
        expected,
        predicted,
        average="macro",
        zero_division=0,
    )

    macro_f1 = f1_score(
        expected,
        predicted,
        average="macro",
        zero_division=0,
    )

    print("\n" + "=" * 72)
    print("OVERALL EXTERNAL PERFORMANCE")
    print("=" * 72)

    print(
        f"\nAccuracy:        "
        f"{accuracy:.4f} "
        f"({accuracy * 100:.2f}%)"
    )

    print(
        f"Macro Precision: "
        f"{macro_precision:.4f} "
        f"({macro_precision * 100:.2f}%)"
    )

    print(
        f"Macro Recall:    "
        f"{macro_recall:.4f} "
        f"({macro_recall * 100:.2f}%)"
    )

    print(
        f"Macro F1-score:  "
        f"{macro_f1:.4f} "
        f"({macro_f1 * 100:.2f}%)"
    )

    print("\n" + "=" * 72)
    print("PER-CATEGORY REPORT")
    print("=" * 72)

    print(
        classification_report(
            expected,
            predicted,
            labels=categories,
            digits=4,
            zero_division=0,
        )
    )

    incorrect = [
        result
        for result in results
        if result["actual"]
        != result["predicted"]
    ]

    print("\n" + "=" * 72)
    print("MISCLASSIFIED EXTERNAL EXAMPLES")
    print("=" * 72)

    if not incorrect:
        print(
            "\nNo misclassified external examples."
        )
    else:
        print(
            f"\nTotal misclassified: "
            f"{len(incorrect)}"
        )

        for index, item in enumerate(
            incorrect,
            start=1,
        ):
            print(
                f"\n{index}. {item['text']}"
            )

            print(
                f"   Actual:     "
                f"{item['actual']}"
            )

            print(
                f"   Predicted:  "
                f"{item['predicted']}"
            )

            print(
                f"   Confidence: "
                f"{item['confidence']:.2f}%"
            )

            print(
                f"   Gap:        "
                f"{item['gap']:.2f}%"
            )

            print(
                f"   Level:      "
                f"{item['confidence_level']}"
            )

            print(
                f"   Ambiguous:  "
                f"{item['ambiguous']}"
            )

    ambiguous_results = [
        result
        for result in results
        if result["ambiguous"]
    ]

    print("\n" + "=" * 72)
    print("AMBIGUITY SUMMARY")
    print("=" * 72)

    print(
        f"\nAmbiguous predictions: "
        f"{len(ambiguous_results)}/"
        f"{len(results)}"
    )

    print(
        f"Ambiguous percentage: "
        f"{(
            len(ambiguous_results)
            / len(results)
        ) * 100:.2f}%"
    )

    matrix = confusion_matrix(
        expected,
        predicted,
        labels=categories,
    )

    print("\n" + "=" * 72)
    print("CONFUSION MATRIX")
    print("=" * 72)

    print("\nCategory index:")

    for index, category in enumerate(
        categories
    ):
        print(
            f"  {index}: {category}"
        )

    print("\nMatrix:")
    print(matrix)

    correct = (
        len(results)
        - len(incorrect)
    )

    print("\n" + "=" * 72)
    print("EXTERNAL EVALUATION SUMMARY")
    print("=" * 72)

    print(
        f"\nCorrect predictions: "
        f"{correct}/{len(results)}"
    )

    print(
        f"Incorrect predictions: "
        f"{len(incorrect)}/{len(results)}"
    )

    print(
        f"Accuracy: "
        f"{accuracy * 100:.2f}%"
    )

    print(
        f"Macro F1-score: "
        f"{macro_f1 * 100:.2f}%"
    )

    print(
        "\nThe production ComplaintClassifier "
        "was evaluated against a separate "
        "external test dataset that was not "
        "used to train the model."
    )


if __name__ == "__main__":
    main()
