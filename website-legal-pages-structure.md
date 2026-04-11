# Dossentry Website Legal Pages Structure

Last updated: 2026-04-11  
Purpose: define the minimum legal pages Dossentry should publish before public promotion and first paid customer conversations.  
This is a structure document, not legal advice.

## Goal

Publish the minimum website legal pages needed to:

- support public promotion
- support the landing page lead form
- support demo usage
- support early SaaS sales conversations

## Pages To Publish

| Page | Publish before public promotion? | Publish before first paid customer? | Why |
|---|---|---|---|
| Privacy Policy | Yes | Yes | Required practical disclosure for a site collecting personal information |
| Terms of Service | Yes | Yes | Sets website/demo usage rules and liability boundaries |
| Security & Data Handling | Recommended | Yes | Helps answer buyer questions fast |
| SaaS Agreement / MSA | No | Yes | Contract for paid customer use |
| DPA / Data Processing Addendum | No | Recommended | Needed if customer asks about processing obligations |

---

## 1. Privacy Policy

### Required footer label

`Privacy Policy`

### Where it should appear

- website footer
- landing page footer
- demo login page footer
- any workflow review form page

### Core sections

1. **Who operates the service**
   - legal company name
   - trade name `Dossentry`
   - contact email

2. **What information we collect**
   - full name
   - work email
   - company name
   - role title
   - workflow notes
   - demo login activity
   - optional cookies / analytics if used

3. **How we use the information**
   - respond to workflow review requests
   - operate the hosted demo
   - improve product and support
   - communicate with prospects/customers

4. **How information is shared**
   - service providers
   - legal compliance
   - no sale of data unless that is actually true

5. **Hosted demo vs customer production environments**
   - hosted demo data is separate from formal self-hosted production deployments
   - production customer data can remain in customer-controlled infrastructure

6. **Retention**
   - how long lead form submissions and demo-related records are retained

7. **Security**
   - reasonable safeguards language
   - no absolute guarantee language

8. **Your rights / contact**
   - privacy contact method
   - California-specific rights if applicable

9. **Policy updates**
   - effective date
   - how updates are posted

### Dossentry-specific language to include

- Dossentry offers a hosted evaluation demo and may also be delivered as a self-hosted customer deployment.
- In self-hosted customer deployments, customer case data, uploaded evidence, and staff accounts may remain in customer-controlled infrastructure.
- Optional Pro knowledge workspace can be configured with customer-owned AI API keys.

---

## 2. Terms of Service

### Required footer label

`Terms of Service`

### Where it should appear

- website footer
- landing page footer
- demo login page footer

### Core sections

1. **Acceptance of terms**
   - by accessing website/demo, user agrees

2. **Who may use the service**
   - business/professional use
   - authorized users only

3. **Guest demo rules**
   - shared evaluation workspace
   - sample data only
   - no expectation of persistence
   - no misuse / scraping / attempts to disrupt

4. **No promise that hosted demo equals final production deployment**
   - hosted demo is for evaluation
   - formal production use may be self-hosted

5. **Intellectual property**
   - Dossentry website, software, materials remain owned by company/licensors

6. **Acceptable use**
   - no unlawful use
   - no reverse engineering where prohibited by law/contract
   - no credential abuse
   - no security circumvention

7. **Disclaimers**
   - service is provided as-is for demo/evaluation
   - no guarantee of uninterrupted availability

8. **Limitation of liability**
   - broad website/demo limitation
   - separate paid customer contract can override

9. **Termination / suspension**
   - can suspend demo access or abusive use

10. **Governing law and venue**
   - coordinate with your California legal counsel preference

11. **Contact**
   - company contact email

### Dossentry-specific language to include

- Dossentry's guest demo is a shared evaluation environment and may be reset without notice.
- Customer production environments may be deployed in customer infrastructure under separate commercial terms.

---

## 3. Security & Data Handling Page

### Footer label

`Security`

### Why add it

This page is not always legally required, but it lowers friction with:

- 3PL operators
- ops managers
- procurement
- IT reviewers

### Core sections

1. **Deployment models**
   - hosted demo
   - self-hosted customer deployment

2. **Data ownership**
   - customer owns customer data
   - hosted demo is separate from production self-hosted data

3. **Authentication and account control**
   - customer admins can create/disable staff accounts
   - guest demo is restricted

4. **Photo and evidence handling**
   - where uploaded evidence is stored in hosted demo
   - how self-hosted deployments keep data customer-side

5. **BYOD / shared device position**
   - works on standard phones and shared warehouse devices
   - recommend customer-owned shared devices for formal operations if desired

6. **AI add-on position**
   - optional Pro knowledge workspace
   - customer-owned AI keys
   - no claim that vendor owns customer prompts/data in self-hosted mode

7. **Security contact**
   - reporting address for issues

---

## 4. SaaS Agreement / MSA

This is not a website page.  
This is a sales/legal document for paid customers.

### Minimum sections

1. Parties
2. Order form precedence
3. Licensed scope / permitted use
4. Hosted demo vs self-hosted production scope
5. Customer responsibilities
6. Fees and payment
7. Support scope
8. Customer data ownership
9. Confidentiality
10. Security and privacy
11. Third-party services / BYO AI disclaimer
12. Warranty disclaimer
13. Limitation of liability
14. Term / termination
15. Data export / deletion at end
16. Governing law / venue

### Dossentry-specific clauses to add

- self-hosted deployment responsibility split
- no guarantee for customer-owned infrastructure outside agreed support scope
- optional local knowledge workspace and customer-owned API key responsibility

---

## 5. DPA / Data Processing Addendum

### You may not need to publish this publicly right now

But be ready with a draft if a buyer asks:

- who is controller vs processor
- subprocessors
- security controls
- deletion / return of data
- cross-border handling if relevant

---

## Footer Implementation Plan

### Minimum footer links now

- `Privacy Policy`
- `Terms of Service`
- `Security`
- `Contact`

### Demo login footer

Add the same links on:

- `demo.dossentry.com/admin/auth/login`

Reason:

- users may land there directly without seeing the main site footer

---

## Copy Rules

### Do say

- `Hosted demo`
- `Self-hosted production deployment`
- `Customer-owned infrastructure`
- `Customer-owned AI keys`
- `Shared guest workspace resets regularly`

### Do not say

- `Fully compliant`
- `Guaranteed secure`
- `Guaranteed uptime`
- `No legal risk`
- `We never touch customer data`  
  unless that is literally true across every deployment mode

---

## Publish Order

1. Privacy Policy
2. Terms of Service
3. Security page
4. Contract pack for paid customers

---

## Final Standard

Before public promotion, the website should be able to answer these four questions without a manual email:

1. Who is collecting my information?
2. How is my information used?
3. What rules apply if I use the demo?
4. If I buy, does production data stay with me or with you?
