# Dossentry Self-Hosted Setup Proposal Template

Last updated: 2026-04-17 America/Los_Angeles

Use this as a customer-facing proposal template for a paid Docker self-hosted deployment.

Replace:

- legal entity name
- customer name
- dates
- pricing
- scope counts
- mailing address

This is a commercial template, not legal advice.

## Cover

**Proposal Title:** Dossentry Self-Hosted Deployment and Guided Onboarding  
**Prepared For:** `{{Customer Company}}`  
**Prepared By:** `{{Your Legal Entity Name}} d/b/a Dossentry`  
**Date:** `{{Date}}`

## 1. Executive Summary

This proposal covers a guided self-hosted deployment of Dossentry for `{{Customer Company}}`.

Dossentry is designed for the warehouse-side portion of return exception handling:

- capturing inspection evidence
- standardizing brand-specific rules
- recording case history and recommendations
- supporting multi-user warehouse workflows in a browser

This proposal is for a **customer-controlled Docker deployment**, not a hosted shared demo and not a full WMS replacement.

## 2. Recommended Use Case

This deployment is a good fit if `{{Customer Company}}` needs:

- a browser-based warehouse workflow
- customer-owned infrastructure for case data and uploaded evidence
- multi-user access for owner, ops, and inspection roles
- a narrower control layer for returns exceptions rather than a full shopper-facing returns platform

## 3. What This Project Includes

The recommended initial scope is:

- `1` self-hosted workspace
- `1` warehouse environment
- Docker-based deployment on customer-controlled infrastructure
- initial owner account creation
- optional initial ops and inspector accounts
- guided install session
- first-login smoke test
- post-login admin handoff
- workspace access walkthrough
- initial onboarding for up to `{{3-5}}` brand rule profiles if customer SOP materials are available

## 4. What This Project Does Not Include

Unless explicitly added in writing, this proposal does not include:

- WMS replacement
- shopper-facing return portal replacement
- label generation or exchange flows
- ERP or WMS integration buildout
- custom single sign-on
- multi-site enterprise rollout
- automated upgrade channel
- self-service installer
- guaranteed `24/7` support
- on-prem network or server administration outside the Dossentry package itself

## 5. Delivery Approach

The recommended delivery model is:

1. scope confirmation
2. customer env and credential collection
3. guided Docker install
4. bootstrap of a `blank` production workspace
5. owner login and smoke test
6. workspace access and admin handoff
7. optional initial brand-rule onboarding

## 6. Responsibilities Split

### Dossentry Responsibilities

- provide the Docker self-hosted package
- guide the initial installation
- help validate the environment variables used for setup
- run the initial bootstrap and acceptance checks
- provide a handoff checklist and guided-install SOP
- provide remote assistance during the agreed setup window

### Customer Responsibilities

- provide the host or VM
- provide Docker-capable infrastructure
- provide domain, DNS, and HTTPS setup where required
- provide SMTP credentials if email delivery is needed
- provide admin emails and secure passwords
- provide any brand SOP or playbook material that should be configured during onboarding
- maintain backups of customer-owned infrastructure and storage

## 7. Acceptance Criteria

The deployment will be treated as complete when all of these pass:

- the homepage loads
- `/admin/auth/login` loads
- the customer owner account can sign in
- the owner reaches the workspace
- `Settings -> Workspace Access` is accessible
- the agreed initial accounts are present

If brand-rule onboarding is included, completion also requires:

- the agreed number of initial brand rule profiles have been created from customer-provided material

## 8. Timeline

Recommended initial timeline:

- preflight and env review: `1-2 business days`
- guided install session: `60-90 minutes`
- initial handoff and corrections: `1-3 business days`

If customer materials are ready, a standard first deployment can usually be completed within `{{3-5}}` business days from kickoff.

## 9. Commercial Structure

### Recommended Founding Package

- one-time guided setup fee: `{{USD 1,500}}`
- initial install support window: `{{included for 30 days}}`
- optional annual updates and remote support renewal: `{{USD 1,200 / year}}`

### Recommended Payment Schedule

- `50%` due at kickoff
- `50%` due on successful acceptance

This structure is recommended because the current package is a guided deployment, not a zero-touch self-serve product.

## 10. Optional Add-Ons

Optional items can be quoted separately:

- additional warehouse rollout
- additional brand rule profile onboarding
- workflow review workshop
- evidence export or brand-review process training
- extended support window
- custom integration scoping

## 11. Assumptions

This proposal assumes:

- the customer wants a self-hosted deployment
- the customer can provide a Docker-capable environment
- the customer will use a `blank` production workspace rather than a demo workspace
- the customer understands that this product is not a full system-of-record for inventory reconciliation

## 12. Next Step

If `{{Customer Company}}` wants to proceed:

1. approve commercial scope
2. confirm deployment contact
3. complete the env checklist
4. schedule the guided install session

## 13. Signature Block

Accepted by:

**Customer:** `{{Customer Company}}`  
Name: `{{Name}}`  
Title: `{{Title}}`  
Date: `{{Date}}`

**Vendor:** `{{Your Legal Entity Name}} d/b/a Dossentry`  
Name: `{{Name}}`  
Title: `{{Title}}`  
Date: `{{Date}}`
