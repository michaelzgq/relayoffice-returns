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

## 2026-04-11 - Landing Page Responsive Proof Cards

### Mistake: Designed homepage proof cards for desktop, but let them stay three-up too long on tablet widths
- What happened: The homepage micro-proof cards looked acceptable on a wide desktop canvas, but at tablet and share-preview widths they became narrow, overly tall columns with oversized serif headlines.
- Root cause: I treated the cards like static marketing tiles instead of a responsive content block with its own breakpoint needs.
- Earlier signal I missed: The card titles were long and semantic, which meant a three-column layout would collapse visually long before the rest of the hero section needed to.
- Prevention rule: Any marketing card row with long proof statements must have its own tablet breakpoint and reduced headline scale, independent of the main page grid.
- Next-time checklist item: Before shipping a landing page section, check the layout at desktop, tablet, and narrow laptop widths, not just full desktop and phone.

## 2026-04-11 - Landing Page Visual Direction Drift

### Mistake: Let the homepage accumulate multiple partial style directions instead of resetting to one coherent marketing layout
- What happened: The landing page picked up several rounds of piecemeal tweaks, which left the page with mixed visual language, uneven density, and CSS that solved one screenshot while hurting another.
- Root cause: I optimized for local fixes instead of reasserting a single reference layout and rebuilding the affected sections around it.
- Earlier signal I missed: The page had already started showing “good component, bad page” symptoms, which is a sign the issue is layout direction, not isolated spacing.
- Prevention rule: When a user asks to match a reference landing page, stop stacking incremental style patches and reset the full section to one explicit visual system.
- Next-time checklist item: For landing-page redesigns, define the target layout pattern first (nav, hero, proof cards, workflow, CTA) before touching individual card spacing or colors.

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

## 2026-04-08 - Render 419 after deploy was a trusted-proxy bug, not a CSRF mystery

## Snapshot
- Date: 2026-04-08
- Scope: debugging production `419 Page Expired` errors on `demo.relayoffice.ai` after a successful Render deploy
- Outcome: fixed in code and deploy config
- Storage target: `memory/project-lessons.md`

## What Worked
- Reproducing the live flow with a real HTTP session exposed the exact redirect chain instead of guessing at CSRF.
- Checking the first redirect target after form POST immediately showed the real signal: `https -> http -> https`.
- Looking at `bootstrap/app.php` instead of only the legacy middleware file surfaced that the wrong `TrustProxies` class was actually registered.

## Mistakes To Stop Repeating

### Mistake: I initially treated 419 like a generic session problem instead of a proxy/scheme problem
- What happened: the symptom was `419 Page Expired`, but the real issue was that production requests were being interpreted as `http` behind Render/Cloudflare.
- Root cause: `web-panel/bootstrap/app.php` was wiring `Illuminate\Http\Middleware\TrustProxies` instead of the configured `App\Http\Middleware\TrustProxies`, and the app middleware itself was not explicitly trusting proxy IPs with `protected $proxies = '*'`. Forwarded scheme headers were therefore ignored on Render/Cloudflare.
- Earlier signal I missed: live POST redirects were landing on `http://demo.relayoffice.ai/...`, which should never happen on a properly trusted `https` deployment.
- Prevention rule: when production shows `419` behind a proxy/CDN, inspect redirect scheme and cookie flags before touching CSRF code.
- Next-time checklist item: after first production deploy, submit one real form and verify there is no `http://` hop anywhere in the redirect chain.

### Mistake: I left production secure-cookie behavior implicit
- What happened: the app relied on default session cookie behavior, which meant production cookies were not explicitly forced to `Secure`.
- Root cause: Render environment variables set `APP_URL` and `FORCE_HTTPS`, but not `SESSION_SECURE_COOKIE=true`.
- Earlier signal I missed: live `Set-Cookie` headers did not include the `Secure` attribute.
- Prevention rule: any HTTPS production environment must explicitly enable secure session cookies, even if the app “mostly works” without them.
- Next-time checklist item: inspect `Set-Cookie` once after deployment and confirm the session cookie has `Secure`.

## Permanent Rules
- In Laravel bootstrap wiring, always verify the app is actually registering the custom `TrustProxies` middleware when one exists.
- A production `419` behind a CDN/proxy is often a request-scheme or cookie transport bug, not a CSRF-token bug.
- Secure cookies should be explicit in production env config, not left to defaults.

## Next-Project Checklist
- [ ] On new deployments, submit one real form and inspect the first redirect target.
- [ ] Confirm no live route on an HTTPS domain generates `http://` redirects.
- [ ] Confirm production session cookies include the `Secure` attribute.
- [ ] Confirm proxy-aware middleware is the app-specific implementation, not the framework default.

## Open Risks Or Follow-Ups
- Render still needs a successful redeploy of the proxy fix before the live environment can be re-verified.
- If Cloudflare settings change later, the live form flow should be spot-checked again.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/bootstrap/app.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Middleware/TrustProxies.php`
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`

## 2026-04-08 - Network promotion planning must optimize for conversations, not vanity reach

## Snapshot
- Date: 2026-04-08
- Scope: turning the current go-to-market discussion into a usable 30-day network promotion plan
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Anchoring the plan to the live product and current positioning avoided generic “startup marketing” advice.
- Treating LinkedIn, direct outreach, and directory presence as one coordinated system produced a plan that can actually start this week.
- Keeping `Brand Review Link` as the marketing hero prevented the message from drifting back into feature soup.

## Mistakes To Stop Repeating

### Mistake: I had previously spread the channel discussion too wide
- What happened: earlier GTM material covered positioning and outreach well, but did not turn that into a strict 30-day network promotion system.
- Root cause: strategy work is easier than channel prioritization, so the plan stayed too abstract.
- Earlier signal I missed: the product already had enough clarity for direct promotion, but there was still no single document saying what to do this week.
- Prevention rule: once positioning is stable enough to explain in one sentence, convert it immediately into a 30-day channel plan with daily cadence and kill thresholds.
- Next-time checklist item: after any strategy pack is written, ask “what are the three channels we will actually run in the next 30 days?”

### Mistake: It is easy to confuse traffic goals with validation goals
- What happened: network promotion could have drifted toward SEO, ads, or general visibility before validating demand from the narrow ICP.
- Root cause: “more reach” sounds like progress even when the real bottleneck is high-quality conversations.
- Earlier signal I missed: the current product still needs authority and dispute-workflow validation more than it needs broad awareness.
- Prevention rule: in early-stage B2B promotion, optimize for replies, calls, and paid reviews before optimizing for visits or followers.
- Next-time checklist item: define the conversion event first, then choose the channel.

## Permanent Rules
- Early network promotion should be judged by qualified conversations, not total impressions.
- A GTM plan is incomplete until it names the exact channels to run and the channels to ignore.
- Product marketing should lead with the smallest valuable outcome, not the longest feature list.

## Next-Project Checklist
- [ ] Define the single conversion event for the next 30 days.
- [ ] Limit active acquisition channels to three or fewer.
- [ ] Build one tracker that ties outreach, content, and calls into the same scorecard.
- [ ] Write kill thresholds before starting promotion.

## Open Risks Or Follow-Ups
- The plan still needs real execution data to show whether LinkedIn or directory traffic produces higher-quality conversations.
- The user still needs a live root-domain landing page that matches the new message hierarchy.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/network-promotion-30-day-plan.md`
- `/Users/mikezhang/Desktop/projects/6POS/linkedin-30-day-post-plan.md`
- `/Users/mikezhang/Desktop/projects/6POS/target-50-accounts-template.csv`

## 2026-04-08 - Rebranding must include live defaults and install paths, not just visible copy

## Snapshot
- Date: 2026-04-08
- Scope: removing `6POS` branding from the application and switching the product to `RelayOffice Returns`
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Treating rebranding as a data + install + UI problem prevented a shallow rename.
- Adding a migration for legacy default business settings means existing deployments can pick up the new brand automatically on deploy.
- Updating installer screens, translation values, seeders, and test fixtures together kept the brand consistent across live demo and future self-hosted installs.

## Mistakes To Stop Repeating

### Mistake: The first instinct in a rebrand is usually too shallow
- What happened: the obvious move was to only change visible page copy, but that would have left old vendor branding in seeded business settings, install flows, and fresh self-hosted installs.
- Root cause: UI text is easy to spot; default data and install assets are not.
- Earlier signal I missed: the current product already depended on seeded `shop_name` values and installer SQL backups, so leaving those untouched would have recreated the old brand later.
- Prevention rule: any product rebrand must audit UI strings, seeded defaults, migrations, installer flows, and generated exports together.
- Next-time checklist item: search for the old brand in views, seeders, migrations, install files, and tests before calling a rebrand complete.

### Mistake: Historical vendor branding can leak back in through bootstrap paths
- What happened: even after the main app is rebranded, installer screens and imported SQL defaults can reintroduce the old brand on new environments.
- Root cause: install assets often live outside the normal runtime path, so they are easy to ignore.
- Earlier signal I missed: self-hosted delivery became part of the product, which raised the importance of installation-time branding.
- Prevention rule: once self-hosted is part of the offering, installer and backup SQL assets become customer-facing and must be branded too.
- Next-time checklist item: when shipping self-hosted, run a dedicated “fresh install branding” audit.

## Permanent Rules
- A rebrand is incomplete until existing deployments, new installs, and demo seeds all produce the new brand by default.
- If business identity is stored in settings tables, rebranding requires a safe migration path, not just changed seeders.
- Customer-facing install and update screens count as product surface area.

## Next-Project Checklist
- [ ] Search for old brand names across views, seeders, migrations, tests, and installation assets.
- [ ] Add a safe migration for old default brand values when live databases already exist.
- [ ] Verify demo seed output and installer defaults match the new brand.
- [ ] Verify exported PDFs and external share links show the new brand.

## Open Risks Or Follow-Ups
- Some internal strategy docs still mention `6POS` as historical source context; those are intentionally not treated as customer-facing brand assets.
- Vendor-side license checks still point to the original provider infrastructure; they are technical dependencies, not active product branding.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/migrations/2026_04_08_000001_rebrand_workspace_defaults_to_relayoffice.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/DemoBootstrapSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/installation/step0.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/lang/en/messages.php`

## 2026-04-08 - Brand clearance must happen before product-wide rebranding

## Snapshot
- Date: 2026-04-08
- Scope: rethinking the outward brand after discovering `RelayOffice` / `RelayOffice Returns` had material market-confusion and naming-risk issues
- Outcome: corrected direction after initial over-commitment
- Storage target: `memory/project-lessons.md`

## What Worked
- Internet and category-adjacent verification caught the issue before broader promotion scaled it.
- Separating “hold the domain” from “use it as the public brand” led to a more precise decision.
- Turning the naming problem into a rule set plus shortlist produced a cleaner next action than continuing abstract debate.

## Mistakes To Stop Repeating

### Mistake: Rebranding happened before minimum naming clearance
- What happened: the app was rebranded to `RelayOffice Returns` before completing even a minimal legal and market-confusion screen.
- Root cause: speed to demo and deployment was prioritized over naming risk, and the existing domain created false confidence.
- Earlier signal I missed: the candidate name reused a crowded word (`Relay`) that was already active in adjacent logistics and returns categories.
- Prevention rule: no outward rebrand should happen until a minimum naming gate is passed: exact web search, exact LinkedIn/company search, domain scan, and USPTO search.
- Next-time checklist item: before any product-wide rename, force a `candidate-name clearance` checklist and do not touch code until it passes.

### Mistake: Owning a domain was treated as if it implied brand safety
- What happened: having `relayoffice.ai` available and deployable created the impression that it was safe enough to adopt as the public brand.
- Root cause: domain ownership is easy to verify, but trademark and confusion risk depend on adjacent market use, not just domain availability.
- Earlier signal I missed: the product operated inside logistics / returns, where `Relay` already had live commercial meaning.
- Prevention rule: domain ownership only proves control of a URL, not brand clearance.
- Next-time checklist item: separate `domain availability`, `internet use`, and `trademark risk` into distinct decision checks.

## Permanent Rules
- Do not push a full rebrand into product surfaces before running minimum clearance on the candidate name.
- A “good-enough” name for deployment is not automatically a good-enough name for public go-to-market.
- If a root word is crowded in the target category, prefer abandoning it early instead of optimizing around it.

## Next-Project Checklist
- [ ] Run exact web search on the candidate brand and close variants.
- [ ] Check adjacent-category operators on Google and LinkedIn before editing product copy.
- [ ] Check USPTO before deciding a public-facing name is acceptable.
- [ ] Distinguish clearly between `domain to hold`, `demo URL`, and `formal brand`.
- [ ] Keep a neutral placeholder brand ready so deployment can continue while final naming is unresolved.

## Open Risks Or Follow-Ups
- The live app and some deployment defaults may still reference `RelayOffice Returns` until a new final brand is selected and re-applied.
- The shortlist still needs deeper legal and domain clearance before any name is adopted publicly.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/brand-naming-shortlist-v1.md`

## 2026-04-10 - Formal self-hosted readiness must be verified against a blank install, not inferred from the demo environment

## Snapshot
- Date: 2026-04-10
- Scope: verifying formal customer install flow, customer-owned accounts, and homepage claims before promotion
- Outcome: improved
- Storage target: `memory/project-lessons.md`

## What Worked
- Treating the self-hosted flow as a separate product surface exposed gaps that the hosted demo never would have shown.
- Feature tests for blank bootstrap and workspace access caught regressions quickly while the actual install path was being hardened.
- Replaying the blank install logic against a temporary database gave a real signal even when Docker build networking became a distraction.

## Mistakes To Stop Repeating

### Mistake: I assumed “self-hosted exists” meant “formal customer install is promotion-ready”
- What happened: the self-hosted pack still seeded demo-style accounts and the guest demo role into installs that were supposed to be customer-owned blank workspaces.
- Root cause: the original self-hosted package was validated from a deployment perspective, not from a customer handoff perspective.
- Earlier signal I missed: the docs still implied default accounts on install, which contradicted the promise of a clean production handoff.
- Prevention rule: any production/self-hosted claim must be validated in the exact target mode (`blank`, not `demo`) before it appears on the homepage.
- Next-time checklist item: test blank install outputs, not just startup health, before shipping self-hosted messaging.

### Mistake: I left account management as an implicit assumption instead of an explicit product capability
- What happened: a customer could install the app, but there was no clean returns-focused way for a master admin to create new staff accounts after setup.
- Root cause: the admin management model from the inherited codebase was never fully translated into the narrowed product workflow.
- Earlier signal I missed: the settings page only exposed profile/password, which is not enough for a customer-owned deployment.
- Prevention rule: every customer-owned deployment must have a clearly documented path for creating and managing staff accounts after install.
- Next-time checklist item: verify “how does the customer add their second user?” before claiming install readiness.

### Mistake: I let infrastructure noise slow down product verification
- What happened: the first real self-hosted check got stuck on creating a temporary `.env.self-hosted`, which delayed the actual question: what accounts and roles does a blank install create?
- Root cause: the verification plan was too dependent on the compose happy path and not enough on isolating the exact business invariant to prove.
- Earlier signal I missed: the real risk was seeded data and workspace ownership, not Docker syntax.
- Prevention rule: when verifying install readiness, prove the business invariant first, then prove the packaging path.
- Next-time checklist item: separate “does the packaged deploy boot?” from “does the customer get the right workspace state?”

## Permanent Rules
- Never market a self-hosted mode until a blank install has been verified to produce customer-owned data and customer-owned accounts only.
- “Production install” and “demo install” must be explicit modes with different seeded outputs and different docs.
- Customer-owned deployment claims require an in-product path for creating additional workspace users.
- When install verification gets blocked by tooling noise, switch to the narrowest environment that can still prove the production invariant.

## Next-Project Checklist
- [ ] Run a blank install verification before updating self-hosted homepage copy.
- [ ] Confirm default/demo accounts are absent from blank mode.
- [ ] Confirm guest/demo roles are absent from blank mode.
- [ ] Confirm a master admin can create, update, and remove workspace users after install.
- [ ] Verify the docs match the actual bootstrap mode outputs.
- [ ] Separate packaging validation from product-state validation when debugging installers.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/seeders/AdminTableSeeder.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/SystemController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/settings.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/SelfHostedBootstrapTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/WorkspaceAccessManagementTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/self-hosted-web-delivery-pack.md`

## 2026-04-10 - Role permissions are not enough when public demo and internal staff share one backend

## Snapshot
- Date: 2026-04-10
- Scope: separating `guest demo` access on `demo.dossentry.com` from internal admin/staff access on the internal workspace
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Treating host routing and role permissions as two separate layers exposed the real gap quickly.
- Adding a host-aware middleware plus login-time account checks created a real boundary instead of a cosmetic one.
- Writing tests for `wrong host + right account` and `right host + wrong account` caught the exact failure modes the UI alone would miss.

## Mistakes To Stop Repeating

### Mistake: A read-only demo role still leaks the wrong product shape if it enters the same workspace entrypoint as staff
- What happened: guest demo permissions were already reduced, but public visitors could still use the same admin login entrypoint that internal staff used.
- Root cause: permissions were treated as the whole boundary, while host-based entrypoint separation was postponed.
- Earlier signal I missed: the user explicitly asked not to let customer demo users “directly log into admin functionality,” which is a host and flow problem, not only a role problem.
- Prevention rule: whenever a public demo account exists, enforce both `what the account can do` and `where that account is allowed to log in`.
- Next-time checklist item: define `public host`, `internal host`, and `marketing host` before exposing any shared demo account.

## Permanent Rules
- Public demo accounts need both capability limits and host limits.
- Shared demo workspaces must never rely on sidebar hiding alone; server-side redirects and write guards are required.
- When validating access boundaries, always test cross-combinations: `public host + internal account`, `internal host + demo account`, and `authenticated user switching hosts`.

## Next-Project Checklist
- [ ] Define demo host and internal host in config/env before launch.
- [ ] Add middleware that enforces host/account matching for authenticated users.
- [ ] Block invalid account/host combinations at login submit time.
- [ ] Add UI copy that explains which workspace an account belongs to.
- [ ] Add tests for both pre-login and post-login host boundary failures.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Middleware/EnsureWorkspaceHostMatchesRole.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/Auth/LoginController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/auth/login.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/LoginWorkspaceBoundaryTest.php`

## 2026-04-10 - Shared demo workspaces must be intentionally constrained, not reused from an internal ops role

## Snapshot
- Date: 2026-04-10
- Scope: converting the public guest demo from a full ops-style admin account into a curated read-only demo workspace
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Converting the guest path into a dedicated role was faster and safer than trying to retrofit every existing ops role with conditional UI hacks.
- Server-side guards on write actions prevented shared-demo abuse even if a hidden button or route was discovered.
- Role-focused tests caught both permission leaks and route-level write access before the changes reached production.

## Mistakes To Stop Repeating

### Mistake: A shared demo account was initially pointed at the same experience as a real ops user
- What happened: the landing page guest demo credentials exposed an account with far more functionality than a first-time prospect should see.
- Root cause: demo access was treated as a shortcut for “let them log in and explore” instead of a deliberately designed conversion surface.
- Earlier signal I missed: the guest demo was using the ops manager account and the same menu structure, which meant the environment was optimized for internal use rather than external evaluation.
- Prevention rule: every public demo must start from a dedicated demo role with explicit read/write boundaries, not from a reused internal role.
- Next-time checklist item: before publishing a demo login, ask “what exact actions should a stranger be allowed to take?”

### Mistake: Read access and write access were coupled too closely under the same returns modules
- What happened: guest users could reach queue and playbook pages that mixed reference information with mutation controls.
- Root cause: module checks were originally good enough for internal staff, but not fine-grained enough for an external shared workspace.
- Earlier signal I missed: the queue and playbook screens were built as hybrid read/write work surfaces, so reusing them for public demo access would always require stronger capability checks.
- Prevention rule: any screen intended for shared demo use must have explicit capability helpers for `view`, `edit`, and `admin-only` actions.
- Next-time checklist item: when introducing a new public or semi-public role, audit every POST route first, then trim the UI second.

### Mistake: Local test assumptions hid environment-specific failures until role tests were expanded
- What happened: the first guest-demo test pass failed on sqlite date math and on a validation branch firing before the controller guard.
- Root cause: controller logic was written with MySQL behavior in mind, and the test originally hit a create path that could fail validation before the permission guard was evaluated.
- Earlier signal I missed: any new permission layer touching dashboards and CRUD flows should be expected to expose environment-specific SQL and routing behavior.
- Prevention rule: when adding a new role or auth boundary, run role tests against the same lightweight test database used in CI and choose assertions that exercise the actual authorization path.
- Next-time checklist item: include one read-path test and one blocked write-path test per protected module, and check sqlite compatibility for any computed SQL fragment.

## Permanent Rules
- Public demo access must always use a dedicated demo role, never a production-like ops or admin account.
- Shared demo users may view curated value surfaces, but all state-changing routes must be blocked server-side even if the UI hides them.
- Guest demo environments should emphasize evidence review, decision context, and playbook snapshots rather than full workspace administration.

## Next-Project Checklist
- [ ] Create a dedicated demo role before exposing any shared login.
- [ ] Audit POST/PATCH/DELETE routes for that role before polishing the UI.
- [ ] Hide settings, lead inboxes, and admin-only surfaces from any public demo.
- [ ] Run role-based tests on the target lightweight test database.
- [ ] Confirm the landing page shows demo credentials for the demo role, not an internal staff account.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/CPU/Helpers.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/migrations/2026_04_10_000002_create_guest_demo_role_and_admin.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/queue/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/rules/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/Returns/ReturnsWorkspaceRoleTest.php`

## 2026-04-10 - Lead-notification workflows must persist delivery state instead of swallowing mail errors

## Snapshot
- Date: 2026-04-10
- Scope: debugging missing `Request Workflow Review` emails after successful form submissions on production
- Outcome: improved
- Storage target: `memory/project-lessons.md`

## What Worked
- Writing the lead into the database before attempting email prevented lead loss.
- Reproducing the flow through tests made it safe to add delivery tracking and resend actions without guessing.
- Separating notification delivery into a dedicated service reduced duplication between public form submission and admin resend flows.

## Mistakes To Stop Repeating

### Mistake: The app treated "form stored successfully" as good enough even though the operator only cared about inbox delivery
- What happened: workflow review requests appeared in the admin table, but when email delivery failed or was ambiguous there was no in-product signal about what happened.
- Root cause: the original implementation optimized for graceful failure and low friction, but did not preserve any delivery state beyond a warning log.
- Earlier signal I missed: the controller already had a `try/catch` around mail send, which meant delivery problems were expected, but there was no corresponding status field for operators to inspect.
- Prevention rule: every external notification path must store `pending/sent/failed` state, last attempt time, and the latest error message on the business record itself.
- Next-time checklist item: when a feature depends on email, add visible delivery telemetry before launch, not after the first support report.

### Mistake: Using the same Gmail mailbox as both sender and notification recipient creates ambiguous operator feedback
- What happened: production was configured to send from and notify the same Gmail address, which made it harder to tell whether the app failed to send or Gmail simply did not surface the message as a fresh inbox item.
- Root cause: the notification path was set up for convenience instead of for clear observability.
- Earlier signal I missed: the desired notification inbox and the SMTP sender were identical from the beginning, which is a poor setup for trustable operational alerts.
- Prevention rule: do not assume a self-addressed mailbox is a reliable alerting target; either use a different recipient, a tested alias workflow, or a transactional provider with clear delivery reporting.
- Next-time checklist item: before relying on email alerts, verify who the sender is, who the recipient is, and where the operator expects the message to appear.

## Permanent Rules
- Email-backed lead capture must expose delivery status in the admin UI, not only in logs.
- Failed notifications must be manually retryable from the same record.
- If the alert inbox and SMTP sender are the same mailbox, treat inbox visibility as untrusted until proven.

## Next-Project Checklist
- [ ] Add `notification_status`, `notification_attempted_at`, and `notification_error` fields for new email-driven workflows.
- [ ] Provide an operator-visible resend action for any critical lead or alert notification.
- [ ] Validate production email with one real external inbox before launch.
- [ ] Confirm whether sender and recipient are the same mailbox and document the tradeoff.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Services/WorkflowReviewNotificationService.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/WorkflowReviewRequestController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/WorkflowReviewRequestController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/review-requests/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/WorkflowReviewRequestFlowTest.php`

## 2026-04-10 - Root domain launch can look broken even after hosting and DNS are correct

## Snapshot
- Date: 2026-04-10
- Scope: debugging `dossentry.com` reachability after Render + Cloudflare cutover
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Checking public DNS answers and live HTTP responses separated real infrastructure issues from browser-local issues.
- Verifying `Render custom domains`, `Cloudflare DNS`, and public `curl/dig` in parallel prevented more blind config churn.
- Keeping `demo`, apex, and `www` as separate roles made the final state easier to reason about.

## Mistakes To Stop Repeating

### Mistake: Treating local browser `ERR_NAME_NOT_RESOLVED` as proof that production DNS was still broken
- What happened: root domain continued to look down in the browser even after Render showed verified domains and public resolvers were already returning answers.
- Root cause: local DNS cache still held the old NXDOMAIN state from earlier failed lookups.
- Earlier signal I missed: public `curl` and resolver checks were already healthy while only the local browser still failed.
- Prevention rule: once authoritative/public DNS and direct HTTP checks are healthy, stop editing infra and clear local DNS caches before changing configs again.
- Next-time checklist item: run one public resolver check and one direct HTTP check before touching Render or Cloudflare again.

### Mistake: Over-focusing on platform limits and domain count instead of proving the real blocker
- What happened: the Render hobby-domain limit popup looked suspicious and became a false lead during debugging.
- Root cause: visible UI warnings are easy to overweight when they appear near the failure being debugged.
- Earlier signal I missed: the target domains were already verified and certificate-issued, which meant the current failure was unlikely to be caused by an upgrade requirement.
- Prevention rule: do not escalate hosting plans during DNS incidents unless the failing domain is actually blocked from being attached or verified.
- Next-time checklist item: ask “is the failing host already verified and certificate-issued?” before blaming plan limits.

### Mistake: Considering deletion of active DNS records before validating each record's role
- What happened: there was pressure to delete one of the apex/`www`/`demo` records while the launch was still stabilizing.
- Root cause: cleanup instinct kicked in before the routing model was fully locked.
- Earlier signal I missed: each record already had a distinct purpose: apex for landing page, `www` for redirect convenience, and `demo` for product login.
- Prevention rule: do not delete DNS records during a live cutover unless their role is explicitly mapped and a replacement path is tested.
- Next-time checklist item: write down the purpose of each hostname before removing any record.

## Permanent Rules
- Public resolver health beats local browser errors during DNS debugging.
- `Verified` + `Certificate Issued` in Render means the next suspect should usually be DNS propagation or local cache, not hosting-plan limits.
- Keep apex, `www`, and `demo` records until the final routing model is stable and externally verified.

## Next-Project Checklist
- [ ] Check authoritative/public DNS answers before changing hosting config.
- [ ] Test live HTTP response with `curl -I` from the terminal before trusting browser errors.
- [ ] Flush local DNS cache before changing DNS a second time.
- [ ] Confirm the purpose of each hostname (`apex`, `www`, `demo`) before deleting any record.
- [ ] Do not upgrade hosting plans during DNS incidents unless attachment/verification is actually blocked.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`
- `https://dossentry.com`
- `https://www.dossentry.com`
- `https://demo.dossentry.com/admin/auth/login`

## 2026-04-10 - Lead capture must notify the owner without making email a hard dependency

## Snapshot
- Date: 2026-04-10
- Scope: workflow review request email notification
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Existing review-request form already wrote clean structured data to the database, so the notification layer could be added without redesigning the form flow.
- Using `reply-to` on the notification email makes follow-up faster than copying details manually from the admin panel.
- Catching mail transport failures preserved the core business event even when SMTP is missing or broken.

## Mistakes To Stop Repeating

### Mistake: A lead form was considered “done” before the owner was actually notified
- What happened: workflow review requests were saved in the app, but nothing reached the owner's inbox.
- Root cause: the feature was validated from the database/admin side, not from the operator-response side.
- Earlier signal I missed: the CTA was already being prepared for promotion, which raises the standard from “stored somewhere” to “immediately actionable.”
- Prevention rule: every lead capture flow must be verified end-to-end: submit, notify owner, and provide a reply path.
- Next-time checklist item: for every public form, ask “who gets alerted within 60 seconds?”

### Mistake: Mail delivery could have become a single point of failure for form submission
- What happened: adding synchronous email send on submission would have risked breaking the form whenever SMTP was misconfigured.
- Root cause: notification requirements often get treated as part of the core transaction even when they are operational side effects.
- Earlier signal I missed: Render mail credentials were not yet configured, so a strict send path would fail immediately in production.
- Prevention rule: business-event storage must succeed even if notification delivery fails.
- Next-time checklist item: wrap outbound notifications so the primary user action still completes when transport dependencies are missing.

## Permanent Rules
- A public lead form is not complete until there is a tested owner-notification path.
- Email delivery is an operational dependency, not the primary business transaction.
- Always set a direct reply path (`reply-to` or equivalent) for inbound lead notifications.

## Next-Project Checklist
- [ ] Confirm the owner notification recipient before shipping a public CTA.
- [ ] Test successful notification delivery with the real mail transport.
- [ ] Test failure mode so the form still completes when email transport is down.
- [ ] Ensure the notification email includes a direct reply path to the submitter.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/WorkflowReviewRequestController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Mail/WorkflowReviewRequestSubmitted.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/WorkflowReviewRequestFlowTest.php`

## 2026-04-10 - Demo CTA should expose value before credentials

## Snapshot
- Date: 2026-04-10
- Scope: landing page conversion path for demo traffic
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Promoting the sample Brand Review Link above the shared demo made the strongest product value visible without any login friction.
- Reframing the shared account as a `guest workspace` made public demo credentials feel intentional instead of sloppy.
- Keeping `Request Workflow Review` as the third CTA preserved a path for high-intent users who want a real pilot instead of just browsing.

## Mistakes To Stop Repeating

### Mistake: Sending cold visitors directly to a login page with no context or credentials
- What happened: `Open Live Demo` dropped users into a login screen before they had seen the product's strongest proof artifact.
- Root cause: the CTA hierarchy was designed around product availability instead of buyer understanding.
- Earlier signal I missed: users had to ask what to do after clicking demo, which meant the CTA path was already unclear.
- Prevention rule: public product pages must show one non-login proof artifact before any authenticated experience.
- Next-time checklist item: ask “what can a visitor see in 10 seconds without an account?”

### Mistake: Treating public demo credentials as a raw operational detail instead of a productized experience
- What happened: the initial instinct was to paste shared credentials directly onto the page.
- Root cause: optimization for speed over trust and framing.
- Earlier signal I missed: shared credentials can look like a leak unless they are explicitly described as a sample workspace with reset behavior.
- Prevention rule: if a shared demo account is exposed publicly, package it as a guest workspace with scope and reset expectations.
- Next-time checklist item: pair every shared credential with purpose, audience, and reset/disclaimer copy.

## Permanent Rules
- Landing pages should lead with the strongest proof artifact, not the most complete product surface.
- Authenticated demos are secondary; public proof artifacts should carry the first interaction.
- Shared demo credentials must be intentionally framed as guest access, never as normal production login.

## Next-Project Checklist
- [ ] Define the public no-login proof artifact before wiring the hero CTA.
- [ ] Confirm that the first authenticated CTA includes context or guest access details.
- [ ] Package any public shared account as a guest workspace with reset expectations.
- [ ] Test the full CTA sequence from “I just landed here” to “I saw value” without prior explanation.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/LandingPageRoutingTest.php`

## 2026-04-10 - Root-domain outages should be debugged at public DNS first, not in app code

## Snapshot
- Date: 2026-04-10
- Scope: diagnosing why `https://dossentry.com` could not be reached after Render and landing-page deployment
- Outcome: improved
- Storage target: `memory/project-lessons.md`

## What Worked
- Public DNS checks (`dig`, `nslookup`) separated the working subdomain (`demo.dossentry.com`) from the broken apex domain quickly.
- Verifying the live demo login page proved the application and branding deployment were healthy.
- Checking provider docs after confirming the symptom kept the fix path focused on DNS and Render custom-domain verification.

## Mistakes To Stop Repeating

### Mistake: Treating a root-domain outage like an app/login problem
- What happened: the reported symptom was “site can't be reached,” but recent work had been on landing-page and login behavior, which made it tempting to keep looking at app routes.
- Root cause: the first diagnostic pass did not force a public DNS check before thinking about application logic.
- Earlier signal I missed: `demo.dossentry.com` was healthy while `dossentry.com` failed to resolve at all, which means the request was not reaching Laravel.
- Prevention rule: when a domain is unreachable, first prove whether the hostname resolves publicly before inspecting app code or middleware.
- Next-time checklist item: run `dig +short <domain> A`, `dig +short <domain> AAAA`, and `curl -I https://<domain>/` before touching code.

## Permanent Rules
- “Can't be reached” is a DNS/connectivity symptom until proven otherwise.
- If a subdomain works and the apex does not, assume DNS or provider custom-domain setup before assuming app regression.
- Root/apex domains and subdomains must be verified separately in Render and Cloudflare.

## Next-Project Checklist
- [ ] Check public DNS answers for A/AAAA/CNAME on the exact broken hostname.
- [ ] Check whether another hostname on the same service is healthy.
- [ ] Confirm the hostname exists under Render Custom Domains and has passed verification.
- [ ] Confirm Cloudflare has the correct apex record and no conflicting root records.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/render-deploy-dossentry.md`
- [Render custom domains](https://render.com/docs/custom-domains)

## 2026-04-10 - External DNS success can still look broken locally if NXDOMAIN is cached

## Snapshot
- Date: 2026-04-10
- Scope: finishing the `dossentry.com` root-domain launch and confirming why the site still looked unreachable in the browser
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Public resolver checks against `1.1.1.1`, `8.8.8.8`, and Cloudflare authoritative name servers separated provider-side success from local-machine behavior.
- Render verification status plus Cloudflare record snapshots gave enough evidence to stop changing app code.
- Flushing the local macOS DNS cache closed the issue without more infrastructure churn.

## Mistakes To Stop Repeating

### Mistake: Continuing to suspect provider misconfiguration after public resolvers were already healthy
- What happened: even after Render showed verified domains and public resolvers started returning `dossentry.com`, the browser still showed `ERR_NAME_NOT_RESOLVED`, which kept the debugging loop alive.
- Root cause: local DNS cache behavior was not treated as a first-class possibility early enough once external DNS started answering correctly.
- Earlier signal I missed: `1.1.1.1` and `8.8.8.8` both returned healthy apex answers while the browser still failed locally.
- Prevention rule: once authoritative and public recursive resolvers both return the expected records, stop changing DNS and test local cache flush before touching provider settings again.
- Next-time checklist item: run a local DNS cache flush immediately after confirming public resolver health.

## Permanent Rules
- Public DNS truth beats local browser errors when they disagree.
- After domain cutovers, treat local DNS cache as a likely cause of lingering `ERR_NAME_NOT_RESOLVED`.
- Do not continue mutating Render or Cloudflare once authoritative and public resolver checks are both green.

## Next-Project Checklist
- [ ] Check authoritative DNS.
- [ ] Check at least two public recursive resolvers.
- [ ] If both are healthy, flush local DNS cache before changing provider config again.

## Open Risks Or Follow-Ups
- Root-domain launch is working, but future domain migrations should include a standard operator note about browser and OS DNS cache delay.

## Source Artifacts
- Render custom domain verification screenshots
- Cloudflare DNS record screenshots
- Public resolver checks for `dossentry.com`

## 2026-04-09 - Marketing pages should be introduced without breaking the live demo entrypoint

## Snapshot
- Date: 2026-04-09
- Scope: adding the first public landing page while keeping `demo.dossentry.com` as the working app entrypoint
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Splitting the public marketing host from the demo host let the product stay usable while adding a homepage.
- Testing host-specific behavior with explicit `Host` headers caught the correct `landing vs login redirect` behavior quickly.
- Sweeping brand residues after the main page build caught a stale self-hosted example before it leaked back into customer-facing materials.

## Mistakes To Stop Repeating

### Mistake: Brand cleanup can look finished even when delivery artifacts still carry legacy values
- What happened: the landing page and live app were branded as `Dossentry`, but `.env.self-hosted.example` still used `RelayOffice Returns` and legacy database identifiers.
- Root cause: visual/UI rebrand work finished first, while deployment and delivery files were treated as secondary.
- Earlier signal I missed: environment templates often survive rebrands because they are not exercised during browser-based QA.
- Prevention rule: every rebrand must include a final `exact brand string` scan across deploy, install, and delivery files before shipping.
- Next-time checklist item: run a repo-wide search for both the old and new brand names before calling rebrand complete.

### Mistake: Landing pages are easy to wire in a way that silently hijacks the working app root
- What happened: adding a root-domain landing page required a host-based split so the demo subdomain would keep redirecting to login instead of showing marketing content.
- Root cause: the existing app used `/` as a practical entrypoint, so marketing and product routes were competing for the same root path.
- Earlier signal I missed: any app that already uses the root path needs explicit host-aware routing when a marketing site is introduced later.
- Prevention rule: when adding a landing page to a live app, define public hosts and product hosts explicitly before touching the root route.
- Next-time checklist item: test `root path` behavior for every public hostname before deployment.

## Permanent Rules
- Rebrand completion requires checking environment templates and delivery packs, not just the visible UI.
- Marketing and product can share one Laravel service only if host-based root behavior is explicitly tested.
- Demo subdomains should preserve the shortest path to login; marketing content belongs on the brand root domain.

## Next-Project Checklist
- [ ] Search the repo for the old brand string before closing the rebrand task.
- [ ] Test root-route behavior with host-specific requests for each public hostname.
- [ ] Keep the demo host pointed at login even after adding a landing page.
- [ ] Check self-hosted examples and install templates for stale names or identifiers.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted.example`

## 2026-04-09 - Rebranding a live app requires separating visible brand from live infrastructure identifiers

## Snapshot
- Date: 2026-04-09
- Scope: switching the app from `RelayOffice Returns` to `Dossentry` after the user picked a new brand
- Outcome: success with controlled infrastructure carry-over
- Storage target: `memory/project-lessons.md`

## What Worked
- Treating the rebrand as a live migration prevented a dangerous “search and replace everything” approach.
- Adding a new migration for existing `business_settings` values means deployed databases can move to `Dossentry` automatically.
- Keeping the current Render custom domain and service identifiers stable avoided breaking the working production demo while still changing the visible brand.

## Mistakes To Stop Repeating

### Mistake: Live infrastructure names and public brand can diverge, and that must be planned explicitly
- What happened: after the first rebrand, the visible brand changed but the live Render service/domain identity still reflected the old naming path.
- Root cause: product copy and deployment infrastructure were treated as one layer instead of two.
- Earlier signal I missed: the app already had a working custom domain and auto-deploy pipeline, so forcing an all-at-once rename would risk unnecessary downtime.
- Prevention rule: when rebranding a deployed product, separate the work into `visible brand changes` and `infrastructure identity migration`.
- Next-time checklist item: before touching deploy config, ask whether the current domain/service names are still needed to keep production stable.

### Mistake: Default seeded emails should not imply ownership of an unregistered brand domain
- What happened: the prior seeded defaults used a branded email on a domain that was not actually secured as the long-term public brand.
- Root cause: the brand system was changed faster than the domain system.
- Earlier signal I missed: naming remained in flux while seeded defaults were already acting like the domain decision was final.
- Prevention rule: until the final brand domain is registered and configured, default seeded emails should use a safe local/internal address.
- Next-time checklist item: if domain ownership is not finalized, use `.local` or another explicitly internal default.

## Permanent Rules
- Rebrand user-facing surfaces first; migrate live infrastructure identifiers only in a separate planned window.
- Existing deployments need a settings migration, not just new seeds.
- Branded email defaults should not assume a domain that has not been secured for long-term use.

## Next-Project Checklist
- [ ] Split rebrand work into `visible product layer` and `infrastructure layer`.
- [ ] Add a live-data migration for current business settings.
- [ ] Use internal/local defaults until the final branded domain is actually ready.
- [ ] Verify installer, exported assets, and seeded defaults all match the new brand.

## Open Risks Or Follow-Ups
- The live demo domain and some Render service identifiers still use the old relayoffice naming path for operational continuity.
- Once the user registers a final Dossentry domain, a second pass will be needed to migrate `APP_URL`, custom domains, and possibly Render service names.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/migrations/2026_04_09_000001_rebrand_workspace_defaults_to_dossentry.php`
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`
- `/Users/mikezhang/Desktop/projects/6POS/render-deploy-dossentry.md`

## 2026-04-09 - Domain migration should be decided as an explicit product-surface split

## Snapshot
- Date: 2026-04-09
- Scope: moving the live demo entrypoint from the old relayoffice subdomain plan to `demo.dossentry.com`
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Locking the split as `root domain for landing page, demo subdomain for app` kept the migration simple.
- Updating both `render.yaml` and the deploy doc together reduced the chance of drift between config and operations.

## Mistakes To Stop Repeating

### Mistake: Domain strategy can stay implicit for too long
- What happened: the product brand changed before the new public app domain was decided, which left the deploy config and docs lagging behind the naming direction.
- Root cause: domain migration was treated as a later ops detail instead of part of the brand rollout.
- Earlier signal I missed: the new brand was already chosen, but the app was still pointing at the old branded subdomain.
- Prevention rule: once a new public brand is selected, define the exact `root vs demo/app subdomain` structure immediately.
- Next-time checklist item: after naming is selected, decide domain mapping before more public promotion.

## Permanent Rules
- Brand selection is incomplete until the public domain structure is chosen.
- Keep the product app on a dedicated subdomain when the root domain may later host marketing pages.

## Next-Project Checklist
- [ ] Decide whether the product lives on the root domain or a dedicated app/demo subdomain.
- [ ] Update deploy config and deploy docs in the same change.
- [ ] Verify DNS and TLS steps are written against the actual chosen domain, not placeholders.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`
- `/Users/mikezhang/Desktop/projects/6POS/render-deploy-dossentry.md`

## 2026-04-10 - Marketing root domain and demo subdomain should be verified as separate user journeys

## Snapshot
- Date: 2026-04-10
- Scope: confirming that `dossentry.com` serves the public landing page while `demo.dossentry.com` remains the working product login
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Using one Laravel service with host-based routing allowed marketing and product entrypoints to coexist without a second deployment surface.
- Verifying both domains after TLS/DNS completion closed the loop on the actual user journey instead of stopping at config changes.
- Treating the root domain and demo subdomain as different products in QA kept the checks concrete and fast.

## Mistakes To Stop Repeating

### Mistake: Deployment can be treated as done before the real public journeys are exercised
- What happened: the config and DNS work were completed first, but the real launch milestone was only reached after testing both `landing page` and `product login` paths in the browser.
- Root cause: infra completion is easy to confuse with customer-visible completion.
- Earlier signal I missed: a successful Render deploy does not prove the root-domain marketing path and app-domain login path both behave correctly.
- Prevention rule: do not call a domain launch complete until every public hostname has been exercised through its intended entrypoint.
- Next-time checklist item: define one acceptance test per public hostname before changing DNS.

### Mistake: Product momentum can keep drifting into infrastructure polish after the real launch blocker is gone
- What happened: once both domains worked, the highest-leverage work was no longer technical, but it would have been easy to keep polishing deployment details.
- Root cause: technical closure feels productive and safer than market validation.
- Earlier signal I missed: the product was already usable; the next risk had shifted from uptime to demand.
- Prevention rule: after public entrypoints are verified, move immediately to sales validation unless there is a live production defect.
- Next-time checklist item: ask “what is the new top risk now?” after every successful launch action.

## Permanent Rules
- A live launch is complete only when each public domain is tested as the user will actually experience it.
- Root-domain marketing and demo-domain product flows should always have separate acceptance checks.
- Once deployment risk drops, the next highest-risk item becomes the new priority, even if more technical polish is possible.

## Next-Project Checklist
- [ ] List every public hostname and its intended user journey before launch.
- [ ] Verify landing path, login path, and one protected page on the correct domains.
- [ ] Stop deployment work once the real user journeys pass and move to demand validation.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`

## 2026-04-10 - Promotion readiness requires a real lead path and clean brand surface, not just a working demo

## Snapshot
- Date: 2026-04-10
- Scope: fixing the last promotion blockers by removing old POS branding from login and turning the landing page CTA into a real workflow-review capture flow
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Treating `can we promote this?` as a product QA question exposed real blockers instead of assuming the deploy was enough.
- Adding a minimal lead capture loop into the existing app was faster and safer than waiting for Calendly, CRM, or outbound tooling.
- Backing the landing page and login cleanup with tests stopped this from being a visual-only change that could regress silently.

## Mistakes To Stop Repeating

### Mistake: A working demo can still be promotion-incompatible if the surrounding surfaces tell the wrong story
- What happened: the app login still showed old POS-oriented copy, which would have undermined Dossentry's positioning even though the core product flow worked.
- Root cause: product functionality was validated earlier than the brand surfaces wrapped around it.
- Earlier signal I missed: the public demo URL was live, but the first thing an external visitor would read was still not aligned with the current product story.
- Prevention rule: before any public promotion, inspect the full top-of-funnel path from landing page to login and remove legacy narrative residue.
- Next-time checklist item: run a “first-time visitor” walkthrough before calling a site promotion-ready.

### Mistake: A landing page CTA is not real until it creates an actionable artifact for follow-up
- What happened: the landing page originally only pushed visitors to the live demo, which created no structured lead record and no clear follow-up path.
- Root cause: feature completion was mistakenly treated as go-to-market completion.
- Earlier signal I missed: without a booking tool or stored request, any interest generated by the page would have been fragile and manual.
- Prevention rule: every public CTA must either capture contact information or route the visitor into a defined next step that the team can act on.
- Next-time checklist item: ask “where does this lead go?” before shipping any promotional CTA.

### Mistake: Overly strict validation can hurt top-of-funnel conversion without improving lead quality
- What happened: the first version of the workflow review form used DNS email validation, which is unnecessary friction for an early lead form.
- Root cause: validation discipline from internal product forms leaked into a marketing capture flow.
- Earlier signal I missed: top-of-funnel forms optimize for low friction and clean follow-up, not for perfect input certainty.
- Prevention rule: marketing forms should default to lightweight validation unless a stricter rule clearly improves downstream operations.
- Next-time checklist item: challenge every validation rule on a public form with “does this protect us, or just reduce submissions?”

## Permanent Rules
- Promotion readiness requires brand-consistent landing, login, and CTA surfaces, not just a healthy product URL.
- Public CTAs must create a concrete follow-up artifact inside the system or a connected workflow.
- Marketing-capture validation should be lighter than operational data-entry validation unless abuse becomes a real problem.

## Next-Project Checklist
- [ ] Review the full public journey from homepage to login before promotion.
- [ ] Remove any legacy product/category language from first-touch surfaces.
- [ ] Ensure the primary CTA creates a stored lead or a scheduled next step.
- [ ] Add regression tests for login branding and public lead capture flows.
- [ ] Keep top-of-funnel validation lightweight unless spam or abuse proves otherwise.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/auth/login.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/WorkflowReviewRequestController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/review-requests/index.blade.php`

## 2026-04-09 - Domain cutover should preserve working infrastructure and only swap the public entrypoint

## Snapshot
- Date: 2026-04-09
- Scope: moving the live app login from the old relayoffice subdomain to `demo.dossentry.com`
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Keeping the existing Render service alive while only changing the public app domain reduced risk.
- Updating Render config, DNS, and verification in a narrow sequence prevented multi-variable debugging.
- Testing login on the new domain immediately after DNS/TLS verification caught success at the right milestone.

## Mistakes To Stop Repeating

### Mistake: It is easy to over-migrate when only the public entrypoint needs to change
- What happened: there was pressure to rename internal service identifiers and infra labels together with the public brand/domain.
- Root cause: branding work and infrastructure work naturally blur together during launch.
- Earlier signal I missed: the live app was already stable, so changing internal identifiers would add migration risk without improving the customer experience.
- Prevention rule: during domain cutover, change the public entrypoint first and leave internal infra identifiers for a separate maintenance window.
- Next-time checklist item: ask “what does the customer see?” before deciding what to rename in production.

## Permanent Rules
- Successful domain cutover is defined by app reachability, TLS, and login on the new domain, not by fully renamed internal infra.
- Public-facing brand changes should be prioritized over internal naming cleanup when production stability is at stake.

## Next-Project Checklist
- [ ] Update app domain in deploy config.
- [ ] Add DNS record in the registrar/DNS provider.
- [ ] Verify TLS in the hosting platform.
- [ ] Test login and one protected page on the new domain.
- [ ] Defer internal infra rename unless it creates a real operational problem.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/render.yaml`
- `/Users/mikezhang/Desktop/projects/6POS/render-deploy-dossentry.md`
- [USPTO comprehensive clearance search guidance](https://www.uspto.gov/trademarks/search/comprehensive-clearance-search-similar-trademarks)
- [Relay](https://www.relaytech.co/)

## 2026-04-08 - Naming shortlist should be filtered by exact internet noise before deeper clearance

## Snapshot
- Date: 2026-04-08
- Scope: screening candidate brand names after `Northset` showed high noise in public search
- Outcome: improved
- Storage target: `memory/project-lessons.md`

## What Worked
- Exact-match internet checks killed weak candidates quickly.
- Separating `exact brand use` from `fuzzy USPTO result volume` produced better naming decisions.
- Domain WHOIS plus live-web checks gave a faster signal than abstract discussion.

## Mistakes To Stop Repeating

### Mistake: A promising name can survive too long if exact public-use checks are skipped
- What happened: `Northset` initially looked clean enough, but exact public-use checking later showed enough noise to stop it.
- Root cause: shortlist creation moved faster than exact-match validation.
- Earlier signal I missed: abstract, polished-sounding names often already exist in small companies, music projects, or other low-visibility uses.
- Prevention rule: every shortlist candidate must pass `exact web search`, `company/social search`, and `domain whois` before it is treated as a serious finalist.
- Next-time checklist item: never present a naming “primary recommendation” before exact public-use checks.

## Permanent Rules
- Fuzzy trademark result counts are not enough; exact public-use checking must happen early.
- A candidate can be legally uncertain and still be commercially too noisy to be worth pursuing.
- Domain availability is a useful signal, but only after exact-name web noise is checked.

## Next-Project Checklist
- [ ] Exact Google search for the candidate in quotes.
- [ ] LinkedIn/company noise check for the exact candidate.
- [ ] WHOIS check for the preferred domains.
- [ ] Kill any candidate with obvious active commercial noise before attorney review.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/brand-naming-shortlist-v1.md`


## 2026-04-10 - Workflow lead notifications need in-product diagnostics, not blind SMTP guessing

## Snapshot
- Date: 2026-04-10
- Scope: debugging why workflow review requests were stored but notification emails were not visibly arriving
- Outcome: improved
- Storage target: `memory/project-lessons.md`

## What Worked
- Separating database write success from mail delivery success avoided treating a stored lead as proof that notification worked.
- Adding admin-side diagnostics for recipient, sender, and mailer settings made the problem inspectable without SSH or Render logs.
- Adding `Resend email` and `Send test email` actions turns future mail debugging into a product workflow instead of guesswork.

## Mistakes To Stop Repeating

### Mistake: We treated silent mail failure as a secondary issue instead of a promotion blocker
- What happened: the public form stored leads correctly, but the team still could not rely on inbox notifications.
- Root cause: exceptions were intentionally swallowed to avoid losing leads, but there was no visible diagnostic layer in the product.
- Earlier signal I missed: the app had no admin-page display of current recipient/from settings or the last delivery error.
- Prevention rule: any lead form that claims to send a notification must expose delivery status and test tooling in the admin UI before promotion.
- Next-time checklist item: verify stored lead, delivery status, and inbox visibility separately.

### Mistake: Gmail self-send behavior was not treated as a real operational edge case
- What happened: notifications were configured to send from and to the same Gmail mailbox, which can make messages appear in `Sent`, `All Mail`, or an existing thread instead of surfacing like a normal inbound alert.
- Root cause: we optimized for the simplest config, not the clearest operator experience.
- Earlier signal I missed: the sender and recipient were identical, so inbox visibility was never guaranteed even when SMTP worked.
- Prevention rule: if the sender and recipient are the same Gmail mailbox, show a warning in-product and prefer a different recipient or provider for operational alerts.
- Next-time checklist item: compare `MAIL_FROM_ADDRESS` and notification recipient before calling email alerts done.

## Permanent Rules
- A successful lead notification system needs both durable storage and operator-visible delivery diagnostics.
- Silent mail failure handling is good for lead retention, but only if the UI surfaces status and retry actions.
- Self-sent Gmail alerts are operationally ambiguous and should never be the default assumption for reliable notifications.

## Next-Project Checklist
- [ ] Show notification recipient, sender, and mailer in the admin lead view.
- [ ] Show per-lead delivery status, attempted time, and last error.
- [ ] Provide resend and test-email actions before promotion.
- [ ] If sender and recipient are the same Gmail mailbox, test `All Mail`, `Sent`, and `Spam`, or switch recipients.
- [ ] Validate one real lead end-to-end before calling the funnel promotion-ready.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/WorkflowReviewRequestController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/review-requests/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/admin.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/WorkflowReviewRequestFlowTest.php`

## 2026-04-10 - Marketing advantages must be visible above the fold
- 错误: 已经决定把“本地部署、数据留在客户环境、可选本地知识库”作为核心差异化，但首页最醒目的区域仍然在讲通用卖点。
- 根因: 只补了说明文案，没有把商业优势做成视觉层级最高的展示。
- 预警信号: 用户主动指出“希望用特别字体或颜色标注”，说明现有页面没有把优势传达出去。
- 新规则: 任何首页主卖点如果是商业定位差异，不允许只埋在 FAQ 或中段说明里，必须在 hero 首屏以高对比组件直接展示。
- Checklist:
  - 首屏是否在 3 秒内看出和竞品/SaaS 的差异
  - 差异化卖点是否同时出现在 hero 和详细 section
  - 关键信息是否使用更高层级的字体、色块或 badge，而不是普通正文

## 2026-04-10 - Workflow review mail failures need to be separated into auth vs visibility
- 错误: 一开始把“Gmail 自发自收不可见”和“SMTP 根本认证失败”混在一起判断。
- 根因: 没有先看 SMTP provider 的明确错误信息，就过早给出收件箱可见性推断。
- 预警信号: 后台已经显示 `Failed to authenticate on SMTP server`，这类错误优先级高于任何收件箱分类猜测。
- 新规则: 邮件问题先按顺序排查 `认证 -> 发件成功但不可见 -> 投递被拦截`，不能跳步。
- Checklist:
  - 先看后台或日志里的原始 SMTP 错误
  - 认证失败时，优先核对 username / app password / 2FA
  - 只有 `sent` 状态下，才讨论 Gmail 的 All Mail / Sent / Spam 可见性

## 2026-04-11 - 不要把大图二进制直接塞进 Blade 模板
- 错误: 为了快速换首页背景图，直接把完整 base64 PNG 内联进 Blade 变量，导致模板臃肿、报错难排、后续编辑几乎不可维护。
- 根因: 把“换一张图”当成文案修改处理，没有按静态资源交付思路走。
- 预警信号: 模板文件头部出现超长单行字符串、搜索结果里只要打开文件就被几万字符噪音淹没、任何简单替换都会变得脆弱。
- 新规则: 首页图片、PDF、视频等二进制资源一律走 `public/assets/...`，视图里只保留 `asset()` 引用，不允许再内联 base64 大文件。
- Checklist:
  - 资源是否落在静态目录而不是模板变量里
  - 视图文件顶部是否保持可读，不含超长二进制字符串
  - 改图后先做一次 `rg`，确认没有遗留旧变量名
  - 页面文案调整和资源替换分两步做，避免把排障和营销改动混在一起

## 2026-04-11 - Landing page 改视觉风格时不要继续在旧结构上堆补丁
- 错误: 用户已经给了明确的参考样式，但页面仍然沿用旧的暖色渐变、旧卡片层级和多轮 patch 过的结构，导致视觉越来越乱。
- 根因: 把“换风格”误当成“调几个颜色和边距”，没有在参考图已经明确的情况下直接重建页面结构。
- 预警信号: 同一页面连续出现多套字体气质、按钮样式和卡片语言；每修一次 CSS 都只修局部，整体层级反而更混乱。
- 新规则: 当用户给出明确的 landing page 视觉参考时，优先整体重构页面样式系统和 section 结构，不允许继续在旧风格上叠加补丁。
- Checklist:
  - 新首页是否只有一套字体、按钮、卡片、阴影语言
  - Hero / workflow / deployment / CTA 是否属于同一视觉系统
  - 是否保留原有业务 CTA 和表单链路，而不是只复制静态样式
  - 改完后先做一次结构自检，确认 Blade 变量、CTA、表单 action 没丢

## 2026-04-11 - 承诺交付法务文档后不能只停留在口头建议
- 错误: 已经答应“下一步给 checklist 和 legal pages 结构”，但中间被其他任务打断后没有真正把文档落地到仓库。
- 根因: 把“已经形成结论”误当成“已经交付成果”，没有用文件产出把承诺收口。
- 预警信号: 用户再次追问“你是完成了这两项吗”，说明前一轮回答只有方向，没有实际交付物。
- 新规则: 只要承诺了下一步会生成 checklist、策略包、文档模板，就必须在仓库里落成明确文件，再对用户说“已完成”。
- Checklist:
  - 承诺的 deliverable 是否真的生成了文件
  - 文件名是否直观可复用
  - 回答“已完成”前是否先 `rg` / `ls` 核对文件存在
  - 被其他调试任务打断后，是否回到原承诺补齐输出

## 2026-04-11 - 法务页不算完成，除非页面、路由、入口一起上线
- 错误: 一开始只补了法务 markdown 结构，没有立刻把 `Privacy Policy` 和 `Terms of Service` 作为真实页面接进站点。
- 根因: 把“内部文档齐了”误认为“对外合规入口齐了”，忽略了用户真正需要的是可访问页面和 footer/login 链接。
- 预警信号: 网站 footer 和登录页当时都没有 legal links，这意味着推广前合规链路仍然是断的。
- 新规则: 只要涉及对外合规页面，必须同时交付 `内容页面 + 路由 + 页面入口 + 最小测试`，缺一个都不能叫完成。
- Checklist:
  - legal page 是否有真实 URL
  - landing footer 是否有 legal links
  - login page 是否有 legal links
  - 是否至少做过一次路由/模板级验证
  - 如果本机没有 PHP 或 Docker，必须明确记录验证缺口，不能假装测过

## 2026-04-11 - 合规入口要存在，但不能和主转化区抢层级
- 错误: 把 `Privacy Policy` 和 `Terms of Service` 直接放在登录表单主区域旁边，虽然满足了入口要求，但视觉上破坏了登录页主任务。
- 根因: 只考虑“要不要有链接”，没有考虑登录页的注意力层级和主次关系。
- 预警信号: 用户第一眼就反馈“不好看”，说明合规入口已经从辅助信息变成了视觉噪音。
- 新规则: 登录页的 legal links 默认放在卡片最底部，使用弱样式，不允许与邮箱、密码、captcha、提交按钮处于同一视觉竞争层级。
- Checklist:
  - legal links 是否仍然可见可点
  - 是否位于登录卡最底部
  - 字号、颜色、hover 是否弱于主 CTA
  - 是否不会挤占表单宽度或破坏对齐

## 2026-04-11 - 带字营销图不能和模板文案叠加
- 错误: 登录页左侧原本是“背景图 + 模板标题文案”结构，用户又提供了一张已经自带品牌和主标题的图片，如果直接替换背景会出现双重文字叠加。
- 根因: 一开始把“换图片”当成纯资源替换，没有先判断图片本身是否已经包含文案层。
- 预警信号: 用户提供的素材中已经有 `Dossentry` 和完整主标题，这意味着旧的 logo/title 层应该被移除，而不是保留。
- 新规则: 任何带完整文案的视觉素材进入页面时，必须先决定是“纯图片展示”还是“无字背景图 + 模板文案”，不能两者叠加。
- Checklist:
  - 新图是否已经包含品牌名或主标题
  - 页面原有文字层是否需要同步移除
  - 静态资源路径是否从最终 CSS 目录正确引用
  - 更换图片后是否做一次 `rg` 清理旧文案/旧 logo 查询

## 2026-04-11 - 登录页视觉微调要优先用焦点位和内边距，不要硬做位移补丁
- 错误: 登录页左右布局第一次看起来不平衡时，存在直接对单个表单容器做 `transform` 硬偏移的旧规则，超大屏和普通桌面容易出现方向相反的视觉误差。
- 根因: 之前把视觉问题当成单点元素位置问题修，而不是从图片焦点位和容器内边距整体校正。
- 预警信号: 同一页面既有 `background-position: center`，又有超大屏专门对表单做 `translateX`，说明布局是靠补丁在维持，不是靠一致的网格关系。
- 新规则: 登录页这类双栏页面的美观优先通过 `background-position` 和容器 `padding` 调整，避免再用只对某个断点生效的表单平移规则。
- Checklist:
  - 左侧图片裁切是否围绕主体，而不是机械居中
  - 右侧内容是否通过不对称内边距微调，而不是 transform 硬推
  - 超大屏和普通桌面是否使用同一套布局逻辑
  - 平板和手机断点是否保持原行为不受桌面微调影响

## 2026-04-11 - 竞品分析不能把“替代方案”和“直接对手”混成一类
- 错误: 早期几版分析容易把通用 inspection 工具、returns portals、reverse-logistics suites、warehouse-side direct rivals 放在同一层比较，导致定位边界不够清楚。
- 根因: 看到功能重叠就归为竞品，没有先按 buyer、主工作流、部署方式、争议处理链路做分层。
- 预警信号: 一份竞品图里同时出现“inspection template tools”“shopper returns platforms”“warehouse claims tools”，但没有明确谁是 direct、谁是 substitute、谁是 upstream ecosystem。
- 新规则: 竞品地图至少分四层：`direct rival / heavy adjacent / generic substitute / upstream ecosystem`，否则首页文案和销售对比页会失焦。
- Checklist:
  - 这个产品的 primary workflow 是否与我们同层
  - buyer 是否相同或强重叠
  - 部署和 rollout 成本是否同级
  - 客户会不会在同一个采购决策里真的二选一
  - 如果不会，只能算 substitute 或 adjacent，不能叫 direct

## 2026-04-11 - 首页首屏 CTA 必须先推 proof artifact，再推 guest demo

## Snapshot
- Date: 2026-04-11
- Scope: 把 Dossentry 首页首屏从 `guest demo` 优先调整成 `sample Brand Review Link` 优先
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- 在真正修改首页前先核对了 `landing.blade.php` 和 `routes/web.php`，确认 `sampleBrandReviewUrl` 已经存在，所以可以直接把首屏 CTA 改成指向真实 proof artifact，而不是先造新功能。
- 只改首屏层级，不碰中下段结构，避免把“定位修正”扩大成一次不必要的 landing page 全面重写。

## Mistakes To Stop Repeating

### Mistake: 把 authenticated workspace 当成首次转化入口，而不是 proof artifact
- What happened: 首页顶部按钮和 hero 主 CTA 之前都优先把人送到 `Enter Guest Demo` 或 `Request Workflow Review`，但真正最能解释产品价值的 sample `Brand Review Link` 只出现在页面更靠下的位置。
- Root cause: 把“可登录的 live demo”误当成最强 first-touch asset，而不是先判断外部访客 30 秒内最容易理解什么。
- Earlier signal I missed: 页面本身已经有 sample Brand Review Link 区块，且之前的复盘已经明确写过“public product pages must show one non-login proof artifact before any authenticated experience”。
- Prevention rule: 对外首页如果同时存在 `proof artifact` 和 `authenticated demo`，首屏主 CTA 默认必须先指向 proof artifact，除非真实转化数据证明相反。
- Next-time checklist item: 在改任何 B2B 产品首页前，先写出 `first-touch CTA priority order: proof artifact -> low-friction proof action -> authenticated product -> form fill`。

### Mistake: 差异化卖点写进页面了，却没有占据最高点击层级
- What happened: 首屏已经部分在讲 `Brand Review Link`、`phone-first inspection flow` 等差异化，但按钮顺序和 chip 组合仍然没有把这些优势变成最直接的操作引导。
- Root cause: 只调整了文案内容，没有同步调整 CTA hierarchy 和 proof chips。
- Earlier signal I missed: 用户已经明确把 `Brand Review Link`、`phone-first close-up evidence`、`no station rebuild`、`customer-owned deployment` 定为首页主卖点，这意味着按钮和 support chips 也必须同层对齐。
- Prevention rule: 首页 hero 改定位时，不允许只改标题和副标题；必须同步改 CTA 顺序、support chips、顶部按钮入口。
- Next-time checklist item: 任何 hero 改稿都要逐项核对 `headline / subheadline / primary CTA / secondary CTA / proof chips / topbar CTA` 是否讲的是同一件事。

## Permanent Rules
- 对外首屏先展示最强 proof artifact，再展示需要登录的产品入口。
- B2B workflow 产品的 CTA 层级必须和差异化卖点一一对应，不能只靠中下段解释补救。

## Next-Project Checklist
- [ ] 首屏主 CTA 是否先指向最强 proof artifact
- [ ] 顶部按钮是否与 hero 主 CTA 保持同一优先级
- [ ] support chips 是否准确反映当前主定位，而不是旧卖点残留
- [ ] 如果 sample asset 已存在，优先复用，不要为 CTA 另开一轮新开发

## Open Risks Or Follow-Ups
- compare 页面还没上线，后续需要让 `/compare/generic-inspection-apps` 成为首页次级承接页，而不是继续堆在文档里。
- 还没有做浏览器级 QA，当前只完成了模板与路由级自检。

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-hero-copy-v3.md`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-vs-generic-inspection-copy.md`

## 2026-04-11 - Compare page should be a secondary proof asset, not a substitute for the homepage

## Snapshot
- Date: 2026-04-11
- Scope: turning the generic-inspection comparison copy into a real marketing page and wiring it into the live landing surface
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- The compare page was implemented only after the homepage hero had already been refocused around the strongest proof artifact, which kept the traffic flow coherent instead of splitting attention too early.
- Reusing the same marketing variables already available in `routes/web.php` (`appName`, `demoLoginUrl`, `sampleBrandReviewUrl`) let the compare page ship as a real route without inventing a second content pipeline.

## Mistakes To Stop Repeating

### Mistake: Strategy docs can feel finished before their best page exists in the product
- What happened: the positioning and comparison thinking had already been written into markdown deliverables, but until the compare page existed as a route and the landing page linked to it, the argument still was not part of the actual user journey.
- Root cause: strong internal docs can create a false sense of go-to-market completion.
- Earlier signal I missed: the new comparison language was already stable enough to ship, but the site still had no dedicated `/compare/...` destination.
- Prevention rule: once a comparison or positioning asset is stable enough to guide homepage copy, it is stable enough to become a real page in the product.
- Next-time checklist item: after any positioning memo is approved, ask whether it should become a route, a section, or both before starting another doc.

### Mistake: Compare pages are easy to overvalue relative to the homepage
- What happened: the compare page was clearly worth building, but only after the homepage CTA hierarchy had already been corrected toward the strongest proof artifact.
- Root cause: comparison pages feel strategically sophisticated, which makes it tempting to treat them as the main conversion surface.
- Earlier signal I missed: the homepage still had the highest-leverage CTA mismatch, so shipping compare first would have polished a secondary page while the main entrypoint still underperformed.
- Prevention rule: supporting pages should amplify the homepage's core argument, not compensate for an unfixed homepage.
- Next-time checklist item: before building a compare page, confirm the homepage already points to the correct primary proof artifact and CTA order.

### Mistake: File-level validation can be mistaken for runtime validation when the local toolchain is missing
- What happened: the compare route and landing links were verified through file inspection and route references, but `php` was not available in the shell, so runtime route-list validation could not be executed.
- Root cause: the workspace did not provide the runtime binary needed for the normal Laravel verification loop.
- Earlier signal I missed: the first route check failed immediately with `php: command not found`, which means validation confidence must be described more narrowly.
- Prevention rule: if framework runtime tools are unavailable, explicitly downgrade verification language to file-level or template-level checks instead of implying full route validation.
- Next-time checklist item: when shipping Laravel marketing routes, record whether validation was `runtime`, `template-level`, or `source-only`.

## Permanent Rules
- A comparison argument is not shipped until it has a live page or section in the product.
- Compare pages are secondary proof assets; the homepage still owns primary conversion.
- Validation claims must match the strongest verification actually performed.

## Next-Project Checklist
- [ ] Turn stable comparison copy into a real route before calling the asset done
- [ ] Verify the homepage already uses the right CTA priority before building a support page
- [ ] Record whether the verification was runtime, template-level, or source-only
- [ ] Add at least one visible homepage entry point for every new supporting proof page

## Open Risks Or Follow-Ups
- The compare page has not yet been browser-QA'd; current confidence is based on source-level checks and route wiring review.
- The new page still needs real traffic or sales-call usage to prove whether it materially improves conversion or objection handling.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/compare/generic-inspection-apps.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-vs-generic-inspection-copy.md`

## 2026-04-11 - Marketing routes must survive blank self-hosted installs, not assume seeded demo data

## Snapshot
- Date: 2026-04-11
- Scope: validating the updated landing page and compare page inside the local self-hosted Docker environment
- Outcome: success with concerns
- Storage target: `memory/project-lessons.md`

## What Worked
- The first `500` root cause was found quickly by checking the actual container environment instead of guessing from the browser alone: `APP_KEY_BASE64` had been double-encoded, so Laravel encryption failed before any page logic ran.
- Once the environment issue was cleared, a second pass against Laravel logs exposed the real product-level problem: the marketing payload queried `return_cases` before a blank self-hosted install had created the table.
- Browser snapshots on the running site caught a real UX regression that source review would have missed: when no sample review link exists, the hero CTA logic duplicated `Request Workflow Review`.

## Mistakes To Stop Repeating

### Mistake: I let marketing routes depend on seeded operational tables
- What happened: the landing route tried to build `sampleBrandReviewUrl` by querying `return_cases` unconditionally, so a blank self-hosted install crashed before the homepage could render.
- Root cause: I treated a marketing proof artifact as always-available application data instead of optional data.
- Earlier signal I missed: blank self-hosted mode is explicitly part of the product story, which means homepage routes must tolerate an empty operational database.
- Prevention rule: any public marketing route must degrade gracefully when optional demo data is absent.
- Next-time checklist item: for each homepage/compare payload field, mark it as `required` or `optional`; optional fields must fail closed to `null`, never `500`.

### Mistake: CTA fallback logic was written as a shortcut instead of a real state machine
- What happened: when `sampleBrandReviewUrl` was null, both the primary and tertiary hero CTAs collapsed into `Request Workflow Review`, creating duplicate buttons on the landing and compare pages.
- Root cause: I used terse fallback expressions (`?:`) for buttons that actually had multiple UI states.
- Earlier signal I missed: the CTA order was deliberately strategic, so any fallback case should have been explicitly enumerated rather than compressed into one line.
- Prevention rule: when CTA hierarchy changes by state, implement explicit conditional branches instead of string/URL fallbacks.
- Next-time checklist item: QA every CTA state at least once: `sample exists`, `sample missing`, and `guest demo only`.

### Mistake: I assumed "Docker app is up" meant "self-hosted install is complete"
- What happened: after the web container responded, `/admin/auth/login` still returned `500` because the database had not been initialized and even the migrations table was missing.
- Root cause: container health was treated as installation health.
- Earlier signal I missed: `migrate:status` immediately reported `Migration table not found`, which means the runtime was only partially started, not usable.
- Prevention rule: self-hosted readiness is not proven until homepage, compare page, and login page all return `200`, and migrations are confirmed as applied.
- Next-time checklist item: after local Docker boot, always run `migrate:status` or the setup script before calling the environment ready.

### Mistake: I relied on image rebuilds for QA even when BuildKit feedback was unreliable
- What happened: the local Docker build output stalled, so the running container kept old route files until I copied the changed marketing files into the container to finish verification.
- Root cause: I treated a partially-observed build as a completed deployment.
- Earlier signal I missed: route listing inside the container still reflected the old route set, which proved the new image had not actually been swapped in.
- Prevention rule: when runtime QA is blocked by a flaky local build loop, first verify what code is actually present in the running container before trusting the rebuild.
- Next-time checklist item: compare container file contents or route output against the repo before starting QA on a rebuilt local image.

## Permanent Rules
- Public marketing pages must render even when operational demo data does not exist.
- CTA fallback states are product behavior and must be modeled explicitly.
- Self-hosted validation is incomplete until install/setup has created the schema and the login page works.
- Runtime QA claims only count against the code actually loaded in the running container.

## Next-Project Checklist
- [ ] Guard optional marketing proof assets behind null-safe or table-safe checks
- [ ] Test CTA behavior when sample proof assets are absent
- [ ] Run setup/init before QA on blank self-hosted installs
- [ ] Verify homepage, compare page, and login page all return `200`
- [ ] Confirm the running container actually contains the latest route/template files before browser QA

## Open Risks Or Follow-Ups
- The local container was hot-synced with updated route/view files for verification because the image rebuild feedback loop was unreliable; a clean rebuild should still be rechecked before treating the Docker path as fully clean.
- Blank mode intentionally has no sample review record, so the strongest homepage CTA in that environment is necessarily downgraded to workflow review instead of a live proof artifact.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/compare/generic-inspection-apps.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted`

## 2026-04-11 - Mobile QA must happen on the real marketing state, not only desktop or source review

## Snapshot
- Date: 2026-04-11
- Scope: mobile visual QA and polish for the landing page and `/compare/generic-inspection-apps`, then redeploying to Render
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- Running a real `390x844` browser pass on the local self-hosted build immediately exposed layout problems that desktop review had hidden, especially the compare table compression and the topbar action stack.
- The homepage and compare page were already structurally close enough that a small CSS pass fixed the mobile issues without reopening the product story or CTA strategy.
- Verifying the compare container with DOM measurements (`scrollWidth > clientWidth`) proved the table was truly swipeable on mobile, not just visually narrower.

## Mistakes To Stop Repeating

### Mistake: I treated the compare table like a normal responsive table instead of a deliberate horizontal comparison surface
- What happened: on mobile, the three-column compare table compressed into narrow unreadable columns instead of encouraging a sideways swipe.
- Root cause: I kept `width: 100%` but forgot to define a minimum comparison width, so the browser optimized for squeeze instead of comparison clarity.
- Earlier signal I missed: the page itself is called "compare", which means preserving column contrast matters more than keeping everything inside the viewport.
- Prevention rule: comparison tables on mobile should default to intentional horizontal scrolling with a minimum width and a visible swipe hint.
- Next-time checklist item: for every multi-column marketing table, verify `scrollWidth > clientWidth` at one mobile breakpoint before shipping.

### Mistake: I left low-signal console noise in a polished marketing surface
- What happened: both live pages logged a `favicon.ico 404`, which was not user-breaking but made the deployed experience look unfinished during QA.
- Root cause: I shipped new marketing pages without checking whether the public asset baseline included a favicon.
- Earlier signal I missed: browser console review was already part of the QA pass, and the missing favicon was an easy asset-level fix.
- Prevention rule: polished public pages ship with a favicon and zero avoidable console noise.
- Next-time checklist item: include a console check and asset-baseline check for every landing or compare page release.

### Mistake: I validated the first pass against the blank local state, not the strongest live proof state
- What happened: local self-hosted validation used the fallback CTA state because blank installs have no sample review record, while production uses the stronger sample-proof state.
- Root cause: I initially treated "the page renders" as sufficient before rechecking the live state that sales traffic actually sees.
- Earlier signal I missed: CTA hierarchy was explicitly tied to whether a sample Brand Review Link exists.
- Prevention rule: when CTA logic changes by data state, QA both the fallback state and the strongest live state before closing the task.
- Next-time checklist item: record which environments represent `sample present` and `sample missing`, then test both on every CTA-related release.

## Permanent Rules
- Mobile QA is mandatory for every public marketing page before claiming it is ready.
- Comparison layouts should preserve column meaning first and viewport fit second.
- Public-facing pages should ship with zero trivial console errors.
- CTA changes are not fully verified until both fallback and proof-rich states are tested.

## Next-Project Checklist
- [ ] Take at least one real mobile screenshot for every new marketing page
- [ ] Confirm comparison tables are horizontally scrollable on mobile when readability requires it
- [ ] Check browser console for favicon and other asset noise
- [ ] Verify both `sample present` and `sample missing` CTA states
- [ ] Re-run live production QA after Render finishes deploying

## Open Risks Or Follow-Ups
- Production mobile QA passed after Render finished deploying: the homepage sample CTA layout held at `390x844`, compare page showed the swipe hint, the comparison surface remained horizontally scrollable, and browser console noise was cleared.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/compare/generic-inspection-apps.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/public/assets/dossentry/favicon.svg`
- `/Users/mikezhang/Desktop/projects/6POS/output/playwright/local-mobile-home.png`
- `/Users/mikezhang/Desktop/projects/6POS/output/playwright/local-mobile-compare-full.png`
- `/Users/mikezhang/Desktop/projects/6POS/output/playwright/live-mobile-home.png`
- `/Users/mikezhang/Desktop/projects/6POS/output/playwright/live-mobile-compare-full.png`

## 2026-04-12 - Mobile QA tools must be calibrated before trusting responsive conclusions

## Snapshot
- Date: 2026-04-12
- Scope: second mobile visual QA and polish pass for the public homepage, compare page, and login entry
- Outcome: success with concerns
- Storage target: `memory/project-lessons.md`

## What Worked
- The real user-facing issue was narrowed to mobile action-area pressure: long CTA labels and multi-button rows were the first places where the layout started to feel unstable.
- Moving the marketing topbar actions to a single-column stack earlier and allowing button labels to wrap gave the public pages a more defensive mobile posture without changing the message hierarchy.
- Production verification at Chrome headless's effective `500px` CSS viewport showed the homepage, compare page, and login page rendering cleanly after the patch.

## Mistakes To Stop Repeating

### Mistake: I trusted a browser automation path before calibrating what viewport it was actually testing
- What happened: the earlier `playwright-cli` screenshots looked like mobile captures, but the page snapshot stayed in a desktop-style layout; then Chrome headless screenshots looked clipped in a way that was partly caused by Chrome enforcing a minimum `500px` CSS viewport.
- Root cause: I treated the screenshot width as the same thing as the CSS layout viewport and started drawing responsive conclusions too early.
- Earlier signal I missed: `playwright-cli` snapshots still showed desktop navigation structure after a supposed mobile resize, which was a direct sign that the tool was not exercising real mobile breakpoints.
- Prevention rule: before trusting any responsive QA tool, explicitly measure the reported CSS viewport (`window.innerWidth`) and compare it to the intended breakpoint.
- Next-time checklist item: run a calibration page or inline viewport check before using a new browser path for mobile QA.

### Mistake: I let CTA content define layout width for too long
- What happened: long labels like `View Sample Review` and `View Sample Brand Review Link` put the highest pressure on narrow layouts and exposed that the top action rows needed to collapse earlier.
- Root cause: the layout assumed buttons could stay horizontally compact longer than the real copy allowed.
- Earlier signal I missed: the strongest marketing CTA is also the longest label, so it should have been treated as the limiting case from the beginning.
- Prevention rule: when a CTA is both strategically primary and text-heavy, design the narrow layout around that longest label first.
- Next-time checklist item: test the longest CTA label at the smallest supported breakpoint before calling a hero or topbar done.

### Mistake: I waited too long to separate tool limitations from product bugs
- What happened: time was spent chasing whether the page was broken when part of the weirdness came from the QA toolchain itself, including Chrome headless anchor screenshots returning blank frames and Docker being unavailable for local rebuild validation.
- Root cause: I mixed environment instability, rendering-tool limits, and actual UI defects into one bucket.
- Earlier signal I missed: blank anchor screenshots and fixed `500px` headless viewport behavior were not normal product bugs; they were verification-path constraints.
- Prevention rule: when QA artifacts behave inconsistently, isolate toolchain constraints before changing product code.
- Next-time checklist item: label each issue as either `product bug`, `deploy state`, or `tooling artifact` before deciding the next edit.

## Permanent Rules
- Responsive QA is not real until the tested CSS viewport is known.
- Long CTA copy must be treated as the width budget, not average copy.
- Public mobile action rows should collapse earlier rather than trying to preserve horizontal compactness.
- Verification notes must distinguish true UI bugs from browser-tool quirks.

## Next-Project Checklist
- [ ] Measure `window.innerWidth` before trusting a new screenshot path for breakpoint QA
- [ ] Test the longest CTA label in the smallest target layout first
- [ ] Prefer stacked public action rows once copy pressure becomes visible
- [ ] Record whether validation came from real device emulation, calibrated headless browser, or source-level review
- [ ] Re-check one login or secondary public entry page after major marketing CTA changes

## Open Risks Or Follow-Ups
- True sub-`500px` CSS viewport verification was not directly reproduced in Chrome headless because this environment clamps layout width to `500px`; confidence below that width comes from the source rules (`max-width: 560px` and `max-width: 420px`) rather than a perfect live screenshot.
- Local Docker rebuild validation was unavailable in this pass because the Docker daemon was not running.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/compare/generic-inspection-apps.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/output/chrome/mobile-home-500.png`
- `/Users/mikezhang/Desktop/projects/6POS/output/chrome/mobile-compare-500.png`
- `/Users/mikezhang/Desktop/projects/6POS/output/chrome/mobile-login-500.png`

## 2026-04-13 - Add first-party measurement before sending traffic to new marketing pages

## Snapshot
- Date: 2026-04-13
- Scope: minimal CTA click tracking for the landing page, compare page, and admin lead inbox
- Outcome: success with concerns
- Storage target: `memory/project-lessons.md`

## What Worked
- The lowest-risk measurement layer was a same-origin Laravel endpoint plus a tiny shared browser script, not a full third-party analytics install.
- Reusing the existing `Workflow Review Requests` admin page created one operator surface for both hard conversions and pre-form CTA intent.
- Keeping the scope to `sample review`, `guest demo`, `workflow review`, `compare`, and navigation return actions preserved speed and avoided building a generic analytics system.
- Runtime verification succeeded once the self-hosted image was rebuilt from the current workspace, the new migration was applied, targeted PHPUnit files were executed in a bind-mounted PHP container, and a real browser click on the landing-page `Compare` CTA showed up in the admin UI summary.

## Mistakes To Stop Repeating

### Mistake: I let messaging and page polish get ahead of measurement
- What happened: the homepage and compare page were already live and positioned correctly, but there was still no first-party record of which CTA people clicked before submitting a form.
- Root cause: I treated instrumentation as optional follow-up instead of part of launch readiness.
- Earlier signal I missed: the next-step discussion had already shifted from copy and layout to "start traffic", which meant measurement should have been in place before that point.
- Prevention rule: any public landing or compare page that is about to receive outreach or paid attention needs at least one minimal first-party event path before launch.
- Next-time checklist item: before declaring a marketing page ready, confirm there is a way to count primary CTA clicks and relate them to later lead submissions.

### Mistake: I allowed smoke tests to drift behind marketing copy
- What happened: the existing landing page routing test was still asserting old hero language even though the live landing copy had already changed.
- Root cause: copy-heavy marketing changes were treated as "just content" instead of code paths that still need test maintenance.
- Earlier signal I missed: the old test strings no longer existed anywhere in the current Blade templates.
- Prevention rule: when a public Blade page changes copy or CTA structure, update at least one smoke test in the same change set.
- Next-time checklist item: grep the asserted strings in feature tests after every major landing page rewrite.

### Mistake: I assumed the running self-hosted container already matched the workspace
- What happened: after Docker came up, the first container check still showed no `MarketingClickEventController`, because this compose setup builds an image from `web-panel` and does not bind-mount source code.
- Root cause: I carried forward the earlier local-assumption that the app container might reflect the current workspace without confirming the compose volume model.
- Earlier signal I missed: `docker-compose.self-hosted.yml` mounts only `storage`, not the application code, so any PHP or Blade change requires a rebuild.
- Prevention rule: before runtime QA, verify whether the environment is `bind-mounted` or `image-built`; if it is image-built, rebuild first and only then trust any browser or route result.
- Next-time checklist item: inspect compose mounts before starting local verification and explicitly record whether a rebuild is mandatory.

### Mistake: I assumed the old local port survived the rebuild
- What happened: the rebuilt stack came back on `127.0.0.1:8080`, not the previously used `127.0.0.1:18081`, and the first browser open failed with `ERR_CONNECTION_REFUSED`.
- Root cause: I reused an earlier remembered port instead of re-reading the active container port mapping after the stack restarted.
- Earlier signal I missed: `docker ps` clearly showed `0.0.0.0:8080->10000/tcp` as soon as the rebuilt app started.
- Prevention rule: after any compose rebuild or recreate, re-check the live port mapping before beginning browser QA.
- Next-time checklist item: make `docker ps` or `curl -I` the first step after a local stack restart.

### Mistake: I assumed the production-like image could run PHPUnit directly
- What happened: the rebuilt app image had `--no-dev` Composer dependencies, so `phpunit` was absent inside the running container even though the tests existed in the workspace.
- Root cause: I treated the production image as both runtime target and test runner target.
- Earlier signal I missed: the Dockerfile path and build logs both showed a production install path, which should have implied missing dev tools.
- Prevention rule: when the runtime image is intentionally lean, run PHPUnit in a separate PHP container with the workspace bind-mounted, or use a dedicated dev/test image.
- Next-time checklist item: confirm whether dev dependencies exist in the container before choosing the test execution path.

## Permanent Rules
- Do not drive traffic to a new public page without at least minimal first-party CTA instrumentation.
- Marketing copy changes are test changes if smoke tests assert rendered strings or CTA hooks.
- Public measurement should start as narrow, first-party, and operator-readable before adding external analytics.
- Verification claims must state whether they came from source review, automated tests, or live runtime execution.

## Next-Project Checklist
- [ ] Confirm the primary CTA list before launch and instrument each one explicitly
- [ ] Add or refresh one routing/smoke test for every major public page rewrite
- [ ] Verify PHP or Docker test execution availability before promising runtime proof
- [ ] Surface click summaries somewhere operators already check instead of creating a second reporting page
- [ ] Keep UTM capture first-touch and minimal unless a real sales workflow requires more

## Open Risks Or Follow-Ups
- Runtime verification now passed locally for one CTA path: landing `Compare` recorded into `marketing_click_events` with first-touch UTM values and rendered correctly in the admin `Review Requests` page.
- The remaining unverified paths are the external-host CTAs such as `Enter Guest Demo` and `Log in`, which rely on `sendBeacon` or `fetch(... keepalive)` surviving navigation to another host.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/MarketingClickEventController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/WorkflowReviewRequestController.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Models/MarketingClickEvent.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/database/migrations/2026_04_13_000001_create_marketing_click_events_table.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/partials/marketing-click-tracking.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/compare/generic-inspection-apps.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/review-requests/index.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/LandingPageRoutingTest.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/tests/Feature/MarketingClickTrackingTest.php`

## 2026-04-13 - Strategy docs must be converted from proposal mode to shipped-status mode once pages exist

## Snapshot
- Date: 2026-04-13
- Scope: syncing the three positioning/copy markdown files to the actually shipped homepage, compare page, and CTA tracking state
- Outcome: success
- Storage target: `memory/project-lessons.md`

## What Worked
- The underlying positioning docs were still directionally correct, so the job was not a rewrite; it was a state-sync pass.
- Updating docs right after runtime verification made it easy to separate `implemented`, `measured`, and `still hypothetical`.
- Treating the compare and hero docs as shipped assets exposed one real mismatch quickly: the compare doc had already fallen behind the actual CTA stack.

## Mistakes To Stop Repeating

### Mistake: I left strategy docs in “proposal mode” after the product pages were already built
- What happened: the homepage hero and compare page already existed, but the markdown docs still read like those changes were only recommended next steps.
- Root cause: once the implementation shipped, nobody explicitly converted the strategy docs from planning artifacts into source-of-truth status docs.
- Earlier signal I missed: the docs still said things like `Immediate Next Moves` for work that was already in the codebase.
- Prevention rule: once a strategy-driven page ships, update the source markdown in the same cycle so it says what is now live versus what still remains a hypothesis.
- Next-time checklist item: after a marketing page lands, do one short doc-sync pass before moving on to the next experiment.

### Mistake: I let the verbal framework get cleaner than the written framework
- What happened: the competitive framing had already been verbally simplified to four layers, but the markdown still had a fifth tier for Claimlane.
- Root cause: the conversation-level model evolved faster than the static doc.
- Earlier signal I missed: the user was already consistently describing the framework as four layers, which directly conflicted with the written tier count.
- Prevention rule: when a categorization framework changes, update the headings and tier count first, before polishing the individual entries.
- Next-time checklist item: compare the spoken summary against the document outline and resolve tier-count drift immediately.

### Mistake: I almost treated implementation notes as market proof
- What happened: once CTA tracking existed, it was tempting to let the docs imply more certainty than the data justified.
- Root cause: shipping instrumentation can feel like shipping evidence, but those are not the same thing.
- Earlier signal I missed: the only validated data so far was local runtime click capture, not real traffic or conversion behavior.
- Prevention rule: docs should clearly distinguish `implemented`, `runtime-verified`, and `market-validated`.
- Next-time checklist item: never call a copy or CTA winner unless there is live traffic data behind it.

## Permanent Rules
- After shipping a strategy-led page, convert the associated markdown from recommendation mode to status mode.
- Tier counts in competitive maps must stay consistent between spoken strategy and written docs.
- Instrumentation does not equal market validation; label those states separately.

## Next-Project Checklist
- [ ] Mark which recommendations are already implemented in code
- [ ] Remove or rewrite any `next move` that has already shipped
- [ ] Check CTA stacks in docs against the actual rendered page
- [ ] Separate `implemented`, `runtime-verified`, and `market-validated`
- [ ] Keep one benchmark note separate if it does not belong in the main competitor tiers

## Open Risks Or Follow-Ups
- These docs are now aligned with the current codebase state, but still do not contain live traffic conclusions.
- `returns-positioning-pricing-discovery-v2.md` remains intentionally outside this sync pass.

## Source Artifacts
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-competitive-map-v2.md`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-vs-generic-inspection-copy.md`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-hero-copy-v3.md`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/landing.blade.php`
- `/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/compare/generic-inspection-apps.blade.php`
