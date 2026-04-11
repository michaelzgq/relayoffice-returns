# Dossentry Promotion Legal Checklist

Last updated: 2026-04-11  
Status: working checklist, not legal advice

## Goal

Make Dossentry safe enough to promote publicly without pretending the legal stack is "done."  
This checklist is for:

- public landing page launch
- cold email / outbound outreach
- demo access
- first paid customer readiness

## What Is Already True

- You already have a California corporation taxed as an S corporation.
- You already have an EIN.
- You can sign contracts and accept payment in the corporate entity's name.
- You do **not** need to open a second LLC just to sell Dossentry.

## What Is Not Automatically Solved By Having An S-Corp

- Los Angeles local business tax registration accuracy
- trade name / DBA / FBN compliance
- website privacy disclosures
- website terms
- cold email compliance
- paid-customer contract package

---

## Promotion Gate

Use this table as the real go / no-go screen.

| Item | Required before public promotion? | Why it matters | Status to reach |
|---|---|---|---|
| Confirm or update LA BTRC business activity description | Yes | LA requires a Business Tax Registration Certificate for business activity in the city and asks for a description of business activities | `confirmed` or `submitted for update` |
| Determine whether `Dossentry` requires DBA / FBN filing | Yes | If public-facing brand is not the corporation's legal name, Los Angeles County FBN rules may apply | `confirmed not needed` or `filed` |
| Privacy Policy published on website | Yes | California online privacy law requires conspicuous posting if you collect personal information from California residents | `live` |
| Terms of Service published on website | Strong yes | Not the same as the privacy law, but needed to define demo use, service scope, and liability boundaries | `live` |
| Cold email process reviewed against CAN-SPAM | Yes if doing outbound | FTC rules apply to commercial email, including B2B email | `compliant process ready` |
| SaaS Agreement / MSA + Order Form | Before first paid customer | Needed for paid use, data ownership, self-hosted scope, liability limits, termination | `draft ready` |
| DPA / security addendum | Before customer requests it | Useful for any buyer who asks how data is handled, especially because Dossentry touches operational case data | `draft optional` |

---

## Action Checklist

### 1. Los Angeles BTRC

**Objective:** make sure the city tax registration matches what you are actually doing.

Do this:

- Confirm your company already has a valid Los Angeles Business Tax Registration Certificate.
- Confirm the record includes an accurate business activity description.
- If the current description is old or unrelated, submit an information update.

Working description example:

`Software as a Service; Technology Services; self-hosted workflow software`

Notes:

- The BTRC is a tax registration certificate, not a special SaaS operating license.
- Accuracy still matters because the city asks for the description of business activities.

Evidence:

- Los Angeles Office of Finance says businesses conducting activities in the city must obtain a BTRC and provide a description of business activity.  
  Source: [How to Register for a BTRC](https://finance.lacity.gov/index.php/tax-education/new-business-registration/how-register-btrc)
- Los Angeles Office of Finance provides a Taxpayer Information Update Form for updates including DBA and business information changes.  
  Source: [Taxpayer Information Update Form (PDF)](https://finance.lacity.gov/sites/g/files/wph1721/files/2022-12/TAXPAYER%20INFORMATION%20UPDATE%20FORM_Revised%205.2022%20-%20Approved.pdf)

Exit criteria:

- You have either:
  - a screenshot/PDF showing the activity description is already acceptable, or
  - a submitted update confirmation

### 2. Determine Whether `Dossentry` Needs DBA / FBN

**Objective:** confirm whether your public brand can be used under the current legal entity name without a fictitious name filing.

You must answer one question:

**Is `Dossentry` the exact legal name of the corporation?**

- If **yes**, FBN may not be needed for the brand itself.
- If **no**, and you are doing business for profit publicly under `Dossentry`, treat FBN review as a promotion blocker.

What to do:

- Check the corporation's exact legal name on California records.
- If the legal name differs from `Dossentry`, review Los Angeles County FBN filing rules.
- If required, file FBN and track publication requirement.

Evidence:

- LA County FBN rules describe filing requirements and publication requirements.  
  Source: [FBN Requirements](https://www.lavote.gov/home/county-clerk/fictitious-business-names/filing/requirements)  
  Source: [FBN Publication](https://www.lavote.gov/home/county-clerk/fictitious-business-names/publication)
- LA Office of Finance also distinguishes legal business name from fictitious/DBA name in its BTRC guidance.  
  Source: [How to Register for a BTRC](https://finance.lacity.gov/index.php/tax-education/new-business-registration/how-register-btrc)

Exit criteria:

- You have one of:
  - written confirmation from counsel or your own records that no FBN is needed
  - a filed FBN plus publication in progress/completed

### 3. Privacy Policy

**Objective:** comply with California online privacy disclosure rules before asking people to submit names and work emails.

Why this is mandatory:

- Dossentry's landing page collects:
  - full name
  - work email
  - company name
  - role title
  - monthly volume
  - workflow notes

This is enough to trigger privacy disclosure concerns.

California AG guidance says operators of commercial websites collecting personally identifiable information on California consumers must conspicuously post a privacy policy.

Evidence:

- [California AG privacy policy guidance](https://oag.ca.gov/node/36676)
- [CalOPPA summary from California AG](https://oag.ca.gov/node/36743)

Exit criteria:

- Privacy Policy page is live
- Footer links to it
- Landing page and demo login page can reach it

### 4. Terms of Service

**Objective:** define the rules of use for the hosted demo and website before strangers start using it.

This is not the same as the privacy policy.  
This should cover:

- who can use the site
- guest demo usage rules
- no warranty for the demo
- account suspension / misuse
- intellectual property
- acceptable use
- limitation of liability
- governing law / venue

Status:

- Strongly recommended before public promotion
- Required before taking real customer usage seriously

Exit criteria:

- Terms page is live
- Footer links to it
- Demo and landing can reach it

### 5. CAN-SPAM For Outbound

**Objective:** avoid getting sloppy once you start outreach.

FTC guidance is explicit:

- CAN-SPAM covers commercial email
- it applies to B2B too
- sender identity must be accurate
- opt-out must work

Evidence:

- [FTC CAN-SPAM compliance guide](https://www.ftc.gov/business-guidance/resources/can-spam-act-compliance-guide-business)

Minimum outreach rules for Dossentry:

- real sender name and real domain
- no deceptive subject lines
- include physical mailing address
- include a simple opt-out line
- honor opt-out requests

Exit criteria:

- outbound template includes address and opt-out
- outreach list process has suppression handling

### 6. Paid Customer Contract Pack

**Objective:** be ready before the first serious buyer asks for paper.

This is not an official statutory requirement.  
It is a commercial requirement.

Minimum set:

- MSA / SaaS Agreement
- Order Form / Quote
- DPA or security addendum if customer asks

Key clauses:

- service scope
- self-hosted responsibility split
- support scope
- customer data ownership
- confidentiality
- limitations of liability
- no 100% uptime guarantee
- termination and data export
- third-party AI / BYO API disclaimer if Pro add-on is sold

Exit criteria:

- at least one editable contract template exists before first paid deal

---

## S-Corp Ongoing Rules

These are not promotion blockers, but do not ignore them.

- California FTB says S corporations are subject to the annual $800 minimum franchise tax and filing requirements.  
  Source: [FTB S corporations](https://www.ftb.ca.gov/file/business/types/corporations/s-corporations.html)
- IRS says corporate officers are generally employees and raises the reasonable compensation issue.  
  Source: [IRS paying yourself](https://www.irs.gov/businesses/small-businesses-self-employed/paying-yourself)

Operational reminder:

- keep separate business bank account
- keep separate records
- do not mix company and personal revenue/expenses casually
- ask CPA when recurring SaaS income becomes meaningful

---

## Practical Go / No-Go

### You can start limited promotion if:

- BTRC description is confirmed or update submitted
- FBN/DBA question is answered
- Privacy Policy is live
- Terms of Service is live
- outbound templates follow CAN-SPAM

### You should not start broad promotion if:

- you are still unsure whether `Dossentry` requires FBN
- there is no Privacy Policy page
- there is no Terms page
- you plan to cold email without opt-out/address handling

### You can close the first paid deal only if:

- contract pack exists
- pricing and scope are written
- deployment/support responsibility is clear

---

## Source List

- [Los Angeles Office of Finance - How to Register for a BTRC](https://finance.lacity.gov/index.php/tax-education/new-business-registration/how-register-btrc)
- [Los Angeles Office of Finance - Taxpayer Information Update Form (PDF)](https://finance.lacity.gov/sites/g/files/wph1721/files/2022-12/TAXPAYER%20INFORMATION%20UPDATE%20FORM_Revised%205.2022%20-%20Approved.pdf)
- [Los Angeles Office of Finance - Tax Information Booklet](https://finance.lacity.gov/tax-information-booklet)
- [Los Angeles County RR/CC - FBN Requirements](https://www.lavote.gov/home/county-clerk/fictitious-business-names/filing/requirements)
- [Los Angeles County RR/CC - FBN Publication](https://www.lavote.gov/home/county-clerk/fictitious-business-names/publication)
- [Los Angeles County RR/CC - FBN Fees](https://www.lavote.gov/home/county-clerk/fictitious-business-names/fees)
- [California AG - How to Read a Privacy Policy](https://oag.ca.gov/node/36676)
- [California AG - Privacy Legislation Enacted in 2003](https://oag.ca.gov/node/36743)
- [FTC - CAN-SPAM Compliance Guide for Business](https://www.ftc.gov/business-guidance/resources/can-spam-act-compliance-guide-business)
- [California FTB - S corporations](https://www.ftb.ca.gov/file/business/types/corporations/s-corporations.html)
- [IRS - Paying yourself](https://www.irs.gov/businesses/small-businesses-self-employed/paying-yourself)
