import json
import re
import unicodedata

from collections import Counter
from pathlib import Path

from scipy.sparse import hstack

from sklearn.feature_extraction.text import (
    TfidfVectorizer,
)
from sklearn.linear_model import (
    LogisticRegression,
)


class ComplaintClassifier:

    def __init__(self):
        self.training_path = (
            Path(__file__).resolve().parents[2]
            / "data"
            / "complaint_training.json"
        )

        self.training_data = (
            self._load_training_data()
        )

        texts = [
            self._normalize_text(
                item["text"]
            )
            for item in self.training_data
        ]

        labels = [
            item["category"]
            for item in self.training_data
        ]

        self.category_counts = Counter(
            labels
        )

        self.word_vectorizer = (
            TfidfVectorizer(
                lowercase=False,
                ngram_range=(1, 3),
                sublinear_tf=True,
                min_df=1,
                max_df=0.98,
                token_pattern=(
                    r"(?u)\b\w+\b"
                ),
            )
        )

        self.char_vectorizer = (
            TfidfVectorizer(
                lowercase=False,
                analyzer="char_wb",
                ngram_range=(3, 5),
                sublinear_tf=True,
                min_df=1,
                max_df=0.98,
            )
        )

        word_matrix = (
            self.word_vectorizer
            .fit_transform(
                texts
            )
        )

        char_matrix = (
            self.char_vectorizer
            .fit_transform(
                texts
            )
        )

        self.training_matrix = (
            hstack([
                word_matrix,
                char_matrix,
            ])
            .tocsr()
        )

        self.model = (
            LogisticRegression(
                max_iter=3000,
                class_weight="balanced",
                random_state=42,
            )
        )

        self.model.fit(
            self.training_matrix,
            labels,
        )

    def _load_training_data(self):
        if not self.training_path.exists():
            raise FileNotFoundError(
                (
                    "Complaint training data "
                    "was not found: "
                    f"{self.training_path}"
                )
            )

        with open(
            self.training_path,
            "r",
            encoding="utf-8",
        ) as file:
            data = json.load(
                file
            )

        if (
            not isinstance(
                data,
                list,
            )
            or not data
        ):
            raise ValueError(
                (
                    "Complaint training data "
                    "must be a non-empty list."
                )
            )

        cleaned_data = []
        seen_examples = set()

        for index, item in enumerate(
            data
        ):
            if not isinstance(
                item,
                dict,
            ):
                raise ValueError(
                    (
                        f"Training item "
                        f"{index + 1} must "
                        "be an object."
                    )
                )

            text = str(
                item.get(
                    "text",
                    "",
                )
            ).strip()

            category = str(
                item.get(
                    "category",
                    "",
                )
            ).strip()

            if (
                not text
                or not category
            ):
                raise ValueError(
                    (
                        f"Training item "
                        f"{index + 1} must "
                        "contain non-empty "
                        "'text' and "
                        "'category'."
                    )
                )

            normalized_key = (
                self._normalize_text(
                    text
                ),
                category.lower(),
            )

            if (
                normalized_key
                in seen_examples
            ):
                continue

            seen_examples.add(
                normalized_key
            )

            cleaned_data.append({
                "text": text,
                "category": category,
            })

        if len(
            cleaned_data
        ) < 2:
            raise ValueError(
                (
                    "Not enough valid "
                    "training data."
                )
            )

        categories = {
            item["category"]
            for item in cleaned_data
        }

        if len(categories) < 2:
            raise ValueError(
                (
                    "At least two complaint "
                    "categories are required."
                )
            )

        category_counts = Counter(
            item["category"]
            for item in cleaned_data
        )

        categories_with_too_few_examples = [
            category
            for category, count
            in category_counts.items()
            if count < 2
        ]

        if (
            categories_with_too_few_examples
        ):
            raise ValueError(
                (
                    "Every complaint category "
                    "must contain at least two "
                    "training examples. "
                    "Insufficient categories: "
                    + ", ".join(
                        categories_with_too_few_examples
                    )
                )
            )

        return cleaned_data

    def _normalize_text(
        self,
        text: str,
    ) -> str:
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

        for (
            pattern,
            replacement,
        ) in (
            abbreviation_replacements
            .items()
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

    def _create_feature_vector(
        self,
        normalized_text: str,
    ):
        word_vector = (
            self.word_vectorizer
            .transform([
                normalized_text
            ])
        )

        char_vector = (
            self.char_vectorizer
            .transform([
                normalized_text
            ])
        )

        combined_vector = (
            hstack([
                word_vector,
                char_vector,
            ])
            .tocsr()
        )

        return (
            word_vector,
            combined_vector,
        )

    def _get_top_terms(
        self,
        word_vector,
        limit: int = 5,
    ) -> list[str]:
        if word_vector.nnz == 0:
            return []

        feature_names = (
            self.word_vectorizer
            .get_feature_names_out()
        )

        row = (
            word_vector
            .toarray()[0]
        )

        ranked_indices = (
            row.argsort()[::-1]
        )

        terms = []

        for index in ranked_indices:
            if row[index] <= 0:
                continue

            term = feature_names[
                index
            ]

            if term not in terms:
                terms.append(
                    term
                )

            if len(terms) >= limit:
                break

        return terms

    def _confidence_level(
        self,
        confidence: float,
        confidence_gap: float,
    ) -> str:
        if (
            confidence >= 0.20
            and confidence_gap >= 0.10
        ):
            return "High"

        if (
            confidence >= 0.12
            and confidence_gap >= 0.05
        ):
            return "Moderate"

        return "Low"

    def _review_reason(
        self,
        best_confidence: float,
        confidence_gap: float,
        has_known_terms: bool,
        confidence_level: str,
    ) -> str | None:
        if not has_known_terms:
            return (
                "The complaint contains very "
                "few terms recognized from the "
                "current training data."
            )

        if confidence_gap < 0.05:
            return (
                "The top complaint types have "
                "very similar prediction scores."
            )

        if best_confidence < 0.12:
            return (
                "The classifier has low "
                "confidence in the predicted "
                "complaint type."
            )

        if (
            confidence_level
            == "Moderate"
        ):
            return (
                "The classifier has moderate "
                "confidence in the predicted "
                "complaint type."
            )

        if (
            confidence_level
            == "Low"
        ):
            return (
                "The prediction should be "
                "reviewed because the available "
                "evidence is not strong enough "
                "for a higher confidence level."
            )

        return None

    def predict(
        self,
        text: str,
    ):
        original_text = str(
            text
        ).strip()

        if not original_text:
            raise ValueError(
                (
                    "Complaint text cannot "
                    "be empty."
                )
            )

        normalized_text = (
            self._normalize_text(
                original_text
            )
        )

        if len(
            normalized_text
        ) < 3:
            raise ValueError(
                (
                    "Complaint text is too "
                    "short to classify."
                )
            )

        (
            word_vector,
            feature_vector,
        ) = self._create_feature_vector(
            normalized_text
        )

        probabilities = (
            self.model.predict_proba(
                feature_vector
            )[0]
        )

        classes = (
            self.model.classes_
        )

        ranked = sorted(
            zip(
                classes,
                probabilities,
            ),
            key=lambda item: item[1],
            reverse=True,
        )

        suggestions = []

        for (
            category,
            confidence,
        ) in ranked[:3]:
            confidence = float(
                confidence
            )

            suggestions.append({
                "complaint_type":
                    category,

                "confidence": round(
                    confidence,
                    4,
                ),

                "confidence_percentage":
                    round(
                        confidence * 100,
                        2,
                    ),
            })

        best_category = (
            ranked[0][0]
        )

        best_confidence = float(
            ranked[0][1]
        )

        second_confidence = (
            float(
                ranked[1][1]
            )
            if len(ranked) > 1
            else 0.0
        )

        confidence_gap = (
            best_confidence
            - second_confidence
        )

        has_known_terms = (
            word_vector.nnz > 0
        )

        confidence_level = (
            self._confidence_level(
                best_confidence,
                confidence_gap,
            )
        )

        ambiguous = (
            not has_known_terms
            or confidence_gap < 0.05
            or best_confidence < 0.12
        )

        review_reason = (
            self._review_reason(
                best_confidence,
                confidence_gap,
                has_known_terms,
                confidence_level,
            )
        )

        top_terms = (
            self._get_top_terms(
                word_vector
            )
        )

        return {
            "complaint_type":
                best_category,

            "confidence": round(
                best_confidence,
                4,
            ),

            "confidence_percentage":
                round(
                    best_confidence * 100,
                    2,
                ),

            "confidence_level":
                confidence_level,

            "confidence_gap": round(
                confidence_gap,
                4,
            ),

            "confidence_gap_percentage":
                round(
                    confidence_gap * 100,
                    2,
                ),

            "ambiguous":
                ambiguous,

            "human_review_required":
                True,

            "review_reason":
                review_reason,

            "recognized_terms":
                top_terms,

            "suggestions":
                suggestions,
        }
