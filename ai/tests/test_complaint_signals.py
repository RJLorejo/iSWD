
import unittest

from ai.app.triage.signals import ComplaintSignalExtractor


class ComplaintSignalExtractorTests(unittest.TestCase):
    def setUp(self):
        self.extractor = ComplaintSignalExtractor()

    def signals_for(self, complaint):
        return self.extractor.extract(complaint)["signals"]

    def test_no_water_is_detected_in_hiligaynon(self):
        signals = self.signals_for(
            "Wala gid tubig sa tanan nga gripo."
        )
        self.assertTrue(signals["complete_service_loss"])

    def test_no_water_is_detected_in_filipino(self):
        signals = self.signals_for(
            "Nawalan ng tubig sa aming mga gripo."
        )
        self.assertTrue(signals["complete_service_loss"])

    def test_negated_flooding_is_not_detected(self):
        signals = self.signals_for(
            "There is no flooding near our house."
        )
        self.assertFalse(signals["possible_flooding"])

    def test_negated_leak_is_not_detected(self):
        signals = self.signals_for(
            "There is no leak near the water meter."
        )
        self.assertFalse(signals["possible_leakage"])

    def test_positive_leak_is_detected(self):
        signals = self.signals_for(
            "There is a leak on the main road."
        )
        self.assertTrue(signals["possible_leakage"])
        self.assertTrue(signals["public_road"])

    def test_positive_flooding_and_burst_pipe_are_detected(self):
        signals = self.signals_for(
            "A pipe burst and water is flooding the road."
        )
        self.assertTrue(signals["possible_flooding"])
        self.assertTrue(signals["possible_leakage"])
        self.assertTrue(signals["possible_large_leak"])

    def test_negated_burst_pipe_is_not_detected_as_leakage(self):
        signals = self.signals_for(
            "There is no pipe burst or water leak on our road."
        )
        self.assertFalse(signals["possible_leakage"])
        self.assertFalse(signals["possible_large_leak"])

    def test_no_flooding_does_not_create_large_leak_by_combination(self):
        signals = self.signals_for(
            "There is a small leak, but no flooding."
        )
        self.assertTrue(signals["possible_leakage"])
        self.assertFalse(signals["possible_flooding"])
        self.assertFalse(signals["possible_large_leak"])

    def test_tagalog_weak_water_supply_is_detected(self):
        result = self.extractor.extract(
            "Mahina ang tubig at halos wala nang lumalabas sa gripo."
        )
        self.assertTrue(result["signals"]["weak_water_supply"])
        self.assertFalse(result["signals"]["complete_service_loss"])

    def test_cebuano_extended_duration_is_detected(self):
        result = self.extractor.extract(
            "Walay tubig sukad gahapon sa among lugar. Palihog og check."
        )
        self.assertTrue(result["signals"]["complete_service_loss"])
        self.assertTrue(result["signals"]["extended_duration"])
        self.assertIn("sukad gahapon", result["duration_mentions"])
        self.assertNotIn("extended_duration", result["negated_signals"])

    def test_weak_water_supply_is_detected_in_tagalog(self):
        signals = self.signals_for(
            "Mahina ang agos ng tubig sa aming gripo."
        )
        self.assertTrue(signals["weak_water_supply"])

    def test_duration_alone_does_not_create_flooding_or_leakage(self):
        signals = self.signals_for(
            "Walang tubig mula kahapon."
        )
        self.assertTrue(signals["extended_duration"])
        self.assertFalse(signals["possible_flooding"])
        self.assertFalse(signals["possible_leakage"])

    def test_hiligaynon_no_water_with_duration_is_detected(self):
        result = self.extractor.extract(
            "Wala kami tubig halin pa kahapon. Palihog tan-awa."
        )

        self.assertTrue(result["signals"]["complete_service_loss"])
        self.assertTrue(result["signals"]["extended_duration"])
        self.assertIn(
            "halin pa kahapon",
            result["duration_mentions"],
        )

    def test_several_houses_is_detected_as_multiple_households(self):
        signals = self.signals_for(
            "A large pipe burst and several houses are flooded."
        )

        self.assertTrue(signals["multiple_households"])
        self.assertTrue(signals["possible_flooding"])
        self.assertTrue(signals["possible_large_leak"])

    def test_negated_hiligaynon_no_water_is_not_detected(self):
        signals = self.signals_for(
            "Indi kami wala tubig."
        )

        self.assertFalse(signals["complete_service_loss"])

if __name__ == "__main__":
    unittest.main()
