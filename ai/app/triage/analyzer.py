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
        """
        Perform the complete AI-assisted complaint analysis.

        The classifier predicts the most appropriate complaint type.

        The signal extractor identifies operational indicators
        found in the consumer's description.

        The urgency analyzer determines operational urgency.

        The review analyzer identifies ambiguity and situations
        that require human verification.

        Supporting evidence explains the AI recommendation using
        detected indicators from the complaint description.
        """

        # ---------------------------------------------------------
        # 1. Complaint Classification
        # ---------------------------------------------------------

        classification = self.classifier.predict(
            description
        )


        # ---------------------------------------------------------
        # 2. Operational Signal Detection
        # ---------------------------------------------------------

        operational_analysis = (
            self.signal_extractor.extract(
                description
            )
        )

        signals = operational_analysis[
            "signals"
        ]


        # ---------------------------------------------------------
        # 3. Urgency Analysis
        # ---------------------------------------------------------

        urgency = self.urgency_analyzer.analyze(
            signals
        )


        # ---------------------------------------------------------
        # 4. Human Review Analysis
        # ---------------------------------------------------------

        review = self.review_analyzer.analyze(
            classification,
            signals,
        )


        # ---------------------------------------------------------
        # 5. Supporting Evidence / Explanation
        # ---------------------------------------------------------

        supporting_evidence = (
            self._build_supporting_evidence(
                classification,
                operational_analysis,
            )
        )


        # ---------------------------------------------------------
        # Final AI Analysis
        # ---------------------------------------------------------

        return {
            "classification": classification,

            "supporting_evidence":
                supporting_evidence,

            "operational_analysis":
                operational_analysis,

            "urgency":
                urgency,

            "review":
                review,
        }


    def _build_supporting_evidence(
        self,
        classification: dict,
        operational_analysis: dict,
    ):
        """
        Build a human-readable explanation of the AI
        classification using operational indicators detected
        from the complaint description.

        This does not create another prediction model.

        It explains the existing classification using evidence
        already detected by the signal extractor.
        """

        complaint_type = (
            classification.get(
                "complaint_type",
                "Unknown",
            )
        )

        signals = (
            operational_analysis.get(
                "signals",
                {},
            )
        )

        detected_evidence = (
            operational_analysis.get(
                "evidence",
                [],
            )
        )


        # ---------------------------------------------------------
        # Evidence specifically relevant to each complaint type
        # ---------------------------------------------------------

        category_signal_map = {

            "Large Consumption": [
                "abnormal_consumption",
                "meter_issue",
            ],

            "Erroneous Reading": [
                "reading_discrepancy",
                "meter_issue",
            ],

            "Malfunction Meter": [
                "meter_issue",
                "reading_discrepancy",
            ],

            "No Water": [
                "complete_service_loss",
                "weak_water_supply",
                "multiple_households",
                "extended_duration",
            ],

            "Mainline Leakage": [
                "possible_leakage",
                "possible_large_leak",
                "public_road",
                "possible_flooding",
                "multiple_households",
            ],

            "Service Connection Leakage": [
                "possible_leakage",
                "possible_large_leak",
            ],

            "Dirty Water": [
                "dirty_water",
            ],

            "Re-classification": [
                "classification_request",
            ],
        }


        # ---------------------------------------------------------
        # Human-readable labels for detected indicators
        # ---------------------------------------------------------

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

            if signals.get(
                signal_key,
                False,
            ):

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


        # ---------------------------------------------------------
        # Build category-specific summary
        # ---------------------------------------------------------

        summary = (
            self._classification_summary(
                complaint_type,
                signals,
            )
        )


        # ---------------------------------------------------------
        # Fallback when no category-specific signal was detected
        # ---------------------------------------------------------

        if not indicators:

            for signal_key, detected in signals.items():

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


        # ---------------------------------------------------------
        # Return Supporting Evidence
        # ---------------------------------------------------------

        return {

            "summary":
                summary,

            "indicators":
                indicators,

            "detected_evidence":
                detected_evidence,

        }


    def _classification_summary(
        self,
        complaint_type: str,
        signals: dict,
    ):
        """
        Generate a short explanation for the suggested
        complaint classification.

        These explanations are deterministic and based on
        detected operational signals rather than generated
        text from an external language model.
        """

        if complaint_type == "Large Consumption":

            if signals.get(
                "abnormal_consumption"
            ):

                return (
                    "The complaint indicates an unusually "
                    "high water bill or abnormal water "
                    "consumption, which supports the "
                    "Large Consumption classification."
                )

            return (
                "The complaint description is most similar "
                "to reported Large Consumption concerns."
            )


        if complaint_type == "Erroneous Reading":

            if signals.get(
                "reading_discrepancy"
            ):

                return (
                    "The complaint indicates a possible "
                    "difference or error in the recorded "
                    "meter reading, which supports the "
                    "Erroneous Reading classification."
                )

            if signals.get(
                "meter_issue"
            ):

                return (
                    "The complaint contains meter-related "
                    "information that may involve an "
                    "incorrect recorded reading."
                )

            return (
                "The complaint description is most similar "
                "to reported Erroneous Reading concerns."
            )


        if complaint_type == "Malfunction Meter":

            if signals.get(
                "meter_issue"
            ):

                return (
                    "The complaint contains information "
                    "about the water meter that may indicate "
                    "a meter-related problem."
                )

            return (
                "The complaint description is most similar "
                "to reported Malfunction Meter concerns."
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

            if signals.get(
                "weak_water_supply"
            ):

                return (
                    "The complaint indicates weak or reduced "
                    "water supply and is most closely related "
                    "to a No Water service concern."
                )

            return (
                "The complaint description is most similar "
                "to reported No Water concerns."
            )


        if complaint_type == "Mainline Leakage":

            if signals.get(
                "possible_large_leak"
            ):

                return (
                    "The complaint indicates a potentially "
                    "significant water leak that may involve "
                    "a main distribution line."
                )

            if (
                signals.get(
                    "possible_leakage"
                )
                and signals.get(
                    "public_road"
                )
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

            return (
                "The complaint description is most similar "
                "to reported Mainline Leakage concerns."
            )


        if complaint_type == "Service Connection Leakage":

            if signals.get(
                "possible_leakage"
            ):

                return (
                    "The complaint contains leakage "
                    "indicators that may involve the "
                    "consumer's service connection."
                )

            return (
                "The complaint description is most similar "
                "to reported Service Connection Leakage "
                "concerns."
            )


        if complaint_type == "Dirty Water":

            if signals.get(
                "dirty_water"
            ):

                return (
                    "The complaint describes dirty, cloudy, "
                    "brown, or otherwise abnormal water "
                    "quality, which supports the Dirty Water "
                    "classification."
                )

            return (
                "The complaint description is most similar "
                "to reported Dirty Water concerns."
            )


        if complaint_type == "Re-classification":

            if signals.get(
                "classification_request"
            ):

                return (
                    "The complaint requests a review or "
                    "correction of the consumer's account "
                    "classification."
                )

            return (
                "The complaint description is most similar "
                "to reported account Re-classification "
                "requests."
            )


        return (
            "The complaint description is most similar to "
            f"reported {complaint_type} concerns."
        )
