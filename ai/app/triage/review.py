class ReviewAnalyzer:

    def analyze(
        self,
        classification: dict,
        signals: dict,
    ):
        reasons = []
        verification_questions = []

        complaint_type = classification.get(
            "complaint_type"
        )

        confidence = classification.get(
            "confidence",
            0,
        )

        confidence_gap = classification.get(
            "confidence_gap",
            0,
        )

        ambiguous = classification.get(
            "ambiguous",
            False,
        )

        if ambiguous:
            reasons.append(
                "The complaint contains information that may fit more than one complaint type."
            )

        if confidence < 0.60:
            reasons.append(
                "The classification confidence is low and should be confirmed by Customer Service."
            )

        if confidence_gap < 0.15:
            reasons.append(
                "The leading complaint types have relatively close classification probabilities."
            )

        if (
            signals.get("meter_issue")
            and signals.get("abnormal_consumption")
            and signals.get("reading_discrepancy")
        ):
            reasons.append(
                "The complaint contains overlapping meter, consumption, and reading-discrepancy indicators."
            )

            verification_questions.extend([
                "What is the meter reading printed on the current bill?",
                "What is the current reading shown on the physical water meter?",
                "Is the current bill or recorded consumption significantly higher than previous months?",
                "Has the consumer observed any unusual behavior or visible problem with the meter?",
            ])

        elif (
            signals.get("meter_issue")
            and signals.get("reading_discrepancy")
        ):
            verification_questions.extend([
                "What reading appears on the current bill?",
                "What reading is currently displayed on the physical meter?",
                "Has the consumer noticed any physical problem with the meter?",
            ])

        if (
            signals.get("complete_service_loss")
            and signals.get("possible_leakage")
        ):
            reasons.append(
                "The complaint reports both service interruption and possible leakage, which may indicate overlapping Engineering concerns."
            )

            verification_questions.extend([
                "Is the consumer currently receiving any water?",
                "Are nearby households experiencing the same problem?",
                "Where exactly is the reported water leakage located?",
                "Is water continuously flowing from the reported location?",
            ])

        elif signals.get(
            "complete_service_loss"
        ):
            verification_questions.extend([
                "Is the consumer completely without water or only experiencing weak water pressure?",
                "Are nearby households experiencing the same problem?",
                "When did the water service interruption begin?",
            ])

        if signals.get(
            "possible_large_leak"
        ):
            verification_questions.extend([
                "Is the reported leakage located on a road or other public area?",
                "Is the water flow continuous or increasing?",
            ])

        if signals.get(
            "dirty_water"
        ):
            verification_questions.extend([
                "What color or appearance does the water have?",
                "When was the dirty water first observed?",
                "Are nearby households experiencing the same water-quality concern?",
            ])

        if signals.get(
            "classification_request"
        ):
            verification_questions.extend([
                "What is the consumer's current account classification?",
                "What classification is the consumer requesting?",
                "What information supports the requested classification change?",
            ])

        verification_questions = list(
            dict.fromkeys(
                verification_questions
            )
        )

        reasons = list(
            dict.fromkeys(
                reasons
            )
        )

        return {
            "required": True,
            "recommended_complaint_type": complaint_type,
            "reasons": reasons,
            "verification_questions": verification_questions,
            "human_confirmation_required": True,
        }
