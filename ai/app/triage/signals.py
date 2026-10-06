import re


class ComplaintSignalExtractor:

    def extract(self, text: str):
        normalized = self._normalize(text)

        signals = {
            "public_road": False,
            "possible_flooding": False,
            "multiple_households": False,
            "area_wide_impact": False,
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
                "main road",
                "intersection",
                "corner",
                "dalan",
                "kalsada",
                "highway",
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
                "water everywhere",
                "road is flooded",
                "street is flooded",
                "baha",
                "ginabaha",
                "nagabaha",
                "nag baha",
                "nabaha",
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
                "multiple houses",
                "multiple households",
                "nearby houses",
                "nearby households",
                "neighbors",
                "neighbours",
                "our neighbors",
                "our neighbours",
                "mga silingan",
                "mga kasilingan",
                "madamo nga balay",
                "madamo balay",
                "damong balay",
                "daghang balay",
                "daghan balay",
                "mga silingan namon",
                "mga silingan namo",
            ],
            (
                "Complaint indicates that multiple "
                "households may be affected."
            ),
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "area_wide_impact",
            [
                "whole area",
                "entire area",
                "our area",
                "whole community",
                "entire community",
                "community affected",
                "whole neighborhood",
                "entire neighborhood",
                "whole barangay",
                "entire barangay",
                "whole purok",
                "entire purok",
                "several streets",
                "multiple streets",
                "many consumers",
                "multiple consumers",
                "many customers",
                "multiple customers",
                "large area",
                "wider area",
                "amon area",
                "bilog nga area",
                "bilog nga purok",
                "bilog nga barangay",
                "tibuok area",
                "tibuok purok",
                "tibuok barangay",
                "daghang consumer",
                "madamo nga consumer",
            ],
            (
                "Complaint indicates possible "
                "area-wide or community impact."
            ),
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
                "nothing comes out",
                "nothing coming out",
                "wala tubig",
                "wala gid tubig",
                "wala na tubig",
                "wala na gid tubig",
                "wala naga gwa tubig",
                "wala nagaguwa tubig",
                "wala ga gwa tubig",
                "walay tubig",
                "wala nay tubig",
                "walay agas",
                "wala tubod ang gripo",
            ],
            (
                "Complaint indicates possible "
                "complete loss of water service."
            ),
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "weak_water_supply",
            [
                "low pressure",
                "low water pressure",
                "weak pressure",
                "weak water pressure",
                "weak water",
                "very little water",
                "slow water flow",
                "weak flow",
                "reduced water flow",
                "hinay ang tubig",
                "hinay gid ang tubig",
                "hinay tubig",
                "hinay ang agas",
                "hinay kaayo ang tubig",
                "hinay kaayo ang agas",
                "mahina ang tubig",
                "mahina tubig",
                "mahina ang pressure",
            ],
            (
                "Complaint indicates weak or "
                "reduced water supply."
            ),
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "possible_leakage",
            [
                "leak",
                "leaking",
                "leakage",
                "water coming out",
                "water coming from",
                "water flowing out",
                "water keeps flowing",
                "water escaping",
                "pipe leaking",
                "water on the pavement",
                "water from the pavement",
                "water coming out of the pavement",
                "tubig nga nagaguwa",
                "tubig nga naga guwa",
                "tubig nga padayon nagaguwa",
                "padayon nagaguwa",
                "nagaguwa nga tubig",
                "naga guwa nga tubig",
                "ga leak",
                "naga leak",
                "tagas",
                "nagatagas",
            ],
            (
                "Complaint contains possible "
                "leakage indicators."
            ),
        )

        large_leak_context = (
            self._contains_any(
                normalized,
                [
                    "large leak",
                    "major leak",
                    "massive leak",
                    "severe leak",
                    "strong leak",
                    "heavy leak",
                    "burst pipe",
                    "pipe burst",
                    "burst main",
                    "broken main",
                    "broken mainline",
                    "mainline burst",
                    "main line burst",
                    "water gushing",
                    "gushing water",
                    "strong water flow",
                    "large amount of water",
                    "dako nga tagas",
                    "grabe nga tagas",
                    "kusog nga tagas",
                    "dako kaayo nga leak",
                    "kusog kaayo ang tubig",
                ],
            )
            or (
                signals["possible_leakage"]
                and signals["possible_flooding"]
            )
        )

        if large_leak_context:
            signals[
                "possible_large_leak"
            ] = True

            evidence.append(
                (
                    "Complaint contains indicators "
                    "of a potentially significant "
                    "water leak."
                )
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
                "yellow water",
                "discolored water",
                "water has sand",
                "sand in the water",
                "sandy water",
                "particles",
                "sediment",
                "brown ang tubig",
                "brown ang water",
                "malabo ang tubig",
                "marumi ang tubig",
                "may balas",
                "may balas ang tubig",
                "balas sa tubig",
                "lapok",
            ],
            (
                "Complaint contains water-quality "
                "or dirty-water indicators."
            ),
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
            (
                "Complaint contains meter-related "
                "information."
            ),
        )

        self._detect(
            normalized,
            signals,
            evidence,
            "abnormal_consumption",
            [
                "large consumption",
                "high consumption",
                "higher consumption",
                "very high consumption",
                "unusually high consumption",
                "unusual consumption",
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
                "high bill",
                "higher bill",
                "large bill",
                "unusually high bill",
                "bill doubled",
                "bill increased",
                "bill is higher",
                "bill is almost twice",
                "water bill increased",
                "higher than normal",
                "much higher than normal",
                "sobrang taas ng bill",
                "mataas ang bill",
                "biglang tumaas ang bill",
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
                "taas kaayo ang bill",
                "nisaka ang bill",
            ],
            (
                "Complaint indicates unusually high "
                "recorded water consumption or bill."
            ),
        )

        reading_terms = self._contains_any(
            normalized,
            [
                "reading",
                "meter reading",
                "recorded reading",
                "bill reading",
            ],
        )

        discrepancy_terms = self._contains_any(
            normalized,
            [
                "does not match",
                "doesn't match",
                "different from",
                "incorrect",
                "incorrect reading",
                "wrong reading",
                "reading is wrong",
                "sala ang reading",
                "lain ang meter reading",
                "mali ang reading",
            ],
        )

        if (
            reading_terms
            and discrepancy_terms
        ):
            signals[
                "reading_discrepancy"
            ] = True

            evidence.append(
                (
                    "Complaint indicates a possible "
                    "discrepancy in the recorded "
                    "meter reading."
                )
            )

        classification_terms = (
            self._contains_any(
                normalized,
                [
                    "reclassification",
                    "re-classification",
                    "classification",
                    "consumer category",
                    "account category",
                ],
            )
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

        if (
            classification_terms
            and change_terms
        ):
            signals[
                "classification_request"
            ] = True

            evidence.append(
                (
                    "Complaint appears to request "
                    "review or correction of account "
                    "classification."
                )
            )

        if self._has_extended_duration(
            normalized
        ):
            signals[
                "extended_duration"
            ] = True

            evidence.append(
                (
                    "Complaint indicates that the "
                    "concern may have continued for "
                    "an extended period."
                )
            )

        return {
            "signals": signals,
            "evidence": evidence,
        }

    def _normalize(
        self,
        text: str,
    ) -> str:
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

            evidence.append(
                message
            )

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
            "since morning",
            "since this morning",
            "halin kahapon",
            "halin kagab-i",
            "halin kagab i",
            "halin sang aga",
            "pila ka adlaw",
            "sukad buntag",
            "sukad kagabii",
            "sukad gahapon",
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
            r"\b\d+\s+ka\s+adlaw\b",
            r"\b\d+\s+ka\s+oras\b",
        ]

        return any(
            re.search(
                pattern,
                text,
            )
            for pattern in patterns
        )
