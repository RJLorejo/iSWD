
import unittest

from ai.app.triage.input_validator import (
    ComplaintInputValidator,
)


class ComplaintInputValidatorTests(unittest.TestCase):

    def setUp(self):
        self.validator = ComplaintInputValidator()

    def test_random_characters_are_rejected(self):
        result = self.validator.validate(
            "jcscufhdufhsbvhds"
        )
        self.assertFalse(result["valid"])

    def test_unrelated_text_is_rejected(self):
        result = self.validator.validate(
            "gwapo ko"
        )
        self.assertFalse(result["valid"])

    def test_cebuano_no_water_is_accepted(self):
        result = self.validator.validate(
            "Walay tubig sukad gahapon"
        )
        self.assertTrue(result["valid"])

    def test_tagalog_low_pressure_is_accepted(self):
        result = self.validator.validate(
            "Mahina ang agos ng tubig sa gripo namin"
        )
        self.assertTrue(result["valid"])

    def test_english_leak_is_accepted(self):
        result = self.validator.validate(
            "There is a leak near our water meter"
        )
        self.assertTrue(result["valid"])

    def test_empty_text_is_rejected(self):
        result = self.validator.validate("")
        self.assertFalse(result["valid"])


if __name__ == "__main__":
    unittest.main()
