
import re
from typing import Any


class ComplaintSignalExtractor:
    """
    Extract reported operational signals from a complaint description.

    These phrase-based indicators are not verified facts.
    Positive signals indicate a condition appears to be reported.
    Negated signals should not be treated as positive signals.
    Final classification and urgency are handled separately.
    """

    SIGNAL_PHRASES = {
        "possible_flooding": [
            "flooding",
            "flooded",
            "flood water",
            "water flooding",
            "water is flooding",
            "baha",
            "pagbaha",
        ],
        "possible_large_leak": [
            "large leak",
            "major leak",
            "heavy leakage",
            "massive leak",
            "burst mainline",
            "burst water pipe",
            "pipe burst",
            "burst pipe",
            "broken mainline",
            "ruptured pipe",
            "gushing water",
            "water gushing",
            "water is gushing",
            "water pouring out",
            "gushing",
            "kusog nga agas",
        ],
        "possible_leakage": [
            "leak",
            "leaking",
            "leakage",
            "water escaping",
            "dripping water",
            "pipe burst",
            "burst pipe",
            "burst water pipe",
            "burst mainline",
            "broken pipe",
            "ruptured pipe",
            "water pouring out",
            "water gushing",
            "tumutulo",
            "tagas",
        ],
        "public_road": [
            "public road",
            "road",
            "highway",
            "street",
            "roadway",
            "dalan",
            "karsada",
        ],
        "multiple_households": [
            "multiple households",
            "many households",
            "several households",
            "many houses",
            "several houses",
            "several homes",
            "entire neighborhood",
            "whole neighborhood",
            "multiple homes",
            "many consumers",
            "several consumers",
            "pila ka panimalay",
        ],
        "area_wide_impact": [
            "entire area",
            "whole area",
            "whole barangay",
            "entire barangay",
            "large area",
            "widespread outage",
            "area-wide",
            "community-wide",
            "barangay-wide",
            "whole community",
        ],
        "complete_service_loss": [
            "no water",
            "no water supply",
            "without water",
            "water supply stopped",
            "water stopped completely",
            "no water at all",
            "no water coming out",
            "no water from the tap",
            "no water in our taps",
            "walang tubig",
            "nawalan ng tubig",
            "walang supply ng tubig",
            "wala tubig",
            "wala gid tubig",
            "wala gid sang tubig",
            "wala sang tubig",
            "wala na tubig",
            "walay tubig",
            "wala tubig",
            "wala kami tubig",
            "wala gid tubig",
            "wala gid sang tubig",
        ],
        "weak_water_supply": [
            "low water pressure",
            "weak water pressure",
            "water pressure is weak",
            "water pressure is low",
            "weak water supply",
            "low pressure",
            "weak flow",
            "slow water flow",
            "little water coming out",
            "hinay ang agas",
            # Tagalog
            "mahina ang tubig",
            "mahina ang agos ng tubig",
            "mahina ang pressure",
            "mababa ang pressure",
            "halos wala nang lumalabas sa gripo",
            "mahina ang daloy ng tubig",
            # Cebuano / Hiligaynon
            "hinay ang tubig",
            "hinay ang agos sa tubig",
            "mahina ang agos",
            "hinay ang pressure",
        ],
        "extended_duration": [
            "for several days",
            "for many days",
            "for weeks",
            "for several weeks",
            "for months",
            "since yesterday",
            "since last week",
            "since last month",
            "for a long time",
            "for hours",
            "all day",
            "two days",
            "three days",
            "one week",
            "several hours",
            # Cebuano / Hiligaynon
            "sukad gahapon",
            "halin kahapon",
            "halin sang kahapon",
            "halin pa kahapon",
            "sukad sang kahapon",
            "sukad sang semana",
            "halin sang nagligad nga semana",
            # Tagalog
            "mula kahapon",
            "simula kahapon",
            "mula noong isang linggo",
            "maghapon",
        ],
        "meter_issue": [
            "water meter",
            "meter stand",
            "meter reading",
            "meter is damaged",
            "damaged meter",
            "broken meter",
            "meter malfunction",
            "meter is not working",
            "metro",
        ],
    }

    SIGNAL_MESSAGES = {
        "possible_flooding": "Possible flooding is reported.",
        "possible_large_leak": "Possible large or forceful leakage is reported.",
        "possible_leakage": "Possible water leakage is reported.",
        "public_road": "A road or public-access location is mentioned.",
        "multiple_households": "Possible impact on multiple households is reported.",
        "area_wide_impact": "Possible wider-area impact is reported.",
        "complete_service_loss": "Complete water service loss may be reported.",
        "weak_water_supply": "Weak water supply or pressure may be reported.",
        "extended_duration": "A potentially extended duration is mentioned.",
        "meter_issue": "A water-meter-related issue is mentioned.",
    }

    NEGATION_PHRASES = [
        "not experiencing",
        "not affected by",
        "not having",
        "free from",
        "doesn't",
        "doesnt",
        "don't",
        "dont",
        "didn't",
        "didnt",
        "isn't",
        "isnt",
        "aren't",
        "arent",
        "wasn't",
        "wasnt",
        "weren't",
        "werent",
        "cannot",
        "can't",
        "cant",
        "never",
        "without",
        "none",
        "not",
        "no",
        "wala",
        "walay",
        "indi",
        "dili",
    ]

    CONTRAST_WORDS = [
        "but",
        "however",
        "although",
        "yet",
        "except",
        "pero",
        "apang",
    ]

    def extract(self, description: str) -> dict[str, Any]:
        """Extract signals, supporting evidence, negations, and durations."""
        normalized = self._normalize(description or "")

        signals = {key: False for key in self.SIGNAL_PHRASES}
        evidence = []
        negated_signals = []

        for key, phrases in self.SIGNAL_PHRASES.items():
            matches = self._find_matches(normalized, phrases)

            if not matches:
                continue

            positive_match = False
            negated_match = False

            for match in matches:
                if self._is_negated(normalized, match):
                    negated_match = True
                else:
                    positive_match = True

            if positive_match:
                signals[key] = True
                evidence.append(self.SIGNAL_MESSAGES[key])
            elif negated_match:
                negated_signals.append(key)

        # Keep duration detection separate from the water-condition signals.
        duration_mentions = self._find_duration_mentions(normalized)

        if duration_mentions:
            # A duration phrase can be mentioned even when the water condition
            # itself is negated, e.g. "Walang tubig mula kahapon."
            signals["extended_duration"] = True

            if "extended_duration" in negated_signals:
                negated_signals.remove("extended_duration")

            if self.SIGNAL_MESSAGES["extended_duration"] not in evidence:
                evidence.append(self.SIGNAL_MESSAGES["extended_duration"])

        return {
            "signals": signals,
            "evidence": evidence,
            "negated_signals": negated_signals,
            "duration_mentions": duration_mentions,
        }

    @staticmethod
    def _normalize(text: str) -> str:
        """Normalize text for consistent phrase matching."""
        text = (text or "").lower()
        text = text.replace("’", "'")
        text = re.sub(r"[\r\n\t]+", " ", text)
        text = re.sub(r"\s+", " ", text)
        return text.strip()

    def _find_matches(
        self,
        text: str,
        phrases: list[str],
    ) -> list[tuple[int, int, str]]:
        """Find whole-phrase matches and return their character positions."""
        matches = []

        for phrase in phrases:
            normalized_phrase = self._normalize(phrase)

            if not normalized_phrase:
                continue

            pattern = (
                r"(?<!\w)"
                + re.escape(normalized_phrase).replace(r"\ ", r"\s+")
                + r"(?!\w)"
            )

            for match in re.finditer(pattern, text):
                matches.append(
                    (match.start(), match.end(), normalized_phrase)
                )

        # Remove duplicate matches with identical positions.
        unique_matches = sorted(set(matches), key=lambda item: (item[0], item[1]))
        return unique_matches

    def _is_negated(
        self,
        text: str,
        match: tuple[int, int, str],
    ) -> bool:
        """
        Check for nearby negation words.

        This is a practical heuristic, not a complete natural-language
        understanding system.
        """
        start, _, _ = match
        context_start = max(0, start - 65)
        preceding_text = text[context_start:start].strip()

        if not preceding_text:
            return False

        # A contrast word can separate an earlier negation from a later,
        # independently asserted condition.
        last_contrast_position = -1

        for contrast in self.CONTRAST_WORDS:
            pattern = r"(?<!\w)" + re.escape(contrast) + r"(?!\w)"

            for contrast_match in re.finditer(pattern, preceding_text):
                last_contrast_position = max(
                    last_contrast_position,
                    contrast_match.end(),
                )

        if last_contrast_position >= 0:
            preceding_text = preceding_text[last_contrast_position:].strip()

        for negation in sorted(
            self.NEGATION_PHRASES,
            key=len,
            reverse=True,
        ):
            pattern = r"(?<!\w)" + re.escape(negation) + r"(?!\w)"
            negation_matches = list(re.finditer(pattern, preceding_text))

            if not negation_matches:
                continue

            last_negation = negation_matches[-1]
            text_after_negation = preceding_text[last_negation.end():].strip()

            # Do not carry negation across a punctuation-separated clause.
            if re.search(r"[,;.!?]", text_after_negation):
                continue

            # "Not only ... but also ..." is not a negation.
            if negation == "not" and re.match(
                r"only\b",
                text_after_negation,
            ):
                continue

            return True

        return False

    def _find_duration_mentions(self, text: str) -> list[str]:
        """Return recognized duration phrases found in the description."""
        duration_phrases = self.SIGNAL_PHRASES["extended_duration"]
        found = []

        for phrase in duration_phrases:
            normalized_phrase = self._normalize(phrase)
            pattern = (
                r"(?<!\w)"
                + re.escape(normalized_phrase).replace(r"\ ", r"\s+")
                + r"(?!\w)"
            )

            if re.search(pattern, text):
                found.append(normalized_phrase)

        return sorted(set(found))
