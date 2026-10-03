from math import atan2, cos, radians, sin, sqrt
from typing import Any

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


class SimilarComplaintDetector:

    def __init__(self):
        self.text_weight = 0.45
        self.location_weight = 0.30
        self.type_weight = 0.15
        self.time_weight = 0.10

        self.related_threshold = 0.60
        self.possible_threshold = 0.40

    def detect(
        self,
        complaint: dict[str, Any],
        candidates: list[dict[str, Any]],
        limit: int = 5,
    ):
        if not candidates:
            return {
                "has_possible_related_complaints": False,
                "count": 0,
                "matches": [],
            }

        descriptions = [
            str(
                complaint.get(
                    "description",
                    "",
                )
            )
        ]

        descriptions.extend(
            str(
                candidate.get(
                    "description",
                    "",
                )
            )
            for candidate in candidates
        )

        text_scores = self._calculate_text_similarity(
            descriptions
        )

        matches = []

        for index, candidate in enumerate(
            candidates,
            start=1,
        ):
            text_similarity = float(
                text_scores[index]
            )

            location_result = (
                self._calculate_location_similarity(
                    complaint,
                    candidate,
                )
            )

            type_similarity = (
                self._calculate_type_similarity(
                    complaint,
                    candidate,
                )
            )

            time_similarity = (
                self._calculate_time_similarity(
                    complaint,
                    candidate,
                )
            )

            score = self._calculate_combined_score(
                text_similarity=text_similarity,
                location_similarity=location_result[
                    "similarity"
                ],
                type_similarity=type_similarity,
                time_similarity=time_similarity,
                has_location=location_result[
                    "available"
                ],
            )

            relationship = (
                self._relationship_level(
                    score
                )
            )

            evidence = self._build_evidence(
                complaint=complaint,
                candidate=candidate,
                text_similarity=text_similarity,
                location_result=location_result,
                type_similarity=type_similarity,
                time_similarity=time_similarity,
            )

            if relationship == "Unlikely":
                continue

            matches.append({
                "complaint_id": candidate.get(
                    "id"
                ),
                "complaint_no": candidate.get(
                    "complaint_no"
                ),
                "relationship": relationship,
                "score": round(
                    score,
                    4,
                ),
                "score_percentage": round(
                    score * 100,
                    2,
                ),
                "text_similarity": round(
                    text_similarity,
                    4,
                ),
                "text_similarity_percentage": round(
                    text_similarity * 100,
                    2,
                ),
                "distance_km": location_result[
                    "distance_km"
                ],
                "same_division": (
                    complaint.get("division_id")
                    == candidate.get("division_id")
                ),
                "same_complaint_type": (
                    complaint.get(
                        "complaint_category_id"
                    )
                    == candidate.get(
                        "complaint_category_id"
                    )
                ),
                "status": candidate.get(
                    "status"
                ),
                "evidence": evidence,
                "human_confirmation_required": True,
            })

        matches.sort(
            key=lambda item: item["score"],
            reverse=True,
        )

        matches = matches[:limit]

        return {
            "has_possible_related_complaints": (
                len(matches) > 0
            ),
            "count": len(matches),
            "matches": matches,
        }

    def _calculate_text_similarity(
        self,
        descriptions: list[str],
    ):
        cleaned = [
            description.strip()
            if description.strip()
            else "empty"
            for description in descriptions
        ]

        vectorizer = TfidfVectorizer(
            lowercase=True,
            ngram_range=(1, 2),
            sublinear_tf=True,
        )

        matrix = vectorizer.fit_transform(
            cleaned
        )

        return cosine_similarity(
            matrix[0:1],
            matrix,
        )[0]

    def _calculate_location_similarity(
        self,
        complaint: dict,
        candidate: dict,
    ):
        lat1 = complaint.get("latitude")
        lon1 = complaint.get("longitude")

        lat2 = candidate.get("latitude")
        lon2 = candidate.get("longitude")

        if (
            lat1 is None
            or lon1 is None
            or lat2 is None
            or lon2 is None
        ):
            return {
                "available": False,
                "similarity": 0.0,
                "distance_km": None,
            }

        try:
            distance = self._haversine_distance(
                float(lat1),
                float(lon1),
                float(lat2),
                float(lon2),
            )
        except (TypeError, ValueError):
            return {
                "available": False,
                "similarity": 0.0,
                "distance_km": None,
            }

        if distance <= 0.25:
            similarity = 1.0

        elif distance <= 0.50:
            similarity = 0.90

        elif distance <= 1.00:
            similarity = 0.75

        elif distance <= 2.00:
            similarity = 0.50

        elif distance <= 5.00:
            similarity = 0.20

        else:
            similarity = 0.0

        return {
            "available": True,
            "similarity": similarity,
            "distance_km": round(
                distance,
                3,
            ),
        }

    def _calculate_type_similarity(
        self,
        complaint: dict,
        candidate: dict,
    ):
        complaint_type = complaint.get(
            "complaint_category_id"
        )

        candidate_type = candidate.get(
            "complaint_category_id"
        )

        complaint_division = complaint.get(
            "division_id"
        )

        candidate_division = candidate.get(
            "division_id"
        )

        if (
            complaint_type is not None
            and candidate_type is not None
            and complaint_type == candidate_type
        ):
            return 1.0

        if (
            complaint_division is not None
            and candidate_division is not None
            and complaint_division
            == candidate_division
        ):
            return 0.50

        return 0.0

    def _calculate_time_similarity(
        self,
        complaint: dict,
        candidate: dict,
    ):
        complaint_hours = complaint.get(
            "hours_difference"
        )

        candidate_hours = candidate.get(
            "hours_difference"
        )

        hours = (
            candidate_hours
            if candidate_hours is not None
            else complaint_hours
        )

        if hours is None:
            return 0.0

        try:
            hours = abs(
                float(hours)
            )
        except (TypeError, ValueError):
            return 0.0

        if hours <= 2:
            return 1.0

        if hours <= 6:
            return 0.85

        if hours <= 12:
            return 0.65

        if hours <= 24:
            return 0.45

        if hours <= 48:
            return 0.20

        return 0.0

    def _calculate_combined_score(
        self,
        text_similarity: float,
        location_similarity: float,
        type_similarity: float,
        time_similarity: float,
        has_location: bool,
    ):
        components = [
            (
                text_similarity,
                self.text_weight,
            ),
            (
                type_similarity,
                self.type_weight,
            ),
            (
                time_similarity,
                self.time_weight,
            ),
        ]

        if has_location:
            components.append(
                (
                    location_similarity,
                    self.location_weight,
                )
            )

        total_weight = sum(
            weight
            for _, weight in components
        )

        if total_weight == 0:
            return 0.0

        score = sum(
            value * weight
            for value, weight in components
        ) / total_weight

        return max(
            0.0,
            min(
                score,
                1.0,
            ),
        )

    def _relationship_level(
        self,
        score: float,
    ):
        if score >= self.related_threshold:
            return "Likely Related"

        if score >= self.possible_threshold:
            return "Possibly Related"

        return "Unlikely"

    def _build_evidence(
        self,
        complaint: dict,
        candidate: dict,
        text_similarity: float,
        location_result: dict,
        type_similarity: float,
        time_similarity: float,
    ):
        evidence = []

        if text_similarity >= 0.50:
            evidence.append(
                "The complaint descriptions have strong textual similarity."
            )

        elif text_similarity >= 0.25:
            evidence.append(
                "The complaint descriptions have some textual similarity."
            )

        if location_result["available"]:
            distance = location_result[
                "distance_km"
            ]

            if distance <= 0.50:
                evidence.append(
                    "The reported locations are very close to each other."
                )

            elif distance <= 2.00:
                evidence.append(
                    "The reported locations are within a nearby area."
                )

        if (
            complaint.get(
                "complaint_category_id"
            )
            is not None
            and complaint.get(
                "complaint_category_id"
            )
            == candidate.get(
                "complaint_category_id"
            )
        ):
            evidence.append(
                "Both complaints have the same complaint type."
            )

        elif type_similarity > 0:
            evidence.append(
                "Both complaints belong to the same division."
            )

        if time_similarity >= 0.85:
            evidence.append(
                "The complaints were reported within a short time interval."
            )

        elif time_similarity >= 0.45:
            evidence.append(
                "The complaints were reported within the same general time period."
            )

        return evidence

    def _haversine_distance(
        self,
        lat1: float,
        lon1: float,
        lat2: float,
        lon2: float,
    ):
        earth_radius_km = 6371.0088

        latitude1 = radians(lat1)
        longitude1 = radians(lon1)
        latitude2 = radians(lat2)
        longitude2 = radians(lon2)

        latitude_difference = (
            latitude2 - latitude1
        )

        longitude_difference = (
            longitude2 - longitude1
        )

        a = (
            sin(
                latitude_difference / 2
            )
            ** 2
            + cos(latitude1)
            * cos(latitude2)
            * sin(
                longitude_difference / 2
            )
            ** 2
        )

        c = 2 * atan2(
            sqrt(a),
            sqrt(1 - a),
        )

        return earth_radius_km * c
