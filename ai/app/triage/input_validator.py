
import re
import unicodedata


class ComplaintInputValidator:
    """
    First-pass validation for consumer complaint descriptions.

    This validator does not determine the official complaint category.
    It only checks whether the description contains recognizable
    water-service complaint terms.
    """

    COMPLAINT_TERMS = (
        # English: water service and supply
        "water",
        "no water",
        "no supply",
        "water supply",
        "water service",
        "water interruption",
        "service interruption",
        "low water",
        "low pressure",
        "weak pressure",
        "weak water",
        "weak water pressure",
        "water pressure",
        "weak flow",
        "low flow",
        "no flow",
        "tap",
        "faucet",
        "leak",
        "leaking",
        "leakage",
        "pipe",
        "pipeline",
        "burst pipe",
        "broken pipe",
        "broken water line",
        "flood",
        "flooding",
        "overflow",
        "dirty water",
        "brown water",
        "cloudy water",
        "smelly water",
        "water quality",
        "meter",
        "water meter",
        "meter reading",
        "meter problem",
        "meter damage",
        "high bill",
        "water bill",
        "billing",
        "bill",
        "consumption",
        "high consumption",
        "unusual consumption",
        "connection",
        "service connection",
        "disconnection",
        "reconnection",
        "reconnect",
        "disconnect",
        "sealed connection",
        "sealed meter",
        "reclassification",
        "classification",
        "account classification",

        # Cebuano / Bisaya
        "tubig",
        "walay tubig",
        "wala tubig",
        "wala nay tubig",
        "wala mi tubig",
        "wala kami tubig",
        "hinay ang tubig",
        "hinay ang agos",
        "hinay ang agos sa tubig",
        "gamay ang tubig",
        "tagas",
        "nagtulo",
        "tulo",
        "bali nga tubo",
        "buto ang tubo",
        "baha",
        "hugaw nga tubig",
        "metro sa tubig",
        "bayranan sa tubig",
        "taas ang bill",
        "putol ang tubig",
        "gi putol ang tubig",
        "koneksyon sa tubig",

        # Hiligaynon / Ilonggo
        "wala kami tubig",
        "wala gid tubig",
        "wala tubig",
        "indi kami may tubig",
        "hinay ang agos",
        "hinay ang tubig",
        "tagas",
        "nagatulo",
        "hugaw nga tubig",
        "metro",
        "bayranan",
        "halin pa kahapon",
        "sukad gahapon",
        "putol ang tubig",
        "koneksyon",

        # Tagalog
        "walang tubig",
        "wala kaming tubig",
        "mahina ang tubig",
        "mahina ang agos",
        "mahina ang pressure",
        "mahina ang daloy",
        "tagas ng tubig",
        "tumutulo",
        "sirang tubo",
        "pumutok na tubo",
        "baha",
        "maruming tubig",
        "maduming tubig",
        "metro ng tubig",
        "metro ng tubig",
        "singil sa tubig",
        "bill sa tubig",
        "mataas ang bill",
        "putol ang tubig",
        "naputulan ng tubig",
        "koneksyon ng tubig",
        "konsumo sa tubig",
    )

    def normalize(self, text: str) -> str:
        text = unicodedata.normalize("NFKC", text)
        text = text.lower().strip()

        # Treat punctuation and repeated whitespace consistently.
        text = re.sub(r"[^\w\s]", " ", text, flags=re.UNICODE)
        text = re.sub(r"_+", " ", text)
        text = re.sub(r"\s+", " ", text)

        return text.strip()

    def validate(self, text: str) -> dict:
        if not isinstance(text, str) or not text.strip():
            return {
                "valid": False,
                "status": "invalid_input",
                "message": (
                    "Please describe the water-service problem "
                    "you are experiencing."
                ),
                "matched_terms": [],
            }

        normalized = self.normalize(text)

        if len(normalized) < 3:
            return {
                "valid": False,
                "status": "invalid_input",
                "message": (
                    "Please provide more details about your "
                    "water-service concern."
                ),
                "matched_terms": [],
            }

        # Match complete words or phrases, not arbitrary substrings.
        padded_text = f" {normalized} "
        matched_terms = []

        for term in self.COMPLAINT_TERMS:
            normalized_term = self.normalize(term)

            if f" {normalized_term} " in padded_text:
                matched_terms.append(term)

        if not matched_terms:
            return {
                "valid": False,
                "status": "invalid_input",
                "message": (
                    "Your description does not clearly identify "
                    "a water-service concern. Please describe the "
                    "problem, such as no water, weak water pressure, "
                    "leakage, dirty water, a meter issue, or a billing "
                    "concern. You may write in English, Cebuano, "
                    "Hiligaynon, or Tagalog."
                ),
                "matched_terms": [],
            }

        return {
            "valid": True,
            "status": "accepted",
            "message": (
                "The description contains a recognizable "
                "water-service concern. The suggested category "
                "still needs verification."
            ),
            "matched_terms": list(dict.fromkeys(matched_terms)),
        }
