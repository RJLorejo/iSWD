import re


class ComplaintSignalExtractor:

    def extract(self, text: str):
        normalized = self._normalize(text)

        signals = {
            "public_road": False,
            "possible_flooding": False,
            "multiple_households": False,
            "complete_service_loss": False,
            "weak_water_supply": False,
            "possible_leakage": False,
            "possible_large_leak": False,
            "dirty_water": False,
            "meter_issue": False,
            "abnormal_consumption": False,
            "reading_discrepancy": False,
            "classification_request": False,
            "extended_duration": False,
        }

        evidence = []

        self._detect(
            normalized,
            signals,
            evidence,
            "public_road",
            [
                "road",
                "street",
                "highway",
                "public road",
                "corner",
                "dalan",
                "kalsada",
            ],
            "Complaint mentions a road or public access area.",
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "possible_flooding",
            [
                "flood",
                "flooding",
                "overflow",
                "submerged",
                "baha",
                "ginabaha",
            ],
            "Complaint contains possible flooding indicators.",
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "multiple_households",
            [
                "several houses",
                "several households",
                "many houses",
                "many households",
                "neighbors",
                "neighbours",
                "our area",
                "whole area",
                "entire area",
                "mga silingan",
                "madamo nga balay",
                "amon area",
            ],
            "Complaint indicates that multiple households may be affected.",
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "complete_service_loss",
            [
                "no water",
                "without water",
                "lost water service",
                "water stopped",
                "no water supply",
                "wala tubig",
                "wala gid tubig",
                "wala na tubig",
                "wala na gid",
                "wala gid",
                "walay tubig",
                "wala nay tubig",
                "wala tubod ang gripo",
                "wala tagas",
            ],
            "Complaint indicates possible complete loss of water service.",
        )

        self._detect(
    normalized,
    signals,
    evidence,
    "weak_water_supply",
    [
        "low pressure",
        "weak pressure",
        "weak water",
        "very little water",
        "hinay ang tubig",
        "hinay gid ang tubig",
        "hinay tubig",
        "mahina ang tubig",
        "mahina tubig",
        "hinay ang tagas",
    ],
    "Complaint indicates weak or reduced water supply.",
)

        self._detect(
    normalized,
    signals,
    evidence,
    "possible_leakage",
    [
        "leak",
        "leaking",
        "water coming out",
        "water coming from",
        "water flowing",
        "water keeps flowing",
        "tubig nga nagaguwa",
        "tubig nga naga guwa",
        "tubig nga padayon nagaguwa",
        "padayon nagaguwa",
        "nagaguwa nga tubig",
        "naga guwa nga tubig",
        "tagas",
    ],
    "Complaint contains possible leakage indicators.",
)

        large_leak_context = (
            self._contains_any(
                normalized,
                [
                    "large leak",
                    "major leak",
                    "burst pipe",
                    "broken main",
                    "strong leak",
                    "dako nga tagas",
                ],
            )
            or (
                signals["possible_leakage"]
                and signals["public_road"]
            )
        )

        if large_leak_context:
            signals["possible_large_leak"] = True
            evidence.append(
                "Leakage indicators combined with the reported context may indicate significant water loss."
            )

        self._detect(
            normalized,
            signals,
            evidence,
            "dirty_water",
            [
                "dirty water",
                "brown water",
                "cloudy water",
                "muddy water",
                "particles",
                "sediment",
                "brown ang tubig",
                "malabo ang tubig",
                "lapok",
            ],
            "Complaint contains water-quality or dirty-water indicators.",
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "meter_issue",
            [
                "meter",
                "water meter",
                "metro",
            ],
            "Complaint contains meter-related information.",
        )

        self._detect(
    normalized,
    signals,
    evidence,
    "abnormal_consumption",
    [
        # English - consumption
        "large consumption",
        "high consumption",
        "higher consumption",
        "very high consumption",
        "unusually high consumption",
        "consumption doubled",
        "consumption increased",
        "consumption went up",
        "usage doubled",
        "usage increased",
        "usage went up",
        "twice the normal",
        "twice our normal",
        "double the normal",
        "double our normal",

        # English - bill
        "high bill",
        "higher bill",
        "large bill",
        "bill doubled",
        "bill increased",
        "bill is higher",
        "bill is almost twice",
        "water bill increased",
        "higher than normal",
        "much higher than normal",

        # Hiligaynon / mixed
        "dako ang bill",
        "dako amon bill",
        "dako amu bill",
        "taas ang bill",
        "taas amon bill",
        "nagtaas amon bill",
        "nagmahal amon bill",
        "nagdoble ang consumption",
        "nagtaas ang consumption",
        "sobra kataas",
    ],
    "Complaint indicates unusually high recorded water consumption.",
)

        reading_terms = self._contains_any(
            normalized,
            [
                "reading",
                "meter reading",
                "recorded reading",
            ],
        )

        discrepancy_terms = self._contains_any(
            normalized,
            [
                "does not match",
                "doesn't match",
                "different from",
                "incorrect",
                "wrong reading",
                "sala ang reading",
                "lain ang meter reading",
            ],
        )

        if reading_terms and discrepancy_terms:
            signals["reading_discrepancy"] = True
            evidence.append(
                "Complaint indicates a possible discrepancy in the recorded meter reading."
            )

        classification_terms = self._contains_any(
            normalized,
            [
                "reclassification",
                "re-classification",
                "classification",
                "consumer category",
                "account category",
            ],
        )

        change_terms = self._contains_any(
            normalized,
            [
                "change",
                "changed",
                "update",
                "correct",
                "review",
                "wrong",
                "incorrect",
            ],
        )

        if classification_terms and change_terms:
            signals["classification_request"] = True
            evidence.append(
                "Complaint appears to request review or correction of account classification."
            )

        if self._has_extended_duration(normalized):
            signals["extended_duration"] = True
            evidence.append(
                "Complaint indicates that the concern may have continued for an extended period."
            )

        return {
            "signals": signals,
            "evidence": evidence,
        }

    def _normalize(self, text: str) -> str:
        normalized = text.lower().strip()

        normalized = re.sub(
            r"\s+",
            " ",
            normalized,
        )

        return normalized

    def _detect(
        self,
        text: str,
        signals: dict,
        evidence: list,
        key: str,
        phrases: list[str],
        message: str,
    ):
        if self._contains_any(
            text,
            phrases,
        ):
            signals[key] = True
            evidence.append(message)

    def _contains_any(
        self,
        text: str,
        phrases: list[str],
    ) -> bool:
        return any(
            phrase in text
            for phrase in phrases
        )

    def _has_extended_duration(
        self,
        text: str,
    ) -> bool:
        phrases = [
            "since yesterday",
            "since last night",
            "for two days",
            "for three days",
            "several days",
            "whole day",
            "all day",
            "halin kahapon",
            "halin kagab-i",
            "pila ka adlaw",
            "since morning",
        ]

        if self._contains_any(
            text,
            phrases,
        ):
            return True

        patterns = [
            r"\b\d+\s+days?\b",
            r"\b\d+\s+hours?\b",
            r"\b\d+\s+adlaw\b",
            r"\b\d+\s+oras\b",
        ]

        return any(
            re.search(pattern, text)
            for pattern in patterns
        )
