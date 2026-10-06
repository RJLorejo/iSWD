from collections import Counter
from math import atan2, cos, radians, sin, sqrt
from typing import Any


class AreaIncidentAnalyzer:

    def __init__(self):
        self.compatible_types = {
            "Mainline Leakage",
            "No Water",
            "Low Water Pressure",
            "Dirty Water",
        }

        self.strong_incident_types = {
            "Mainline Leakage",
            "No Water",
            "Low Water Pressure",
        }

        self.high_impact_types = {
            "Mainline Leakage",
            "No Water",
        }

        self.cluster_radius_km = 1.0
        self.maximum_time_hours = 168.0

    def analyze(
        self,
        complaints: list[dict[str, Any]],
    ):
        prepared = self._prepare_complaints(
            complaints
        )

        if not prepared:
            return self._empty_result(
                (
                    "No usable complaints were "
                    "provided for area incident "
                    "analysis."
                )
            )

        location_complaints = [
            complaint
            for complaint in prepared
            if self._has_location(
                complaint
            )
        ]

        if len(location_complaints) < 2:
            return self._empty_result(
                (
                    "At least two complaints with "
                    "valid locations are required "
                    "for area incident analysis."
                ),
                complaint_count=len(
                    prepared
                ),
            )

        clusters = self._build_clusters(
            location_complaints
        )

        analyzed_clusters = []

        for cluster in clusters:
            result = self._analyze_cluster(
                cluster
            )

            if result[
                "possible_area_incident"
            ]:
                analyzed_clusters.append(
                    result
                )

        analyzed_clusters.sort(
            key=self._cluster_sort_key,
            reverse=True,
        )

        if not analyzed_clusters:
            return {
                "possible_area_incident": False,
                "incident_count": 0,
                "complaint_count": len(
                    prepared
                ),
                "incidents": [],
                "reason": (
                    "The available complaints do "
                    "not currently provide enough "
                    "combined geographic, consumer, "
                    "time, and complaint-pattern "
                    "evidence for a possible "
                    "area-wide incident."
                ),
                "human_confirmation_required": True,
            }

        primary = analyzed_clusters[0]

        return {
            "possible_area_incident": True,

            "incident_count": len(
                analyzed_clusters
            ),

            "complaint_count": len(
                prepared
            ),

            "primary_incident": primary,

            "incidents": analyzed_clusters,

            "human_confirmation_required": True,
        }

    def _prepare_complaints(
        self,
        complaints: list[dict[str, Any]],
    ):
        prepared = []

        for complaint in complaints:
            if not isinstance(
                complaint,
                dict,
            ):
                continue

            item = dict(
                complaint
            )

            complaint_type = item.get(
                "complaint_type"
            )

            if complaint_type is not None:
                item["complaint_type"] = (
                    str(
                        complaint_type
                    ).strip()
                )

            item["hours_difference"] = (
                self._safe_float(
                    item.get(
                        "hours_difference"
                    )
                )
            )

            prepared.append(
                item
            )

        return prepared

    def _build_clusters(
        self,
        complaints: list[dict[str, Any]],
    ):
        adjacency = {
            index: set()
            for index in range(
                len(complaints)
            )
        }

        for first_index in range(
            len(complaints)
        ):
            for second_index in range(
                first_index + 1,
                len(complaints),
            ):
                first = complaints[
                    first_index
                ]

                second = complaints[
                    second_index
                ]

                distance = (
                    self._distance_between(
                        first,
                        second,
                    )
                )

                if distance is None:
                    continue

                if (
                    distance
                    > self.cluster_radius_km
                ):
                    continue

                if not self._time_compatible(
                    first,
                    second,
                ):
                    continue

                adjacency[
                    first_index
                ].add(
                    second_index
                )

                adjacency[
                    second_index
                ].add(
                    first_index
                )

        visited = set()
        clusters = []

        for start_index in range(
            len(complaints)
        ):
            if start_index in visited:
                continue

            stack = [
                start_index
            ]

            component = []

            while stack:
                current = stack.pop()

                if current in visited:
                    continue

                visited.add(
                    current
                )

                component.append(
                    complaints[current]
                )

                for neighbor in adjacency[
                    current
                ]:
                    if neighbor not in visited:
                        stack.append(
                            neighbor
                        )

            if len(component) >= 2:
                clusters.append(
                    component
                )

        return clusters

    def _analyze_cluster(
        self,
        complaints: list[dict[str, Any]],
    ):
        complaint_count = len(
            complaints
        )

        consumers = {
            complaint.get(
                "consumer_id"
            )
            for complaint in complaints
            if complaint.get(
                "consumer_id"
            ) is not None
        }

        affected_consumer_count = len(
            consumers
        )

        complaint_types = Counter(
            complaint.get(
                "complaint_type"
            )
            for complaint in complaints
            if complaint.get(
                "complaint_type"
            )
        )

        compatible_count = sum(
            count
            for complaint_type, count
            in complaint_types.items()
            if complaint_type
            in self.compatible_types
        )

        strong_incident_count = sum(
            count
            for complaint_type, count
            in complaint_types.items()
            if complaint_type
            in self.strong_incident_types
        )

        maximum_distance = (
            self._maximum_cluster_distance(
                complaints
            )
        )

        time_span = (
            self._calculate_time_span(
                complaints
            )
        )

        mainline_count = (
            complaint_types.get(
                "Mainline Leakage",
                0,
            )
        )

        no_water_count = (
            complaint_types.get(
                "No Water",
                0,
            )
        )

        low_pressure_count = (
            complaint_types.get(
                "Low Water Pressure",
                0,
            )
        )

        dirty_water_count = (
            complaint_types.get(
                "Dirty Water",
                0,
            )
        )

        distinct_compatible_types = {
            complaint_type
            for complaint_type
            in complaint_types
            if complaint_type
            in self.compatible_types
        }

        distinct_strong_types = {
            complaint_type
            for complaint_type
            in complaint_types
            if complaint_type
            in self.strong_incident_types
        }

        enough_complaints = (
            complaint_count >= 3
        )

        enough_consumers = (
            affected_consumer_count >= 2
        )

        compatible_pattern = (
            compatible_count >= 3
        )

        strong_service_pattern = (
            strong_incident_count >= 3
        )

        cross_type_pattern = (
            len(
                distinct_strong_types
            ) >= 2
        )

        possible_area_incident = (
            enough_complaints
            and enough_consumers
            and compatible_pattern
            and (
                strong_service_pattern
                or cross_type_pattern
            )
        )

        possible_source = None

        if (
            possible_area_incident
            and mainline_count > 0
        ):
            possible_source = (
                "Mainline Leakage"
            )

        dominant_signals = []

        if mainline_count > 0:
            dominant_signals.append(
                "possible_large_leak_source"
            )

        if no_water_count > 0:
            dominant_signals.append(
                "complete_service_loss"
            )

        if low_pressure_count > 0:
            dominant_signals.append(
                "weak_water_supply"
            )

        if dirty_water_count > 0:
            dominant_signals.append(
                "water_quality_concern"
            )

        if affected_consumer_count >= 3:
            dominant_signals.append(
                "multiple_consumers_affected"
            )

        if len(
            distinct_compatible_types
        ) >= 2:
            dominant_signals.append(
                "multiple_related_symptoms"
            )

        suggested_urgency = (
            self._suggest_incident_urgency(
                possible_area_incident=(
                    possible_area_incident
                ),
                complaint_count=(
                    complaint_count
                ),
                consumer_count=(
                    affected_consumer_count
                ),
                mainline_count=(
                    mainline_count
                ),
                no_water_count=(
                    no_water_count
                ),
                low_pressure_count=(
                    low_pressure_count
                ),
                maximum_distance=(
                    maximum_distance
                ),
            )
        )

        reason = (
            self._build_reason(
                possible_area_incident=(
                    possible_area_incident
                ),
                complaint_count=(
                    complaint_count
                ),
                consumer_count=(
                    affected_consumer_count
                ),
                mainline_count=(
                    mainline_count
                ),
                no_water_count=(
                    no_water_count
                ),
                low_pressure_count=(
                    low_pressure_count
                ),
                dirty_water_count=(
                    dirty_water_count
                ),
                maximum_distance=(
                    maximum_distance
                ),
            )
        )

        complaint_items = []

        for complaint in complaints:
            complaint_items.append({
                "id": complaint.get(
                    "id"
                ),

                "complaint_no": (
                    complaint.get(
                        "complaint_no"
                    )
                ),

                "consumer_id": (
                    complaint.get(
                        "consumer_id"
                    )
                ),

                "complaint_type": (
                    complaint.get(
                        "complaint_type"
                    )
                ),

                "status": complaint.get(
                    "status"
                ),

                "latitude": complaint.get(
                    "latitude"
                ),

                "longitude": complaint.get(
                    "longitude"
                ),

                "hours_difference": (
                    complaint.get(
                        "hours_difference"
                    )
                ),
            })

        return {
            "possible_area_incident": (
                possible_area_incident
            ),

            "complaint_count": (
                complaint_count
            ),

            "affected_consumer_count": (
                affected_consumer_count
            ),

            "area_radius_km": (
                round(
                    maximum_distance,
                    3,
                )
                if maximum_distance
                is not None
                else None
            ),

            "time_span_hours": (
                round(
                    time_span,
                    2,
                )
                if time_span
                is not None
                else None
            ),

            "complaint_types": dict(
                complaint_types
            ),

            "possible_source": (
                possible_source
            ),

            "dominant_signals": (
                dominant_signals
            ),

            "suggested_urgency": (
                suggested_urgency
            ),

            "reason": reason,

            "complaints": (
                complaint_items
            ),

            "human_confirmation_required": True,
        }

    def _suggest_incident_urgency(
        self,
        possible_area_incident: bool,
        complaint_count: int,
        consumer_count: int,
        mainline_count: int,
        no_water_count: int,
        low_pressure_count: int,
        maximum_distance: float | None,
    ):
        if not possible_area_incident:
            return "Low"

        widespread_service_loss = (
            no_water_count >= 2
            and consumer_count >= 3
        )

        mainline_with_area_impact = (
            mainline_count >= 1
            and consumer_count >= 3
            and (
                no_water_count >= 1
                or low_pressure_count >= 1
            )
        )

        many_consumers_affected = (
            consumer_count >= 5
        )

        dense_cluster = (
            maximum_distance is not None
            and maximum_distance <= 0.50
            and complaint_count >= 3
            and consumer_count >= 3
        )

        if (
            widespread_service_loss
            or mainline_with_area_impact
            or many_consumers_affected
            or dense_cluster
        ):
            return "High"

        return "Moderate"

    def _build_reason(
        self,
        possible_area_incident: bool,
        complaint_count: int,
        consumer_count: int,
        mainline_count: int,
        no_water_count: int,
        low_pressure_count: int,
        dirty_water_count: int,
        maximum_distance: float | None,
    ):
        if not possible_area_incident:
            return (
                "The complaints are geographically "
                "nearby, but the current number of "
                "reports, distinct consumers, or "
                "complaint pattern is not sufficient "
                "to flag a possible area-wide "
                "incident."
            )

        parts = [
            (
                f"{complaint_count} nearby "
                f"complaints from "
                f"{consumer_count} distinct "
                f"consumers form a possible "
                f"area incident."
            )
        ]

        if maximum_distance is not None:
            parts.append(
                (
                    "The complaints are within "
                    f"approximately "
                    f"{maximum_distance:.2f} km "
                    "of each other."
                )
            )

        pattern_parts = []

        if mainline_count:
            pattern_parts.append(
                (
                    f"{mainline_count} Mainline "
                    "Leakage report"
                    + (
                        "s"
                        if mainline_count != 1
                        else ""
                    )
                )
            )

        if no_water_count:
            pattern_parts.append(
                (
                    f"{no_water_count} No Water "
                    "report"
                    + (
                        "s"
                        if no_water_count != 1
                        else ""
                    )
                )
            )

        if low_pressure_count:
            pattern_parts.append(
                (
                    f"{low_pressure_count} Low "
                    "Water Pressure report"
                    + (
                        "s"
                        if low_pressure_count != 1
                        else ""
                    )
                )
            )

        if dirty_water_count:
            pattern_parts.append(
                (
                    f"{dirty_water_count} Dirty "
                    "Water report"
                    + (
                        "s"
                        if dirty_water_count != 1
                        else ""
                    )
                )
            )

        if pattern_parts:
            parts.append(
                (
                    "The complaint pattern includes "
                    + ", ".join(
                        pattern_parts
                    )
                    + "."
                )
            )

        if mainline_count:
            parts.append(
                (
                    "A Mainline Leakage report may "
                    "be a possible source of the "
                    "nearby service symptoms, but "
                    "this requires SWD confirmation."
                )
            )

        return " ".join(
            parts
        )

    def _calculate_time_span(
        self,
        complaints: list[dict[str, Any]],
    ):
        hours = [
            complaint.get(
                "hours_difference"
            )
            for complaint in complaints
            if complaint.get(
                "hours_difference"
            ) is not None
        ]

        if len(hours) < 2:
            return None

        return max(hours) - min(hours)

    def _time_compatible(
        self,
        first: dict,
        second: dict,
    ):
        first_hours = first.get(
            "hours_difference"
        )

        second_hours = second.get(
            "hours_difference"
        )

        if (
            first_hours is None
            or second_hours is None
        ):
            return True

        difference = abs(
            first_hours
            - second_hours
        )

        return (
            difference
            <= self.maximum_time_hours
        )

    def _maximum_cluster_distance(
        self,
        complaints: list[dict[str, Any]],
    ):
        maximum = 0.0
        found = False

        for first_index in range(
            len(complaints)
        ):
            for second_index in range(
                first_index + 1,
                len(complaints),
            ):
                distance = (
                    self._distance_between(
                        complaints[
                            first_index
                        ],
                        complaints[
                            second_index
                        ],
                    )
                )

                if distance is None:
                    continue

                found = True

                maximum = max(
                    maximum,
                    distance,
                )

        if not found:
            return None

        return maximum

    def _distance_between(
        self,
        first: dict,
        second: dict,
    ):
        if (
            not self._has_location(
                first
            )
            or not self._has_location(
                second
            )
        ):
            return None

        try:
            return self._haversine_distance(
                float(
                    first["latitude"]
                ),
                float(
                    first["longitude"]
                ),
                float(
                    second["latitude"]
                ),
                float(
                    second["longitude"]
                ),
            )

        except (
            TypeError,
            ValueError,
        ):
            return None

    def _has_location(
        self,
        complaint: dict,
    ):
        return (
            complaint.get(
                "latitude"
            ) is not None
            and complaint.get(
                "longitude"
            ) is not None
        )

    def _safe_float(
        self,
        value: Any,
    ):
        if value is None:
            return None

        try:
            return float(
                value
            )

        except (
            TypeError,
            ValueError,
        ):
            return None

    def _cluster_sort_key(
        self,
        incident: dict,
    ):
        urgency_rank = {
            "High": 3,
            "Moderate": 2,
            "Low": 1,
        }

        return (
            urgency_rank.get(
                incident.get(
                    "suggested_urgency"
                ),
                0,
            ),
            incident.get(
                "affected_consumer_count",
                0,
            ),
            incident.get(
                "complaint_count",
                0,
            ),
        )

    def _empty_result(
        self,
        reason: str,
        complaint_count: int = 0,
    ):
        return {
            "possible_area_incident": False,
            "incident_count": 0,
            "complaint_count": complaint_count,
            "incidents": [],
            "reason": reason,
            "human_confirmation_required": True,
        }

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
