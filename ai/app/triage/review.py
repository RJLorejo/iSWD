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

        confidence_level = classification.get(
            "confidence_level",
            "Low",
        )

        ambiguous = classification.get(
            "ambiguous",
            False,
        )

        classifier_review_reason = (
            classification.get(
                "review_reason"
            )
        )

        if classifier_review_reason:
            reasons.append(
                classifier_review_reason
            )

        if (
            ambiguous
            and not classifier_review_reason
        ):
            reasons.append(
                "The complaint may fit more than one "
                "complaint type and should be confirmed "
                "by Customer Service."
            )

        if (
            confidence_level == "Low"
            and not classifier_review_reason
        ):
            reasons.append(
                "The classifier has low confidence in "
                "the suggested complaint type."
            )

        if (
            signals.get("meter_issue")
            and signals.get(
                "abnormal_consumption"
            )
            and signals.get(
                "reading_discrepancy"
            )
        ):
            reasons.append(
                "The complaint contains overlapping "
                "meter, consumption, and meter-reading "
                "indicators."
            )

            verification_questions.extend([
                (
                    "What is the meter reading printed "
                    "on the current bill?"
                ),
                (
                    "What is the current reading shown "
                    "on the physical water meter?"
                ),
                (
                    "Is the current bill or recorded "
                    "consumption significantly higher "
                    "than previous months?"
                ),
                (
                    "Has the consumer observed unusual "
                    "behavior or a visible physical "
                    "problem with the water meter?"
                ),
            ])

        elif (
            signals.get("meter_issue")
            and signals.get(
                "reading_discrepancy"
            )
        ):
            verification_questions.extend([
                (
                    "What reading appears on the "
                    "current bill?"
                ),
                (
                    "What reading is currently displayed "
                    "on the physical water meter?"
                ),
                (
                    "Has the consumer noticed any "
                    "physical problem with the meter?"
                ),
            ])

        elif signals.get(
            "abnormal_consumption"
        ):
            verification_questions.extend([
                (
                    "Is the current bill or consumption "
                    "significantly higher than the "
                    "consumer's previous usage?"
                ),
                (
                    "Has the consumer noticed any "
                    "visible leakage in the property?"
                ),
                (
                    "Does the water meter continue "
                    "moving when all faucets and water "
                    "fixtures are closed?"
                ),
            ])

        if (
            signals.get(
                "complete_service_loss"
            )
            and signals.get(
                "possible_leakage"
            )
        ):
            reasons.append(
                "The complaint reports both complete "
                "water-service interruption and possible "
                "leakage, which may indicate overlapping "
                "Engineering concerns."
            )

            verification_questions.extend([
                (
                    "Is the consumer currently receiving "
                    "any water?"
                ),
                (
                    "Are nearby households experiencing "
                    "the same problem?"
                ),
                (
                    "Where exactly is the reported water "
                    "leakage located?"
                ),
                (
                    "Is water continuously flowing from "
                    "the reported location?"
                ),
            ])

        elif signals.get(
            "complete_service_loss"
        ):
            verification_questions.extend([
                (
                    "Is the consumer completely without "
                    "water or only experiencing weak "
                    "water pressure?"
                ),
                (
                    "Are nearby households experiencing "
                    "the same problem?"
                ),
                (
                    "When did the water-service "
                    "interruption begin?"
                ),
            ])

        if (
            signals.get("weak_water_supply")
            and not signals.get(
                "complete_service_loss"
            )
        ):
            verification_questions.extend([
                (
                    "Is water still flowing from the "
                    "faucet but at weak pressure?"
                ),
                (
                    "Are nearby households also "
                    "experiencing low water pressure?"
                ),
                (
                    "When did the low-pressure problem "
                    "begin?"
                ),
            ])

        if signals.get(
            "possible_large_leak"
        ):
            verification_questions.extend([
                (
                    "Where exactly is the reported "
                    "leakage located?"
                ),
                (
                    "Is the leakage located on a road "
                    "or other public area?"
                ),
                (
                    "Is the water flow continuous or "
                    "increasing?"
                ),
                (
                    "Are several households or a wider "
                    "area affected?"
                ),
            ])

        if signals.get(
            "multiple_households"
        ):
            verification_questions.extend([
                (
                    "Approximately how many nearby "
                    "households are experiencing the "
                    "same water-service problem?"
                ),
                (
                    "Does the concern appear to affect "
                    "only one property or a wider area?"
                ),
            ])

        if signals.get(
            "dirty_water"
        ):
            verification_questions.extend([
                (
                    "What color or appearance does the "
                    "water have?"
                ),
                (
                    "Does the water contain visible "
                    "sand, sediment, or other particles?"
                ),
                (
                    "When was the dirty water first "
                    "observed?"
                ),
                (
                    "Are nearby households experiencing "
                    "the same water-quality concern?"
                ),
            ])

        if signals.get(
            "classification_request"
        ):
            verification_questions.extend([
                (
                    "What is the consumer's current "
                    "account classification?"
                ),
                (
                    "What classification is the "
                    "consumer requesting?"
                ),
                (
                    "What information supports the "
                    "requested classification change?"
                ),
            ])

        if (
            complaint_type
            == "Damaged / Detached Service Connection"
        ):
            verification_questions.extend([
                (
                    "What part of the service connection "
                    "appears damaged or detached?"
                ),
                (
                    "Is water currently leaking from "
                    "the damaged connection?"
                ),
            ])

        if (
            complaint_type
            == "Damaged / Detached Water Meter"
        ):
            verification_questions.extend([
                (
                    "Is the water meter physically "
                    "damaged, loose, or detached?"
                ),
                (
                    "Is there visible leakage around "
                    "the water meter?"
                ),
            ])

        if (
            complaint_type
            == "Meter / Meter Stand Leakage"
        ):
            verification_questions.extend([
                (
                    "Is the leakage coming directly "
                    "from the meter, meter stand, or "
                    "nearby fitting?"
                ),
                (
                    "Is the leakage continuous even "
                    "when water is not being used?"
                ),
            ])

        if (
            complaint_type
            == "Malfunctioning Water Meter"
        ):
            verification_questions.extend([
                (
                    "Does the meter continue moving "
                    "when all faucets and fixtures are "
                    "closed?"
                ),
                (
                    "Is the meter stopped, reversed, "
                    "stuck, or behaving unusually?"
                ),
            ])

        if (
            complaint_type
            == "Erroneous Meter Reading"
        ):
            verification_questions.extend([
                (
                    "What reading appears on the "
                    "consumer's current bill?"
                ),
                (
                    "What reading is currently shown "
                    "on the physical water meter?"
                ),
            ])

        if (
            complaint_type
            == "High / Unusual Consumption"
        ):
            verification_questions.extend([
                (
                    "How does the current consumption "
                    "compare with the consumer's "
                    "previous billing periods?"
                ),
                (
                    "Has there been any significant "
                    "change in water usage?"
                ),
            ])

        if (
            complaint_type
            == "Re-open Sealed Connection"
        ):
            verification_questions.extend([
                (
                    "Is the service connection "
                    "currently sealed?"
                ),
                (
                    "What is the reason for requesting "
                    "the connection to be reopened?"
                ),
            ])

        if (
            complaint_type
            == "Temporary Disconnection"
        ):
            verification_questions.extend([
                (
                    "What is the reason for requesting "
                    "temporary disconnection?"
                ),
                (
                    "Has the consumer confirmed that "
                    "the request is temporary?"
                ),
            ])

        if (
            complaint_type
            == "Sealed Water Meter Concern"
        ):
            verification_questions.extend([
                (
                    "What concern does the consumer "
                    "have regarding the sealed water "
                    "meter?"
                ),
                (
                    "Is the meter currently sealed or "
                    "has the seal been damaged or "
                    "disturbed?"
                ),
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

            "recommended_complaint_type":
                complaint_type,

            "confidence_level":
                confidence_level,

            "ambiguous":
                ambiguous,

            "reasons":
                reasons,

            "verification_questions":
                verification_questions,

            "human_confirmation_required":
                True,
        }
