# Assessment Gap-Closure Notes — Dream Pawn Shop

This file tracks evidence that can be shown during an assessment. It must be kept honest: a written example is not proof that it was executed, and a repository file is not proof that a deployed environment uses it.

## Safe workflow

- Work on `feat/assessment-gap-coverage`, not directly on `main`.
- Review changes in a pull request before merging.
- Run the project's test suite and frontend build in a local/dev environment.
- Never test destructive SQL against the production NeonDB database.
- Record actual test commands and outcomes only after they have been run.

## Current gaps to address

| Area | Current evidence / limitation | Safe next step |
|---|---|---|
| Technology evaluation | The repository identifies Laravel/PHP, PostgreSQL/NeonDB, Blade/Tailwind/Vite, but does not itself prove a formal migration from an older stack. | Keep a short decision record explaining needs, trade-offs, versions, and alternatives considered. Do not claim a migration unless one actually happened. |
| SQL stored procedure | Application logic is primarily implemented in Laravel services; a PostgreSQL procedure is not a substitute for those services. | Use `database/sql/assessment_postgresql_examples.sql` as a separate, explicitly invoked practice example. It is not an application migration. |
| SQL function and transactions | Schema scripts include PostgreSQL trigger/function patterns, but assessors may ask for a standalone function and commit/rollback evidence. | Practice the examples in a disposable PostgreSQL database and capture the actual result. |
| Debugging | PHPUnit feature tests and deployment logs can support the workflow, but only if the candidate can explain a real issue they diagnosed. | Keep a concise incident note: symptom, evidence, root cause, fix, regression test, and actual outcome. |
| Algorithm complexity | There is no formal Big-O report identified in the repository. | Analyze a specific method and its data size assumptions; do not assign complexity to database internals without evidence. |
| Multimedia | Item image upload and display have been implemented in the assessment branch: admin upload, public-disk storage, validation (JPG/JPEG/PNG/WEBP, max 2 MB), member catalog cards and detail image. Feature tests were added for valid and invalid uploads. | Run the CI tests; locally/deployed, run `php artisan storage:link` and verify uploads persist. Render's local filesystem may be ephemeral, so production persistence needs an external object-storage disk or documented redeploy limitation. Do not claim tests passed until CI confirms them. |
| Static checks | CI now validates Composer metadata and runs PHP syntax checks before feature tests. This catches syntax/configuration errors but is not a full semantic static analyzer such as PHPStan. | Confirm CI passes. If adding PHPStan later, first baseline existing warnings and configure an explicit level rather than claiming all static analysis is covered. |
| Alerting and resource monitoring | No external alert delivery or runtime resource dashboard is configured by this change. Laravel logs are useful diagnostic evidence, but are not by themselves a notification service or continuous resource monitor. | Use a real health-check/uptime monitor and configure notifications with a secret-backed provider only if the team has access; monitor Render metrics and record actual measurements. Do not claim alerts or monitoring are active until configured and tested. |

## PostgreSQL examples

See `database/sql/assessment_postgresql_examples.sql`.

- The file is not loaded by Laravel automatically.
- It uses PostgreSQL-specific syntax and should not be run through the SQLite PHPUnit test suite.
- The procedure and function definitions should be reviewed and tested in a disposable PostgreSQL database before they are presented as executed evidence.
- The procedure call is commented out to avoid accidentally archiving a real category.
- The trigger creation example is commented out because the application schema may already have a timestamp trigger.
