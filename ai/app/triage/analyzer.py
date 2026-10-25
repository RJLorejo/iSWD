
from ai.app.classification.classifier import ComplaintClassifier
from ai.app.triage.input_validator import ComplaintInputValidator
from ai.app.triage.signals import ComplaintSignalExtractor
from ai.app.triage.urgency import UrgencyAnalyzer
from ai.app.triage.review import ReviewAnalyzer


class ComplaintAnalyzer:
    """
    Coordinates input validation, ML classification, operational
    signal extraction, urgency assessment, and human-review advice.

    The ML prediction is advisory. Authorized SWD personnel make
    the final complaint classification and operational decisions.
    """

    CATEGORY_SIGNAL_MAP = {
        "Mainline Leakage": [
            "possible_leakage",
            "possible_large_leak",
            "public_road",
            "possible_flooding",
            "multiple_households",
            "area_wide_impact",
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
            "area_wide_impact",
            "extended_duration",
        ],
        "Low Water Pressure": [
            "weak_water_supply",
            "multiple_households",
            "area_wide_impact",
            "extended_duration",
        ],
        "Dirty Water": [
            "dirty_water",
            "multiple_households",
            "area_wide_impact",
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

    SIGNAL_LABELS = {
        "public_road": "Road or public access area mentioned",
        "possible_flooding": "Possible flooding or overflow reported",
        "multiple_households": "Multiple households may be affected",
        "area_wide_impact": "Possible area-wide or community impact",
        "complete_service_loss": "Possible complete loss of water service",
        "weak_water_supply": "Weak or reduced water supply reported",
        "possible_leakage": "Possible water leakage reported",
        "possible_large_leak": "Possible significant water leakage reported",
        "dirty_water": "Dirty or abnormal water quality reported",
        "meter_issue": "Meter-related concern mentioned",
        "abnormal_consumption": "Unusually high consumption or bill reported",
        "reading_discrepancy": "Possible meter-reading discrepancy reported",
        "classification_request": "Account classification review requested",
        "extended_duration": "Concern may have continued for an extended period",
    }

    def __init__(self):
        self.classifier = ComplaintClassifier()
        self.input_validator = ComplaintInputValidator()
        self.signal_extractor = ComplaintSignalExtractor()
        self.urgency_analyzer = UrgencyAnalyzer()
        self.review_analyzer = ReviewAnalyzer()

    def analyze(self, description: str) -> dict:
        # Validate before running ML classification or urgency analysis.
        validation = self.input_validator.validate(description)

        if not validation["valid"]:
            return {
                "valid_complaint": False,
                "status": "invalid_input",
                "message": validation["message"],
                "matched_terms": [],
            }

        classification = self.classifier.predict(description)

        operational_analysis = self.signal_extractor.extract(description)
        signals = operational_analysis.get("signals", {})

        urgency = self.urgency_analyzer.analyze(signals)

        review = self.review_analyzer.analyze(
            classification,
            signals,
        )

        supporting_evidence = self._build_supporting_evidence(
            classification,
            operational_analysis,
        )

        return {
            "valid_complaint": True,
            "status": "accepted",
            "message": (
                "The description was accepted for AI-assisted analysis. "
                "Customer Service must verify the suggested category."
            ),
            "matched_terms": validation["matched_terms"],
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
    ) -> dict:
        complaint_type = classification.get(
            "complaint_type",
            "Unknown",
        )

        signals = operational_analysis.get("signals", {})
        detected_evidence = operational_analysis.get("evidence", [])
        negated_signals = operational_analysis.get(
            "negated_signals",
            [],
        )

        relevant_keys = self.CATEGORY_SIGNAL_MAP.get(
            complaint_type,
            [],
        )

        indicators = [
            self.SIGNAL_LABELS[key]
            for key in relevant_keys
            if signals.get(key, False)
            and key in self.SIGNAL_LABELS
        ]

        indicators = list(dict.fromkeys(indicators))

        return {
            "summary": self._classification_summary(
                complaint_type,
                signals,
                indicators,
            ),
            "indicators": indicators,
            "detected_evidence": detected_evidence,
            "negated_signals": negated_signals,
            "evidence_supports_suggested_category": bool(indicators),
            "human_verification_note": (
                "These indicators are extracted from the complaint text. "
                "They do not independently prove the suggested category "
                "or confirm the actual condition."
            ),
        }

    def _classification_summary(
        self,
        complaint_type: str,
        signals: dict,
        indicators: list,
    ) -> str:
        if not indicators:
            return (
                f"The classifier suggests '{complaint_type}', but the "
                "signal extractor did not find a matching supporting "
                "indicator. Customer Service should verify the category "
                "using the complaint details and applicable SWD procedures."
            )

        if complaint_type == "Mainline Leakage":
            if (
                signals.get("possible_large_leak")
                and (
                    signals.get("public_road")
                    or signals.get("multiple_households")
                    or signals.get("area_wide_impact")
                )
            ):
                return (
                    "The complaint contains indicators of potentially "
                    "significant leakage with possible public-area or "
                    "multi-household impact. Verify the pipe location "
                    "before confirming Mainline Leakage."
                )

            return (
                "The complaint contains possible leakage-related "
                "indicators. Confirm the affected pipe and its location "
                "before assigning Mainline Leakage."
            )

        if complaint_type == "Service Connection Leakage":
            return (
                "The complaint contains possible leakage indicators. "
                "Confirm that the affected pipe is the service connection "
                "under SWD's official classification rules."
            )

        if complaint_type == "Meter / Meter Stand Leakage":
            return (
                "The complaint contains possible leakage and/or meter-related "
                "indicators. Confirm that the leak originates at the meter, "
                "meter stand, or its fittings."
            )

        if complaint_type == "No Water":
            return (
                "The complaint contains indicators of possible complete "
                "water-service loss. Confirm whether water is unavailable "
                "at all relevant taps and whether nearby consumers are affected."
            )

        if complaint_type == "Low Water Pressure":
            return (
                "The complaint contains indicators of weak water supply. "
                "Confirm that some water is still flowing and distinguish "
                "the issue from a complete service interruption."
            )

        if complaint_type == "Dirty Water":
            return (
                "The complaint contains indicators of abnormal water quality. "
                "Verify the water's appearance, duration, and possible extent "
                "of impact using SWD procedures."
            )

        if complaint_type == "Damaged / Detached Service Connection":
            return (
                "The complaint contains a possible connection-related "
                "indicator. Confirm the physical condition of the service "
                "connection; leakage alone does not establish that it is "
                "damaged or detached."
            )

        if complaint_type == "Damaged / Detached Water Meter":
            return (
                "The complaint contains meter-related indicators. Confirm "
                "whether the meter is physically damaged, loose, or detached."
            )

        if complaint_type == "Malfunctioning Water Meter":
            return (
                "The complaint contains meter-related indicators. Confirm "
                "whether the meter is operating incorrectly; a meter mention "
                "alone does not establish a malfunction."
            )

        if complaint_type == "Erroneous Meter Reading":
            return (
                "The complaint contains indicators relevant to a possible "
                "reading discrepancy. Compare the recorded reading with the "
                "physical meter reading and applicable billing records."
            )

        if complaint_type == "High / Unusual Consumption":
            return (
                "The complaint contains indicators of unusually high "
                "consumption or a high bill. Compare billing history and "
                "investigate other possible causes before confirming the issue."
            )

        if complaint_type == "Reclassification":
            return (
                "The complaint contains indicators of a possible account "
                "classification review request. Confirm the current and "
                "requested classifications with Customer Service."
            )

        if complaint_type == "Re-open Sealed Connection":
            return (
                "The classifier suggests a request to reopen a sealed "
                "connection. Confirm the consumer's actual request and "
                "follow the applicable SWD reconnection procedure."
            )

        if complaint_type == "Temporary Disconnection":
            return (
                "The classifier suggests a temporary disconnection request. "
                "Confirm the consumer's intent and follow SWD procedures."
            )

        if complaint_type == "Sealed Water Meter Concern":
            return (
                "The classifier suggests a concern about a sealed water meter. "
                "Confirm the exact concern and the meter's current status."
            )

        return (
            f"The classifier suggests '{complaint_type}'. "
            "Customer Service should verify the complaint details "
            "before confirming the category."
        )
