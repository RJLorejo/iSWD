from ai.app.classification.classifier import ComplaintClassifier
from ai.app.triage.signals import ComplaintSignalExtractor
from ai.app.triage.urgency import UrgencyAnalyzer
from ai.app.triage.review import ReviewAnalyzer


class ComplaintAnalyzer:

    def __init__(self):
        self.classifier = ComplaintClassifier()
        self.signal_extractor = ComplaintSignalExtractor()
        self.urgency_analyzer = UrgencyAnalyzer()
        self.review_analyzer = ReviewAnalyzer()

    def analyze(
        self,
        description: str,
    ):
        classification = self.classifier.predict(
            description
        )

        operational_analysis = (
            self.signal_extractor.extract(
                description
            )
        )

        signals = operational_analysis[
            "signals"
        ]

        urgency = self.urgency_analyzer.analyze(
            signals
        )

        review = self.review_analyzer.analyze(
            classification,
            signals,
        )

        supporting_evidence = (
            self._build_supporting_evidence(
                classification,
                operational_analysis,
            )
        )

        return {
            "classification": classification,
            "supporting_evidence": supporting_evidence,
            "operational_analysis": operational_analysis,
            "urgency": urgency,
            "review": review,
        }

    def _build_supporting_evidence(
        self,
        classification: dict,
        operational_analysis: dict,
    ):
        complaint_type = classification.get(
            "complaint_type",
            "Unknown",
        )

        signals = operational_analysis.get(
            "signals",
            {},
        )

        detected_evidence = (
            operational_analysis.get(
                "evidence",
                [],
            )
        )

        category_signal_map = {
            "Mainline Leakage": [
                "possible_leakage",
                "possible_large_leak",
                "public_road",
                "possible_flooding",
                "multiple_households",
            ],

            "Service Connection Leakage": [
                "possible_leakage",
            ],

            "Meter / Meter Stand Leakage": [
                "possible_leakage",
                "meter_issue",
            ],

            "No Water": [
                "complete_service_loss",
                "multiple_households",
                "extended_duration",
            ],

            "Low Water Pressure": [
                "weak_water_supply",
                "multiple_households",
                "extended_duration",
            ],

            "Dirty Water": [
                "dirty_water",
                "multiple_households",
            ],

            "Damaged / Detached Service Connection": [
                "possible_leakage",
            ],

            "Damaged / Detached Water Meter": [
                "meter_issue",
                "possible_leakage",
            ],

            "Malfunctioning Water Meter": [
                "meter_issue",
                "reading_discrepancy",
                "abnormal_consumption",
            ],

            "Erroneous Meter Reading": [
                "reading_discrepancy",
                "meter_issue",
            ],

            "High / Unusual Consumption": [
                "abnormal_consumption",
                "meter_issue",
            ],

            "Reclassification": [
                "classification_request",
            ],

            "Re-open Sealed Connection": [],

            "Temporary Disconnection": [],

            "Sealed Water Meter Concern": [
                "meter_issue",
            ],
        }

        signal_labels = {
            "public_road":
                "Road or public access area mentioned",

            "possible_flooding":
                "Possible flooding or overflow",

            "multiple_households":
                "Multiple households may be affected",

            "complete_service_loss":
                "Possible complete loss of water service",

            "weak_water_supply":
                "Weak or reduced water supply",

            "possible_leakage":
                "Possible water leakage",

            "possible_large_leak":
                "Possible significant water leakage",

            "dirty_water":
                "Dirty or abnormal water quality",

            "meter_issue":
                "Meter-related concern",

            "abnormal_consumption":
                "Unusually high water consumption or bill",

            "reading_discrepancy":
                "Possible meter-reading discrepancy",

            "classification_request":
                "Request to review account classification",

            "extended_duration":
                "Concern may have continued for an extended period",
        }

        relevant_signal_keys = (
            category_signal_map.get(
                complaint_type,
                [],
            )
        )

        indicators = []

        for signal_key in relevant_signal_keys:
            if not signals.get(
                signal_key,
                False,
            ):
                continue

            label = signal_labels.get(
                signal_key
            )

            if (
                label
                and label not in indicators
            ):
                indicators.append(
                    label
                )

        summary = self._classification_summary(
            complaint_type,
            signals,
        )

        if not indicators:
            for signal_key, detected in (
                signals.items()
            ):
                if not detected:
                    continue

                label = signal_labels.get(
                    signal_key
                )

                if (
                    label
                    and label not in indicators
                ):
                    indicators.append(
                        label
                    )

        return {
            "summary": summary,
            "indicators": indicators,
            "detected_evidence": detected_evidence,
        }

    def _classification_summary(
        self,
        complaint_type: str,
        signals: dict,
    ):
        if complaint_type == "Mainline Leakage":
            if (
                signals.get("possible_large_leak")
                and signals.get("multiple_households")
            ):
                return (
                    "The complaint indicates significant "
                    "water leakage that may affect multiple "
                    "households or a wider service area, "
                    "which supports the Mainline Leakage "
                    "classification."
                )

            if (
                signals.get("possible_leakage")
                and signals.get("public_road")
            ):
                return (
                    "The complaint describes possible water "
                    "leakage in a road or public area, which "
                    "supports the Mainline Leakage "
                    "classification."
                )

            if signals.get(
                "possible_leakage"
            ):
                return (
                    "The complaint contains water leakage "
                    "indicators and is most closely related "
                    "to a Mainline Leakage concern."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Service Connection Leakage"
        ):
            if signals.get(
                "possible_leakage"
            ):
                return (
                    "The complaint contains leakage "
                    "indicators that may involve the "
                    "consumer's service connection."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Meter / Meter Stand Leakage"
        ):
            if (
                signals.get("possible_leakage")
                and signals.get("meter_issue")
            ):
                return (
                    "The complaint indicates water leakage "
                    "around the water meter or meter stand, "
                    "which supports the Meter / Meter Stand "
                    "Leakage classification."
                )

            if signals.get(
                "possible_leakage"
            ):
                return (
                    "The complaint contains leakage "
                    "indicators and is most closely related "
                    "to a meter or meter stand leakage "
                    "concern."
                )

            return self._default_summary(
                complaint_type
            )

        if complaint_type == "No Water":
            if signals.get(
                "complete_service_loss"
            ):
                return (
                    "The complaint indicates that water "
                    "service may be completely unavailable, "
                    "which supports the No Water "
                    "classification."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Low Water Pressure"
        ):
            if signals.get(
                "weak_water_supply"
            ):
                return (
                    "The complaint indicates that water is "
                    "still available but the flow or "
                    "pressure is weak, which supports the "
                    "Low Water Pressure classification."
                )

            return self._default_summary(
                complaint_type
            )

        if complaint_type == "Dirty Water":
            if signals.get(
                "dirty_water"
            ):
                return (
                    "The complaint describes dirty, cloudy, "
                    "brown, sandy, or otherwise abnormal "
                    "water quality, which supports the "
                    "Dirty Water classification."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Damaged / Detached Service Connection"
        ):
            if signals.get(
                "possible_leakage"
            ):
                return (
                    "The complaint describes a possible "
                    "damaged or detached household service "
                    "connection with associated leakage."
                )

            return (
                "The complaint describes a physical "
                "problem involving the consumer's service "
                "connection, which supports the Damaged / "
                "Detached Service Connection "
                "classification."
            )

        if (
            complaint_type
            == "Damaged / Detached Water Meter"
        ):
            if signals.get(
                "meter_issue"
            ):
                return (
                    "The complaint describes a physical "
                    "problem involving the water meter, "
                    "which supports the Damaged / Detached "
                    "Water Meter classification."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Malfunctioning Water Meter"
        ):
            if signals.get(
                "meter_issue"
            ):
                return (
                    "The complaint describes abnormal "
                    "water-meter behavior that may indicate "
                    "a malfunctioning meter."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Erroneous Meter Reading"
        ):
            if signals.get(
                "reading_discrepancy"
            ):
                return (
                    "The complaint indicates a possible "
                    "difference between the recorded meter "
                    "reading and the actual meter reading, "
                    "which supports the Erroneous Meter "
                    "Reading classification."
                )

            if signals.get(
                "meter_issue"
            ):
                return (
                    "The complaint contains meter-related "
                    "information that may involve an "
                    "incorrect recorded reading."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "High / Unusual Consumption"
        ):
            if signals.get(
                "abnormal_consumption"
            ):
                return (
                    "The complaint indicates unusually high "
                    "water consumption or a significant "
                    "increase in the water bill, which "
                    "supports the High / Unusual "
                    "Consumption classification."
                )

            return self._default_summary(
                complaint_type
            )

        if complaint_type == "Reclassification":
            if signals.get(
                "classification_request"
            ):
                return (
                    "The complaint requests a review or "
                    "correction of the consumer's water "
                    "account classification."
                )

            return self._default_summary(
                complaint_type
            )

        if (
            complaint_type
            == "Re-open Sealed Connection"
        ):
            return (
                "The request concerns reopening or "
                "reconnecting a previously sealed water "
                "service connection."
            )

        if (
            complaint_type
            == "Temporary Disconnection"
        ):
            return (
                "The request concerns temporarily "
                "disconnecting or suspending water service "
                "for an existing account."
            )

        if (
            complaint_type
            == "Sealed Water Meter Concern"
        ):
            if signals.get(
                "meter_issue"
            ):
                return (
                    "The complaint concerns the sealed "
                    "status of the consumer's water meter "
                    "and may require Customer Service "
                    "verification."
                )

            return (
                "The request concerns the sealed status "
                "of the consumer's water meter."
            )

        return self._default_summary(
            complaint_type
        )

    def _default_summary(
        self,
        complaint_type: str,
    ):
        return (
            "The complaint description is most similar "
            f"to reported {complaint_type} concerns."
        )
