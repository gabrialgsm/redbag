# RED BAG — Audit and Roadmap

**Last repository audit:** 13 October 2026  
**Branch:** `main`  
**Live domain:** https://www.redbag.app  
**Production app path:** `/home/redbag/htdocs/www.redbag.app`

## Implemented

- Truthful homepage with fabricated metrics/sample requests removed and no claims of automatic matching or notifications.
- Safety & Privacy page, corrected navigation, and basic throttles.
- Production OTP fails closed because no SMS provider is integrated; log OTP is limited to local/testing.
- Volunteer portal with one environment-configured credential, protected review queue, request review actions, and audit events.
- Private request tracking with a high-entropy token; only its SHA-256 hash is stored. Requesters can cancel pending, reviewed, matching or matched requests.
- Volunteer-generated donor shortlist limited to phone-verified, consented, available donors with exact blood group and same district for a phone-verified, volunteer-reviewed, unexpired request.
- Donor private inbox with accept/decline/maybe. Accept requires consent for a RED BAG volunteer to contact the donor; phone numbers are not displayed on public tracking.
- Protected volunteer contact workspace: requester phone is shown only to the configured volunteer; donor phone is shown only when the donor accepted and explicitly consented to volunteer contact. Direct requester-donor phone sharing is not implemented.
- Contact workspace views and contact outcomes are audit logged without placing phone numbers in event metadata.
- CI tests run with temporary SQLite config inside the GitHub runner; deployment checks public `/up`.

## Still not implemented

- Production SMS/OTP provider. New production donor registration and request submission remain disabled until real SMS delivery is integrated.
- Multi-user volunteer/admin accounts, roles, recovery or organization management. Volunteer portal requires `REDBAG_VOLUNTEER_EMAIL` and `REDBAG_VOLUNTEER_PASSWORD_HASH` in production `.env`; no production `.env` was changed.
- SMS/push/email donor notifications; donors must sign in to see their inbox.
- Automated expiry processing, confirmed donation completion, abuse reporting, full privacy/retention operations, and robust donor account recovery.
- Clinical compatibility decisions; these remain with qualified medical professionals/authorized services.
- Full mobile/browser/accessibility testing and backup restore test.

## Release gates

1. Configure volunteer credentials in production `.env` before using the review portal.
2. Integrate and test a real SMS provider before enabling production OTP.
3. Review contact workspace permissions and audit retention before wider pilot.
4. Add expiry scheduling and abuse reporting.
5. Verify deployment and public health after each commit.
6. Test backup restoration in a non-production environment.

## Production safety constraints

- Never commit or overwrite production `.env`.
- Never drop, reset, truncate or seed production tables.
- Keep schema changes additive and preserve existing donor/request data.
- Never claim SMS, automatic notifications or donation completion until implemented and tested.
