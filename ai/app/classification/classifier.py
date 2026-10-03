import json
from pathlib import Path

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression


class ComplaintClassifier:

    def __init__(self):
        self.training_path = (
            Path(__file__).resolve().parents[2]
            / "data"
            / "complaint_training.json"
        )

        self.training_data = self._load_training_data()

        self.vectorizer = TfidfVectorizer(
            lowercase=True,
            ngram_range=(1, 2),
            sublinear_tf=True,
            min_df=1,
        )

        texts = [
            item["text"]
            for item in self.training_data
        ]

        labels = [
            item["category"]
            for item in self.training_data
        ]

        self.training_matrix = self.vectorizer.fit_transform(
            texts
        )

        self.model = LogisticRegression(
            max_iter=2000,
            class_weight="balanced",
        )

        self.model.fit(
            self.training_matrix,
            labels,
        )

    def _load_training_data(self):
        with open(
            self.training_path,
            "r",
            encoding="utf-8",
        ) as file:
            data = json.load(file)

        if not data:
            raise ValueError(
                "Complaint training data is empty."
            )

        for item in data:
            if (
                "text" not in item
                or "category" not in item
            ):
                raise ValueError(
                    "Every training item must contain "
                    "'text' and 'category'."
                )

        return data

    def predict(self, text: str):
        cleaned_text = text.strip()

        if not cleaned_text:
            raise ValueError(
                "Complaint text cannot be empty."
            )

        vector = self.vectorizer.transform([
            cleaned_text
        ])

        probabilities = self.model.predict_proba(
            vector
        )[0]

        classes = self.model.classes_

        ranked = sorted(
            zip(classes, probabilities),
            key=lambda item: item[1],
            reverse=True,
        )

        suggestions = [
            {
                "complaint_type": category,
                "confidence": round(
                    float(confidence),
                    4,
                ),
                "confidence_percentage": round(
                    float(confidence) * 100,
                    2,
                ),
            }
            for category, confidence in ranked[:3]
        ]

        best_category = ranked[0][0]
        best_confidence = float(ranked[0][1])

        second_confidence = (
            float(ranked[1][1])
            if len(ranked) > 1
            else 0.0
        )

        confidence_gap = (
            best_confidence
            - second_confidence
        )

        if best_confidence >= 0.75:
            confidence_level = "High"
        elif best_confidence >= 0.50:
            confidence_level = "Moderate"
        else:
            confidence_level = "Low"

        ambiguous = (
            best_confidence < 0.60
            or confidence_gap < 0.15
        )

        return {
            "complaint_type": best_category,

            "confidence": round(
                best_confidence,
                4,
            ),

            "confidence_percentage": round(
                best_confidence * 100,
                2,
            ),

            "confidence_level": confidence_level,

            "confidence_gap": round(
                confidence_gap,
                4,
            ),

            "ambiguous": ambiguous,

            "human_review_required": True,

            "suggestions": suggestions,
        }
