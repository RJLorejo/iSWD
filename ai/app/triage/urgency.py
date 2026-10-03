class UrgencyAnalyzer:

    def analyze(
        self,
        signals: dict,
    ):
        score = 0
        reasons = []

        if signals.get(
            "possible_flooding"
        ):
            score += 3
            reasons.append(
                "Possible flooding or uncontrolled water accumulation."
            )

        if signals.get(
            "possible_large_leak"
        ):
            score += 3
            reasons.append(
                "Possible significant water loss."
            )

        elif signals.get(
            "possible_leakage"
        ):
            score += 1
            reasons.append(
                "Possible water leakage is reported."
            )

        if signals.get(
            "public_road"
        ):
            score += 2
            reasons.append(
                "Concern may involve a road or public access area."
            )

        if signals.get(
            "multiple_households"
        ):
            score += 2
            reasons.append(
                "Multiple households may be affected."
            )

        if signals.get(
            "complete_service_loss"
        ):
            score += 2
            reasons.append(
                "Possible complete loss of water service."
            )

        if signals.get(
            "extended_duration"
        ):
            score += 1
            reasons.append(
                "Concern may have continued for an extended period."
            )

        if score >= 6:
            level = "High"

        elif score >= 3:
            level = "Moderate"

        else:
            level = "Low"

        if not reasons:
            reasons.append(
                "No strong operational urgency indicators were detected from the available description."
            )

        return {
            "level": level,
            "score": score,
            "reasons": reasons,
            "human_review_required": True,
        }
