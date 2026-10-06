class UrgencyAnalyzer:

    def analyze(
        self,
        signals: dict,
    ):
        score = 0
        reasons = []

        flooding = signals.get(
            "possible_flooding",
            False,
        )

        large_leak = signals.get(
            "possible_large_leak",
            False,
        )

        leakage = signals.get(
            "possible_leakage",
            False,
        )

        public_road = signals.get(
            "public_road",
            False,
        )

        multiple_households = signals.get(
            "multiple_households",
            False,
        )

        area_wide_impact = signals.get(
            "area_wide_impact",
            False,
        )

        complete_service_loss = signals.get(
            "complete_service_loss",
            False,
        )

        weak_water_supply = signals.get(
            "weak_water_supply",
            False,
        )

        extended_duration = signals.get(
            "extended_duration",
            False,
        )

        if flooding:
            score += 3

            reasons.append(
                (
                    "Possible flooding or "
                    "uncontrolled water "
                    "accumulation."
                )
            )

        if large_leak:
            score += 3

            reasons.append(
                (
                    "Possible significant "
                    "water loss."
                )
            )

        elif leakage:
            score += 1

            reasons.append(
                (
                    "Possible water leakage "
                    "is reported."
                )
            )

        if public_road:
            score += 1

            reasons.append(
                (
                    "Concern may involve a "
                    "road or public access area."
                )
            )

        if multiple_households:
            score += 2

            reasons.append(
                (
                    "Multiple households may "
                    "be affected."
                )
            )

        if area_wide_impact:
            score += 3

            reasons.append(
                (
                    "The concern may affect a "
                    "wider service area or "
                    "community."
                )
            )

        if complete_service_loss:
            score += 2

            reasons.append(
                (
                    "Possible complete loss "
                    "of water service."
                )
            )

        elif weak_water_supply:
            score += 1

            reasons.append(
                (
                    "Weak or reduced water "
                    "service is reported."
                )
            )

        if extended_duration:
            score += 1

            reasons.append(
                (
                    "Concern may have continued "
                    "for an extended period."
                )
            )

        community_service_impact = (
            area_wide_impact
            and (
                complete_service_loss
                or weak_water_supply
            )
        )

        widespread_leak_impact = (
            leakage
            and (
                multiple_households
                or area_wide_impact
            )
        )

        major_incident_indicators = (
            flooding
            or (
                large_leak
                and (
                    multiple_households
                    or area_wide_impact
                )
            )
            or community_service_impact
            or widespread_leak_impact
        )

        if major_incident_indicators:
            level = "High"

        elif score >= 4:
            level = "Moderate"

        else:
            level = "Low"

        if not reasons:
            reasons.append(
                (
                    "No strong operational urgency "
                    "indicators were detected from "
                    "the available description."
                )
            )

        return {
            "level": level,
            "score": score,
            "reasons": reasons,

            "impact": {
                "multiple_households":
                    multiple_households,

                "area_wide":
                    area_wide_impact,

                "complete_service_loss":
                    complete_service_loss,

                "weak_water_supply":
                    weak_water_supply,

                "significant_leak":
                    large_leak,

                "flooding":
                    flooding,
            },

            "major_incident_indicators":
                major_incident_indicators,

            "human_review_required": True,
        }
