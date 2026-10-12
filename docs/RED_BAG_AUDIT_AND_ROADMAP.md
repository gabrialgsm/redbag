# RED BAG — Audit and Roadmap

**Last repository audit:** 13 October 2026  
**Branch:** `main`  
**Live domain:** https://www.redbag.app  
**Production app path:** `/home/redbag/htdocs/www.redbag.app`

## Verified repository snapshot

- Latest baseline commit before this P0 pass: `5a82b231ba5ea37f0da8dd388da5e654c9d1dca8` (`fix: correct Laravel entrypoint imports`).
- Stack: Laravel 13, PHP 8.3+, Blade, Vite/Tailwind CSS, MySQL-compatible DB.
- Existing routes: homepage, blood-request submission, donor registration, OTP show/resend/verify, donor dashboard/profile/availability/logout, about and campaigns.
- Existing models/tables: donors, blood_requests, otp_challenges. There are no test files in the current repository tree.
- Latest observed deploy workflow run before this pass: success for `5a82b231ba5ea37f0da8dd388da5e654c9d1dca8`.
- The live domain could not be reached from this execution environment; do not infer live availability from GitHub Actions alone.

## P0 changes in this commit

- Removed hard-coded impact metrics and fabricated recent request cards from the homepage.
- Reworded homepage copy to disclose that automatic matching, notifications and volunteer review are not yet live.
- Removed decorative search and inert newsletter actions; replaced dead footer/coverage/campaign links with real routes or removed them.
- Added a Safety & Privacy page with clear limits and emergency-care guidance.
- Restricted log-based OTP to local/testing environments; production OTP now fails closed because no SMS provider is integrated.
- Added request/register and OTP throttles.
- New donor records start unavailable until the phone is verified; donor/request creation checks OTP availability before writing records.
- Masked phone numbers on the OTP screen.

## Current feature truth

### Implemented in repository (not yet end-to-end verified)
- Donor and blood-request intake forms.
- OTP challenge hashing, five-minute expiry, five-attempt cap and resend cooldown for local/testing.
- Session-based donor dashboard and availability/profile changes.
- Basic route-level request throttling.

### Explicitly not implemented
- Production SMS/OTP delivery.
- Volunteer/admin authentication, roles or review queue.
- Verified request legitimacy checks and request state history.
- Donor matching, donor alerts, accept/decline/maybe responses or safe contact handoff.
- Public request tracking, cancellation/expiry processing and confirmed donation completion.
- Abuse reporting, full privacy/retention operations and robust donor account recovery.
- Automated tests/CI checks and end-to-end mobile/browser testing.

## P0/P1 release gates

1. Add automated tests for validation, OTP attempt/expiry/replay, session authorization and rate limits.
2. Integrate a real SMS provider and delivery/error controls before enabling production OTP.
3. Implement volunteer/admin authentication and role-based access before exposing a review queue.
4. Add an explicit request state machine and audit trail.
5. Match only phone-verified, available donors after human request review; never make clinical compatibility decisions.
6. Add consent-aware donor responses and privacy-preserving request status lookup.
7. Verify live HTTP responses, GitHub Actions deployment, application logs and mobile layouts after each change.
8. Test backup restoration in a non-production environment.

## Production safety constraints

- Never commit or overwrite production `.env`.
- Never drop, reset, truncate or seed production tables as part of deployment.
- Keep changes additive and use reviewed migrations only.
- Never claim SMS, matching, notifications, volunteer review, verified donations or production readiness until implemented and tested.
