# RED BAG — Audit and Roadmap

**Last repository audit:** 13 October 2026  
**Branch:** `main`  
**Live domain:** https://www.redbag.app  
**Production app path:** `/home/redbag/htdocs/www.redbag.app`

## Verified repository snapshot

- P0 truthfulness/security commit: `98c50836cee9b4ba5066de7cda9904e8e839ffbe`.
- Latest stable P0/volunteer baseline before request tracking: `e968c2946e8af31f49420ce4f22c0505b7c3a3c4`.
- Shared Laravel controller base was missing in the repository and has been restored.
- Deployment workflow checks the public Laravel health endpoint `/up`; the latest confirmed health check passed.
- Automated tests cover homepage truthfulness, safety copy, OTP fail-closed behavior and volunteer authentication.

## Implemented

- Removed fabricated homepage metrics/sample requests, corrected misleading capability claims, removed dead links and added a Safety & Privacy page.
- Production OTP fails closed because no SMS provider is integrated; log OTP is limited to local/testing.
- Added basic throttles and a shared Laravel controller base.
- Added single-credential volunteer login, a protected request review queue, and review audit events.
- Volunteer approval is blocked until requester phone verification is recorded.
- Added private request tracking with a 64-character high-entropy token; only its SHA-256 hash is stored.
- After local/testing OTP verification, requester receives the private status link in the same browser session.
- Added requester cancellation for requests that have not moved beyond pending/review/verified states.
- Public tracking view shows only operational request details and a sanitized event timeline; it does not display requester/patient phone numbers or names. A no-referrer policy is set globally.
- Added automated token validation tests and a no-token 404 test.

## Still not implemented

- Production SMS/OTP provider; therefore new public request and donor registration flows remain disabled in production.
- Multi-user volunteer/admin accounts, roles, recovery or organization management. Volunteer portal requires `REDBAG_VOLUNTEER_EMAIL` and `REDBAG_VOLUNTEER_PASSWORD_HASH` in production `.env`; this deployment did not change `.env`.
- Automated donor matching, donor alerts, accept/decline/maybe responses, or consent-aware contact handoff.
- Automatic expiry processing; the tracking page can show an expired request based on its expiry timestamp, but does not run a scheduled expiry transition.
- Donation completion verification, abuse reporting, full privacy/retention operations, and robust donor account recovery.
- Full end-to-end tests against a production-like database, mobile/browser/accessibility testing and backup restore test.

## Release gates

1. Configure volunteer credentials in production `.env` before using the review portal.
2. Integrate and test an actual SMS provider before enabling production OTP.
3. Add donor matching only for phone-verified donors and human-reviewed, unexpired requests; never make clinical compatibility decisions.
4. Add donor responses and consent-aware contact handoff without exposing phone numbers publicly.
5. Test tracking/cancellation and review migrations against a production-like database.
6. Verify live HTTP health and GitHub Actions deployment after each commit.
7. Test backup restoration in a non-production environment.

## Production safety constraints

- Never commit or overwrite production `.env`.
- Never drop, reset, truncate or seed production tables.
- Keep schema changes additive and preserve existing donor/request data.
- Never claim SMS, automatic matching, donor notifications, donation completion or production readiness until implemented and tested.
