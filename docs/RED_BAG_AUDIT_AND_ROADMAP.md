# RED BAG — Audit and Roadmap

**Last repository audit:** 13 October 2026  
**Branch:** `main`  
**Live domain:** https://www.redbag.app  
**Production app path:** `/home/redbag/htdocs/www.redbag.app`

## Verified repository snapshot

- Baseline before P0: `5a82b231ba5ea37f0da8dd388da5e654c9d1dca8`.
- P0 truthfulness/security commit: `98c50836cee9b4ba5066de7cda9904e8e839ffbe`.
- P0 follow-up copy correction: `d5c5e8ef15588f25e6eef2ec456ce27888ce3c5d`.
- Test setup/health check commits: `500cc8c26495837d99576dce8e04b2643ab7f2dc`, `927d0cfff2e421bf4613f629a4bfa88f1dcb80fb`, and `2fb132753baf0a8f430b51a19baddba0422a76ba`.
- Volunteer review increment: `7b8207783a74e9114a56d1ae96b1395172c2c941`; follow-up view fix: `8e1cf04686b30063338f04a77114be2595f7e662`.
- Audit found the repository lacked `app/Http/Controllers/Controller.php`, although the existing controllers extend it. The shared base controller is being restored and controller-backed form actions need regression tests.
- Stack: Laravel 13, PHP 8.3+, Blade, Vite/Tailwind CSS, MySQL-compatible DB.
- Existing core tables: donors, blood_requests, otp_challenges. New audit events table is additive.
- Deployment workflow checks the public Laravel health endpoint `/up`.
- Automated tests cover homepage truthfulness, safety copy, OTP production fail-closed behavior and volunteer authentication.

## Implemented in this increment

- Removed fabricated homepage metrics and hard-coded sample requests.
- Corrected claims that implied matching, notifications or volunteer review were live.
- Added Safety & Privacy page and removed inert/dead actions.
- Production OTP is disabled unless a real delivery implementation exists; log OTP is restricted to local/testing.
- Added basic route throttles.
- Added a single-credential volunteer login gate configured only through server environment values.
- Added a protected review queue for unexpired pending requests.
- Volunteer approval is blocked until requester phone verification is recorded.
- Volunteer actions (approve, reject, needs-info) create an audit event; approve moves the request to `verified`, reject closes it as `cancelled`, and needs-info remains pending with a recorded reason.
- Added an additive `blood_request_events` migration.
- Portal is disabled until production `.env` has `REDBAG_VOLUNTEER_EMAIL` and a bcrypt `REDBAG_VOLUNTEER_PASSWORD_HASH`. No production `.env` was changed.

## Current feature truth

### Implemented (automated tests or deployment checks exist)
- Donor and blood-request intake forms.
- OTP challenge hashing/expiry/attempt cap/resend cooldown in local/testing only.
- Session-based donor dashboard and profile/availability updates.
- Rate limits on intake, OTP and donor profile actions.
- Volunteer portal login with one configured credential; no user/role management.
- Request review queue and audit events for review decisions.
- Homepage/safety truthfulness tests and OTP production-safety tests.
- Deployment health check against `https://www.redbag.app/up`.

### Still not implemented
- Production SMS/OTP provider.
- Multi-user volunteer/admin accounts, recovery, roles or organization management.
- Automated donor matching, alerts, donor accept/decline/maybe responses or safe contact handoff.
- Public request tracking token/page, requester cancellation, automated expiry or confirmed donation completion.
- Abuse reporting, full privacy/retention operations and robust donor account recovery.
- Medical compatibility decisions; these remain with qualified professionals/authorized services.
- Full mobile/browser/accessibility testing and backup restore test.

## Release gates

1. Configure volunteer credentials in production `.env` before using the review portal.
2. Integrate a real SMS provider before enabling production OTP.
3. Add tests for request review transactions and authorization boundaries.
4. Implement request tracking with a high-entropy token whose hash is stored; never expose personal data in public status pages.
5. Match only phone-verified, reviewed requests to phone-verified, available donors; matching is a coordination suggestion, not a clinical decision.
6. Add donor responses and consent-aware contact handoff.
7. Verify live HTTP health and GitHub Actions deployment after each commit.
8. Test backup restoration in a non-production environment.

## Production safety constraints

- Never commit or overwrite production `.env`.
- Never drop, reset, truncate or seed production tables.
- Keep schema changes additive; preserve all existing donor/request data.
- Never claim SMS, matching, notifications, volunteer review, verified donations or production readiness until implemented and tested.
