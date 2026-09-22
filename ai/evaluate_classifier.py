import json
from pathlib import Path

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.metrics import (
    accuracy_score,
    classification_report,
    confusion_matrix,
)


# ---------------------------------------------------------
# Load training data
# ---------------------------------------------------------

DATA_PATH = (
    Path(__file__).resolve().parent
    / "data"
    / "complaint_training.json"
)

with open(DATA_PATH, "r", encoding="utf-8") as file:
    data = json.load(file)


texts = [item["text"] for item in data]
labels = [item["category"] for item in data]


print("=" * 70)
print("AI COMPLAINT CLASSIFIER EVALUATION")
print("=" * 70)

print(f"\nTotal dataset size: {len(data)}")
print(f"Number of categories: {len(set(labels))}")


# ---------------------------------------------------------
# Show category distribution
# ---------------------------------------------------------

print("\nCategory distribution:")

for category in sorted(set(labels)):
    count = labels.count(category)
    print(f"  {category}: {count}")


# ---------------------------------------------------------
# Split dataset
# ---------------------------------------------------------

X_train, X_test, y_train, y_test = train_test_split(
    texts,
    labels,
    test_size=0.20,
    random_state=42,
    stratify=labels
)


print("\nDataset split:")
print(f"  Training samples: {len(X_train)}")
print(f"  Testing samples:  {len(X_test)}")


# ---------------------------------------------------------
# TF-IDF
# ---------------------------------------------------------

vectorizer = TfidfVectorizer(
    lowercase=True,
    stop_words="english",
    ngram_range=(1, 2)
)

X_train_vectorized = vectorizer.fit_transform(X_train)
X_test_vectorized = vectorizer.transform(X_test)


# ---------------------------------------------------------
# Train Logistic Regression
# ---------------------------------------------------------

model = LogisticRegression(
    max_iter=1000
)

model.fit(
    X_train_vectorized,
    y_train
)


# ---------------------------------------------------------
# Predict unseen test data
# ---------------------------------------------------------

y_pred = model.predict(X_test_vectorized)


# ---------------------------------------------------------
# Accuracy
# ---------------------------------------------------------

accuracy = accuracy_score(y_test, y_pred)

print("\n" + "=" * 70)
print("ACCURACY")
print("=" * 70)

print(f"\nAccuracy: {accuracy:.4f}")
print(f"Accuracy percentage: {accuracy * 100:.2f}%")


# ---------------------------------------------------------
# Classification report
# ---------------------------------------------------------

print("\n" + "=" * 70)
print("CLASSIFICATION REPORT")
print("=" * 70)

print(
    classification_report(
        y_test,
        y_pred,
        zero_division=0
    )
)


# ---------------------------------------------------------
# Confusion matrix
# ---------------------------------------------------------

categories = sorted(set(labels))

matrix = confusion_matrix(
    y_test,
    y_pred,
    labels=categories
)

print("\n" + "=" * 70)
print("CONFUSION MATRIX")
print("=" * 70)

print("\nCategories:")
print(categories)

print("\nMatrix:")
print(matrix)


# ---------------------------------------------------------
# Simple explanation
# ---------------------------------------------------------

print("\n" + "=" * 70)
print("EVALUATION COMPLETE")
print("=" * 70)

print(
    "\nThe model was trained using the training portion "
    "and evaluated using previously unseen test examples."
)

print(
    "The reported accuracy, precision, recall, and F1-score "
    "should be used as evaluation evidence."
)
