#!/usr/bin/env python3
"""Read-only staging probes and evidence gate for FoundationOS Education #82.

No database writes, no migrations, no login automation, no unsafe HTTP verbs.
A green health probe is never a completed UAT. All 14 human-verified cases
must have evidence from staging before the acceptance gate can pass.
"""
import argparse
import datetime as dt
import json
import os
import re
import ssl
import sys
import urllib.error
import urllib.parse
import urllib.request

IDS = tuple(f"EDU-{i:02d}" for i in range(1, 15))
SHA_RE = re.compile(r"^[0-9a-f]{40}$")
REQUIRED_EVIDENCE = (
    "positive", "negative", "database", "audit", "ticket",
    "tested_by", "approved_by", "executed_at",
)


def require(condition, message):
    if not condition:
        raise ValueError(message)


def read_json(path):
    with open(path, encoding="utf-8") as handle:
        return json.load(handle)


def write_json(path, value):
    with open(path, "w", encoding="utf-8") as handle:
        json.dump(value, handle, indent=2, ensure_ascii=False)
        handle.write("\n")


def valid_sha(sha):
    return isinstance(sha, str) and SHA_RE.fullmatch(sha.lower()) is not None


def verify_evidence(evidence, expected_sha):
    """Fail closed: CI green, successful /up, or screenshots alone are not UAT."""
    problems = []
    if not valid_sha(expected_sha):
        problems.append("Expected SHA must be a full 40-character commit hash.")
    if evidence.get("environment") != "education-staging":
        problems.append("Evidence must originate from the education-staging environment.")
    deployed_sha = evidence.get("deployed_sha")
    if not valid_sha(deployed_sha) or deployed_sha.lower() != expected_sha.lower():
        problems.append("Operator-verified staging deployed_sha differs from expected SHA.")
    if not str(evidence.get("deployment_proof", "")).strip():
        problems.append("A protected staging deployment SHA proof is required.")
    cases = evidence.get("cases")
    if not isinstance(cases, list):
        return problems + ["cases must be a list of EDU-01 through EDU-14."]

    found = set()
    for case in cases:
        if not isinstance(case, dict):
            problems.append("Malformed case record.")
            continue
        ident = case.get("id")
        if ident not in IDS:
            problems.append(f"Unexpected scenario identifier: {ident}.")
            continue
        if ident in found:
            problems.append(f"Duplicate scenario: {ident}.")
        found.add(ident)
        if case.get("status") != "PASS":
            problems.append(f"{ident}: scenario is not PASS.")
        if case.get("build_sha", "").lower() != expected_sha.lower():
            problems.append(f"{ident}: SHA mismatch.")
        for field in REQUIRED_EVIDENCE:
            if not isinstance(case.get(field), str) or not case[field].strip():
                problems.append(f"{ident}: missing {field} evidence.")
        if case.get("tested_by") == case.get("approved_by") and case.get("tested_by"):
            problems.append(f"{ident}: independent process-owner approval required.")
        try:
            when = dt.datetime.fromisoformat(case["executed_at"])
            if when.tzinfo is None:
                problems.append(f"{ident}: executed_at must include timezone.")
        except (KeyError, ValueError, TypeError):
            problems.append(f"{ident}: executed_at must be valid ISO 8601.")
    problems.extend(f"{ident}: scenario missing." for ident in IDS if ident not in found)
    return problems


class DenyRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, request, fp, code, msg, headers, newurl):
        return None


def staging_base(url):
    parsed = urllib.parse.urlsplit(url)
    require(parsed.scheme == "https" and parsed.hostname and not parsed.username and not parsed.password,
            "Staging URL must be HTTPS without embedded credentials.")
    require(not parsed.query and not parsed.fragment and parsed.path in ("", "/"),
            "Staging URL must be an origin, without path/query.")
    return url.rstrip("/")


def execute_probe(base, probe, timeout):
    """Only GET or HEAD; never record response bodies, cookies or auth tokens."""
    ident = probe["id"]
    require(ident in IDS, "Probe must identify an EDU-xx case.")
    method = probe.get("method", "GET")
    require(method in ("GET", "HEAD"), f"{ident}: only GET and HEAD are permitted.")
    path = probe.get("path", "")
    require(isinstance(path, str) and path.startswith("/") and not path.startswith("//"),
            f"{ident}: path must be a relative path beginning with one slash.")
    require(not urllib.parse.urlsplit(path).scheme and "\\n" not in path and "\\r" not in path,
            f"{ident}: invalid path.")
    statuses = probe.get("expected_status", [])
    require(isinstance(statuses, list) and statuses
            and all(isinstance(x, int) and 100 <= x <= 599 for x in statuses),
            f"{ident}: expected_status must contain HTTP status codes.")
    headers = {"User-Agent": "foundationos-education-uat-readonly/1.0", "Accept": "text/html, application/json"}
    for name, var in probe.get("headers_from_env", {}).items():
        require(name.lower() in ("authorization", "cookie", "x-tenant-id"),
                f"{ident}: unsupported request header.")
        require(re.fullmatch(r"[A-Z][A-Z0-9_]+", var) is not None,
                f"{ident}: invalid environment variable name.")
        require(os.environ.get(var), f"{ident}: missing protected variable {var}.")
        headers[name] = os.environ[var]
    # urllib deliberately uses the default system certificate verifier.
    request = urllib.request.Request(base + path, method=method, headers=headers)
    opener = urllib.request.build_opener(
        urllib.request.HTTPSHandler(context=ssl.create_default_context()),
        DenyRedirect,
    )
    status = None
    try:
        with opener.open(request, timeout=timeout) as response:
            status = response.status
    except urllib.error.HTTPError as exc:
        status = exc.code
        exc.close()
    # Do not record URL/query strings, headers, response body or credential values.
    return {
        "id": ident,
        "label": probe.get("label", "read-only probe"),
        "kind": probe.get("kind", "unspecified"),
        "status": "PASS" if status in statuses else "FAIL",
        "http_code": status,
        "expected_status": statuses,
    }


def probe_main(args):
    spec = read_json(args.spec)
    base = staging_base(spec["staging_url"])
    require(spec.get("environment") == "education-staging", "Target must be education-staging.")
    require(valid_sha(spec.get("expected_sha")), "Expected commit SHA required.")
    require(valid_sha(spec.get("deployed_sha")), "Operator-verified staging SHA required.")
    require(spec["deployed_sha"].lower() == spec["expected_sha"].lower(),
            "Staging deployed SHA does not match the candidate SHA.")
    require(str(spec.get("deployment_proof", "")).strip(),
            "Protected deployment SHA verification reference is required.")
    require(isinstance(spec.get("probes"), list) and len(spec["probes"]) > 0,
            "At least one real HTTPS probe is required.")
    results = []
    for item in spec["probes"]:
        try:
            results.append(execute_probe(base, item, args.timeout))
        except (ValueError, urllib.error.URLError, OSError) as error:
            results.append({
                "id": item.get("id", "unknown"), "status": "FAIL",
                "reason": type(error).__name__,  # never emit secrets or URLs
            })
    result = {
        "environment": "education-staging",
        "deployed_sha": spec["deployed_sha"],
        "executed_at": dt.datetime.now(dt.timezone.utc).isoformat(),
        "kind": "READ_ONLY_HTTP_PROBES_NOT_FULL_UAT",
        "results": results,
        "full_uat_accepted": False,
    }
    write_json(args.output, result)
    print(f"Real staging HTTP probes: {sum(x['status'] == 'PASS' for x in results)}/{len(results)} successful. Full UAT is NOT automatically accepted.")
    return 1 if any(x["status"] != "PASS" for x in results) else 0


def verify_main(args):
    data = read_json(args.evidence)
    issues = verify_evidence(data, args.expected_sha)
    for message in issues:
        print(f"BLOCKED: {message}")
    if issues:
        print(f"UAT NO-GO: {len(issues)} evidence gate issue(s).")
        return 1
    print("Evidence format gate passed for all 14 scenarios. Confirm independent review of linked staging artifacts before release.")
    return 0


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    sub = parser.add_subparsers(dest="command", required=True)
    p = sub.add_parser("probe", help="Run HTTPS GET/HEAD tests against verified staging.")
    p.add_argument("--spec", required=True)
    p.add_argument("--output", required=True)
    p.add_argument("--timeout", type=float, default=12.0)
    v = sub.add_parser("verify", help="Check every EDU-xx UAT acceptance proof.")
    v.add_argument("--evidence", required=True)
    v.add_argument("--expected-sha", required=True)
    args = parser.parse_args()
    try:
        return probe_main(args) if args.command == "probe" else verify_main(args)
    except (ValueError, KeyError, TypeError, json.JSONDecodeError) as error:
        print(f"UAT NO-GO: {error.__class__.__name__}: {error}", file=sys.stderr)
        return 2


if __name__ == "__main__":
    sys.exit(main())
