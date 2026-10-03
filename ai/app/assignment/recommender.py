from math import atan2, cos, radians, sin, sqrt
from typing import Any


class PlumberAssignmentRecommender:

    def __init__(self):
        self.availability_weight = 0.40
        self.workload_weight = 0.35
        self.proximity_weight = 0.25

    def recommend(
        self,
        complaint: dict[str, Any],
        plumbers: list[dict[str, Any]],
        limit: int = 5,
    ):
        if not plumbers:
            return {
                "has_recommendations": False,
                "count": 0,
                "recommendations": [],
                "human_confirmation_required": True,
            }

        recommendations = []

        for plumber in plumbers:
            workload = int(
                plumber.get(
                    "active_workload",
                    0,
                )
            )

            assignments = plumber.get(
                "active_assignments",
                [],
            )

            availability = (
                self._availability_analysis(
                    workload,
                    assignments,
                )
            )

            workload_analysis = (
                self._workload_analysis(
                    workload
                )
            )

            proximity = (
                self._proximity_analysis(
                    complaint,
                    assignments,
                )
            )

            score = self._calculate_score(
                availability_score=availability[
                    "score"
                ],
                workload_score=workload_analysis[
                    "score"
                ],
                proximity_score=proximity[
                    "score"
                ],
                proximity_available=proximity[
                    "available"
                ],
            )

            level = (
                self._recommendation_level(
                    score
                )
            )

            reasons = self._build_reasons(
                availability=availability,
                workload=workload_analysis,
                proximity=proximity,
            )

            considerations = (
                self._build_considerations(
                    availability=availability,
                    workload=workload_analysis,
                    proximity=proximity,
                )
            )

            recommendations.append({
                "plumber_id": plumber.get("id"),
                "name": plumber.get("name"),
                "availability": availability[
                    "status"
                ],
                "active_workload": workload,
                "nearest_active_assignment_km": (
                    proximity["distance_km"]
                ),
                "nearest_active_complaint_no": (
                    proximity["complaint_no"]
                ),
                "recommendation": level,
                "score": round(
                    score,
                    4,
                ),
                "score_percentage": round(
                    score * 100,
                    2,
                ),
                "reasons": reasons,
                "considerations": considerations,
                "human_confirmation_required": True,
            })

        recommendations.sort(
            key=lambda item: (
                item["score"],
                -item["active_workload"],
            ),
            reverse=True,
        )

        recommendations = recommendations[
            :limit
        ]

        return {
            "has_recommendations": (
                len(recommendations) > 0
            ),
            "count": len(recommendations),
            "recommendations": recommendations,
            "human_confirmation_required": True,
        }

    def _availability_analysis(
        self,
        workload: int,
        assignments: list[dict],
    ):
        has_in_progress = any(
            assignment.get("status")
            == "In Progress"
            for assignment in assignments
        )

        if workload == 0:
            return {
                "status": "Available",
                "score": 1.0,
            }

        if has_in_progress:
            if workload >= 3:
                return {
                    "status": "Busy",
                    "score": 0.20,
                }

            return {
                "status": "Working",
                "score": 0.45,
            }

        if workload >= 3:
            return {
                "status": "Busy",
                "score": 0.30,
            }

        return {
            "status": "Assigned",
            "score": 0.60,
        }

    def _workload_analysis(
        self,
        workload: int,
    ):
        if workload <= 0:
            score = 1.0
            level = "No active assignments"

        elif workload == 1:
            score = 0.80
            level = "Light workload"

        elif workload == 2:
            score = 0.55
            level = "Moderate workload"

        elif workload == 3:
            score = 0.30
            level = "High workload"

        else:
            score = 0.10
            level = "Very high workload"

        return {
            "score": score,
            "level": level,
        }

    def _proximity_analysis(
        self,
        complaint: dict,
        assignments: list[dict],
    ):
        complaint_latitude = complaint.get(
            "latitude"
        )

        complaint_longitude = complaint.get(
            "longitude"
        )

        if (
            complaint_latitude is None
            or complaint_longitude is None
        ):
            return {
                "available": False,
                "score": 0.0,
                "distance_km": None,
                "complaint_no": None,
            }

        nearest_distance = None
        nearest_complaint_no = None

        for assignment in assignments:
            latitude = assignment.get(
                "latitude"
            )

            longitude = assignment.get(
                "longitude"
            )

            if (
                latitude is None
                or longitude is None
            ):
                continue

            try:
                distance = (
                    self._haversine_distance(
                        float(
                            complaint_latitude
                        ),
                        float(
                            complaint_longitude
                        ),
                        float(latitude),
                        float(longitude),
                    )
                )
            except (
                TypeError,
                ValueError,
            ):
                continue

            if (
                nearest_distance is None
                or distance
                < nearest_distance
            ):
                nearest_distance = distance
                nearest_complaint_no = (
                    assignment.get(
                        "complaint_no"
                    )
                )

        if nearest_distance is None:
            return {
                "available": False,
                "score": 0.0,
                "distance_km": None,
                "complaint_no": None,
            }

        if nearest_distance <= 0.50:
            score = 1.0

        elif nearest_distance <= 1.00:
            score = 0.90

        elif nearest_distance <= 2.00:
            score = 0.75

        elif nearest_distance <= 5.00:
            score = 0.50

        elif nearest_distance <= 10.00:
            score = 0.25

        else:
            score = 0.10

        return {
            "available": True,
            "score": score,
            "distance_km": round(
                nearest_distance,
                3,
            ),
            "complaint_no": (
                nearest_complaint_no
            ),
        }

    def _calculate_score(
        self,
        availability_score: float,
        workload_score: float,
        proximity_score: float,
        proximity_available: bool,
    ):
        factors = [
            (
                availability_score,
                self.availability_weight,
            ),
            (
                workload_score,
                self.workload_weight,
            ),
        ]

        if proximity_available:
            factors.append(
                (
                    proximity_score,
                    self.proximity_weight,
                )
            )

        total_weight = sum(
            weight
            for _, weight in factors
        )

        if total_weight <= 0:
            return 0.0

        score = sum(
            value * weight
            for value, weight in factors
        ) / total_weight

        return max(
            0.0,
            min(
                score,
                1.0,
            ),
        )

    def _recommendation_level(
        self,
        score: float,
    ):
        if score >= 0.80:
            return "Highly Suitable"

        if score >= 0.65:
            return "Suitable"

        if score >= 0.45:
            return "Alternative"

        return "Lower Priority"

    def _build_reasons(
        self,
        availability: dict,
        workload: dict,
        proximity: dict,
    ):
        reasons = []

        if availability["status"] == "Available":
            reasons.append(
                "No active field assignment was found for this plumber."
            )

        elif availability["status"] == "Assigned":
            reasons.append(
                "The plumber has an assignment but no active work was detected."
            )

        elif availability["status"] == "Working":
            reasons.append(
                "The plumber currently has field work in progress."
            )

        if workload["level"] in [
            "No active assignments",
            "Light workload",
        ]:
            reasons.append(
                "The plumber currently has a relatively light workload."
            )

        if (
            proximity["available"]
            and proximity["distance_km"]
            is not None
        ):
            distance = proximity[
                "distance_km"
            ]

            if distance <= 2:
                reasons.append(
                    "An active assignment is near the new complaint location."
                )

            elif distance <= 5:
                reasons.append(
                    "An active assignment is within the surrounding service area."
                )

        return reasons

    def _build_considerations(
        self,
        availability: dict,
        workload: dict,
        proximity: dict,
    ):
        considerations = []

        if availability["status"] == "Busy":
            considerations.append(
                "The plumber already has several active assignments."
            )

        elif availability["status"] == "Working":
            considerations.append(
                "The plumber is already handling an in-progress assignment."
            )

        if workload["level"] in [
            "High workload",
            "Very high workload",
        ]:
            considerations.append(
                "Current workload may reduce immediate assignment capacity."
            )

        if not proximity["available"]:
            considerations.append(
                "No usable active-assignment location was available for proximity comparison."
            )

        return considerations

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

        return (
            earth_radius_km * c
        )
