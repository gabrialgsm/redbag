# RED BAG — Audit and Roadmap

**Last repository audit:** 13 October 2026  
**Branch:** `main`  
**Live domain:** https://www.redbag.app  
**Production app path:** `/home/redbag/htdocs/www.redbag.app`

## Verified repository snapshot

- P0 truthfulness/security, volunteer review, private request tracking and matching increments are committed directly to `main`.
- The shared Laravel controller base was missing in the repository and has been restored.
- Deployment workflow checks the public Laravel health endpoint `/up`.
- CI uses a temporary SQLite config only inside the GitHub runner; production DB configuration and `.env` are not changed by this workflow.

## Implemented

- Removed fabricated homepage metrics/sample requests, corrected misleading capability claims, removed dead links and added a Safety & Privacy page.
- Production OTP fails closed because no SMS provider is integrated; log OTP is limited to local/testing.
- Added basic throttles and a shared Laravel controller base.
- Added single-credential volunteer login, a protected request review queue, and review audit events.
- Volunteer approval is blocked until requester phone verification is recorded.
- Added private request tracking with a high-entropy token; only its SHA-256 hash is stored. Requesters can cancel eligible requests through their private tracking link.
- Added a volunteer-generated donor shortlist that only includes phone-verified, consented, available donors with the same blood group and district for a phone-verified, volunteer-reviewed, unexpired request.
- Shortlists appear in the donor dashboard inbox. No SMS/push alerts are sent.
- Donors can accept, decline or say maybe. Accepting requires explicit consent for a RED BAG volunteer to contact the donor; the donor's phone number is not shown publicly or directly to the requester.
- Donor responses are recorded in the request event history. A donor response is not proof of donation or clinical eligibility.
- Added automated tests for truthfulness, OTP production safety, volunteer authentication, tracking-token privacy, donor matching filters, donor response consent and ownership boundaries.

## Still not implemented

- Production SMS/OTP provider; therefore new public request and donor registration flows remain disabled in production.
- Multi-user volunteer/admin accounts, roles, recovery or organization management. Volunteer portal requires `REDBAG_VOLUNTEER_EMAIL` and `REDBAG_VOLUNTEER_PASSWORD_HASH` in production `.env`; this deployment did not change `.env`.
- SMS/push/email donor notifications. Donors must sign in to see their inbox.
- Full contact handoff between requester and volunteer. The donor's consent for volunteer contact is recorded, but a protected volunteer contact workspace/call log is not yet implemented.
- Automatic expiry processing; tracking can display an expired request based on its timestamp, but does not run a scheduled expiry transition.
- Donation completion verification, abuse reporting, full privacy/retention operations, robust donor account recovery.
- Full end-to-end testing against a production-like database, mobile/browser/accessibility testing and backup restore test.

## Release gates

1. Configure volunteer credentials in production `.env` before using the review portal.
2. Integrate and test an actual SMS provider before enabling production OTP.
3. Test review, matching, tracking and cancellation migrations against a production-like database.
4. Build a protected volunteer contact workspace with access logging and explicit consent rules.
5. Add expiry scheduling and abuse reporting.
6. Verify live HTTP health and GitHub Actions deployment after each commit.
7. Test backup restoration in a non-production environment.

## Production safety constraints

- Never commit or overwrite production `.env`.
- Never drop, reset, truncate or seed production tables.
- Keep schema changes additive and preserve existing donor/request data.
- Never claim SMS, automated notifications, donation completion or production readiness until implemented and tested.
