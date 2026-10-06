import json
import re
import unicodedata

from collections import Counter
from pathlib import Path

from scipy.sparse import hstack

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.metrics import (
    accuracy_score,
    precision_score,
    recall_score,
    f1_score,
    classification_report,
    confusion_matrix,
)


# ---------------------------------------------------------
# Paths
# ---------------------------------------------------------

DATA_PATH = (
    Path(__file__).resolve().parent
    / "data"
    / "complaint_training.json"
)


# ---------------------------------------------------------
# Text normalization
# Must match the production ComplaintClassifier
# ---------------------------------------------------------

def normalize_text(text: str) -> str:
    text = unicodedata.normalize(
        "NFKC",
        str(text),
    )

    text = (
        text.lower()
        .strip()
    )

    abbreviation_replacements = {
        r"\bwtr\b": "water",
        r"\bwter\b": "water",
        r"\bmtr\b": "meter",
        r"\bsvc\b": "service",
        r"\bconn\b": "connection",
    }

    for pattern, replacement in (
        abbreviation_replacements.items()
    ):
        text = re.sub(
            pattern,
            replacement,
            text,
        )

    text = re.sub(
        r"[^\w\s]",
        " ",
        text,
        flags=re.UNICODE,
    )

    text = re.sub(
        r"_+",
        " ",
        text,
    )

    text = re.sub(
        r"\s+",
        " ",
        text,
    ).strip()

    return text


# ---------------------------------------------------------
# Load dataset
# ---------------------------------------------------------

with open(
    DATA_PATH,
    "r",
    encoding="utf-8",
) as file:
    data = json.load(file)


texts = [
    normalize_text(item["text"])
    for item in data
]

labels = [
    item["category"]
    for item in data
]


print("=" * 70)
print("AI COMPLAINT CLASSIFIER EVALUATION")
print("=" * 70)

print(
    f"\nTotal dataset size: {len(data)}"
)

print(
    f"Number of categories: "
    f"{len(set(labels))}"
)


# ---------------------------------------------------------
# Category distribution
# ---------------------------------------------------------

category_counts = Counter(labels)

print("\nCategory distribution:")

for category in sorted(
    category_counts
):
    print(
        f"  {category}: "
        f"{category_counts[category]}"
    )


# ---------------------------------------------------------
# Train/test split
# ---------------------------------------------------------

X_train, X_test, y_train, y_test = (
    train_test_split(
        texts,
        labels,
        test_size=0.20,
        random_state=42,
        stratify=labels,
    )
)


print("\nDataset split:")

print(
    f"  Training samples: "
    f"{len(X_train)}"
)

print(
    f"  Testing samples:  "
    f"{len(X_test)}"
)


# ---------------------------------------------------------
# Word TF-IDF
# Must match production
# ---------------------------------------------------------

word_vectorizer = TfidfVectorizer(
    lowercase=False,
    ngram_range=(1, 3),
    sublinear_tf=True,
    min_df=1,
    max_df=0.98,
    token_pattern=r"(?u)\b\w+\b",
)


X_train_word = (
    word_vectorizer.fit_transform(
        X_train
    )
)

X_test_word = (
    word_vectorizer.transform(
        X_test
    )
)


# ---------------------------------------------------------
# Character TF-IDF
# Must match production
# ---------------------------------------------------------

char_vectorizer = TfidfVectorizer(
    lowercase=False,
    analyzer="char_wb",
    ngram_range=(3, 5),
    sublinear_tf=True,
    min_df=1,
    max_df=0.98,
)


X_train_char = (
    char_vectorizer.fit_transform(
        X_train
    )
)

X_test_char = (
    char_vectorizer.transform(
        X_test
    )
)


# ---------------------------------------------------------
# Combine word + character features
# ---------------------------------------------------------

X_train_combined = hstack([
    X_train_word,
    X_train_char,
]).tocsr()


X_test_combined = hstack([
    X_test_word,
    X_test_char,
]).tocsr()


print("\nFeature information:")

print(
    f"  Word features: "
    f"{X_train_word.shape[1]}"
)

print(
    f"  Character features: "
    f"{X_train_char.shape[1]}"
)

print(
    f"  Combined features: "
    f"{X_train_combined.shape[1]}"
)


# ---------------------------------------------------------
# Train Logistic Regression
# Must match production
# ---------------------------------------------------------

model = LogisticRegression(
    max_iter=3000,
    class_weight="balanced",
    random_state=42,
)


model.fit(
    X_train_combined,
    y_train,
)


# ---------------------------------------------------------
# Predict test samples
# ---------------------------------------------------------

y_pred = model.predict(
    X_test_combined
)


# ---------------------------------------------------------
# Overall metrics
# ---------------------------------------------------------

accuracy = accuracy_score(
    y_test,
    y_pred,
)

macro_precision = precision_score(
    y_test,
    y_pred,
    average="macro",
    zero_division=0,
)

macro_recall = recall_score(
    y_test,
    y_pred,
    average="macro",
    zero_division=0,
)

macro_f1 = f1_score(
    y_test,
    y_pred,
    average="macro",
    zero_division=0,
)


weighted_precision = precision_score(
    y_test,
    y_pred,
    average="weighted",
    zero_division=0,
)

weighted_recall = recall_score(
    y_test,
    y_pred,
    average="weighted",
    zero_division=0,
)

weighted_f1 = f1_score(
    y_test,
    y_pred,
    average="weighted",
    zero_division=0,
)


print("\n" + "=" * 70)
print("OVERALL PERFORMANCE")
print("=" * 70)


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


print(
    f"\nWeighted Precision: "
    f"{weighted_precision:.4f}"
)

print(
    f"Weighted Recall:    "
    f"{weighted_recall:.4f}"
)

print(
    f"Weighted F1-score:  "
    f"{weighted_f1:.4f}"
)


# ---------------------------------------------------------
# Classification report
# ---------------------------------------------------------

categories = sorted(
    set(labels)
)


print("\n" + "=" * 70)
print("PER-CATEGORY CLASSIFICATION REPORT")
print("=" * 70)


print(
    classification_report(
        y_test,
        y_pred,
        labels=categories,
        zero_division=0,
        digits=4,
    )
)


# ---------------------------------------------------------
# Confusion matrix
# ---------------------------------------------------------

matrix = confusion_matrix(
    y_test,
    y_pred,
    labels=categories,
)


print("\n" + "=" * 70)
print("CONFUSION MATRIX")
print("=" * 70)


print("\nCategory index:")

for index, category in enumerate(
    categories
):
    print(
        f"  {index}: {category}"
    )


print("\nMatrix:")

print(matrix)


# ---------------------------------------------------------
# Incorrect predictions
# ---------------------------------------------------------

incorrect_predictions = []

for text, actual, predicted in zip(
    X_test,
    y_test,
    y_pred,
):
    if actual != predicted:
        incorrect_predictions.append({
            "text": text,
            "actual": actual,
            "predicted": predicted,
        })


print("\n" + "=" * 70)
print("MISCLASSIFIED TEST EXAMPLES")
print("=" * 70)


if not incorrect_predictions:
    print(
        "\nNo misclassified examples "
        "in this test split."
    )

else:
    print(
        f"\nTotal misclassified: "
        f"{len(incorrect_predictions)}"
    )

    for index, item in enumerate(
        incorrect_predictions,
        start=1,
    ):
        print(
            f"\n{index}. "
            f"{item['text']}"
        )

        print(
            f"   Actual:    "
            f"{item['actual']}"
        )

        print(
            f"   Predicted: "
            f"{item['predicted']}"
        )


# ---------------------------------------------------------
# Correct predictions
# ---------------------------------------------------------

correct_count = sum(
    actual == predicted
    for actual, predicted
    in zip(
        y_test,
        y_pred,
    )
)


print("\n" + "=" * 70)
print("EVALUATION SUMMARY")
print("=" * 70)


print(
    f"\nCorrect predictions: "
    f"{correct_count}/{len(y_test)}"
)

print(
    f"Incorrect predictions: "
    f"{len(incorrect_predictions)}/"
    f"{len(y_test)}"
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
    "\nEvaluation completed using a stratified "
    "80/20 train-test split."
)

print(
    "The evaluation model uses the same "
    "normalization, word TF-IDF, character "
    "TF-IDF, and Logistic Regression "
    "configuration as the production "
    "ComplaintClassifier."
)
