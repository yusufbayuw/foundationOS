"""Offline contract tests: prove the UAT evidence gate rejects unexecuted matrices."""
import datetime as dt
import importlib.util
import pathlib
import unittest

MODULE_PATH = pathlib.Path(__file__).with_name("uat_staging.py")
SPEC = importlib.util.spec_from_file_location("uat_staging", MODULE_PATH)
uat = importlib.util.module_from_spec(SPEC)
SPEC.loader.exec_module(uat)

SHA = "a" * 40


def full_evidence():
    timestamp = dt.datetime.now(dt.timezone.utc).isoformat()
    cases = [
        {
            "id": ident,
            "status": "PASS",
            "build_sha": SHA,
            "positive": "restricted-ticket:positive",
            "negative": "restricted-ticket:negative",
            "database": "restricted-ticket:database-before-after",
            "audit": "restricted-ticket:audit-log",
            "ticket": "restricted-ticket:review",
            "tested_by": "Academic QA",
            "approved_by": "Academic Owner",
            "executed_at": timestamp,
        }
        for ident in uat.IDS
    ]
    return {
        "environment": "education-staging",
        "deployed_sha": SHA,
        "deployment_proof": "restricted-ticket:deployed-sha",
        "cases": cases,
    }


class StagingUatGateTests(unittest.TestCase):
    def test_missing_all_scenarios_is_no_go(self):
        self.assertTrue(uat.verify_evidence({
            "environment": "education-staging",
            "deployed_sha": SHA,
            "deployment_proof": "protected",
            "cases": [],
        }, SHA))

    def test_successful_health_probe_is_not_full_uat(self):
        data = full_evidence()
        data["cases"] = []
        data["health"] = {"status": "PASS", "http_code": 200}
        self.assertTrue(uat.verify_evidence(data, SHA))

    def test_foreign_environment_is_no_go(self):
        data = full_evidence()
        data["environment"] = "production"
        self.assertTrue(uat.verify_evidence(data, SHA))

    def test_wrong_deployed_sha_is_no_go(self):
        data = full_evidence()
        data["deployed_sha"] = "b" * 40
        self.assertTrue(uat.verify_evidence(data, SHA))

    def test_one_blocked_scenario_is_no_go(self):
        data = full_evidence()
        data["cases"][-1]["status"] = "BLOCKED"
        self.assertTrue(uat.verify_evidence(data, SHA))

    def test_missing_negative_tenant_proof_is_no_go(self):
        data = full_evidence()
        data["cases"][0]["negative"] = ""
        self.assertTrue(uat.verify_evidence(data, SHA))

    def test_same_tester_and_owner_is_no_go(self):
        data = full_evidence()
        data["cases"][0]["approved_by"] = "Academic QA"
        self.assertTrue(uat.verify_evidence(data, SHA))

    def test_formally_complete_manifest_passes_contract_only(self):
        # The acceptance officer must independently verify the linked evidence.
        self.assertEqual(uat.verify_evidence(full_evidence(), SHA), [])

    def test_read_only_probe_rejects_successful_foreign_tenant_response(self):
        with self.assertRaises(ValueError):
            uat.execute_probe("https://staging.example.test", {
                "id": "EDU-07",
                "kind": "negative",
                "method": "GET",
                "path": "/exam/foreign-result",
                "expected_status": [200],
            }, 2.0)

    def test_positive_probe_must_expect_success(self):
        with self.assertRaises(ValueError):
            uat.execute_probe("https://staging.example.test", {
                "id": "EDU-07",
                "kind": "positive",
                "method": "GET",
                "path": "/exam/own-result",
                "expected_status": [404],
            }, 2.0)

    def test_mutating_http_probe_is_refused(self):
        with self.assertRaises(ValueError):
            uat.execute_probe("https://staging.example.test", {
                "id": "EDU-07",
                "kind": "negative",
                "method": "POST",
                "path": "/exam/results",
                "expected_status": [403],
            }, 2.0)

    def test_rejects_insecure_http_staging(self):
        with self.assertRaises(ValueError):
            uat.staging_base("http://staging.example.test")

    def test_rejects_url_embedded_credentials(self):
        with self.assertRaises(ValueError):
            uat.staging_base("https://user:pass@staging.example.test")


if __name__ == "__main__":
    unittest.main()
