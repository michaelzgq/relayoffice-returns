# Dossentry Hero Copy v3

最后更新时间：2026-04-13 America/Los_Angeles

## What The Hero Must Do

首页 hero 不该再卖：

- generic scanning
- generic inspections
- another returns app

首页 hero 必须在 3 秒内让人知道：

1. 这是 warehouse-side evidence product
2. 它是给 disputed returns 用的
3. 它不要求 station hardware / rebuild
4. 它不是托管你数据的重型 SaaS

## Current Status

这份 hero recommendation 已经在当前首页实现，不再只是候选 copy。

当前状态：

- 当前首页 headline 已经使用这版主推荐
- 当前 CTA 层级是：
  - Primary: `View Sample Brand Review Link`
  - Secondary: `Enter Guest Demo`
  - Tertiary: `Request Workflow Review`
- 如果当前环境没有 sample Brand Review Link，首页会降级成：
  - Primary: `Request Workflow Review`
  - Secondary: `Enter Guest Demo`
- first-party CTA tracking 已经接到首页，但还没有真实流量样本来判断哪条 CTA 真赢

## Best Current Hero Recommendation

### Headline

`Defensible return evidence, generated on the warehouse floor.`

### Subheadline

`Capture the close-ups, serials, packaging damage, timeline, and recommendation brands actually ask for — then send one clean Brand Review Link instead of reconstructing the case from Slack threads and photo folders.`

### Support Chips

- `No station rebuild`
- `Phone-first evidence capture`
- `Brand Review Link`
- `Docker self-hosted`

### Primary CTA

`View Sample Brand Review Link`

### Secondary CTA

`Enter Guest Demo`

### Tertiary CTA

`Request Workflow Review`

## Alternate Hero Option A

### Headline

`Return evidence your warehouse can defend.`

### Subheadline

`Dossentry gives multi-brand warehouse teams one phone-first workflow to capture evidence, apply the right brand playbook, and share a review-ready case record.`

## Alternate Hero Option B

### Headline

`No station cameras. No warehouse rebuild. One review-ready return record.`

### Subheadline

`Use the phones your team already carries to capture the serial, packaging, and damage details fixed stations miss — then send one protected review link to the brand.`

## Alternate Hero Option C

### Headline

`When a brand questions a return, send one link instead of rebuilding the story.`

### Subheadline

`Dossentry turns warehouse-side return handling into a brand-ready evidence record with photos, timeline, playbook context, and recommendation.`

## Lines Worth Reusing Below The Hero

### No-hardware / no-rebuild lines

- `Start on the devices your team already uses.`
- `No dedicated camera install.`
- `No warehouse station rebuild required.`
- `Works alongside your current WMS and returns stack.`

### Phone-first evidence lines

- `Mounted cameras capture the station. Dossentry captures the damage, serial, and packaging details the station misses.`
- `Built for close-up proof, not generic floor coverage.`

### Data ownership lines

- `Your data stays in your environment.`
- `Deploy in Docker on customer-controlled infrastructure.`
- `Staff accounts, case records, and uploaded evidence remain customer-owned.`

## Messaging Guardrails

### Do not say

- `tamper-proof`
- `immutable`
- `works on any phone`
- `better than any camera system`

### Safer language

- `timestamped`
- `access-controlled`
- `brand-ready`
- `customer-hosted`
- `phone-first`

## Recommended A/B Test Order

下面这个顺序现在仍然只是 hypothesis order，不是数据结论。

1. `Defensible return evidence, generated on the warehouse floor.`
2. `When a brand questions a return, send one link instead of rebuilding the story.`
3. `No station cameras. No warehouse rebuild. One review-ready return record.`
