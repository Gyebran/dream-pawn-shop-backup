# Admin backend architecture

- `app/Http/Controllers/Admin`: thin HTTP controllers.
- `app/Http/Requests/Admin`: request validation.
- `app/Services/Admin`: Admin use cases grouped by catalog, loans, fines, reports, and Member accounts.
- `app/Services/Loans/LoanEligibilityService.php`: shared eligibility and two-unit allocation rules.
- `app/Models/Admin` and `app/Models/Member`: role profile records; shared login identity is `App\Models\User`.
- `app/Models/Catalog`, `app/Models/Loans`, and `app/Models/Fines`: business entities.
- Member controllers, requests, and services have dedicated folders and are implemented alongside the Admin workflows.

## Implemented business rules
- Approval atomically reserves all requested units and requires an active Member with no unpaid fines.
- Approved reservations and active loans count toward the two-unit allocation cap at Admin approval.
- Member submission also counts that Member's own pending-request units toward the two-unit cap. This is a confirmed Member-side submission rule; pending requests do not reserve inventory until Admin approval.
- Handover must occur within 24 hours of approval; expired approvals are cancelled and reserved units released.
- The five-day period starts at handover, not approval.
- Late fine is `floor(overdue_seconds / 86400) * 10000` per physical unit.
- Only Admin can confirm returns and fine payments.
- Items are archived and Member accounts deactivated rather than permanently deleted.
- Loan reports can be filtered and exported as CSV.
- A migration adds a unique request/unit constraint and indexes for common operational queries.

## Admin routes
- `POST /admin/login`, `POST /admin/logout`
- `GET /admin/dashboard`
- `GET|POST /admin/categories`; `GET|PATCH /admin/categories/{category}`; `PATCH /admin/categories/{category}/archive`
- `GET|POST /admin/items`; `GET|PATCH /admin/items/{item}`; `PATCH /admin/items/{item}/archive`
- `GET|POST /admin/members`; `GET|PATCH /admin/members/{member}`
- `GET /admin/loan-requests`; POST actions: `approve`, `reject`, `handover`, `confirm-return`
- `GET /admin/fines`; `POST /admin/fines/{fine}/confirm-payment`
- `GET /admin/reports/loans`; `GET /admin/reports/loans/export`

## Member routes
- `POST /member/login`, `POST /member/logout`
- `GET /member/dashboard`, `GET|PATCH /member/profile`
- `GET /member/catalog/categories`, `GET /member/catalog/items`, `GET /member/catalog/items/{item}`
- `GET /member/loan-requests`, `POST /member/loan-requests`, `GET /member/loan-requests/{loan}`
- `GET /member/fines`

Feature tests cover Admin authentication/authorization, catalog and Member account management, reports, loan allocation/workflow, return/fine boundaries, unpaid-fine restrictions, handover timeout, Member auth/profile/catalog/submission rules/ownership, and an end-to-end Member-to-Admin workflow. The suite must pass on the latest commit before NeonDB validation.
