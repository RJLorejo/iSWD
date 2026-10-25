
class ReviewAnalyzer:
    """
    Recommends human verification and asks targeted follow-up questions.

    This component does not approve, reject, or finalize a complaint.
    Customer Service remains responsible for the final decision.
    """

    def analyze(
        self,
        classification: dict,
        signals: dict,
    ) -> dict:
        classification = classification or {}
        signals = signals or {}

        reasons = []
        verification_questions = []

        complaint_type = classification.get("complaint_type")
        confidence_level = classification.get("confidence_level", "Low")
        ambiguous = bool(classification.get("ambiguous", False))

        classifier_review_reason = classification.get("review_reason")

        if classifier_review_reason:
            reasons.append(str(classifier_review_reason))

        if ambiguous:
            reasons.append(
                "The complaint may fit more than one complaint type. "
                "Customer Service should verify the suggested category."
            )

        if str(confidence_level).lower() == "low":
            reasons.append(
                "The classifier reports low confidence in the suggested category."
            )

        # A category prediction is not enough to verify the physical condition.
        if complaint_type in {
            "Mainline Leakage",
            "Service Connection Leakage",
            "Meter / Meter Stand Leakage",
        }:
            if signals.get("possible_leakage"):
                verification_questions.extend([
                    "Where exactly is the reported leak located?",
                    "Which pipe, connection, meter, or fitting appears to be affected?",
                    "Is water continuously flowing from the reported location?",
                ])

            if complaint_type == "Mainline Leakage":
                verification_questions.append(
                    "What evidence indicates that the affected pipe is a "
                    "distribution main rather than a service connection?"
                )

            elif complaint_type == "Service Connection Leakage":
                verification_questions.append(
                    "Does the affected pipe serve an individual property, "
                    "and can its location be identified?"
                )

            elif complaint_type == "Meter / Meter Stand Leakage":
                verification_questions.append(
                    "Is the water coming directly from the meter, meter stand, "
                    "valve, or a nearby fitting?"
                )

        # Potentially serious leakage or public-area impact.
        if signals.get("possible_large_leak"):
            reasons.append(
                "The complaint contains possible significant-leak indicators. "
                "The actual severity and location need verification."
            )
            verification_questions.extend([
                "Is water flowing forcefully or in a large volume?",
                "Is the leak on a road or another public-access area?",
                "Are multiple households or a wider area affected?",
            ])

        if signals.get("possible_flooding"):
            reasons.append(
                "Possible flooding or overflow was reported. Verify the "
                "condition and assess whether prompt field attention is needed."
            )
            verification_questions.extend([
                "Is flooding or overflow currently occurring?",
                "Is water affecting a road, property, or another public area?",
            ])

        if (
            signals.get("possible_leakage")
            and signals.get("complete_service_loss")
        ):
            reasons.append(
                "The complaint contains both possible leakage and complete "
                "service-loss indicators. Confirm whether these refer to "
                "the same incident."
            )
            verification_questions.extend([
                "Is any water currently coming from the consumer's taps?",
                "Where is the reported leakage located?",
                "Are nearby households experiencing the service interruption?",
            ])

        # Water-service availability.
        if signals.get("complete_service_loss"):
            verification_questions.extend([
                "Is there no water at all, or is water still flowing weakly?",
                "When did the interruption begin?",
                "Are nearby households experiencing the same problem?",
            ])

        if signals.get("weak_water_supply"):
            verification_questions.extend([
                "Is water still flowing from the tap but at weak pressure?",
                "Does the problem affect one property or nearby households too?",
                "When did the low-pressure problem begin?",
            ])

        if (
            signals.get("multiple_households")
            or signals.get("area_wide_impact")
        ):
            verification_questions.extend([
                "Approximately how many households are affected?",
                "Which streets, purok, or barangay are affected, if known?",
            ])

        # Water quality.
        if signals.get("dirty_water"):
            verification_questions.extend([
                "What color or appearance does the water have?",
                "Does the water contain visible sand, sediment, or particles?",
                "When was the abnormal water first observed?",
                "Are nearby households experiencing the same concern?",
            ])

        # Meter reading and consumption.
        if (
            signals.get("meter_issue")
            and signals.get("abnormal_consumption")
            and signals.get("reading_discrepancy")
        ):
            reasons.append(
                "The complaint contains overlapping meter, consumption, "
                "and reading-discrepancy indicators."
            )
            verification_questions.extend([
                "What reading appears on the current bill?",
                "What reading is currently displayed on the physical meter?",
                "How does the current consumption compare with previous bills?",
                "Has the consumer noticed a physical meter problem or possible leakage?",
            ])

        elif (
            signals.get("meter_issue")
            and signals.get("reading_discrepancy")
        ):
            verification_questions.extend([
                "What reading appears on the current bill?",
                "What reading is currently displayed on the physical meter?",
                "Has the consumer noticed a physical problem with the meter?",
            ])

        elif signals.get("reading_discrepancy"):
            verification_questions.extend([
                "What reading appears on the current bill?",
                "What reading is currently displayed on the physical meter?",
            ])

        if signals.get("abnormal_consumption"):
            verification_questions.extend([
                "How does the current bill compare with previous billing periods?",
                "Has the consumer noticed a change in water usage or possible leakage?",
            ])

        # Meter-related category checks.
        if complaint_type == "Damaged / Detached Service Connection":
            verification_questions.extend([
                "What part of the service connection appears damaged or detached?",
                "Is there visible leakage from that connection?",
            ])

        if complaint_type == "Damaged / Detached Water Meter":
            verification_questions.extend([
                "Is the meter physically damaged, loose, or detached?",
                "Is there visible leakage around the meter?",
            ])

        if complaint_type == "Malfunctioning Water Meter":
            verification_questions.extend([
                "Does the meter appear stopped, stuck, reversed, or otherwise abnormal?",
                "Has the meter behavior been checked by authorized personnel?",
            ])

        if complaint_type == "Erroneous Meter Reading":
            verification_questions.extend([
                "What reading appears on the current bill?",
                "What reading is currently shown on the physical meter?",
            ])

        if complaint_type == "High / Unusual Consumption":
            verification_questions.extend([
                "How does the current consumption compare with previous billing periods?",
                "Has water usage changed, or is a possible leak suspected?",
            ])

        # Account and connection requests.
        if signals.get("classification_request"):
            verification_questions.extend([
                "What is the consumer's current account classification?",
                "What classification is the consumer requesting?",
                "What information supports the requested change?",
            ])

        if complaint_type == "Re-open Sealed Connection":
            verification_questions.extend([
                "Is the service connection currently sealed?",
                "Is the consumer requesting reconnection, and what is the reason?",
                "Have applicable SWD requirements for reconnection been checked?",
            ])

        if complaint_type == "Temporary Disconnection":
            verification_questions.extend([
                "Is the consumer requesting a temporary disconnection?",
                "What is the reason and intended duration of the request?",
            ])

        if complaint_type == "Sealed Water Meter Concern":
            verification_questions.extend([
                "What concern does the consumer have about the sealed meter?",
                "Is the meter currently sealed, and is the seal visibly damaged?",
            ])

        # Retain order while removing duplicate questions and reasons.
        verification_questions = list(dict.fromkeys(verification_questions))
        reasons = list(dict.fromkeys(reasons))

        return {
            "required": True,
            "recommended_complaint_type": complaint_type,
            "confidence_level": confidence_level,
            "ambiguous": ambiguous,
            "reasons": reasons,
            "verification_questions": verification_questions,
            "human_confirmation_required": True,
        }
