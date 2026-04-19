# Dossentry ICP Screening Standard v1

Last updated: 2026-04-18 America/Los_Angeles

## Goal

Turn the current positioning into a repeatable screen so outbound, demos, and discovery calls focus on the narrowest early-buying ICP:

> **U.S. multi-brand 3PLs that already process returns in-house, visibly inspect or document exception cases, and still need a cleaner way to explain those cases back to brand clients.**

This file is not a market map.
It is the scoring gate for deciding whether an account belongs in the active queue.

## Best-Fit Customer

The best-fit account for the current Dossentry product has most of these traits:

- `3PL / fulfillment operator`, not a software vendor
- serves multiple brands or clearly supports brand-specific workflows
- warehouse staff already inspect, grade, test, re-kit, or route returned items
- the company has to explain return handling back to brand clients
- the current process likely depends on SOPs, folders, spreadsheets, photos, and staff memory
- buyer access is realistic for founder-led outreach

## What Dossentry Actually Solves Today

Use this screen only if the account could plausibly buy the current product:

- phone-first evidence capture
- brand-specific playbooks
- decision queue for exception cases
- Brand Review Link
- Docker self-hosted / customer-owned deployment

If the account mainly wants shopper returns portals, a WMS replacement, or enterprise reverse-logistics orchestration, this is not the right early ICP.

## Hard Exclusion Rules

Skip the account for now if any of these are true:

1. It is primarily a `brand software`, `returns portal`, or `post-purchase platform`
2. It is primarily `freight`, `brokerage`, or `transport`
3. Returns are outsourced away from the warehouse team
4. The company appears to be a heavyweight enterprise reverse-logistics buyer with a long formal procurement motion
5. It is a single-brand operation with no client-facing return explanation problem

## Scoring Model

Score each account from `0-12`.

### 1. Warehouse-Side Returns Ownership (`0-2`)

- `0`: returns are not clearly handled in-house
- `1`: returns handling exists but is broad or vague
- `2`: public evidence of inspection, testing, grading, re-kitting, or next-step routing

### 2. Evidence Burden (`0-2`)

- `0`: no sign of photos, condition checks, serials, or product testing
- `1`: generic QC or returns language only
- `2`: explicit photo, condition, serial, damage, testing, or documentation language

### 3. Client-Facing Exception Pressure (`0-2`)

- `0`: no sign they explain cases back to clients
- `1`: general client-services language
- `2`: public signal that clients are notified, reports are sent, decisions are explained, or next steps are client-driven

### 4. Multi-Brand / Multi-Client Complexity (`0-2`)

- `0`: likely single-brand or unclear
- `1`: multi-channel / broad fulfillment, but weak multi-client signal
- `2`: obvious 3PL or client-services-heavy operator serving multiple brands

### 5. Buyer Accessibility (`0-2`)

- `0`: only generic enterprise intake
- `1`: generic contact path plus plausible role
- `2`: founder, ops leader, or reachable decision-maker is identifiable

### 6. Stack Fit For Current Wedge (`0-2`)

- `0`: looks like they need a full returns suite or WMS overhaul
- `1`: could fit, but broader system needs dominate
- `2`: looks like a lightweight evidence / exception-control layer could slot in beside the current stack

## Tiering Rules

### `Contact Now` (`10-12`)

Use immediate founder-led outreach.

The account strongly fits:

- warehouse-side exception handling
- evidence capture
- client explanation / brand review pressure
- lightweight deployment

### `Active Rotation` (`7-9`)

Relevant, but not first-wave priority.

Use after the first `Contact Now` batch produces reply/click data.

### `Skip For Now` (`0-6`)

Do not spend first-wave outreach on these accounts.

They may still matter later, but they will dilute early message testing.

## Required Public Signals Before Contact

Do not add an account to the active queue unless you can verify at least `3` of these from public sources:

- explicit returns / reverse logistics / return processing language
- inspection, product photos, testing, re-kitting, or condition checks
- multi-brand or client-services-heavy operating model
- an ops/warehouse/client-services decision-maker or at least the exact role to target
- signs of manual or operationally detailed workflow

## Messaging Hook By Fit

### Inspection-Heavy Fit

Lead with:

- close-up evidence
- brand-specific rules
- one review-ready case record

### Client-Services-Heavy Fit

Lead with:

- when a brand questions how a return was handled
- one clean record instead of rebuilding the case
- Brand Review Link

### Self-Hosted / Security-Sensitive Fit

Lead with:

- Docker self-hosted
- customer-owned data
- customer-managed accounts

## Discovery Disqualifiers

Even if a company looked good from public research, downgrade after the first reply or call if they say:

- they only care about shopper return portals
- they already run a heavyweight reverse-logistics platform and want deep suite replacement
- they do not inspect returns in-house
- brand-specific rules are not a real pain
- they do not need to explain return outcomes back to clients

## Near-Term Success Metric

This screen is working only if the first-wave accounts produce:

- replies
- clicks on the compare page
- workflow review requests
- calls where the buyer recognizes the disputed-return explanation problem

If that does not happen, update the scoring model before broadening the list.

## Source Files

- `/Users/mikezhang/Desktop/projects/6POS/dossentry-current-product-overview-2026-04.md`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-real-target-accounts-batch-1-2026-04.md`
- `/Users/mikezhang/Desktop/projects/6POS/dossentry-narrow-icp-reprioritization-2026-04.md`
- `/Users/mikezhang/Desktop/projects/6POS/returns-positioning-pricing-discovery-v2.md`
