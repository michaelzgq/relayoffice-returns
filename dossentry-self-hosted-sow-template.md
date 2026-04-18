# Dossentry Self-Hosted Deployment SOW Template

Last updated: 2026-04-17 America/Los_Angeles

Use this as the working Statement of Work for the first paid self-hosted customer.

This is a commercial operations template, not legal advice.

## Statement Of Work

This Statement of Work (`SOW`) is entered into by and between:

- **Vendor:** `{{Your Legal Entity Name}} d/b/a Dossentry`
- **Customer:** `{{Customer Company}}`

Effective date: `{{Date}}`

This SOW is intended to be used with a separate MSA, services agreement, or signed commercial terms where available.

## 1. Project Name

`{{Customer Company}}` Dossentry Self-Hosted Deployment

## 2. Objective

Vendor will provide a guided Docker-based self-hosted deployment of Dossentry so Customer can operate a customer-controlled workspace for warehouse-side return exception handling.

## 3. In-Scope Services

Vendor will provide:

- initial deployment planning call
- env variable review against the Dossentry Docker handoff checklist
- guided Docker deployment of the Dossentry package
- bootstrap of one `blank` production workspace
- creation and validation of the primary owner account
- optional creation and validation of one ops account and one inspector account if provided in the env
- one acceptance test session
- one admin handoff session
- documentation handoff

If included in the order:

- onboarding of up to `{{3-5}}` brand rule profiles from customer-provided SOP material

## 4. Deliverables

Deliverables for this SOW are:

- a running self-hosted Dossentry workspace in Customer infrastructure
- working owner login
- documented upgrade commands
- documented backup volumes
- workspace access walkthrough for Customer admins
- optional initial brand-rule setup if listed in the order

## 5. Out Of Scope

Unless separately agreed in writing, the following are out of scope:

- custom integration development
- inventory reconciliation implementation
- ERP or WMS connector work
- custom shopper portal flows
- exchange or label generation workflows
- custom mobile app packaging
- SSO / SCIM
- customer server hardening beyond Dossentry package requirements
- enterprise multi-site rollout
- on-site support

## 6. Customer Dependencies

Customer will provide:

- a Docker-capable host or VM
- working network access and DNS where needed
- HTTPS termination or reverse proxy arrangements if needed for production
- admin contact and scheduling availability
- SMTP details if outbound email is required
- final admin email and password values
- any brand SOP material needed for initial playbook configuration

Project timing depends on Customer meeting these dependencies.

## 7. Project Assumptions

This SOW assumes:

- Customer wants a self-hosted deployment
- Customer accepts that Dossentry is not replacing the existing WMS or ERP
- Customer accepts responsibility for customer-controlled infrastructure, backups, and base host security
- Vendor is responsible for Dossentry package guidance and first-time deployment workflow only within the agreed support window

## 8. Acceptance Criteria

The SOW is accepted when:

- homepage is reachable
- `/admin/auth/login` is reachable
- Customer owner credentials authenticate successfully
- Customer owner can access the workspace
- `Settings -> Workspace Access` is accessible
- agreed initial accounts appear in the admin settings screen

If brand-rule onboarding is included:

- the agreed number of initial profiles are created and reviewed with Customer

## 9. Estimated Schedule

Target schedule:

- kickoff and preflight: `{{Date}}`
- installation window: `{{Date}}`
- handoff and acceptance: `{{Date}}`

Typical initial project duration:

- `3-5 business days` from kickoff, assuming Customer dependencies are ready

## 10. Support Window

The initial support window for this SOW is:

- `{{30 calendar days from acceptance}}`

During that window, Vendor will provide reasonable remote support for:

- deployment corrections
- login/path issues related to the Dossentry package
- questions about documented upgrade and backup commands

The support window does not include:

- new feature development
- custom integration work
- ongoing infrastructure management by Vendor

## 11. Fees

Fees are listed in the related Quote / Order Form.

Recommended starting structure:

- one-time guided setup fee
- optional annual support / update renewal

## 12. Change Control

Any change to scope, timeline, integrations, rollout size, or support expectations must be agreed in writing.

Examples that require a change order:

- more warehouses
- additional rule-profile onboarding beyond the original scope
- custom exports or integrations
- custom security or infrastructure requirements

## 13. Responsibility Split

### Vendor

- Dossentry deployment workflow
- package guidance
- initial validation and handoff

### Customer

- infrastructure ownership
- domain and certificate ownership
- backups
- user credential custody
- internal SOP decisions

## 14. Sign-Off

**Customer:** `{{Customer Company}}`  
Name: `{{Name}}`  
Title: `{{Title}}`  
Signature: `{{Signature}}`  
Date: `{{Date}}`

**Vendor:** `{{Your Legal Entity Name}} d/b/a Dossentry`  
Name: `{{Name}}`  
Title: `{{Title}}`  
Signature: `{{Signature}}`  
Date: `{{Date}}`
