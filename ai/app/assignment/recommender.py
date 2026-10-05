from datetime import datetime, timezone
from math import atan2, cos, radians, sin, sqrt
from typing import Any


class PlumberAssignmentRecommender:

    def __init__(self):
        self.workload_weight = 0.40
        self.proximity_weight = 0.35
        self.recent_work_weight = 0.25

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

            recent_assignments = plumber.get(
                "recent_assignments",
                [],
            )

            service_area = plumber.get(
                "service_area"
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

            recent_work = (
                self._recent_work_analysis(
                    complaint,
                    recent_assignments,
                )
            )

            score = self._calculate_score(
                workload_score=(
                    workload_analysis["score"]
                ),
                proximity_score=(
                    proximity["score"]
                ),
                proximity_available=(
                    proximity["available"]
                ),
                recent_work_score=(
                    recent_work["score"]
                ),
                recent_work_available=(
                    recent_work["available"]
                ),
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
                recent_work=recent_work,
                service_area=service_area,
            )

            considerations = (
                self._build_considerations(
                    availability=availability,
                    workload=workload_analysis,
                    proximity=proximity,
                    recent_work=recent_work,
                    service_area=service_area,
                )
            )

            recommendations.append({
                "plumber_id": plumber.get("id"),
                "name": plumber.get("name"),

                "service_area": service_area,

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

                "nearest_recent_assignment_km": (
                    recent_work["distance_km"]
                ),

                "nearest_recent_complaint_no": (
                    recent_work["complaint_no"]
                ),

                "recent_assignment_age_hours": (
                    recent_work["age_hours"]
                ),

                "recent_assignment_age_days": (
                    recent_work["age_days"]
                ),

                "recent_assignment_type": (
                    recent_work["complaint_type"]
                ),

                "recent_type_match": (
                    recent_work["type_match"]
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
            }

        if has_in_progress:
            if workload >= 3:
                return {
                    "status": "Busy",
                }

            return {
                "status": "Working",
            }

        if workload >= 3:
            return {
                "status": "Busy",
            }

        return {
            "status": "Assigned",
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

        score = (
            self._current_proximity_score(
                nearest_distance
            )
        )

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

    def _current_proximity_score(
        self,
        distance_km: float,
    ) -> float:
        if distance_km <= 0.25:
            return 1.0

        if distance_km <= 0.50:
            return 0.90

        if distance_km <= 1.00:
            return 0.75

        if distance_km <= 2.00:
            return 0.50

        if distance_km <= 5.00:
            return 0.25

        return 0.10

    def _recent_work_analysis(
        self,
        complaint: dict,
        recent_assignments: list[dict],
    ):
        complaint_latitude = complaint.get(
            "latitude"
        )

        complaint_longitude = complaint.get(
            "longitude"
        )

        complaint_type = self._normalize_text(
            complaint.get(
                "complaint_type"
            )
        )

        if (
            complaint_latitude is None
            or complaint_longitude is None
        ):
            return self._empty_recent_work()

        best_match = None

        for assignment in recent_assignments:
            latitude = assignment.get(
                "latitude"
            )

            longitude = assignment.get(
                "longitude"
            )

            completed_at = assignment.get(
                "completed_at"
            )

            if (
                latitude is None
                or longitude is None
                or completed_at is None
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

            age_hours = (
                self._hours_since(
                    completed_at
                )
            )

            if age_hours is None:
                continue

            if age_hours < 0:
                continue

            age_days = (
                age_hours / 24
            )

            if age_days > 14:
                continue

            distance_score = (
                self._recent_distance_score(
                    distance
                )
            )

            recency_score = (
                self._recent_recency_score(
                    age_hours
                )
            )

            assignment_type = (
                self._normalize_text(
                    assignment.get(
                        "complaint_type"
                    )
                )
            )

            type_match = bool(
                complaint_type
                and assignment_type
                and complaint_type
                == assignment_type
            )

            type_score = (
                1.0
                if type_match
                else 0.70
            )

            combined_score = (
                distance_score * 0.50
                + recency_score * 0.35
                + type_score * 0.15
            )

            candidate = {
                "available": True,

                "score": max(
                    0.0,
                    min(
                        combined_score,
                        1.0,
                    ),
                ),

                "distance_km": distance,

                "complaint_no": (
                    assignment.get(
                        "complaint_no"
                    )
                ),

                "complaint_type": (
                    assignment.get(
                        "complaint_type"
                    )
                ),

                "age_hours": age_hours,

                "age_days": age_days,

                "type_match": type_match,
            }

            if (
                best_match is None
                or candidate["score"]
                > best_match["score"]
            ):
                best_match = candidate

        if best_match is None:
            return self._empty_recent_work()

        return {
            "available": True,

            "score": round(
                best_match["score"],
                4,
            ),

            "distance_km": round(
                best_match["distance_km"],
                3,
            ),

            "complaint_no": (
                best_match["complaint_no"]
            ),

            "complaint_type": (
                best_match["complaint_type"]
            ),

            "age_hours": round(
                best_match["age_hours"],
                1,
            ),

            "age_days": round(
                best_match["age_days"],
                1,
            ),

            "type_match": (
                best_match["type_match"]
            ),
        }

    def _empty_recent_work(self):
        return {
            "available": False,
            "score": 0.0,
            "distance_km": None,
            "complaint_no": None,
            "complaint_type": None,
            "age_hours": None,
            "age_days": None,
            "type_match": False,
        }

    def _recent_distance_score(
        self,
        distance_km: float,
    ) -> float:
        if distance_km <= 0.25:
            return 1.0

        if distance_km <= 0.50:
            return 0.85

        if distance_km <= 1.00:
            return 0.65

        if distance_km <= 2.00:
            return 0.40

        return 0.15

    def _recent_recency_score(
        self,
        age_hours: float,
    ) -> float:
        if age_hours <= 24:
            return 1.0

        if age_hours <= 72:
            return 0.90

        if age_hours <= 168:
            return 0.70

        if age_hours <= 336:
            return 0.40

        return 0.0

    def _calculate_score(
        self,
        workload_score: float,
        proximity_score: float,
        proximity_available: bool,
        recent_work_score: float,
        recent_work_available: bool,
    ):
        factors = [
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

        if recent_work_available:
            factors.append(
                (
                    recent_work_score,
                    self.recent_work_weight,
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
        recent_work: dict,
        service_area: dict | None,
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

            if distance <= 0.25:
                reasons.append(
                    "The plumber has active work very near the new complaint location."
                )

            elif distance <= 0.50:
                reasons.append(
                    "The plumber has active work near the new complaint location."
                )

            elif distance <= 1.00:
                reasons.append(
                    "The plumber has active work within 1 kilometer of the new complaint."
                )

            elif distance <= 2.00:
                reasons.append(
                    "The plumber has active work within the surrounding area."
                )

        if recent_work["available"]:
            distance = recent_work[
                "distance_km"
            ]

            age_days = recent_work[
                "age_days"
            ]

            if (
                distance is not None
                and distance <= 0.50
            ):
                reasons.append(
                    "The plumber recently completed maintenance near this complaint location."
                )

            elif (
                distance is not None
                and distance <= 1.00
            ):
                reasons.append(
                    "The plumber recently handled maintenance within 1 kilometer of this complaint."
                )

            if recent_work["type_match"]:
                reasons.append(
                    "The plumber recently handled the same complaint type in the area."
                )

            if (
                age_days is not None
                and age_days <= 3
            ):
                reasons.append(
                    "The nearby maintenance work was completed within the last 3 days."
                )

        if (
            service_area
            and service_area.get("name")
        ):
            reasons.append(
                "Permanent service area: "
                + str(
                    service_area.get(
                        "name"
                    )
                )
                + "."
            )

        return reasons

    def _build_considerations(
        self,
        availability: dict,
        workload: dict,
        proximity: dict,
        recent_work: dict,
        service_area: dict | None,
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
                "No usable active-assignment location was available for current-work proximity comparison."
            )

        if not recent_work["available"]:
            considerations.append(
                "No usable recent maintenance work was available for recent-area comparison."
            )

        if (
            service_area is None
            or not service_area.get("name")
        ):
            considerations.append(
                "No permanent service area is currently assigned to this plumber."
            )

        return considerations

    def _hours_since(
        self,
        completed_at: Any,
    ) -> float | None:
        if completed_at is None:
            return None

        if isinstance(
            completed_at,
            datetime,
        ):
            completed = completed_at

        elif isinstance(
            completed_at,
            str,
        ):
            value = completed_at.strip()

            if not value:
                return None

            if value.endswith("Z"):
                value = (
                    value[:-1]
                    + "+00:00"
                )

            try:
                completed = (
                    datetime.fromisoformat(
                        value
                    )
                )
            except ValueError:
                return None

        else:
            return None

        if completed.tzinfo is None:
            completed = completed.replace(
                tzinfo=timezone.utc
            )
        else:
            completed = (
                completed.astimezone(
                    timezone.utc
                )
            )

        now = datetime.now(
            timezone.utc
        )

        difference = now - completed

        return (
            difference.total_seconds()
            / 3600
        )

    def _normalize_text(
        self,
        value: Any,
    ) -> str:
        if value is None:
            return ""

        return " ".join(
            str(value)
            .strip()
            .lower()
            .split()
        )

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
