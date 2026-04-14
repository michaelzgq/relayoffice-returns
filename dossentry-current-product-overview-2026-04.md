# Dossentry Current Product Overview

Customer Edition
Updated: April 13, 2026

## Executive Summary

Dossentry is a warehouse-side exception-control layer for multi-brand returns.

It is built for the point where a returned item is already back in the warehouse and the team needs to show:

- what brand rule applied
- what evidence was captured
- what condition was observed
- what warehouse action was recommended
- what the brand or operator should review next

Dossentry is not a shopper-facing returns portal and it is not a WMS replacement.

It works alongside the systems a warehouse already uses and adds the part most teams still handle through SOP PDFs, shared folders, and chat threads: brand-specific inspection rules, close-up evidence capture, case review, and one clean external review record.

## Key Customer Outcomes

- Phone-first evidence capture from devices staff already use
- Brand-specific playbooks without relying on memory or scattered SOPs
- One Decision Queue for disputed or high-risk cases
- One Brand Review Link instead of screenshot bundles and email stitching
- Docker self-hosted deployment with customer-controlled data

## What Teams Can Do Today

### 1. Set Client Playbooks

Warehouse or ops teams can create client-specific playbooks that define:

- allowed item conditions
- allowed warehouse actions
- recommended default action by condition
- required photo types
- minimum photo count
- whether notes, SKU, and serial are required
- default decision state

This lets one warehouse run different return standards for different brands without asking inspectors to memorize separate SOPs.

### 2. Run Phone-First Inspection

Inspectors can work from a browser-based inspection flow and capture:

- return ID
- brand / client playbook
- SKU or barcode
- serial number
- condition
- warehouse action
- notes
- required photos

The current flow supports:

- live camera scan on supported mobile browsers
- camera-photo scan fallback for phones where live scan is unreliable
- USB / Bluetooth scanner input
- manual typing when needed

This is built around close-up proof from devices warehouse staff already use on the floor.

### 3. Build A Structured Case Record

Each inspection creates a return case with:

- evidence count
- evidence completeness status
- condition and disposition
- SKU and serial details
- notes
- timestamps
- timeline events
- SLA age

This gives ops leads a single place to review what happened instead of reconstructing the case later.

### 4. Review Cases In Decision Queue

Ops teams can review cases in a dedicated Decision Queue with:

- hold
- ready for brand review
- needs review

The queue supports:

- brand filters
- evidence-missing filters
- SLA-age filters
- case-level review
- bulk decision updates
- audit notes

Cases with incomplete evidence are blocked from moving forward until the required proof is present.

### 5. Send A Brand Review Link

For disputed cases, Dossentry can generate a protected read-only Brand Review Link so a brand or operator can review:

- photos
- timeline
- playbook snapshot
- recommendation
- evidence status

This is the core customer-facing proof asset. Instead of sending screenshots, folders, and Slack context, the warehouse can send one clean review record.

### 6. Export A Brand Defense Pack PDF

Teams can open or download a PDF case pack that combines:

- executive summary
- evidence gallery
- timeline
- decision context
- rule coverage

This is useful for escalations, client communication, and internal review.

### 7. Run Daily Ops From One Board

The Ops Board provides a management view of:

- inspections today
- awaiting decision review
- ready for brand review
- over-48-hour stuck cases
- brands with backlog
- missing evidence
- recent inspections

This lets warehouse operations leads see throughput and bottlenecks without pulling multiple reports.

## The End-To-End Workflow

The current product workflow is:

1. Create a client playbook
2. Inspect the returned item on a phone or browser
3. Capture the required photos, notes, SKU, and serial details
4. Create a structured case record automatically
5. Review the case in Decision Queue
6. Generate a Brand Review Link or PDF when the case needs external review
7. Track backlog and stuck cases from Ops Board

In plain English:

Dossentry helps the warehouse move from "we think we followed the SOP" to "here is the exact case record we can show."

## Why Warehouse Teams Use Dossentry

### Standardize Multi-Brand Rules

Different brands want different evidence and decision logic.

Dossentry turns those expectations into live playbooks so warehouse staff do not have to rely on memory or scattered SOPs.

### Capture The Proof Brands Actually Ask For

Mounted stations and generic inspection apps often stop at wide photos or checklist completion.

Dossentry is designed for close-up proof such as:

- serial labels
- packaging damage
- inside-the-box evidence
- side-angle condition photos

### Reduce Brand Back-And-Forth

When a brand questions how a return was handled, the hard part is not opening the inspection form. The hard part is rebuilding the story.

Dossentry shortens that loop by packaging the case into one review-ready record.

### Improve Evidence Readiness Before Decisions Move Forward

The product does not just store images. It tracks whether evidence is complete enough for the next step.

That helps teams avoid pushing weak or incomplete cases forward.

### Keep Existing Systems

Teams do not need to replace their WMS or shopper-facing returns portal to use Dossentry.

The product is meant to sit beside the existing stack and solve the warehouse-side exception layer.

### Avoid A Station Rebuild

Dossentry is browser-based and phone-first. Teams can start on the devices they already use instead of installing fixed camera stations or rebuilding warehouse inspection hardware.

## Best-Fit Customers

Dossentry is strongest for:

- multi-brand 3PLs
- operators serving multiple DTC brands
- warehouse teams handling high-risk or disputed return cases
- teams that are still managing rules and evidence through SOPs, spreadsheets, folders, or chat threads
- teams that need a cleaner record for brand-side review

## What Dossentry Is Not Trying To Replace

The current product is not positioned as:

- a shopper-facing returns portal
- a shipping label or exchange platform
- a full reverse logistics suite
- a system-of-record inventory reconciliation platform
- a WMS replacement

That boundary is intentional.

The product is strongest when used as the warehouse-side evidence and exception-control layer that sits alongside the core stack.

## Deployment And Access Model

The current product supports:

- browser-based warehouse use
- live web demo access
- Docker self-hosted deployment
- customer-controlled data and staff access

This is useful for operators who want the workflow value without committing to a heavy systems migration.

## Current External Entry Points

Website:

- https://dossentry.com

Compare page:

- https://dossentry.com/compare/generic-inspection-apps

Demo login:

- https://demo.dossentry.com/admin/auth/login
