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
            stop_words="english",
            ngram_range=(1, 2)
        )

        texts = [
            item["text"]
            for item in self.training_data
        ]

        labels = [
            item["category"]
            for item in self.training_data
        ]

        self.training_matrix = self.vectorizer.fit_transform(texts)

        self.model = LogisticRegression(
            max_iter=1000
        )

        self.model.fit(
            self.training_matrix,
            labels
        )

    def _load_training_data(self):
        with open(
            self.training_path,
            "r",
            encoding="utf-8"
        ) as file:
            return json.load(file)

    def predict(self, text: str):
        vector = self.vectorizer.transform([text])

        probabilities = self.model.predict_proba(vector)[0]

        classes = self.model.classes_

        ranked = sorted(
            zip(classes, probabilities),
            key=lambda item: item[1],
            reverse=True
        )

        best_category = ranked[0][0]
        best_confidence = float(ranked[0][1])

        suggestions = [
            {
                "category": category,
                "confidence": round(
                    float(confidence),
                    4
                )
            }
            for category, confidence in ranked[:3]
        ]

        if best_confidence >= 0.70:
            confidence_level = "High"
        elif best_confidence >= 0.50:
            confidence_level = "Moderate"
        else:
            confidence_level = "Low"

        return {
            "category": best_category,
            "confidence": round(
                best_confidence,
                4
            ),
            "confidence_percentage": round(
                best_confidence * 100,
                2
            ),
            "confidence_level": confidence_level,
            "manual_review_required": best_confidence < 0.70,
            "suggestions": suggestions
        }
