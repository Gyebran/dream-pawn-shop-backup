# Member backend architecture

Mirrors the Admin backend (`README_ADMIN_BACKEND.md`): thin JSON controllers, Form Request validation, business logic in services, shared rules in `App\Services\Loans\LoanEligibilityService`.

- `app/Http/Middleware/EnsureMember.php`: rejects guests (401) and non-Member or inactive accounts (403). Registered as the `member` middleware alias.
- `app/Http/Controllers/Member`: auth, dashboard, profile, catalog, loan requests, fines.
- `app/Http/Requests/Member`: login, profile update, loan request submission.
- `app/Services/Member`: catalog browsing, dashboard summary, profile, loan request submission and history, own fines.
- `database/seeders/DemoCatalogSeeder.php`: four categories and six demo units so the Member flow works on a fresh database.

## Member routes

| Feature | Endpoint |
|---|---|
| Auth | `POST /member/login`, `POST /member/logout` |
| Dashboard | `GET /member/dashboard` (allocation, unpaid fines, request counts, `eligibility.can_request` with reasons) |
| Profile | `GET /member/profile`, `PATCH /member/profile` (`name`, `email`, `password` + `password_confirmation` + `current_password`) |
| Catalog | `GET /member/catalog/categories`; `GET /member/catalog/items?search=&category_id=&available=1`; `GET /member/catalog/items/{item}` |
| Loan requests | `GET /member/loan-requests?status=`; `POST /member/loan-requests` with `item_ids: [..]` (1 to 2 ids); `GET /member/loan-requests/{loan}` |
| Fines | `GET /member/fines?status=` |

All responses use `{ "message"?, "data" }`. Business rejections return HTTP 422 with a human-readable `message` (PRD F-07 AC3). Requests belonging to another Member return 404 so their existence is not revealed.

Loan request payloads include read-only helper fields for the UI: `unit_count`, `is_overdue`, `overdue_full_days`, `estimated_fine` (only while `active`), `total_fine`, `unpaid_fine`. The stored `fines` rows remain the only authoritative amounts.

## Business rules implemented at submission ("Memenuhi syarat?" in flowchart 5.1)

1. Account must be an active Member (middleware and re-checked inside the transaction).
2. No `unpaid` fines (team handoff rule 10; overrides PRD D-07's temporary assumption).
3. Every requested unit must currently be `available`; archived, reserved and loaned units are rejected with the unit codes named.
4. **Confirmed team rule:** the two-unit cap counts approved reservations + active loans (shared `allocatedUnitCount`) **plus units on the Member's own pending requests** + the new request. The total must not exceed `LoanEligibilityService::MAX_ALLOCATED_UNITS`. Pending units count at submission time; Admin approval continues to apply its own shared allocation validation and does not reserve units until approval.
5. Submission only records the request as `pending` with `requested_at`. Units are reserved by Admin approval (D-02); the 24-hour handover deadline, five-day period and fines stay in the Admin workflow.

## Other workflow decisions

- **No return-request endpoint.** Flowchart 5.2 and handoff rule 9: Member contacts Admin; the ERD has no `return_requested_at`. Add only after an ERD revision.
- **No self-registration** (D-01: Admin creates accounts). Member profile update covers `name`, `email` and `password` because `members` has no extra columns.
- **No Member-side cancellation of a pending request.** Not in BRD/PRD/flowchart; Admin rejects instead. Revisit only if the requirement changes.
- **Catalog requires Member login**, following the flowchart (login precedes browsing). Moving `GET /member/catalog/*` outside the `auth` group would make it public.
- **Guest requests return JSON 401** on protected routes (`bootstrap/app.php`), avoiding redirects to a non-existent `login` route.
- Constants added: `LoanEligibilityService::MAX_ALLOCATED_UNITS` and `Fine::RATE_PER_UNIT_PER_DAY`. Admin services still use their literals and can switch to the constants in a separate cleanup.

## Tests

`tests/Feature/MemberBackendTest.php` covers authentication and authorization, profile, catalog visibility and filters, submission rules (including pending-unit cap), ownership of transactions and fines, overdue helpers, dashboard, and an end-to-end flow through Admin approve, handover, late return, fine and payment confirmation.
