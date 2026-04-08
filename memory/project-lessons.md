## 2026-04-04 - Returns demo P0 bootstrap

## Snapshot
- Date: 2026-04-04
- Scope: feature bootstrap + environment/debugging for the returns demo P0
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Reused the marketplace Laravel admin structure instead of introducing a new frontend stack. That kept the first usable demo inside the codebase the package already supports.
- Switched from assumption-driven coding to runtime validation early enough to catch route boot, env drift, and seed-data gaps before the user saw a broken demo.

## Mistakes To Stop Repeating

### Mistake: Started feature work before verifying the app boot mode
- What happened: Returns routes and pages were added, but the extracted project still loaded only `routes/install.php`, so none of the admin routes were actually reachable.
- Root cause: I treated the unpacked codebase like a normal ready-to-run Laravel app instead of confirming how the vendor package switches from install mode to runtime mode.
- Earlier signal I missed: `php artisan route:list --path=admin/returns` returned no matches even though the route file clearly contained the new routes.
- Prevention rule: After unpacking any purchased or scaffolded app, verify route/service-provider boot mode before writing feature code.
- Next-time checklist item: Run `route:list` for the target module before building controllers or views.

### Mistake: Relied on Docker env overrides without aligning `.env`
- What happened: `artisan migrate` worked, but HTTP requests still failed at login with a DB connection pointed at `127.0.0.1`.
- Root cause: The runtime had two sources of truth for database settings: Docker service env and the checked-in `.env`. I fixed only one at first.
- Earlier signal I missed: CLI and browser behavior diverged, which is a strong sign that process boot configuration is inconsistent.
- Prevention rule: In Dockerized Laravel workspaces, keep `.env` and Compose environment values aligned for core settings like DB host and app URL.
- Next-time checklist item: Validate DB host through both `artisan tinker` and an HTTP page load before moving on.

### Mistake: Assumed an empty migrated database was enough to render admin UI
- What happened: The login page and admin layout depended on `business_settings`, `currency`, and admin seed data that did not exist after plain migrations.
- Root cause: I treated migrations as sufficient bootstrap, but this codebase assumes install-time seed data and settings are always present.
- Earlier signal I missed: Multiple views and helpers used `first()->value` on `business_settings` lookups, which is a direct sign that null-safe cold-start support was never built.
- Prevention rule: For packaged SaaS/codebase acquisitions, define and automate a minimum bootstrap seed before any browser QA.
- Next-time checklist item: Seed admin, business settings, and currency defaults before testing login or dashboard pages.

## Permanent Rules
- Verify runtime boot path before feature implementation when working with vendor or marketplace codebases.
- Never trust a Dockerized Laravel app until both CLI and HTTP prove they are using the same config.
- Treat “minimum seed data” as part of environment setup, not as optional demo polish.

## Next-Project Checklist
- [ ] Confirm route providers, middleware, and boot mode for the target app before adding features.
- [ ] Align `.env`, container env, and app URL before first browser validation.
- [ ] Seed minimum admin/settings/currency data before testing auth flows.
- [ ] Validate one read path and one write path end-to-end before declaring the module usable.

## Open Risks Or Follow-Ups
- Returns media currently uses placeholder file paths for seeded demo evidence; real image assets still need to be added if the user wants screenshot-quality demos.
- The project is not in a git repo yet, so current changes are not versioned or reviewable through normal git workflow.
- Existing admin pages outside the returns module may still rely on additional install-time data not yet seeded.

## Source Artifacts
- Conversation
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Providers/RouteServiceProvider.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/docker-compose.yml`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/DatabaseSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/DemoBootstrapSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/ReturnsDemoSeeder.php`
- Runtime validation via `docker compose`, `artisan migrate`, `artisan db:seed`, HTTP login, `returns` page loads, and one successful `inspect` form submission creating `RMA-2001`

## 2026-04-04 - Admin page rendering and permission repair

## Snapshot
- Date: 2026-04-04
- Scope: debugging the post-login admin page rendering and dashboard access
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Validated the live HTML output instead of trusting the screenshot alone. That separated the CSS-path problem from the permission problem quickly.
- Fixed the runtime compatibility at the source actually used in this environment: the Blade asset paths and the local built-in server static-file handling.

## Mistakes To Stop Repeating

### Mistake: Treated the broken page as a single bug
- What happened: The first screenshot looked like one rendering problem, but it was actually two independent failures: broken static asset URLs and a missing admin role assignment.
- Root cause: I initially optimized for the most visible symptom instead of decomposing the page into style, permission, and data layers.
- Earlier signal I missed: The screenshot already showed both raw unstyled HTML and the explicit text `You do not have access to this content`.
- Prevention rule: When an admin page looks broken, split diagnosis into asset loading, permission gating, and missing data before changing code.
- Next-time checklist item: For any broken admin page, check rendered HTML for asset URLs and check visible authorization messages separately.

### Mistake: Assumed a package using `asset('public/...')` could run unchanged under `php artisan serve`
- What happened: Admin CSS, JS, and images were emitted as `/public/...` and `/storage/app/public/...`, which does not match this local runtime layout.
- Root cause: The vendor package bakes in deployment assumptions that differ from the current dev server and Docker setup.
- Earlier signal I missed: Static asset requests for `/public/assets/...` were redirecting into Laravel auth instead of returning files directly.
- Prevention rule: After booting any vendor Laravel panel, verify one CSS URL and one image URL in the rendered HTML before doing UI work.
- Next-time checklist item: Open one authenticated page, inspect its emitted asset URLs, and compare them with the real `public/` structure immediately.

### Mistake: Seeded an admin user without the role the codebase expects
- What happened: Login succeeded, but dashboard content was hidden because `module_permission_check()` returns false when `role` is missing.
- Root cause: I created the admin account but did not mirror the vendor package's assumption that the master admin has `role_id = 1`.
- Earlier signal I missed: The bundled SQL backup already showed an `admin_roles` seed with `id = 1`, and the helper explicitly special-cases `role_id == 1`.
- Prevention rule: When recreating auth/bootstrap data from a package, seed the role model and the user-to-role link together.
- Next-time checklist item: Compare seeders against the vendor backup SQL for auth tables before declaring the admin environment ready.

## Permanent Rules
- Break “broken page” reports into rendering, authorization, and data bootstrap tracks before editing.
- Verify emitted asset URLs against the active server model, not just against the repository tree.
- Seed package-default roles and role bindings whenever recreating an admin user from scratch.

## Next-Project Checklist
- [ ] Check one CSS URL, one JS URL, and one image URL from rendered HTML on the live page.
- [ ] Check whether the page body contains explicit auth or permission copy before blaming the frontend.
- [ ] Seed or verify default admin roles together with the default admin account.
- [ ] Validate one uploaded file URL end-to-end after fixing asset paths.

## Open Risks Or Follow-Ups
- Some non-standard view files such as `*.blade` without `.php` extension still contain old asset paths, though they are not part of the current returns flow.
- The app still runs in dev mode; if the next goal is a clean stakeholder demo, consider switching to a non-debug environment profile.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/layouts/admin/app.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/auth/login.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/AdminTableSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/server.php`
- Runtime validation via rendered HTML, static asset `200` checks, and dashboard permission verification

## 2026-04-05 - Inspection rule enforcement and upload pipeline repair

## Snapshot
- Date: 2026-04-05
- Scope: bugfix + feature hardening for returns inspection validation, evidence upload, and local WebP runtime support
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Validated both the happy path and the reject path with real HTTP submissions instead of trusting the Blade/UI layer. That exposed runtime issues the code review alone would not have caught.
- Fixed the problem at both layers: code-level fallback in the shared upload helper and environment-level WebP support in the Docker image.

## Mistakes To Stop Repeating

### Mistake: Added an upload-based workflow before verifying the shared image pipeline
- What happened: The new inspection flow saved form data correctly, but legal submissions crashed with `Call to undefined function App\CPU\imagewebp()` during evidence upload.
- Root cause: I implemented and tested the returns flow before confirming that the package-wide upload helper and the local PHP GD build actually supported the default output format.
- Earlier signal I missed: `APPLICATION_IMAGE_FORMAT` was globally set to `webp`, and the running container reported `WebP Support => false` in `gd_info()`.
- Prevention rule: Before shipping any feature that uploads images in a vendor Laravel codebase, verify the shared helper path and the runtime image-library capabilities together.
- Next-time checklist item: Run one real upload through the exact shared helper before declaring any image-based form usable.

### Mistake: Fixed the runtime by fallback first, but did not immediately restore the environment capability
- What happened: The helper fallback stopped the 500 quickly, but until the container was rebuilt the app was silently saving `.jpg` instead of the intended `.webp`.
- Root cause: I prioritized user-visible recovery first, which was correct, but the environment repair still mattered because the codebase's global default format is part of expected behavior.
- Earlier signal I missed: The helper patch solved the symptom even while `imagewebp()` remained unavailable, which could have hidden the incomplete fix if I had stopped there.
- Prevention rule: When adding a compatibility fallback for a broken runtime capability, always follow through and restore the intended runtime if the project depends on it globally.
- Next-time checklist item: After any fallback patch, re-check the original capability and confirm the intended file format or behavior is back.

### Mistake: Left form-request failure routing to generic `Redirect::back()`
- What happened: Validation failures depended on browser/session history and could redirect to the wrong page when the request did not carry a reliable referrer.
- Root cause: The base request handler is generic, but this inspection form has a specific recovery target and optional `case_id` context.
- Earlier signal I missed: `ValidationHandler` already encoded `Redirect::back()` as the global default, which is fragile for multi-step or edit/create hybrid forms.
- Prevention rule: Any FormRequest backing a dedicated workflow page should define its own failure redirect target when the user must land back on one specific screen.
- Next-time checklist item: For every new FormRequest, decide whether generic back navigation is safe or whether `getRedirectUrl()` must be overridden.

## Permanent Rules
- Verify shared helper behavior and runtime binary support before trusting any new upload flow.
- A compatibility fallback is not a full fix if the global default behavior still depends on a missing runtime capability.
- Dedicated workflow forms should own their validation-failure redirect target instead of inheriting generic back-navigation.

## Next-Project Checklist
- [ ] Check global constants and shared helpers before building on top of an existing vendor subsystem.
- [ ] Run `gd_info()` or equivalent capability checks when the app transforms images, PDFs, or video files.
- [ ] Validate one successful submit and one rejected submit with real HTTP requests before closing a form task.
- [ ] Verify stored file extensions and rendered file URLs after environment-level media changes.
- [ ] Override `getRedirectUrl()` for workflow-specific FormRequests when validation must return to one known page.

## Open Risks Or Follow-Ups
- The upload helper now degrades safely when WebP is unavailable, which is useful for portability, but that path should still be documented so future environment drift is obvious.
- Other workflow-specific requests in this codebase may still rely on generic `Redirect::back()` and could show the same navigation fragility.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/CPU/Helpers.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/Dockerfile`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/StoreReturnInspectionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/InspectionController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/inspect.blade.php`
- Runtime validation via `curl` form submissions, `docker compose exec app php -r 'gd_info()'`, and confirmed case creation for `RMA-4004` and `RMA-4007`

## 2026-04-05 - Refund queue operator flow hardening

## Snapshot
- Date: 2026-04-05
- Scope: feature hardening for the refund queue so operators can take direct action from the board
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Reused the existing case-detail refund gate logic instead of inventing a second decision path. That kept queue updates, detail updates, and event logging on one shared controller path.
- Validated both single-case and batch-case actions with real HTTP posts, which caught workflow-level problems that static Blade review would miss.

## Mistakes To Stop Repeating

### Mistake: Shipped the queue as a dashboard before proving it could act as an operator tool
- What happened: The first queue page only showed grouped cards, so every real decision still required opening the case detail page.
- Root cause: I optimized for visible demo progress before locking the operator action loop.
- Earlier signal I missed: The queue already had status columns and decision concepts, which meant the missing piece was actionability, not more presentation.
- Prevention rule: Any admin board framed as a queue must support at least one direct action path before it is considered complete.
- Next-time checklist item: For every queue/list page, verify “Can an operator resolve one item without leaving this screen?”

### Mistake: UI-level disabling alone was not enough protection for release actions
- What happened: The queue now disables `ready_to_release` and `released` in cards with missing evidence, but that client-side block would have been bypassable without request-level validation.
- Root cause: Queue workflows are high-risk state transitions; browser controls are only guidance, not enforcement.
- Earlier signal I missed: The same release rule mattered in both detail view and batch actions, which is a strong sign it belongs in request validation.
- Prevention rule: Any state transition with financial or operational impact must be revalidated server-side, even if the UI already blocks it.
- Next-time checklist item: For every workflow action button, list the non-negotiable server-side rules before finishing the page.

### Mistake: Generic validation redirects are too weak for multi-surface workflows
- What happened: Queue and case-detail updates share request handling, but they need different recovery destinations when validation fails.
- Root cause: Shared form requests were convenient, but the workflow context still matters for user recovery.
- Earlier signal I missed: The refund decision action now exists on both case detail and queue, so redirect behavior is part of the feature contract.
- Prevention rule: When one request type is used from multiple screens, encode explicit redirect intent instead of relying on referrer history.
- Next-time checklist item: For every shared FormRequest, decide whether redirect destination should be driven by request context.

## Permanent Rules
- A queue page is not done until operators can clear work from the queue itself.
- Financially sensitive state changes need server-side rule checks, not only disabled controls in Blade.
- Shared workflow requests must carry explicit redirect context when multiple entry screens exist.

## Next-Project Checklist
- [ ] Confirm each queue or board page has at least one direct single-item action.
- [ ] Add batch action support only after single-item action and validation are already working.
- [ ] Put release/blocking rules in FormRequest validation before styling the UI.
- [ ] Test one valid and one invalid state transition for both single and batch flows.
- [ ] Verify timeline/event logging still works after introducing a new operator entry point.

## Open Risks Or Follow-Ups
- The queue board still paginates a shared result set, so very large queues may split columns across pages. That is acceptable for P0 demo use, but not ideal for a production ops board.
- Released cases still leave the queue, but there is no dedicated “recently released” board yet.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnCaseController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/UpdateRefundDecisionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/BatchUpdateRefundDecisionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/queue/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/admin.php`
- Runtime validation via queue page loads, single update for `RMA-4004`, blocked release attempt for `RMA-1002`, and batch update for `RMA-4003` + `RMA-4007`

## 2026-04-05 - SLA board drill-down and filter integrity

## Snapshot
- Date: 2026-04-05
- Scope: feature hardening for the SLA board so metrics drill into live queue and case filters instead of stopping at static cards
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Reused the existing queue and case list as drill-down targets instead of introducing another reporting page. That kept the implementation shallow and the operator flow coherent.
- Verified dashboard metrics against live query results instead of trusting the visual counts. That caught a count-quality issue before it became demo debt.

## Mistakes To Stop Repeating

### Mistake: Let dashboard metrics exist before they had an action path
- What happened: The SLA board showed meaningful numbers, but operators could not click through to the cases behind those numbers.
- Root cause: I treated the board as presentation first, operations second.
- Earlier signal I missed: The dashboard language already implied actionability with phrases like “stuck” and “missing evidence,” which are not passive reporting concepts.
- Prevention rule: Any ops metric on an admin dashboard must drill into a filtered work queue before it is called complete.
- Next-time checklist item: For every metric card, answer “Where does this click go, and what exact filter state does it open?”

### Mistake: Used truncated collections as if they were true KPI counts
- What happened: The dashboard originally showed `brandsWithHighestBacklog->count()` and `missingEvidenceCases->count()`, which only reflect the limited preview lists, not the real totals.
- Root cause: I reused presentation collections for KPI numbers instead of computing totals separately.
- Earlier signal I missed: Both preview queries had `limit(5)` / `limit(10)`, which is a direct warning that their collection length is not a metric.
- Prevention rule: Never drive KPI cards from preview collections; compute totals and previews as separate queries.
- Next-time checklist item: When a controller returns both a “top N” list and a metric card, verify the card is backed by an independent total query.

### Mistake: Added cross-page filters without first deciding how those filters survive follow-up actions
- What happened: Once the dashboard linked into filtered queue states, queue actions needed to preserve `brand`, `SLA`, and `evidence` context or the operator would lose their place.
- Root cause: Drill-down state is part of workflow state, not just URL decoration.
- Earlier signal I missed: Queue updates already needed custom redirect context in the previous step, so adding more filters automatically raised the same requirement.
- Prevention rule: Whenever a board supports filtered drill-down plus in-place actions, preserve all active filter state through the post-action redirect.
- Next-time checklist item: After adding a new filter to a workflow page, list every form on that page that must carry the filter forward.

## Permanent Rules
- Ops metrics are not finished until they open the exact work queue behind the number.
- KPI totals and preview lists must come from separate queries.
- Filter state is workflow state and must survive postback actions.

## Next-Project Checklist
- [ ] Add a clickable destination for every operational dashboard metric.
- [ ] Separate total-count queries from preview-list queries.
- [ ] Verify one dashboard click lands on the intended filtered queue state.
- [ ] Verify filtered queue actions redirect back to the same filtered state.
- [ ] Test at least one filter on both board view and table view when both surfaces exist.

## Open Risks Or Follow-Ups
- The SLA board now drills into queue and cases, but it still depends on current database time. If demo scenarios need stable screenshots, seed data timestamps may need a dedicated fixture strategy.
- Queue pagination is still page-based rather than infinite or virtualized; that is fine for P0 demo use, but not yet ideal for a heavy ops team.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnCaseController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/UpdateRefundDecisionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/BatchUpdateRefundDecisionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/dashboard/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/queue/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/index.blade.php`
- Runtime validation via dashboard page loads, `queue?filter_status=hold&min_sla_hours=48`, `cases?min_sla_hours=24&evidence_missing=1`, and query cross-checks against live return-case ages

## 2026-04-05 - Demo dataset reset command

## Snapshot
- Date: 2026-04-05
- Scope: developer tooling for restoring the local returns demo dataset to a clean canonical state
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Added a dedicated artisan command instead of a UI button, which keeps the reset explicit and hard to trigger by accident.
- Verified the reset by running it against the live local database and checking that the dataset returned to the canonical five seeded cases.

## Mistakes To Stop Repeating

### Mistake: Treated an idempotent seeder as if it were a full environment reset
- What happened: `ReturnsDemoSeeder` could restore known rows, but it did not clear out all runtime-created cases, events, and evidence from prior QA passes.
- Root cause: Seeders and reset tools solve different problems; I initially used the former like it was the latter.
- Earlier signal I missed: The environment already contained QA-generated `RMA-400x` cases, which could not disappear unless the returns tables were explicitly wiped first.
- Prevention rule: If a demo environment will be mutated during QA or sales rehearsal, create a dedicated reset command instead of relying on plain seeding.
- Next-time checklist item: For every mutable demo workspace, decide up front whether it needs a true reset command or just seeders.

### Mistake: Did not isolate the reset scope early enough
- What happened: The reset requirement emerged only after queue and SLA work had already changed demo case states multiple times.
- Root cause: I prioritized feature iteration over demo-environment hygiene.
- Earlier signal I missed: As soon as the workflow started creating and updating real cases during validation, the environment stopped being stable demo data.
- Prevention rule: The moment QA begins mutating demo data, add or schedule a reset path before continuing feature development.
- Next-time checklist item: After the first end-to-end QA write test in a demo workspace, ask “How do we get back to a known baseline?”

### Mistake: Browser-flow validation habits do not translate cleanly to `curl -L` login testing
- What happened: A follow-up verification step falsely looked broken because `curl -L` replayed the login redirect as `POST /admin`, and later one probe hit a CSRF mismatch while reusing a stale flow.
- Root cause: Command-line redirect handling is not the same as browser redirect handling, especially around auth and CSRF-protected forms.
- Earlier signal I missed: Previous login probes had already shown that non-browser validation needs tighter control over redirect behavior.
- Prevention rule: For CLI verification of Laravel auth flows, avoid `curl -L` on login POSTs; inspect the 302 and then do a fresh GET with the authenticated cookie jar.
- Next-time checklist item: When validating a login flow with `curl`, separate “POST credentials” from “GET destination page.”

## Permanent Rules
- Seeders are not reset tools; build a dedicated reset command when demo data is expected to drift.
- Add a reset path as soon as QA starts mutating a demo workspace.
- Treat CLI auth verification as a different protocol path from browser auth verification.

## Next-Project Checklist
- [ ] Decide whether the demo workspace needs a one-command reset before deep QA starts.
- [ ] Keep destructive reset capability out of the UI unless there is a strong operator reason.
- [ ] Verify reset commands by checking both tool output and post-reset database shape.
- [ ] Separate auth POST and post-login GET when validating Laravel flows with `curl`.

## Open Risks Or Follow-Ups
- The reset command wipes all returns cases, evidence, events, and refund decisions in this local workspace. That is correct for demo hygiene, but it should not be used if you start storing pilot data in the same database.
- If pilot data enters this environment later, we should split demo data and pilot data into separate databases or add a safer, more selective reset path.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Console/Commands/ResetReturnsDemo.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/ReturnsDemoSeeder.php`
- Runtime validation via `php artisan returns:reset-demo --force` and post-reset database checks showing canonical cases `RMA-1001` through `RMA-1005`

## 2026-04-05 - Returns test harness hardening and authenticated QA sweep

## Snapshot
- Date: 2026-04-05
- Scope: hardening the returns P0 through automated tests, config cleanup, and an authenticated end-to-end QA pass against the live local app
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Writing focused feature tests for rules, inspection, queue actions, and demo reset exposed real environment flaws quickly. The tests found schema drift and config idempotence problems before those issues turned into demo-time failures.
- Using OCR-assisted captcha solving against the live local login let me verify real authenticated pages without weakening the application or adding a one-off bypass just for QA.

## Mistakes To Stop Repeating

### Mistake: Built test fixtures from model assumptions instead of real table schema
- What happened: The first test run failed because the fixture created `admins.name`, but the real table uses `f_name` and `l_name`.
- Root cause: I trusted the current model surface and did not cross-check the underlying migrations before building fixture data.
- Earlier signal I missed: The project has legacy schema drift already, and the `Admin` model fillable fields did not prove the database had matching columns.
- Prevention rule: When adding the first automated test around a legacy model, read the actual migration or live schema before writing fixtures.
- Next-time checklist item: Before writing any test fixture for an inherited table, verify the minimum required columns from migrations or `sqlite .schema`.

### Mistake: Left global config constants non-idempotent in a file that Laravel reloads
- What happened: Returns tests passed assertions but emitted PHP warnings because `config/constant.php` redefined `TELEPHONE_CODES`, `TIME_ZONE`, and `MODULE_PERMISSION` on repeated application boots.
- Root cause: I treated a Laravel config file like a one-time bootstrap include and used bare `const` definitions inside it.
- Earlier signal I missed: The file lived under `config/`, which Laravel loads more than once across tests and commands.
- Prevention rule: Never use unguarded global `const` declarations inside Laravel config files; use returned config arrays or guarded `define()` calls.
- Next-time checklist item: If a config file exports globals instead of returning an array, make it idempotent before adding test coverage.

### Mistake: Wrote QA assertions against intended copy before capturing live page copy
- What happened: The first authenticated QA script failed on the rules page because I asserted planned wording like “Brand rule profiles” instead of the actual shipped heading “Returns Rules Console”.
- Root cause: I validated against my implementation intent rather than observed runtime output.
- Earlier signal I missed: The Blade templates had already been iterated multiple times, so headings were not guaranteed to match my memory.
- Prevention rule: For runtime QA, capture the real title and heading first, then freeze assertions against observed output.
- Next-time checklist item: On the first authenticated smoke pass, print page title and top headings before turning those checks into hard assertions.

## Permanent Rules
- Legacy-table test fixtures must be grounded in migrations, not model assumptions.
- Laravel config files must be safe to load repeatedly.
- Smoke-test assertions should be based on observed runtime output, not remembered copy.

## Next-Project Checklist
- [ ] Before writing fixtures, inspect the real schema for every legacy auth or admin table involved.
- [ ] Run the new feature test suite once before doing browser QA, so environment issues fail fast.
- [ ] Eliminate warnings and deprecations, not just failing assertions, before calling the task done.
- [ ] For authenticated smoke tests, record real page titles and headings before locking assertions.
- [ ] If the app uses captcha in local dev, decide whether QA should solve it, bypass it safely, or add a dedicated dev-only strategy.

## Open Risks Or Follow-Ups
- Local authenticated QA currently relies on OCR against the simple default captcha. That is acceptable for dev validation, but a stable dev-only login bypass or a disabled captcha setting would make repeated automated QA less flaky.
- The `Admin` model still does not reflect the real `admins` schema cleanly. Tests now work because fixtures use the real columns, but the model drift remains technical debt.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Concerns/BuildsReturnsFixtures.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsRuleProfileValidationTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsInspectionFlowTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/RefundQueueFlowTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsDemoResetCommandTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/config/constant.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/phpunit.xml`
- Runtime validation via `docker compose exec app php artisan test tests/Feature/Returns` and authenticated QA across `/admin`, `/admin/returns/rules`, `/admin/returns/inspect`, `/admin/returns/cases`, `/admin/returns/queue`, `/admin/returns/dashboard`, and `/admin/returns/cases/1/export`

## 2026-04-06 - Returns-only workspace cutover

## Snapshot
- Date: 2026-04-06
- Scope: converting the local admin from a POS-first shell into a returns-only workspace with role-based landing, role-scoped navigation, and inspector-safe case visibility
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Constraining the cutover to navigation, route middleware, landing behavior, and case visibility let us materially change product perception without rewriting the whole monolith.
- Combining automated feature tests with authenticated runtime checks on all three personas caught both backend permission bugs and UI-level residual POS leaks.

## Mistakes To Stop Repeating

### Mistake: Treated the sidebar as the whole product shell
- What happened: I cut the left navigation to returns-only, but the top header still exposed `New Sale` and old order shortcuts, so the app still visually leaked its POS origin.
- Root cause: I optimized for the most obvious navigation surface and forgot that header actions are also primary product affordances.
- Earlier signal I missed: The layout has both `_sidebar.blade.php` and `_header.blade.php`, which means any scope-cut that only touches one of them is incomplete by default.
- Prevention rule: When changing product positioning through UI scope, audit every persistent navigation surface together: sidebar, header, footer, quick actions, and landing redirects.
- Next-time checklist item: Before calling a verticalization pass done, grep the rendered shell for legacy product nouns like `sale`, `coupon`, `stock`, `supplier`, and `order`.

### Mistake: Created an inspector test role without checking the monolith's sentinel role semantics
- What happened: The first inspector test accidentally created role id `1`, which the legacy permission helper treats as super admin, so the inspector incorrectly landed on Ops Board and inherited broad access.
- Root cause: I assumed role ids were generic records, but this codebase encodes a hard bypass on `role_id == 1`.
- Earlier signal I missed: `Helpers::module_permission_check()` already contained a role-id shortcut, so any new role fixture needed to respect that sentinel value.
- Prevention rule: In legacy permission systems, inspect helper shortcuts and reserved ids before creating any new seeded or test role.
- Next-time checklist item: When adding a new persona, document reserved ids and role bypass rules before writing fixtures or seed data.

### Mistake: Forgot that the returns slice still depends on shared business settings from the old app shell
- What happened: Inspector case-page tests crashed because shared layout code still expected `pagination_limit`, `shop_name`, and `footer_text` business settings, even though the returns feature itself did not use them directly.
- Root cause: I treated returns pages like an isolated feature, but they still render inside the legacy admin layout and shared helper stack.
- Earlier signal I missed: Prior bugs had already shown that this monolith hides critical assumptions in layout partials and global helpers, not only in feature controllers.
- Prevention rule: When extracting a vertical workflow from a monolith, enumerate layout-level config dependencies and seed them explicitly in tests.
- Next-time checklist item: For any new vertical slice test, render at least one full Blade page early and note which shared settings the shell requires.

### Mistake: Reached for OCR again when exact session state was available on the same machine
- What happened: OCR worked for admin and ops but failed repeatedly for inspector login during runtime QA, even though the real captcha code was already stored in the Laravel session file and accessible locally.
- Root cause: I defaulted to a probabilistic external technique instead of using the strongest available internal signal.
- Earlier signal I missed: We already had filesystem and container access, and the app uses file sessions, which meant the captcha value could be read deterministically after decrypting `laravel_session`.
- Prevention rule: If the app and test runner share the same machine, prefer exact internal state inspection over OCR or visual guessing for auth-related QA.
- Next-time checklist item: Before automating any captcha-protected local workflow, ask whether the answer already exists in session storage, logs, or a local database row.

## Permanent Rules
- A product-scope cut is incomplete until sidebar, header, footer, and login landing all tell the same story.
- Never create roles blindly in a legacy monolith; inspect reserved ids and permission bypasses first.
- Vertical-slice tests must seed shared layout settings, not just feature-local data.
- On local environments, deterministic internal state beats OCR and other heuristic automation.

## Next-Project Checklist
- [ ] Grep all persistent layout partials for legacy nouns before declaring a rescope complete.
- [ ] Inspect permission helpers and reserved role ids before adding new personas or seed roles.
- [ ] Seed required business settings for any feature test that renders the full admin shell.
- [ ] For local auth QA, check whether session, cookie, or storage inspection can replace OCR.
- [ ] Validate all intended personas, not just super admin, before calling a vertical workflow shippable.

## Open Risks Or Follow-Ups
- The app is now visually and behaviorally returns-first, but legacy POS routes still exist behind direct URLs. If we keep narrowing the product, the next step is route-level deprecation or a dedicated returns-only shell.
- `CURRENT_APP_FEATURES_MANUAL.md` now has an updated top-level summary, but the deeper legacy module sections still describe modules that are hidden from the default UI. If the app stays on this narrower strategy, the manual should eventually split into `returns workspace` and `legacy modules`.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/layouts/admin/partials/_sidebar.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/layouts/admin/partials/_header.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/admin.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/CPU/Helpers.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/DashboardController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnCaseController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/InspectionController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/EvidenceExportController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/AdminTableSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/ReturnsDemoSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsWorkspaceRoleTest.php`
- Runtime validation via `docker compose exec app php artisan test tests/Feature/Returns` and persona-specific authenticated checks for `admin@admin.com`, `ops@admin.com`, and `inspector@admin.com`

## 2026-04-06 - Inspector-first inspection flow simplification

## Snapshot
- Date: 2026-04-06
- Scope: reducing the `Inspect Return` experience to an inspector-safe workflow by hiding ops-only fields, defaulting refund status from playbooks, and tightening runtime/test coverage
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Moving the simplification into both Blade and request/controller logic prevented a fake UI-only simplification. The form is simpler, and the server now enforces the same boundary.
- Adding a persona-specific runtime check for inspector login caught a residual `refund_status` leak that ordinary feature assertions would have missed at the first pass.

## Mistakes To Stop Repeating

### Mistake: Simplified the UI before fully removing the underlying field contract
- What happened: The inspector form no longer rendered the refund-status select, but the page HTML still leaked `name="refund_status"` through a JavaScript selector string and the server still accepted the field shape.
- Root cause: I treated field removal as a rendering concern first, instead of a complete input-contract change spanning template, JS, request rules, and controller defaults.
- Earlier signal I missed: The view still referenced `refund_status` in multiple places, including front-end script hooks, which meant the field contract was not actually gone.
- Prevention rule: When hiding an input for a persona, remove or rename every front-end selector tied to that input and give the backend an explicit default path.
- Next-time checklist item: After removing a form field from a persona flow, grep the template for that field name and add a runtime assertion that the raw HTML no longer contains it.

### Mistake: Left ops concepts in the inspector path longer than necessary
- What happened: Inspector users were still exposed to `refund status` and `received_at`, both of which are operations concepts rather than dock-door capture tasks.
- Root cause: I initially optimized for reuse of the admin form rather than for minimizing training cost for the primary operator.
- Earlier signal I missed: Our own product thesis was already clear that inspectors should capture facts, while ops decides refund movement.
- Prevention rule: In a role-split workflow, the capture role should only see facts to collect, not downstream approval controls.
- Next-time checklist item: For each persona-facing screen, label every field as either `fact capture`, `ops control`, or `admin config` and hide anything outside that persona’s job.

## Permanent Rules
- A simplified persona flow is not done until the field is removed from Blade, JavaScript hooks, request validation, and controller persistence.
- Inspectors capture facts; ops users control refund movement.

## Next-Project Checklist
- [ ] Grep for removed field names across Blade and inline scripts, not just rendered markup.
- [ ] Add one persona-specific runtime assertion whenever a field becomes role-specific.
- [ ] Give every hidden ops field a server-side default or override path before shipping.

## Open Risks Or Follow-Ups
- The inspector flow is now narrower, but condition and disposition still require some warehouse judgment. If we keep chasing “zero training,” the next candidate is playbook-driven suggested disposition presets.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/inspect.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/StoreReturnInspectionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/InspectionController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsInspectionFlowTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsWorkspaceRoleTest.php`
- Runtime validation via `docker compose exec app php artisan test tests/Feature/Returns` and authenticated inspector-page checks confirming `refund_status` and `received_at` are absent from `/admin/returns/inspect`

## 2026-04-06 - Playbook-driven disposition defaults

## Snapshot
- Date: 2026-04-06
- Scope: reducing inspector judgment by adding `condition -> recommended disposition` mappings to playbooks and wiring them through inspection defaults, validation, demo seed data, and tests
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Adding the recommendation as a narrow extension of the existing playbook model let us improve the operator flow without creating a new decision engine or branching the inspection workflow.
- Making the server able to resolve a missing `disposition_code` from playbook data turned the recommendation into a real contract instead of a UI hint, which keeps the flow robust even if the frontend changes later.

## Mistakes To Stop Repeating

### Mistake: The original playbook schema only enforced constraints, not defaults
- What happened: The first version of playbooks could say which conditions and warehouse actions were allowed, but it still forced inspectors to decide the disposition themselves every time.
- Root cause: I optimized for policy enforcement first and did not carry the "zero training" product thesis all the way into the schema design.
- Earlier signal I missed: The previous retrospective already identified `condition and disposition still require some warehouse judgment` as the next friction point.
- Prevention rule: When simplifying an operator workflow, model not only what is forbidden but also what should happen by default for the common path.
- Next-time checklist item: For every new rules table, ask two separate questions: `what values are allowed?` and `what value should the operator get by default?`

### Mistake: A frontend auto-select alone would have created a fake simplification
- What happened: The easy version of this feature was to auto-check a radio button in JavaScript, but that would still leave the backend requiring a manually posted disposition and would break on any non-standard submission path.
- Root cause: The monolith already has a history of UI-only simplifications drifting away from server rules.
- Earlier signal I missed: Earlier work on inspector-safe refund status showed the same pattern: UI removal without backend defaults is fragile.
- Prevention rule: Any workflow simplification that changes operator choice count must be implemented in request resolution and persistence, not only in Blade or JavaScript.
- Next-time checklist item: When a field becomes optional in the operator flow, add a `resolved*()` method or equivalent backend default path before touching the frontend.

### Mistake: Seed data and tests would have silently lied without recommendation coverage
- What happened: After adding the new mapping field, the demo data and fixtures would still look functional even if they never exercised condition-based defaults, which would make the feature feel less real in the product and leave regression gaps.
- Root cause: It is easy to patch controller logic and forget that demo datasets are part of the product contract in a sales-driven app.
- Earlier signal I missed: This project already relies heavily on canonical demo cases and seeded playbooks for both manual QA and demos.
- Prevention rule: When a feature changes the operator decision model, update demo seed data and one end-to-end test that proves the new default is actually used.
- Next-time checklist item: For every new playbook field, update three places in the same pass: factory/fixture, demo seeder, and one runtime-like feature test.

## Permanent Rules
- Playbooks must encode both guardrails and defaults if the goal is low-training execution.
- A recommended action is not shipped until the backend can resolve it without trusting the frontend.
- In demo-first products, seeded data is part of the UX and must evolve with the workflow.

## Next-Project Checklist
- [ ] For every operator-facing choice, ask whether it should be required, recommended, or fully automatic.
- [ ] Implement backend default resolution before adding client-side auto-selection.
- [ ] Extend demo seed data whenever a playbook schema changes.
- [ ] Add at least one feature test that omits the field and proves the backend chose the intended default.
- [ ] Re-run demo reset after schema changes so the local environment matches the new product story.

## Open Risks Or Follow-Ups
- The flow now defaults the warehouse action, but inspectors still choose the condition manually. If we keep pushing toward near-zero training, the next leverage point is condition-specific capture guidance or photo prompts, not more menu work.
- Existing cases created before this migration will have no recommendation history attached beyond the event meta added on new submissions. If audit explainability becomes important, the next step is storing which playbook recommendation was shown at decision time.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/migrations/2026_04_06_000001_add_recommended_dispositions_to_brand_rule_profiles_table.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Models/BrandRuleProfile.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/StoreBrandRuleProfileRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/StoreReturnInspectionRequest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnsRuleController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/InspectionController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/rules/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/inspect.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/ReturnsDemoSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsRuleProfileValidationTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsInspectionFlowTest.php`
- Validation via `docker compose exec app php artisan migrate --force`, `docker compose exec app php artisan test tests/Feature/Returns`, and `docker compose exec app php artisan returns:reset-demo --force --bootstrap`

## 2026-04-06 - Competitive positioning reset after Loop x Two Boxes verification

## Snapshot
- Date: 2026-04-06
- Scope: re-checking the market narrative after verifying Loop + Two Boxes, AfterShip, and ReverseLogix overlap, then rewriting the go-to-market positioning around a narrower, more defensible wedge
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Browsing primary vendor sources before repeating the market story prevented a weak “empty market” narrative from becoming part of the sales material.
- Reframing the product from broad `returns app` language to `exception-control layer` produced a sharper wedge that better matches what the product actually does today.

## Mistakes To Stop Repeating

### Mistake: Letting an appealing uniqueness story survive longer than the evidence supported
- What happened: The working narrative leaned too hard on “no one serves multi-brand 3PL warehouse-side returns,” but direct competitor verification showed that Two Boxes, Loop, AfterShip, and ReverseLogix all overlap parts of that workflow.
- Root cause: I accepted a strategically useful claim before stress-testing it against current primary sources.
- Earlier signal I missed: The category already had obvious adjacent players, which should have triggered a mandatory “prove the overlap boundary” check before using uniqueness language.
- Prevention rule: Never use “no one else does X” in strategy or GTM materials until primary-source competitor checks are complete.
- Next-time checklist item: Before finalizing a market position, verify the top 3-5 adjacent competitors on their own site, help center, or official launch posts and rewrite the claim in the weakest defensible form.

### Mistake: Confusing a segment label with the real wedge
- What happened: “Multi-brand 3PL” sounded narrow, but it is still too broad to serve as the actual wedge because competitors can also claim parts of that segment.
- Root cause: Segment definition was treated as positioning, when the real moat comes from workflow shape and adoption model.
- Earlier signal I missed: The product’s strongest live differentiators were already about implementation style and control layer behavior, not the headline segment alone.
- Prevention rule: The wedge must be defined by a painful workflow and a faster adoption path, not only by customer type.
- Next-time checklist item: For any positioning statement, force a second sentence that answers `why this instead of the heavier incumbent?`

### Mistake: Repeating unsupported implementation claims about competitors
- What happened: Some comparative language drifted toward specifics like long implementation timelines or missing capabilities without enough current evidence.
- Root cause: It is easy to fill gaps with plausible market assumptions when the direction feels right.
- Earlier signal I missed: Exact claims about rollout time, pricing, and scope are high-volatility facts that should be sourced or removed.
- Prevention rule: If a competitor fact changes often or cannot be verified from a primary source, do not turn it into a sharp sales claim.
- Next-time checklist item: Mark every competitor statement as one of `verified`, `inference`, or `remove before external use`.

## Permanent Rules
- Uniqueness claims need primary-source verification before they enter strategy docs or outreach copy.
- Segment is not wedge; workflow pain plus low-friction adoption is wedge.
- If a competitor comparison cannot be sourced, soften it or cut it.

## Next-Project Checklist
- [ ] Verify the top adjacent competitors from official sources before writing any “why now / why us” paragraph.
- [ ] Separate `verified fact` from `sales inference` inside every positioning draft.
- [ ] Write the wedge as `who + painful workflow + adoption advantage`, not just `who`.
- [ ] Remove any competitor timing or pricing claim that cannot be sourced on the same day.
- [ ] Re-check competitive claims before recording Looms or sending cold outreach.

## Open Risks Or Follow-Ups
- The product story is now sharper, but it still needs real call transcripts to validate that `exception-control layer` is language the buyer actually uses.
- The next GTM artifact should probably be a one-page landing page draft using this new positioning so outreach and demo video match exactly.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/returns-gtm-positioning-pack.md`
- Loop x Two Boxes official announcement, `2024-02-05`: https://www.businesswire.com/news/home/20240205834638/en/Loop-Announces-New-Reverse-Logistics-Offerings-to-Transform-Operations-and-Boost-Retail-Margins
- Loop official post, `2024-02-13`: https://www.loopreturns.com/blog/two-boxes-x-loop/
- Loop help article updated `2026-02-24`: https://help.loopreturns.com/en/articles/1920385
- Two Boxes official site: https://www.twoboxes.com/
- AfterShip grading docs: https://support.aftership.com/en/returns/article/returned-item-grading-168nmhr/
- AfterShip returns portal page: https://www.aftership.com/lv/returns/returns-management-portal
- ReverseLogix buyer guide PDF: https://www.reverselogix.com/wp-content/uploads/ReverseLogix-Returns-Mgmt-Software-Buyers-Guide.pdf

## 2026-04-06 - GTM asset packaging for first outreach

## Snapshot
- Date: 2026-04-06
- Scope: turning the new positioning into immediate outbound assets: landing page copy, outreach execution guide, and a tracker template
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Converting the strategy into concrete assets immediately reduced the risk of staying in “good analysis, no selling” mode.
- Splitting the materials into three narrow files kept each artifact usable: one for landing page copy, one for outbound execution, and one for contact tracking.

## Mistakes To Stop Repeating

### Mistake: Strategy documents can feel like progress even when they do not produce outreach-ready assets
- What happened: The positioning work was directionally correct, but until it became landing copy, outreach scripts, and a tracker, it still did not help start conversations.
- Root cause: Business work often rewards clear thinking on paper, but early revenue only moves when materials are directly usable by the operator.
- Earlier signal I missed: The next blockers were already obvious: “what do I send?” and “how do I track outreach?” not “what is the market thesis?”
- Prevention rule: After any GTM strategy pass, immediately convert it into the smallest outbound asset set needed to book calls.
- Next-time checklist item: Do not end a positioning task until there is at least one CTA-ready page, one outreach script, and one tracking format.

### Mistake: There is still a temptation to overbuild the website before talking to buyers
- What happened: The natural next step could have drifted into a full website or branded funnel, even though the immediate need is simply to explain the workflow clearly enough to start conversations.
- Root cause: Building assets feels safer than selling, especially when the product is still early.
- Earlier signal I missed: The sales goal is only five good conversations and one paid review, which does not require a polished multi-page marketing site.
- Prevention rule: Early GTM assets should optimize for booking calls, not for looking complete.
- Next-time checklist item: Before creating any new marketing asset, ask whether it directly increases replies, booked calls, or paid reviews in the next 14 days.

## Permanent Rules
- A strategy is not operational until it can be sent, posted, or tracked the same day.
- Early marketing should optimize for conversations, not completeness.
- One narrow CTA is better than a polished but diluted launch story.

## Next-Project Checklist
- [ ] After writing strategy, create at least one outreach-ready asset in the same work block.
- [ ] Keep one CTA per page or message.
- [ ] Add a tracker template before sending the first outreach message.
- [ ] Refuse to build a full marketing site unless it clearly improves the next 14-day sales goal.

## Open Risks Or Follow-Ups
- These assets are ready, but they are still untested. The next source of truth must be actual reply quality and discovery call outcomes.
- The next highest-value follow-up is probably a one-page overview PDF or a real first batch target list, depending on whether the operator is blocked on collateral or contacts.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/returns-landing-page-copy.md`
- `/Users/mikezhang/Desktop/projects/6POS/returns-outreach-execution-pack.md`
- `/Users/mikezhang/Desktop/projects/6POS/returns-outreach-tracker-template.csv`
- `/Users/mikezhang/Desktop/projects/6POS/returns-gtm-positioning-pack.md`

## 2026-04-06 - AI strategy sequencing for returns product

## Snapshot
- Date: 2026-04-06
- Scope: deciding whether AI should be added to the current returns product, and if so, which AI layer should be built first
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Grounding the AI decision in both current product strengths and current competitor facts prevented an “AI because AI” roadmap.
- Separating `easy to demo` from `safe to sell` clarified why evidence-assistant features are the right first AI layer and auto-grading is not.

## Mistakes To Stop Repeating

### Mistake: The most exciting AI feature is not always the highest-leverage first AI feature
- What happened: AI grading and AI disposition suggestion sound impressive and are easy to imagine in a demo, but they attack the part of the workflow with the highest trust and responsibility burden.
- Root cause: Product excitement can overweight visible intelligence and underweight operational trust.
- Earlier signal I missed: The current product’s strongest value is already in evidence completeness and refund control, not in classification intelligence.
- Prevention rule: The first AI feature should strengthen the product’s current moat, not try to invent a new moat before the old one is sold.
- Next-time checklist item: Before prioritizing an AI feature, write down whether it improves the strongest existing workflow or starts a new competitive battle.

### Mistake: AI suggestions and AI decisions must be treated as different product categories
- What happened: It is easy to blur together “AI suggests a condition” and “AI effectively decides what happens,” but those create very different trust, liability, and audit requirements.
- Root cause: AI roadmap discussions tend to collapse suggestion, recommendation, and automation into one bucket.
- Earlier signal I missed: The returns workflow already has a clear human control boundary around refund movement and exception handling.
- Prevention rule: In operations products, AI assistant features should ship before AI decisioning features, and every AI output must be explicitly labeled as suggestion or decision.
- Next-time checklist item: For each proposed AI capability, classify it as `assistant`, `recommender`, or `automator` before estimating effort or business value.

### Mistake: Public cost estimates are dangerous before real workload measurement
- What happened: It is tempting to quote neat per-case AI costs early, but actual economics depend on image count, image size, prompt design, and traffic patterns.
- Root cause: Early strategy discussions often compress model pricing into simplistic per-case assumptions.
- Earlier signal I missed: Even with public model pricing, the real cost drivers are workload-specific and still unknown in this product.
- Prevention rule: Never turn model pricing into public per-case unit economics before running a representative workload sample.
- Next-time checklist item: Prototype on a fixed batch of real or realistic cases before using any AI cost number in pricing or sales copy.

## Permanent Rules
- First AI should reinforce the strongest existing workflow, not chase the flashiest demo.
- AI assistant comes before AI decider in operational products.
- No public AI cost claim without measured workload data.

## Next-Project Checklist
- [ ] For each proposed AI feature, classify it as assistant, recommender, or automator.
- [ ] Confirm whether the feature strengthens current moat or starts a new feature war.
- [ ] Require a human-confirmation path for any early operational AI output.
- [ ] Add audit fields before shipping any AI-generated suggestion.
- [ ] Measure real token/image workload before discussing AI economics externally.

## Open Risks Or Follow-Ups
- The roadmap now favors AI Evidence Assistant first, but the exact UX still needs a concrete product spec and likely one prototype run on sample cases.
- If a real pilot customer explicitly asks for AI grading early, the right response is probably a narrow suggestion mode with human override, not full automation.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/returns-ai-strategy-report.md`
- Renow returns solution: https://renow.ai/returns-solution/
- Renow recommerce platform: https://renow.ai/renow-recommerce-platform/
- Renow inspection app announcement: https://renow.ai/renow-revolutionizes-time-consuming-warehouse-inspections-with-ai-powered-inspection-app-at-shoptalk-barcelona/
- Two Boxes official site: https://www.twoboxes.com/
- OpenAI pricing: https://openai.com/api/pricing/
- OpenAI GPT-4o model docs: https://platform.openai.com/docs/models/gpt-4o

## 2026-04-06 - Packaging existing workflow as Premium Exception Lane

## Snapshot
- Date: 2026-04-06
- Scope: reframing the current returns product from generic returns tooling into a `Premium Exception Lane` offer for high-risk return cases
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Reframing the product around high-risk exceptions created a cleaner buying story without requiring any new core feature to exist first.
- The best ideas in this pass came from changing the scope of adoption, not expanding the product surface area.

## Mistakes To Stop Repeating

### Mistake: It is easy to assume differentiation must come from new functionality
- What happened: The stronger commercial idea was not a new feature, but a narrower packaging of the existing workflow around high-risk exceptions.
- Root cause: Product thinking often defaults to roadmap expansion instead of reframing what already exists into a sharper buying reason.
- Earlier signal I missed: The current workflow already naturally fits wrong item, empty box, and evidence-heavy disputes better than ordinary low-risk returns.
- Prevention rule: Before adding features, ask whether the better move is to narrow the use case and tighten the buying story around the existing product.
- Next-time checklist item: For any “what should we build next?” question, produce at least one answer that changes packaging or scope instead of functionality.

### Mistake: Internal workflow value and client-facing value need to be separated early
- What happened: The `Premium Exception Lane` idea can mean two different things: an internal exception-control tool or a client-facing premium service the 3PL can sell to brands.
- Root cause: The same workflow can create value on two levels, but those levels create different sales narratives and validation criteria.
- Earlier signal I missed: Discovery calls need to explicitly test whether buyers see this as internal cost control or a differentiated service offering.
- Prevention rule: When a workflow could create both internal and external value, treat those as separate commercial hypotheses and test them explicitly.
- Next-time checklist item: Add at least one discovery-call question that distinguishes `internal ops tool` from `client-facing premium service`.

## Permanent Rules
- A sharper commercial package can matter more than a bigger feature set.
- High-risk exception workflows are often easier to sell than full end-to-end platforms.
- Always separate internal ROI and customer-facing monetization stories in early GTM testing.

## Next-Project Checklist
- [ ] Before expanding features, test whether narrower packaging makes the current product easier to buy.
- [ ] Define whether the offer is internal tool, client-facing service, or both.
- [ ] Build one demo story around the highest-pain exception case, not the most ordinary workflow.
- [ ] Ask buyers whether they would sell the capability externally or only use it internally.

## Open Risks Or Follow-Ups
- This framing is strong, but it still needs real discovery call evidence to prove whether 3PLs see exception handling as a premium service or only as internal risk control.
- The next logical artifact is probably a `Brand Defense Pack` sample because that is the clearest expression of the idea.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/premium-exception-lane-strategy.md`

## 2026-04-06 - Smoothing Premium Exception Lane sales sequencing

## Snapshot
- Date: 2026-04-06
- Scope: revising the Premium Exception Lane strategy after critique to reduce early sales friction without changing the core direction
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- The underlying positioning survived critique, which is a good sign that the direction is strong and only needed execution-level smoothing.
- Turning `exception lane` from an upfront concept into a customer-discovered need makes the same strategy much easier to sell.

## Mistakes To Stop Repeating

### Mistake: A strong internal concept can still be the wrong opening language in sales
- What happened: `Premium Exception Lane` is a useful internal and strategic frame, but presenting it too early in discovery can force the buyer to learn our architecture before they have fully articulated their own pain.
- Root cause: Product naming often arrives earlier than buyer recognition.
- Earlier signal I missed: Many smaller 3PLs do not formally classify returns into exception lanes even when they clearly experience “messy cases.”
- Prevention rule: In early discovery, sell the pain before the category label; let the buyer describe the messy cases first, then introduce the narrower workflow framing.
- Next-time checklist item: For each new positioning idea, write one discovery version that avoids the product term entirely and starts from the lived pain language.

### Mistake: Early setup fees can hurt trust-building more than they help cash collection
- What happened: Charging a setup fee too early risks adding a separate approval hurdle before the product has enough trust assets like testimonials, case studies, and references.
- Root cause: Monetization logic was applied before proof-building logic.
- Earlier signal I missed: At this stage, trust is scarcer than configuration labor.
- Prevention rule: When a product still lacks social proof, use early setup work to buy proof assets first, then monetize setup after the first few successful customers.
- Next-time checklist item: Before charging any onboarding or setup fee, ask whether trust or labor is the scarcer resource right now.

### Mistake: Client-facing monetization questions can be sequenced too early
- What happened: Asking in the first discovery call whether the 3PL could sell the capability as a premium service risks jumping ahead of the buyer’s current problem framing.
- Root cause: There are two value layers here, internal control and external monetization, and they mature at different times in the conversation.
- Earlier signal I missed: Most first calls are still about whether the internal ops pain is real enough to solve.
- Prevention rule: Validate the internal pain first; only after that is clear should the conversation expand to external monetization or sales differentiation.
- Next-time checklist item: Split discovery questions into `internal pain`, `pilot readiness`, and `external monetization` stages instead of mixing them into one first call.

## Permanent Rules
- Let the buyer say “messy cases” before you say “exception lane.”
- Early proof assets can be worth more than early setup revenue.
- Sequence internal pain before external monetization in discovery calls.

## Next-Project Checklist
- [ ] Write pain-first talk tracks that do not require the buyer to adopt our terminology immediately.
- [ ] Use the first few customers to collect proof assets before optimizing onboarding revenue.
- [ ] Separate first-call, second-call, and post-pilot questions by maturity of buyer understanding.
- [ ] Add one validation signal for “helps win or retain brand clients” whenever the product claims brand-facing value.

## Open Risks Or Follow-Ups
- The Premium Exception Lane framing is now smoother, but it still depends on real discovery evidence to prove whether buyers actually resonate with “messy cases” as the entry point.
- The next obvious artifact is still a concrete `Brand Defense Pack` sample because that will test both internal ops value and brand-facing differentiation.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/premium-exception-lane-strategy.md`
- User critique in this thread on lane concept timing, early setup fees, and call sequencing

## 2026-04-06 - Brand Defense Pack hardening

## Snapshot
- Date: 2026-04-06
- Scope: turning the existing returns evidence export into a stronger Brand Defense Pack with a sharper browser preview, a real PDF download path, and export-focused tests
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Reframing the export around `share readiness`, `decision basis`, and `what this pack shows` made the output feel like a client-facing artifact instead of an internal debug page.
- Splitting preview HTML and PDF HTML into separate templates avoided CSS/rendering fragility and made the export path much safer.

## Mistakes To Stop Repeating

### Mistake: A good on-screen export is not automatically a good PDF export
- What happened: The browser preview already had richer styling, but reusing that same template for PDF generation would have been risky because mPDF does not reliably support the full visual layout used for the screen version.
- Root cause: It is tempting to treat “print this page” and “generate a dependable PDF artifact” as the same problem.
- Earlier signal I missed: The app already used mPDF elsewhere with simpler templates, which was a clue that complex layout reuse would be brittle.
- Prevention rule: If an artifact must work both in browser and as downloadable PDF, design a dedicated PDF template unless the renderer fully supports the screen layout.
- Next-time checklist item: For every exported artifact, decide early whether browser preview and PDF should share a template or deliberately diverge.

### Mistake: Seeded demo media paths can make export features look more complete than they really are
- What happened: Demo cases have media rows in the database, but the corresponding image files are not guaranteed to exist on disk, which could break or weaken the export if the template assumes every file is physically present.
- Root cause: Seed data and storage realism drifted apart.
- Earlier signal I missed: The seeded media file names were generated in the database seeder without any paired file creation logic.
- Prevention rule: Export features that reference media must tolerate missing files gracefully, especially in demo and test environments.
- Next-time checklist item: Whenever a seeded record includes file paths, verify whether those files actually exist and add fallback rendering if they do not.

### Mistake: HTML assertion details can waste time when the product value is elsewhere
- What happened: The first failing test got stuck on how Laravel escapes `&` in rendered assertions, even though the export feature itself was working.
- Root cause: The test asserted an exact heading string too literally instead of checking the higher-signal content blocks that define the artifact.
- Earlier signal I missed: The real regression risk was whether the new sections existed at all, not whether one title contained an exact escaped character sequence.
- Prevention rule: Tests for rich exports should assert durable value-bearing fragments, not brittle HTML-encoding details.
- Next-time checklist item: Prefer testing stable section names or business phrases over exact encoded punctuation in response content.

## Permanent Rules
- Preview HTML and export PDF are different products unless proven otherwise.
- Demo file paths are not the same thing as demo files; export flows need graceful fallback behavior.
- For artifact tests, assert the business-critical sections, not fragile markup encoding.

## Next-Project Checklist
- [ ] Decide whether export preview and PDF need separate templates before implementing layout polish.
- [ ] Verify whether seeded file references point to real files before relying on them in exports.
- [ ] Add at least one HTML preview test and one PDF response test for every business-critical export.
- [ ] Test exported artifacts against their real audience: internal review, external sharing, or both.

## Open Risks Or Follow-Ups
- The Brand Defense Pack is stronger now, but it still needs one intentionally polished sample case with real images if it is going to be used in sales or demos.
- If this artifact becomes a core sales tool, the next likely improvement is AI-assisted summary text or a more explicit brand-facing cover page.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/EvidenceExportController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/export.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/export-pdf.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/show.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/BrandDefensePackExportTest.php`
- Validation via `docker compose exec app php artisan test tests/Feature/Returns`

## 2026-04-06 - Incorporating critique into strategy docs without turning suggestions into facts

## Snapshot
- Date: 2026-04-06
- Scope: revising the AI strategy report after strong external critique, deciding which suggestions should be adopted directly and which needed softer framing
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Treating the feedback as inputs to evaluate rather than conclusions to copy kept the document stronger and more defensible.
- The revision improved the report materially by adding missing go-to-market, retention, and star-product sections while preserving the original core judgment on AI sequencing.

## Mistakes To Stop Repeating

### Mistake: Good critique can still contain overconfident claims that should not be copied verbatim
- What happened: Several suggested additions were strategically strong but phrased as hard promises, such as flat-rate unlimited pricing and a 30-minute onboarding promise, even though the current product does not yet support them as factual commitments.
- Root cause: High-quality external feedback can feel so aligned that it is tempting to paste it into the strategy doc without reclassifying fact vs target vs hypothesis.
- Earlier signal I missed: We had already established in prior work that onboarding and pricing claims were still partially hypothetical and needed softer wording.
- Prevention rule: External critique improves strategy, but every suggested statement still needs to be tagged as `fact`, `target`, `offer hypothesis`, or `future promise`.
- Next-time checklist item: Before merging feedback into a strategy doc, rewrite every strong statement into one of four buckets: verified fact, current offer, testable hypothesis, or future target.

### Mistake: Strategy docs were still too internally focused until challenged
- What happened: The original AI report was correct on sequencing but underweight on GTM differentiators, retention mechanics, and the “star moment” that actually creates word of mouth.
- Root cause: I optimized the document around product judgment instead of full product success mechanics.
- Earlier signal I missed: The user’s stated goal was not just “pick the right AI,” but “become the first star product,” which requires acquisition, retention, and story design.
- Prevention rule: When the user’s goal is category breakout, every product strategy doc must include acquisition, retention, and referral mechanics, not only feature sequencing.
- Next-time checklist item: For any strategy memo tied to growth ambition, force three sections: go-to-market leverage, retention mechanism, and first-word-of-mouth moment.

## Permanent Rules
- Never merge external advice into a strategy doc without reclassifying each claim by certainty level.
- Strong product strategy must cover acquisition, retention, and referral moments, not just roadmap order.
- A future promise should never be presented as if the current product has already earned it.

## Next-Project Checklist
- [ ] Re-tag every imported suggestion as fact, current offer, hypothesis, or target.
- [ ] Add GTM, retention, and referral sections to any strategy doc that influences roadmap.
- [ ] Soften pricing and onboarding language unless the current product can actually deliver it today.
- [ ] Preserve the original core thesis unless the critique disproves it, rather than replacing the thesis wholesale.

## Open Risks Or Follow-Ups
- The AI report is stronger now, but the next step should probably be a concrete AI V1 spec so the roadmap can turn into implementation choices.
- The strongest unresolved question is whether the first AI should be built before or after the first paying subscription, which can only be answered by actual buyer conversations.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/returns-ai-strategy-report.md`
- User critique in this thread on phase exits, GTM differentiators, retention mechanics, and star-product moments

## 2026-04-07 - Demo evidence asset generation for Brand Defense Pack

## Snapshot
- Date: 2026-04-07
- Scope: making the canonical returns demo dataset generate real evidence images on disk so Brand Defense Pack demos survive reset commands and sales walkthroughs
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Generating branded PNG evidence assets inside [ReturnsDemoSeeder.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/ReturnsDemoSeeder.php) closed the realism gap between seeded database rows and actual exportable media.
- Hardening the write path with explicit font, encode, and disk-write checks turned a silent demo-risk into a fail-fast path.

## Mistakes To Stop Repeating

### Mistake: I trusted a parallel verification result on a sequential workflow
- What happened: I briefly concluded that `returns:reset-demo --bootstrap` was broken because the follow-up file existence check reported zero assets.
- Root cause: I ran the reset command, verification query, and full test suite in parallel even though the second and third steps depended on the first finishing.
- Earlier signal I missed: The “missing files” result contradicted a direct standalone seeder run that had just produced the files correctly, which should have forced me to question the measurement setup before changing code.
- Prevention rule: Never parallelize validation steps when later checks depend on earlier filesystem or database mutations.
- Next-time checklist item: Before using parallel tool calls, mark each verification step as either `independent` or `depends on previous state`; only parallelize the independent ones.

### Mistake: Demo asset realism was added before command-path verification
- What happened: I improved the seeder to generate real PNG files, but I had not yet verified the exact operator command that sales demos actually rely on: `returns:reset-demo --force --bootstrap`.
- Root cause: I validated the lower-level seeder and feature tests before validating the user-facing command path end to end.
- Earlier signal I missed: The reset command is the actual demo recovery workflow, so it should have been the first acceptance test after changing demo media generation.
- Prevention rule: When a feature exists mainly to support demos or operations, verify the real operator command or UI flow before trusting lower-level checks.
- Next-time checklist item: Write down the top-level acceptance action first, then test helper layers second.

## Permanent Rules
- Do not parallelize checks that read state mutated by another in-flight command.
- For demo tooling, verify the real reset or bootstrap path before trusting isolated unit behavior.
- Seeded file references are only complete when the disk artifact exists after the canonical reset command finishes.

## Next-Project Checklist
- [ ] For any stateful verification, separate independent reads from state-dependent reads before deciding to parallelize.
- [ ] After changing demo seed data, run the exact reset/bootstrap command that operators will use.
- [ ] Add explicit write-path guards when a seeded workflow depends on generated files.
- [ ] Confirm both database rows and on-disk artifacts after demo reset changes.

## Open Risks Or Follow-Ups
- The demo dataset now generates stable evidence images, but the next useful polish step is curating one especially strong “hero” case for Loom and sales screenshots.
- If the Brand Defense Pack becomes a core sales artifact, the export should eventually get a dedicated cover block or AI-generated external summary.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/ReturnsDemoSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Console/Commands/ResetReturnsDemo.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsDemoResetCommandTest.php`
- Validation via `docker compose exec app php artisan returns:reset-demo --force --bootstrap`
- Validation via `docker compose exec app php artisan test tests/Feature/Returns`

## 2026-04-07 - V0 completion audit before deployment

## Snapshot
- Date: 2026-04-07
- Scope: auditing whether the current returns product is actually “done” against the plan before moving to cloud deployment
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Separating `core shipped V0` from `strict original plan V0` made the deployment decision much clearer and avoided a fake binary answer.
- Grounding the report in routes, views, controllers, migrations, and passing tests kept the conclusion defensible instead of relying on memory or prior summaries.

## Mistakes To Stop Repeating

### Mistake: “V0 complete” is ambiguous unless the acceptance frame is frozen first
- What happened: The current product can honestly be described as complete if the scope is the narrowed returns-only pilot, but incomplete if judged against every line item from the original broader P0 plan.
- Root cause: Product scope evolved while the original planning document remained broader and more literal.
- Earlier signal I missed: The app had already deliberately cut Flutter and other nice-to-have items, which meant a single yes/no completion answer was going to be misleading.
- Prevention rule: Before any readiness audit, explicitly define whether the benchmark is `current agreed scope` or `original plan document`.
- Next-time checklist item: Every readiness report must include a section called `Acceptance Frame` before giving a go/no-go verdict.

### Mistake: Deployment readiness is not the same thing as feature completeness
- What happened: The product can be feature-complete enough for demo and pilot use while still not being production-stack ready because deployment config is still local-dev oriented.
- Root cause: It is easy to collapse product readiness and infrastructure readiness into one question.
- Earlier signal I missed: `docker-compose.yml` still used `APP_ENV=local`, `APP_DEBUG=true`, and `php artisan serve`, which is a direct clue that “can ship the feature” and “can deploy safely” are separate gates.
- Prevention rule: Treat feature readiness and deployment readiness as two independent gates with separate checklists.
- Next-time checklist item: Any “can we deploy now?” review must split output into `feature gate` and `infra gate`.

## Permanent Rules
- A completion audit must freeze the acceptance frame before scoring progress.
- Product readiness and deployment readiness must never share the same pass/fail bucket.
- Use code and test evidence, not memory, when deciding whether a build is ready to move stages.

## Next-Project Checklist
- [ ] Declare whether the audit uses current scope or original plan before giving a verdict.
- [ ] Score feature completeness and deployment readiness separately.
- [ ] Cite routes, views, controllers, and tests for every high-confidence readiness claim.
- [ ] Flag non-blocking gaps explicitly so they do not get mistaken for launch blockers.

## Open Risks Or Follow-Ups
- The current build is ready for pilot cloud deployment, but a production deployment stack still needs to be chosen and configured.
- If the product keeps evolving, the original P0 plan should eventually be superseded by a tighter `current pilot scope` document to avoid future audit ambiguity.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/v0-status-and-cloud-readiness-report.md`
- `/Users/mikezhang/Desktop/projects/6POS/6pos-p0-development-plan.md`
- `/Users/mikezhang/Desktop/projects/6POS/CURRENT_APP_FEATURES_MANUAL.md`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/admin.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnCaseController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/InspectionController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnsRuleController.php`
- Validation via `docker compose exec app php artisan test tests/Feature/Returns`

## 2026-04-07 - Render deployment prep for relayoffice.ai

## Snapshot
- Date: 2026-04-07
- Scope: preparing the returns app for the easiest low-cost pilot deployment on Render under `demo.relayoffice.ai`
- Outcome: partial success
- Storage target: `memory/project-lessons.md`

## What Worked
- Using a separate Render production stack instead of mutating local `docker-compose` avoided contaminating the dev environment.
- Verifying the actual production image with `docker build` and then a container-level `/healthz` smoke test exposed two real deployment bugs before they became live failures.

## Mistakes To Stop Repeating

### Mistake: I trusted `composer.json` more than `composer.lock` for the PHP target
- What happened: I initially prepared the Render image on `PHP 8.2`, then the production build failed because the lockfile already contained packages requiring `PHP 8.4`.
- Root cause: I checked the declared platform constraint in `composer.json` but did not verify the real locked dependency graph before finalizing the runtime image.
- Earlier signal I missed: The first Docker build failed with explicit package errors like `symfony/clock v8.0.0 requires php >=8.4`, which means the runtime target should have been validated from the lockfile first.
- Prevention rule: For deployment targets, treat `composer.lock` and an actual production build as the source of truth, not `composer.json` alone.
- Next-time checklist item: Before recommending a PHP runtime version, run a production `composer install` or image build against the lockfile.

### Mistake: I almost stopped at “image builds” without proving the app can boot
- What happened: The production image built successfully, but the first smoke test returned `500` on `/healthz`.
- Root cause: Build success only proves dependency resolution and filesystem assembly; it does not prove runtime boot, environment propagation, or web server wiring.
- Earlier signal I missed: The deployment stack included custom shell bootstrap, nginx templating, and php-fpm, which is exactly the kind of setup where build-time success is not enough.
- Prevention rule: A deployment stack is not “ready” until a containerized smoke test hits the app over HTTP and gets the expected health response.
- Next-time checklist item: After every production image build, run the image locally and verify `/healthz` before writing deployment instructions.

### Mistake: I wrote an env bootstrap script that exported values in a child process
- What happened: `bootstrap-app-env.sh` exported `APP_KEY`, but `start.sh` and `predeploy.sh` executed it instead of sourcing it, so `supervisord` and later `artisan` commands did not inherit the derived variable.
- Root cause: I used a shell helper that mutates environment state, but I invoked it as a subprocess instead of in the current shell.
- Earlier signal I missed: `/proc/1/environ` showed `APP_KEY_BASE64` but not `APP_KEY`, and Laravel kept throwing `No application encryption key has been specified`.
- Prevention rule: Any shell helper whose purpose is to export runtime variables must be sourced, not executed, if downstream commands depend on those exports.
- Next-time checklist item: If a bootstrap script sets `export` variables, verify those variables are visible in the long-running process environment after startup.

### Mistake: I treated Git hosting as an assumed detail instead of a deployment prerequisite
- What happened: The Render blueprint and domain plan were ready before I explicitly confirmed that the project directory was not even a git repository yet.
- Root cause: I focused on infra shape first and only later checked the actual deployment source path required by Render.
- Earlier signal I missed: The project had previously been handled as a local working directory, and no git directives or remotes had been used.
- Prevention rule: Before finalizing any managed-platform deployment plan, confirm the code source exists in the format the platform requires.
- Next-time checklist item: Run `git rev-parse --is-inside-work-tree` and `git remote -v` before writing “click here to deploy” instructions.

## Permanent Rules
- Lockfile and real image builds outrank manifest declarations when choosing deployment runtime versions.
- Build success is not deployment readiness; require an HTTP smoke test on the built image.
- Environment bootstrap scripts that export values must be sourced if later commands need those values.
- Managed deploy instructions must verify the repo/remote prerequisite before claiming the path is ready.

## Next-Project Checklist
- [ ] Check `composer.lock` and run a production build before fixing the runtime version.
- [ ] Run the built image locally and verify `/healthz` over HTTP.
- [ ] Inspect the running process environment when boot issues point to missing env vars.
- [ ] Confirm git repo and remote availability before choosing a Git-based deploy path.
- [ ] Separate “deployment assets ready” from “platform prerequisites satisfied.”

## Open Risks Or Follow-Ups
- The app is deployment-ready for Render, but actual cloud launch is still blocked until the code is pushed to a GitHub repo.
- The local smoke test only verified `/healthz`; the first real Render deploy still needs post-deploy QA against database-backed pages like login, queue, and Brand Defense Pack.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`
- `/Users/mikezhang/Desktop/projects/6POS/render-deploy-relayoffice-ai.md`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/Dockerfile.render`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/docker/render/php-fpm-render.conf`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/bootstrap-app-env.sh`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/predeploy.sh`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/start.sh`
- Validation via `docker build -f /Users/mikezhang/Desktop/projects/6POS/web-panel/Dockerfile.render -t relayoffice-render-test /Users/mikezhang/Desktop/projects/6POS/web-panel`
- Validation via `curl http://127.0.0.1:18080/healthz`
- Validation via `git -C /Users/mikezhang/Desktop/projects/6POS rev-parse --is-inside-work-tree`

## 2026-04-07 - GitHub repo bootstrap and first push

## Snapshot
- Date: 2026-04-07
- Scope: turning the local returns project into a clean GitHub repo and pushing it to `relayoffice-returns`
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Adding a root `.gitignore` before the first push kept the repo focused on the deployable app and docs instead of shipping Codecanyon archives and QA artifacts.
- GitHub push protection caught a real issue early and prevented a dirty first public history.

## Mistakes To Stop Repeating

### Mistake: I treated first push as a git task instead of a source hygiene task
- What happened: The initial commit pushed almost everything cleanly, but GitHub blocked it because a third-party minified asset contained a Mapbox access token.
- Root cause: I checked local noise like zip files and caches, but I did not scan committed static assets for embedded secrets before the first public push.
- Earlier signal I missed: The project originated from a commercial template package, and template JS/CSS often ships with demo tokens, example keys, or third-party service defaults.
- Prevention rule: On first public push of any template-derived codebase, run a secret scan against committed assets before pushing.
- Next-time checklist item: Before first push, scan the staged tree for token-like strings in minified JS, CSS, and config files.

### Mistake: I needed to rewrite history because I pushed too soon after the first commit
- What happened: The first local root commit had to be replaced with a clean one after push protection rejected it.
- Root cause: I optimized for “get to GitHub fast” before proving the initial commit was push-safe.
- Earlier signal I missed: A first commit in a legacy or vendor-derived codebase is the exact moment when a pre-push secret review matters most.
- Prevention rule: Treat the first commit as publishable release material, not as a scratch checkpoint.
- Next-time checklist item: Run secret checks before creating or at least before publishing the first root commit.

## Permanent Rules
- First public push of a template-derived project requires a secret scan across static assets.
- GitHub push protection should be treated as a code hygiene signal, not something to bypass with an unblock link.
- A root commit should be treated as release-grade history if it is intended to be pushed immediately.

## Next-Project Checklist
- [ ] Add root `.gitignore` before the first `git add .` in mixed-source projects.
- [ ] Scan staged files for token patterns before the first push.
- [ ] Prefer replacing vendor demo tokens with neutral defaults instead of requesting GitHub unblock.
- [ ] Confirm the remote push works before declaring the repo bootstrap done.

## Open Risks Or Follow-Ups
- The repo is now on GitHub, but Render deployment still needs to be run from the remote repository and then QA’d against the live domain.
- The app still includes some legacy POS assets and storage font files that are not blockers now, but could be trimmed later if you want a more product-focused public repo.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/.gitignore`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/.gitignore`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/public/assets/admin/js/theme.min.js`
- `https://github.com/michaelzgq/relayoffice-returns.git`
- Validation via `git -C /Users/mikezhang/Desktop/projects/6POS push -u origin main`

## 2026-04-07 - Render blueprint MySQL import fix

## Snapshot
- Date: 2026-04-07
- Scope: fixing the Render Blueprint import failure for `relayoffice-returns`
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Using the exact Render MySQL example pattern exposed that the failure was in our blueprint spec, not in the user's form inputs or GitHub permissions.
- Replacing the direct registry image reference with a tiny repo-local MySQL Dockerfile created a deployment path we can validate locally and keep under version control.

## Mistakes To Stop Repeating

### Mistake: I assumed a Render private service could consume `docker.io/mysql:8.0` directly in the way I wrote it
- What happened: The Blueprint page loaded the repo correctly, but Render rejected `services[0].image` with `image "docker.io/mysql:8.0" not found`.
- Root cause: I wrote the first `pserv` block from general schema intuition instead of matching Render's documented MySQL example for private services.
- Earlier signal I missed: The error was not a generic YAML failure; it named the `image` field specifically, which meant the blueprint structure itself was the likely problem.
- Prevention rule: For managed platform resources like databases, prefer vendor example patterns over "probably valid" generic schema guesses.
- Next-time checklist item: Before publishing deploy docs, compare each nontrivial service block against an official platform example, especially databases and private services.

### Mistake: I focused too much on form fields when the real bug was the repository content
- What happened: The user asked how to fill the Blueprint page, but the visible fields were already fine.
- Root cause: Platform UIs make configuration feel like a form problem even when the imported config file is the actual source of truth.
- Earlier signal I missed: The page said "A Blueprint file was found, but there was an issue," which explicitly pointed to the repo file, not the user-entered name/branch/path fields.
- Prevention rule: When a Blueprint UI reports a parsed-file error, debug the imported file first and only then explain the form.
- Next-time checklist item: Separate "UI field guidance" from "imported config validation" in deployment troubleshooting.

## Permanent Rules
- Render Blueprint errors that name a specific field should be treated as repo-config bugs first, not user form mistakes.
- Database services on managed platforms should start from official examples, then be adapted minimally.
- Repo-local infra Dockerfiles are often safer than raw registry references when platform parsing is picky.

## Next-Project Checklist
- [ ] Match each Blueprint service type against an official example before declaring the config ready.
- [ ] Validate tiny infra Dockerfiles locally when replacing direct registry image references.
- [ ] If the UI says the file was found but has an issue, inspect the imported file before changing user-entered fields.

## Open Risks Or Follow-Ups
- The updated `render.yaml` still needs to be re-imported in Render and confirmed in the Blueprint UI.
- First live deploy still requires post-deploy QA on login, queue, and Brand Defense Pack against the cloud database.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`
- `/Users/mikezhang/Desktop/projects/6POS/docker/mysql/Dockerfile`
- `/Users/mikezhang/Desktop/projects/6POS/render-deploy-relayoffice-ai.md`
- Validation via `docker build -f /Users/mikezhang/Desktop/projects/6POS/docker/mysql/Dockerfile /Users/mikezhang/Desktop/projects/6POS/docker/mysql`

## 2026-04-07 - Render first-access failure after deploy

## Snapshot
- Date: 2026-04-07
- Scope: debugging why the Render app URL looked inaccessible right after first deploy
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Probing the live service paths separately (`/healthz`, `/`, `/admin/auth/login`) isolated the problem quickly.
- The distinction between "service is up" and "app is usable" prevented a wrong DNS or routing diagnosis.

## Mistakes To Stop Repeating

### Mistake: I initially framed first-access troubleshooting too much around URL reachability
- What happened: The deployed app looked "not accessible," but the service was actually responding and only the login page was failing.
- Root cause: First-access debugging often defaults to DNS/routing thinking, even when the app process is healthy and only application initialization is broken.
- Earlier signal I missed: `/healthz` was already part of the deploy contract and should have been the first check before discussing hostnames.
- Prevention rule: On first deploy, always test a health endpoint and the first database-backed page separately.
- Next-time checklist item: Verify `/healthz`, `/`, and the first auth page independently before concluding a site is unreachable.

### Mistake: The deploy guide listed seed commands, but the failure mode was not spelled out
- What happened: The app migrated successfully, but the login view still 500'd because required seeded settings like `shop_logo` were missing.
- Root cause: The guide told the user what to run, but not what specific symptom would appear if they skipped it.
- Earlier signal I missed: The login Blade template dereferences `BusinessSetting::where(...)->first()->value` without a null-safe operator for `shop_logo`.
- Prevention rule: When a first-run seed is required for app boot paths, document the exact broken symptom that appears without it.
- Next-time checklist item: Call out "login page 500 if seeders have not run" in the deployment runbook.

## Permanent Rules
- A green deploy does not mean the first authenticated page works.
- Health check, redirect root, and first DB-backed screen should all be tested in cloud environments.
- Seed-dependent views should either be documented explicitly or hardened against empty tables.

## Next-Project Checklist
- [ ] After first deploy, check `/healthz`.
- [ ] After first deploy, check `/`.
- [ ] After first deploy, check the login page before handing the URL to the user.
- [ ] If seeders are required, document the exact page that will fail without them.

## Open Risks Or Follow-Ups
- The live Render app still needs `db:seed` and demo reset before the login page will work normally.
- The custom domain `demo.relayoffice.ai` still needs DNS verification after the app is usable.

## Source Artifacts
- `https://relayoffice-returns-app.onrender.com/healthz`
- `https://relayoffice-returns-app.onrender.com/`
- `https://relayoffice-returns-app.onrender.com/admin/auth/login`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/auth/login.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/predeploy.sh`

## 2026-04-08 - Render custom domain verification lag

## Snapshot
- Date: 2026-04-08
- Scope: debugging `demo.relayoffice.ai` after Cloudflare DNS was added
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Checking the public domain externally separated "Render dashboard still shows an error" from "the domain is actually live."
- Verifying CNAME resolution and testing the real HTTPS response prevented unnecessary DNS churn.

## Mistakes To Stop Repeating

### Mistake: I treated the Render certificate/error badge as the source of truth for longer than necessary
- What happened: The dashboard still showed verification/certificate trouble, but the public domain was already resolving and serving the app over HTTPS.
- Root cause: Managed platform status indicators can lag behind actual DNS and certificate readiness.
- Earlier signal I missed: Once the DNS record was correct and external resolution matched the expected target, the next step should have been an external HTTPS probe immediately.
- Prevention rule: For custom-domain launches, treat public DNS resolution plus live HTTPS response as the final truth, not the platform badge alone.
- Next-time checklist item: After adding a DNS record, test `dig` and a real HTTPS request before assuming the platform status is accurate.

## Permanent Rules
- Render/Cloudflare UI status can lag behind working DNS and live HTTPS.
- A correct `CNAME` plus successful HTTPS response is stronger evidence than a stale dashboard badge.
- Do not keep changing DNS once the public resolver shows the right target unless there is fresh contradictory evidence.

## Next-Project Checklist
- [ ] After adding a custom-domain DNS record, verify the public resolver target.
- [ ] Probe the final HTTPS URL before making more DNS changes.
- [ ] Only revisit CAA/AAAA if DNS is correct and HTTPS still fails.

## Open Risks Or Follow-Ups
- Render dashboard status may need a manual refresh before it matches reality.
- If the user later enables Cloudflare proxying, re-check certificate and origin behavior.

## Source Artifacts
- `https://demo.relayoffice.ai`
- `https://demo.relayoffice.ai/admin/auth/login`
- `demo.relayoffice.ai CNAME -> relayoffice-returns-app.onrender.com`

## 2026-04-08 - Competitive map correction after Rabot and Two Boxes review

## Snapshot
- Date: 2026-04-08
- Scope: correcting the product positioning after deeper review of Rabot, Two Boxes, WMS substitutes, and refund-decision ownership
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Re-checking the actual product boundary of each competitor prevented a shallow "they look similar, so they are the same threat" conclusion.
- Separating direct rivals, adjacent rivals, and substitutes produced a more accurate positioning map than lumping everything into one competitor bucket.

## Mistakes To Stop Repeating

### Mistake: I over-indexed on surface overlap instead of product center-of-gravity
- What happened: Rabot was initially framed as the most direct rival because it also touches proof and returns.
- Root cause: I weighted visible overlap in features too heavily and did not distinguish between a product's primary workflow and a secondary add-on workflow.
- Earlier signal I missed: Rabot's pricing and hardware model are station-based and oriented around camera-backed proof, which implies a different operational center than a browser-first returns exception tool.
- Prevention rule: When mapping competitors, first identify each product's primary operational loop before comparing overlapping features.
- Next-time checklist item: For every competitor, answer "What is this product primarily hired to do?" before assigning direct-rival status.

### Mistake: I treated refund gate as more validated than it actually is
- What happened: Product framing leaned toward release control before validating whether 3PLs actually own the refund decision.
- Root cause: The workflow logic in the product made the refund-control story feel natural, but decision authority is an organizational fact, not a product assumption.
- Earlier signal I missed: In many real returns setups, the physical processor and the refund approver are not the same party.
- Prevention rule: Any workflow that assumes decision authority must be validated explicitly with customer interviews before it becomes the core pitch.
- Next-time checklist item: Ask "Who owns the refund decision today?" in the first discovery conversation, not later.

## Permanent Rules
- Direct rival status should be based on primary workflow, not partial feature overlap.
- Zero-hardware is only a moat if the product stays exception-first and browser-first.
- Authority questions outrank feature questions when positioning operational software.

## Next-Project Checklist
- [ ] Split competitors into direct, adjacent, and substitute layers before writing positioning.
- [ ] Validate buyer authority before locking the core product story.
- [ ] Prefer shareable case surfaces over proof-media escalation unless customers explicitly demand richer media.

## Open Risks Or Follow-Ups
- Refund authority is still not validated with live customer calls.
- Product copy and queue semantics should be updated if evidence shows brands, not 3PLs, own final refunds.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/returns-competition-positioning-summary.md`
- `/Users/mikezhang/Desktop/projects/6POS/returns-gtm-positioning-pack.md`

## 2026-04-08 - Authority split and Brand Review Link execution bridge

## Snapshot
- Date: 2026-04-08
- Scope: turning the competition summary into a 30-day execution bridge covering authority split, Brand Review Link, pricing hypotheses, channels, and pilot learning
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Converting strategy into explicit `if / then` paths exposed where the product story changes materially instead of only cosmetically.
- Defining Brand Review Link as read-only in v1 kept the idea high-leverage without turning it into a premature cross-company workflow platform.

## Mistakes To Stop Repeating

### Mistake: I had treated "highest-leverage feature" as if it could also carry full workflow complexity on day one
- What happened: Brand Review Link clearly surfaced as the strongest next feature, but without constraints it could easily expand into approvals, disputes, and external collaboration.
- Root cause: High-value features create pressure to make them complete immediately, even when the first version only needs to prove one job-to-be-done.
- Earlier signal I missed: The moment external users can take actions, the product stops being a share surface and starts becoming a multi-party workflow system.
- Prevention rule: For any new external-facing feature, lock the v1 job-to-be-done first and push all role-changing or workflow-changing actions out of scope unless already validated.
- Next-time checklist item: When a feature crosses company boundaries, define a strict "read-only vs actionable" boundary before discussing UI details.

## Permanent Rules
- A product-positioning document is not enough; it must be followed by an execution bridge with decision branches, learning loops, and channel plans.
- External share surfaces should launch as read-only unless action-taking by the external party is already validated.
- The first pilot should optimize for learning velocity, not for long-duration adoption theater.

## Next-Project Checklist
- [ ] After any strategy memo, write the `if / then` execution split before discussing roadmap.
- [ ] For external-facing features, define the minimal v1 surface and explicit out-of-scope actions.
- [ ] Keep first pilots to `2-3 weeks` unless the learning objective truly requires longer.

## Open Risks Or Follow-Ups
- Refund authority still needs live customer validation.
- Pricing tiers remain hypotheses until at least a few real customer conversations test willingness to pay.
- Brand Review Link still needs a proper product spec if it becomes the next build target.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/returns-authority-brand-review-execution-plan.md`
- `/Users/mikezhang/Desktop/projects/6POS/returns-competition-positioning-summary.md`

## 2026-04-08 - Local desktop + BYO API + lifetime license strategy review

## Snapshot
- Date: 2026-04-08
- Scope: evaluating whether the product should pivot from hosted web software to a local desktop app with customer-owned data, BYO API keys, and one-time pricing
- Outcome: partial success
- Storage target: `memory/project-lessons.md`

## What Worked
- Challenging the packaging model separately from the product problem exposed that deployment model and pricing model can silently change the ICP.
- Comparing against local-first and perpetual-license tools clarified that buy-once models work best for personal tools or admin utilities, not automatically for multi-party operational systems.

## Mistakes To Stop Repeating

### Mistake: It is easy to project founder preferences onto the buyer
- What happened: "Users hate subscriptions" and "people don't want their data on our server" sounded compelling, but those are not yet validated buying triggers for the target ops teams.
- Root cause: Product strategy can drift when the founder's own purchase preferences masquerade as market evidence.
- Earlier signal I missed: The strongest current product ideas depend on shared workflows, external review links, and multi-role use, all of which are weakened by a purely local single-machine setup.
- Prevention rule: Separate founder discomfort from validated buyer objections before changing pricing or architecture.
- Next-time checklist item: When proposing a packaging pivot, ask "Is this solving a buyer objection we have heard repeatedly, or a founder preference?"

### Mistake: I initially treated "desktop" and "data ownership" as the same architectural answer
- What happened: Local desktop sounded like the cleanest path to customer-owned data and no hosting burden.
- Root cause: Desktop packaging feels like the default escape hatch from SaaS, but for multi-user operational products the real alternative is often self-hosted web.
- Earlier signal I missed: The current product is already browser-first, inspector-friendly, and built around multiple roles plus external sharing.
- Prevention rule: For B2B ops software, test self-hosted web before considering a full desktop rewrite.
- Next-time checklist item: If the real customer need is data custody, evaluate self-hosted deployment before local desktop.

## Permanent Rules
- Packaging changes can silently change the ICP even when the workflow problem stays the same.
- BYO API is a feature choice, not a business model by itself.
- Perpetual pricing works best when paired with paid updates, support windows, or optional recurring collaboration services.

## Next-Project Checklist
- [ ] Validate whether subscription aversion is a repeated buyer objection before redesigning pricing.
- [ ] Validate whether data-hosting concern means "local files," "self-hosted," or simply "exportability and backups."
- [ ] For any local-first idea, test whether the product still supports the key multi-user and external-sharing workflow.

## Open Risks Or Follow-Ups
- No live customer evidence yet shows that desktop/local-only would convert faster than browser-based deployment.
- If the product later prioritizes multi-brand operators over 3PLs, the packaging recommendation may change.

## Source Artifacts
- `https://tableplus.com/pricing`
- `https://www.typingmind.com/buy`
- `https://obsidian.md/pricing`

## 2026-04-08 - Self-hosted web delivery pack smoke test

## Snapshot
- Date: 2026-04-08
- Scope: validating that the new self-hosted web delivery pack can actually boot, initialize, and reach the admin workspace outside the existing Render demo
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Keeping the self-hosted package as a thin layer on top of the existing production Docker image avoided a second deployment architecture.
- Testing the package end-to-end with a temporary `.env.self-hosted` file caught real integration issues instead of only validating static docs.
- Reading the captcha value from the Laravel session was far more reliable than trying to OCR the login image.

## Mistakes To Stop Repeating

### Mistake: I initially treated compose validation as equivalent to deployment validation
- What happened: the compose file parsed successfully with a sample env file, but that still did not prove that migrations, seeding, session storage, and login would work together.
- Root cause: config-level validation is cheaper and tempting, but it only proves syntax and interpolation, not application behavior.
- Earlier signal I missed: the package includes custom bootstrap logic and seeded auth flows, so a real smoke test was always necessary.
- Prevention rule: any customer-facing deployment pack must be proven by a full boot + init + HTTP smoke test before being called ready.
- Next-time checklist item: after writing a deployment guide, always run the exact documented commands in a clean environment.

### Mistake: I underestimated how easy it is for shell quoting noise to hide the real problem
- What happened: complex one-liner shell commands made it harder to tell whether login failures were caused by the app, CSRF/session behavior, or just command quoting.
- Root cause: long inline command chains optimize for speed but reduce debuggability.
- Earlier signal I missed: the first curl-based login attempt failed with `419`, which was a strong hint that this should move to either a real script or a browser-driven test immediately.
- Prevention rule: when a validation step needs cookies, CSRF, and multiple round-trips, move to a script or browser automation early.
- Next-time checklist item: if an HTTP test needs more than one request and stateful cookies, do not keep extending a one-liner.

## Permanent Rules
- Deployment assets are not validated until the exact install instructions have been run end-to-end.
- Compose `env_file` wiring and service behavior must both be tested; passing one does not prove the other.
- For auth smoke tests, prefer real browser automation or server-side session introspection over brittle OCR hacks.

## Next-Project Checklist
- [ ] Validate new deployment packs with boot, init, health check, and one authenticated page load.
- [ ] Use a temporary env file that matches the documented install path.
- [ ] Clean up temporary containers, volumes, and scripts before calling the task complete.

## Open Risks Or Follow-Ups
- The self-hosted pack still does not include licensing, update entitlements, or a customer-friendly installer.
- The default login captcha is expected but should be called out in delivery docs so customers do not mistake it for a deployment issue.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/docker-compose.self-hosted.yml`
- `/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted.example`
- `/Users/mikezhang/Desktop/projects/6POS/self-hosted-web-delivery-pack.md`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/self-hosted/init.sh`

## 2026-04-08 - Decision-control layer and Brand Review Link implementation

## Snapshot
- Date: 2026-04-08
- Scope: converting the product from refund-gate language to decision-review language and shipping signed external Brand Review Links
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Keeping the stored status codes unchanged while layering neutral labels on top avoided a risky migration and still changed the user-facing product story.
- Using Laravel signed URLs for external sharing delivered a real Brand Review Link without introducing user accounts, notifications, or a new permissions system.
- Hiding internal notes in the external share view preserved the “read-only review surface” rule from the strategy docs.

## Mistakes To Stop Repeating

### Mistake: I initially considered changing the underlying status machine before validating the value of the surface
- What happened: the strategy docs suggested new status semantics, and the first instinct was to rename or migrate actual stored values.
- Root cause: strategy language can make a deep model change feel cleaner than it really is.
- Earlier signal I missed: tests, seed data, validation rules, and playbooks were already tightly coupled to the existing stored status values.
- Prevention rule: when strategy changes the story before it changes the workflow, ship a UI label layer first and migrate stored state only after the new behavior is validated.
- Next-time checklist item: ask “Do we need new stored values, or just a new user-facing interpretation?”

### Mistake: External sharing features naturally try to grow into collaboration systems
- What happened: as soon as the Brand Review Link existed, the obvious next step would have been Accept/Dispute actions and cross-company feedback loops.
- Root cause: external share links create pressure to add interactive workflow immediately.
- Earlier signal I missed: the highest-value first job is simple reviewability, not external state changes.
- Prevention rule: first external surfaces should prove visibility and trust, not multi-party action handling.
- Next-time checklist item: for any external-facing feature, define what external users can explicitly *not* do in v1.

## Permanent Rules
- Keep storage-compatible status codes when a language-layer change can deliver the product shift safely.
- Signed read-only links are the fastest valid bridge from internal tool to customer-facing proof surface.
- External review views must hide internal notes unless external note sharing is explicitly designed and validated.

## Next-Project Checklist
- [ ] Before changing state machines, try a user-facing label layer first.
- [ ] For share features, separate internal and external-safe fields explicitly.
- [ ] Add tests for signed-link validity, forbidden unsigned access, and hidden internal content.

## Open Risks Or Follow-Ups
- The Brand Review Link is still read-only; there is no external acknowledgement or dispute workflow yet.
- If discovery calls show brands expect a different artifact than a signed web link, the surface may need to adapt.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/EvidenceExportController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/ReturnCaseController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/brand-review.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/BrandReviewLinkTest.php`
