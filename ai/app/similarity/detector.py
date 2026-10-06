from math import atan2, cos, radians, sin, sqrt
from typing import Any

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


class SimilarComplaintDetector:

    def __init__(self):
        self.location_weight = 0.40
        self.text_weight = 0.25
        self.type_weight = 0.15
        self.time_weight = 0.15
        self.consumer_weight = 0.05

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

        text_scores = (
            self._calculate_text_similarity(
                descriptions
            )
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

            time_result = (
                self._calculate_time_similarity(
                    complaint,
                    candidate,
                )
            )

            consumer_result = (
                self._calculate_consumer_similarity(
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
                time_similarity=time_result[
                    "similarity"
                ],
                consumer_similarity=consumer_result[
                    "similarity"
                ],
                has_location=location_result[
                    "available"
                ],
                has_time=time_result[
                    "available"
                ],
                has_consumer=consumer_result[
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
                time_result=time_result,
                consumer_result=consumer_result,
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

                "location_similarity": round(
                    location_result["similarity"],
                    4,
                ),

                "location_similarity_percentage": round(
                    location_result["similarity"]
                    * 100,
                    2,
                ),

                "distance_km": location_result[
                    "distance_km"
                ],

                "same_complaint_type": (
                    type_similarity == 1.0
                ),

                "type_score": round(
                    type_similarity,
                    4,
                ),

                "type_score_percentage": round(
                    type_similarity * 100,
                    2,
                ),

                "time_similarity": round(
                    time_result["similarity"],
                    4,
                ),

                "time_similarity_percentage": round(
                    time_result["similarity"]
                    * 100,
                    2,
                ),

                "hours_difference": time_result[
                    "hours_difference"
                ],

                "same_consumer": consumer_result[
                    "same_consumer"
                ],

                "consumer_match_available": (
                    consumer_result[
                        "available"
                    ]
                ),

                "consumer_score": round(
                    consumer_result[
                        "similarity"
                    ],
                    4,
                ),

                "consumer_score_percentage": round(
                    consumer_result[
                        "similarity"
                    ]
                    * 100,
                    2,
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
        lat1 = complaint.get(
            "latitude"
        )

        lon1 = complaint.get(
            "longitude"
        )

        lat2 = candidate.get(
            "latitude"
        )

        lon2 = candidate.get(
            "longitude"
        )

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
            distance = (
                self._haversine_distance(
                    float(lat1),
                    float(lon1),
                    float(lat2),
                    float(lon2),
                )
            )

        except (
            TypeError,
            ValueError,
        ):
            return {
                "available": False,
                "similarity": 0.0,
                "distance_km": None,
            }

        if distance <= 0.10:
            similarity = 1.0

        elif distance <= 0.25:
            similarity = 0.95

        elif distance <= 0.50:
            similarity = 0.85

        elif distance <= 1.00:
            similarity = 0.70

        elif distance <= 2.00:
            similarity = 0.45

        elif distance <= 5.00:
            similarity = 0.15

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

        if (
            complaint_type is None
            or candidate_type is None
        ):
            return 0.0

        if (
            complaint_type
            == candidate_type
        ):
            return 1.0

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
            return {
                "available": False,
                "similarity": 0.0,
                "hours_difference": None,
            }

        try:
            hours = abs(
                float(hours)
            )

        except (
            TypeError,
            ValueError,
        ):
            return {
                "available": False,
                "similarity": 0.0,
                "hours_difference": None,
            }

        if hours <= 2:
            similarity = 1.0

        elif hours <= 6:
            similarity = 0.95

        elif hours <= 12:
            similarity = 0.90

        elif hours <= 24:
            similarity = 0.80

        elif hours <= 48:
            similarity = 0.65

        elif hours <= 72:
            similarity = 0.50

        elif hours <= 120:
            similarity = 0.30

        elif hours <= 168:
            similarity = 0.15

        else:
            similarity = 0.0

        return {
            "available": True,

            "similarity": similarity,

            "hours_difference": round(
                hours,
                2,
            ),
        }

    def _calculate_consumer_similarity(
        self,
        complaint: dict,
        candidate: dict,
    ):
        complaint_consumer = complaint.get(
            "consumer_id"
        )

        candidate_consumer = candidate.get(
            "consumer_id"
        )

        if (
            complaint_consumer is None
            or candidate_consumer is None
        ):
            return {
                "available": False,
                "same_consumer": False,
                "similarity": 0.0,
            }

        same_consumer = (
            complaint_consumer
            == candidate_consumer
        )

        return {
            "available": True,

            "same_consumer":
                same_consumer,

            "similarity": (
                1.0
                if same_consumer
                else 0.0
            ),
        }

    def _calculate_combined_score(
        self,
        text_similarity: float,
        location_similarity: float,
        type_similarity: float,
        time_similarity: float,
        consumer_similarity: float,
        has_location: bool,
        has_time: bool,
        has_consumer: bool,
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
        ]

        if has_location:
            components.append(
                (
                    location_similarity,
                    self.location_weight,
                )
            )

        if has_time:
            components.append(
                (
                    time_similarity,
                    self.time_weight,
                )
            )

        if has_consumer:
            components.append(
                (
                    consumer_similarity,
                    self.consumer_weight,
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
        if (
            score
            >= self.related_threshold
        ):
            return "Likely Related"

        if (
            score
            >= self.possible_threshold
        ):
            return "Possibly Related"

        return "Unlikely"

    def _build_evidence(
        self,
        complaint: dict,
        candidate: dict,
        text_similarity: float,
        location_result: dict,
        type_similarity: float,
        time_result: dict,
        consumer_result: dict,
    ):
        evidence = []

        if location_result[
            "available"
        ]:
            distance = location_result[
                "distance_km"
            ]

            if distance <= 0.25:
                evidence.append(
                    (
                        "The reported locations "
                        "are extremely close to "
                        "each other."
                    )
                )

            elif distance <= 0.50:
                evidence.append(
                    (
                        "The reported locations "
                        "are very close to each "
                        "other."
                    )
                )

            elif distance <= 2.00:
                evidence.append(
                    (
                        "The reported locations "
                        "are within a nearby area."
                    )
                )

        if text_similarity >= 0.50:
            evidence.append(
                (
                    "The complaint descriptions "
                    "have strong textual "
                    "similarity."
                )
            )

        elif text_similarity >= 0.25:
            evidence.append(
                (
                    "The complaint descriptions "
                    "have some textual "
                    "similarity."
                )
            )

        if type_similarity == 1.0:
            evidence.append(
                (
                    "Both complaints have the "
                    "same complaint type."
                )
            )

        if time_result["available"]:
            hours = time_result[
                "hours_difference"
            ]

            if hours <= 6:
                evidence.append(
                    (
                        "The complaints were "
                        "reported within a short "
                        "time interval."
                    )
                )

            elif hours <= 24:
                evidence.append(
                    (
                        "The complaints were "
                        "reported within the same "
                        "day."
                    )
                )

            elif hours <= 72:
                evidence.append(
                    (
                        "The complaints were "
                        "reported within a few "
                        "days of each other."
                    )
                )

            elif hours <= 168:
                evidence.append(
                    (
                        "The complaints were "
                        "reported within the same "
                        "week."
                    )
                )

        if consumer_result[
            "same_consumer"
        ]:
            evidence.append(
                (
                    "Both complaints were "
                    "reported under the same "
                    "consumer account."
                )
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

        latitude1 = radians(
            lat1
        )

        longitude1 = radians(
            lon1
        )

        latitude2 = radians(
            lat2
        )

        longitude2 = radians(
            lon2
        )

        latitude_difference = (
            latitude2
            - latitude1
        )

        longitude_difference = (
            longitude2
            - longitude1
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

        return (
            earth_radius_km * c
        )
