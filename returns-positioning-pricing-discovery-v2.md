# Returns Positioning, Pricing, And Discovery Rewrite V2

最后更新时间：2026-04-11 America/Los_Angeles

## Goal

把当前产品的对外卖法从：

- `功能覆盖了很多 returns 需求`

收口成：

- `一个更容易成交的窄切口`
- `一个更稳的收费方式`
- `一套能验证核心假设的 discovery script`

这份文档只服务一个目标：

**未来 `14` 天内，用最少的新增功能，拿到 `5-10` 个高质量对话，并验证这条产品线到底该卖 `decision control` 还是 `brand-ready evidence`.**

## Executive Verdict

不要对外卖：

- another returns platform
- another WMS module
- `7 / 11` requirements covered
- inspector workflow software

要卖：

**`High-Risk Return Exception Control for Multi-Brand 3PLs`**

更稳的一句话版本：

**`When a brand challenges how your warehouse handled a return, your team should be able to send one record with the evidence, timeline, and recommendation instead of rebuilding the case from SOPs, folders, and chat.`**

中文：

**`当品牌质疑仓库如何处理一个退货 case 时，你的团队应该能直接发出一份带证据、时间线和建议动作的记录，而不是再从 SOP、文件夹和聊天里重拼一次。`**

## What Changed

这次重写最重要的变化不是文案更漂亮，而是商业假设更稳。

### Old Framing That Is Too Broad

- 我们覆盖了 returns 的很多核心需求
- 我们也有 queue、SLA、inspection、export
- 我们可以先卖 `$199 / month` 的轻量软件

问题：

- 会被拉去和 `Loop / AfterShip / ReverseLogix / WMS` 比全家桶
- 会把 buyer 视角和 operator 视角混在一起
- 会把 `inventory reconciliation` 这种 system-of-record 问题过早拉进战场

### New Framing

先只卖一个更窄的问题：

**`high-risk return exceptions across brands are hard to inspect, explain, and defend`**

这让产品的最强资产更集中：

- per-brand live playbooks
- evidence completeness gate
- timeline + case record
- Brand Review Link
- browser-first workflow

## The Core Market Truth

`JD` 能证明的，是岗位工作真实存在。

`JD` 不能证明的，是：

- 谁愿意付钱
- 为什么现在愿意付钱
- 会不会把现有 WMS / returns stack 之外再买一层工具

所以这次不再用：

- `11 个需求覆盖了 7 个`

作为主结论。

更有价值的结论是：

### [Fact] Problem Exists

仓库侧 returns inspection, grading, evidence, disposition, SLA, and exception handling 是真实岗位工作，不是想象出来的流程。

### [Fact] Competitors Exist

Two Boxes、Loop、AfterShip、ReverseLogix 都已经覆盖这条大链路的不同部分。

### [Inference] Your Open Window

你的窗口不是做一个更完整的 returns suite。

你的窗口是：

**`for teams that already have some stack, but still cannot cleanly handle high-risk multi-brand exceptions at the warehouse layer.`**

## The Biggest Unverified Assumption

这件事不确认，其他功能优先级都会漂。

### Core Question

**`退款 / 最终处置决定，到底在谁手里？`**

### Path A: 3PL Controls The Decision

如果真实世界里是 3PL 在做最终 hold / release / decision：

主卖点可以继续放在：

- Decision Queue
- evidence gate before release
- SLA visibility
- recommended action

### Path B: Brand Controls The Decision

如果真实世界里是品牌方做最终决定：

主卖点必须切成：

- evidence readiness
- brand-ready case record
- signed Brand Review Link
- recommendation + timeline + proof packaging

### Safe Default Before Validation

在真实电话验证前，统一使用中性语言：

- `Decision Queue`
- `recommended action`
- `evidence readiness`
- `brand-ready review`

不要把对外叙事钉死在：

- `refund release control`

## What You Should Sell Now

### Product Category

**`Warehouse-side exception-control layer`**

### Short Positioning Statement

`A browser-based exception-control layer for multi-brand 3PLs. Standardize brand rules, capture the right evidence, and generate a brand-ready record for the return cases that should not be handled from memory.`

### Plain-English Explanation

`We do not replace your returns portal or your WMS. We handle the messy layer after the item is physically back: inspection rules, proof capture, exception review, and the case record you need when a client asks what happened.`

### What You Are Not

- not a shopper-facing returns portal
- not a label / exchange platform
- not a full WMS
- not a reverse logistics suite
- not an inventory reconciliation system

## The Real Wedge

不要把 wedge 讲成：

- mobile inspection
- SLA board
- dashboard cards

这些都重要，但不够尖。

最值得打穿的 wedge 是：

### `Brand Review Link + Evidence-Ready Exception Workflow`

原因：

1. 它能同时适配 `3PL controls` 和 `brand controls` 两条 authority 路径。
2. 它天然更接近 buyer 在意的结果，而不只是 inspector 的日常操作。
3. 它能把你的价值从“内部操作工具”拉到“跨组织解释工具”。
4. 它已经有产品基础，不是纯 roadmap。

## ICP

### Best-Fit ICP

| 维度 | 标准 |
|---|---|
| 公司类型 | 多品牌 3PL |
| 客户结构 | 服务 `5+` 个电商品牌更佳 |
| 当前流程 | SOP docs、sheet、Slack、training 仍是主要系统 |
| 痛点 | wrong item、empty box、damage dispute、missing parts、serial mismatch |
| 更强信号 | 被品牌 challenge 过，或需要对外解释 case |
| 联系人 | owner、ops lead、returns manager、client success lead |

### Good Signal

- 网站提到 returns management
- 网站提到 multiple brands / DTC brands
- 公开内容里有 quality control、inspection、returns processing
- 明显不是单一品牌自营店

### Bad Signal

- 只做 freight / brokerage
- 已深度上马重型 enterprise reverse logistics stack
- 痛点全部在 shopper portal
- 单品牌仓，没有明显 brand-rule complexity

## Anti-Goals

未来 `30` 天不要被这几个方向带跑：

- inventory sync 作为主卖点
- AI grading 作为主卖点
- self-hosted 作为首屏主卖点
- `$199 unlimited everything` 作为价格锚点
- “我们覆盖大多数 returns 需求” 作为销售主线

## Pricing Rewrite

现在最危险的不是定得太高，而是定得太像一个便宜泛工具。

### Why `$199 Flat SaaS` Is Too Early

风险：

- 会让 buyer 以为这是轻量辅助工具，而不是一个带 setup 和 workflow value 的系统
- 会把你拖进低价软件比较
- 在 authority 和真实购买理由未验证前，太早锁死 packaging

### Better Pricing Ladder

#### Offer 1: Paid Workflow Review

名字：

- `Returns Exception Workflow Review`

价格假设：

- `$300-$500` 一次

卖什么：

- 当前 exception workflow map
- rule drift / evidence gap / response bottleneck review
- 一份改进建议
- 一份 sample case record walkthrough

为什么先卖这个：

- 验证 willingness to pay
- 拿到真实 SOP 和 objection
- 不要求对方先承诺系统替换

#### Offer 2: Founding Pilot

名字：

- `Founding Exception-Control Pilot`

价格假设：

- setup fee: `$500-$1,500`
- monthly pilot: `$300-$800`

范围限制：

- `1` workspace
- `1` warehouse
- `3-5` brand playbooks
- `1` exception lane
- 明确月 case 上限

为什么这样卖：

- 更像交付结果，不像卖账号
- 更符合你当前真实能力和人工介入程度
- 能避免过早承诺“无限品牌 / 无限 case / 自助上线”

#### Offer 3: Later SaaS Packaging

只有在这几个点被验证后再做：

- authority clear
- there is repeated workflow pain
- there is repeated willingness to pay
- onboarding can be made repeatable

再考虑公开月费套餐。

## Messaging Hierarchy Rewrite

### Primary Message

`When a brand questions how your warehouse handled a high-risk return, your team should be able to send one record with the evidence, timeline, and recommendation.`

### Secondary Message

`We turn each client brand's return SOP into a live inspection playbook instead of leaving the workflow in PDFs, spreadsheets, and tribal knowledge.`

### Tertiary Message

`No hardware. No heavy implementation project. Start from a browser.`

### Contrast Message

`This is not a full returns suite. It is a control layer for the messy cases.`

## Website / Demo Narrative

官网和 demo 不要先从 queue 开始讲。

更好的顺序：

1. messy exception arrives
2. brand-specific playbook guides inspection
3. evidence gate prevents incomplete case handling
4. timeline + recommendation are captured
5. brand review record can be shared

### Best Demo Story

不要演：

- “看，我们有很多页面”

要演：

- “这个 case 为什么不能靠记忆处理”
- “为什么不同品牌规则不同”
- “为什么证据不全不能继续”
- “为什么最后发出去的一条 record 能减少解释成本”

## Discovery Call Rewrite

Discovery call 不是为了证明你功能全。

它只为了回答这 `5` 个问题：

1. 他们是否真的有 `high-risk exception` 概念
2. brand-specific rule drift 是否真实存在
3. 证据是不是经常难找或不齐
4. authority 到底在 3PL 还是品牌
5. 对外解释 case 是否痛苦到值得付钱

### Opening

```text
I’m not trying to replace your whole returns stack.
I’m trying to understand what happens when a messy return lands in the warehouse and someone later has to explain or defend that case.
```

### Core Questions

1. `Which return cases create the most pushback from clients or internal teams?`
2. `When those cases happen, where does the team look for the evidence and the rule they were supposed to follow?`
3. `Who defines the inspection SOP for each brand, and how does the warehouse know when it changes?`
4. `Who makes the final hold / release / refund decision when the case is not clean?`
5. `When a client challenges a return decision, how do you currently explain what happened?`
6. `Is there a case record or shareable summary today, or does someone rebuild the story manually?`
7. `How long would it take to onboard a new brand's return rules into live warehouse operations?`

### Must-Have Follow-Ups

如果对方说 `we already have SOPs`：

```text
Are those SOPs enough in the moment, when an associate is moving fast across multiple brands and exception types?
```

如果对方说 `we already use a returns tool`：

```text
Does that tool also solve the warehouse-side proof and explanation problem for disputed or high-risk cases, or mostly the shopper-facing flow?
```

如果对方说 `our team handles it fine`：

```text
If a brand challenged a case tomorrow, how quickly could your team send one clean record with the photos, timeline, rule snapshot, and recommendation?
```

## Decision Rubric For Calls

每个 call 后只按下面打分，不要靠感觉。

### Strong Signal

- `+3` 明确存在 messy exceptions
- `+3` 明确提到 brand/client challenge
- `+2` 明确提到 evidence hard to find / incomplete
- `+2` 明确提到 SOP drift / training burden
- `+2` 愿意看 sample Brand Review Link 或 sample PDF
- `+2` 愿意付 review fee

### Weak Signal

- `-3` 对方只想要 shopper portal / labels / exchanges
- `-3` 没有多品牌复杂度
- `-2` 没有任何 exception concept
- `-2` 完全不在乎对外解释 case

### Interpretation

- `7+` strong follow-up
- `4-6` nurture and test alternate angle
- `1-3` weak fit
- `0 or below` disqualify

## What To Build Next

这部分只保留高杠杆项，不追求“功能更全”。

### Priority 1

把现有 `Brand Review Link` 做成真正可公开演示的销售资产：

- 一个匿名 sample link
- 一个 sample PDF
- 一段 `60-90` 秒 Loom
- 官网首屏或中段明确提它的价值

### Priority 2

做一个最小 manager summary，不做大 dashboard 项目：

- 本月 case 数
- 平均处理时长
- over-SLA case 数
- missing-evidence case 数
- disposition split

目的：

- 给 buyer 一个管理视角截图
- 不是为了变成 reporting product

### Priority 3

如果 discovery 持续出现 exception bucket 需求，再做独立 `Exception Queue`。

### Not Now

- deep inventory reconciliation
- official WMS plugins
- AI auto grading
- onboarding wizard
- big analytics suite

## Kill / Pivot Rules

### Keep Going

如果 `10` 个高质量对话里至少出现：

- `3` 次 brand/client challenge
- `3` 次 evidence hard-to-find
- `2` 次 willingness to see or use a shareable case record
- `1` 次 willingness to pay for workflow review

继续推进。

### Pivot Toward Evidence-Ready Review

如果大部分对话显示：

- 品牌掌握最终决定权
- 但 3PL 很痛苦地在解释 case

则主叙事彻底转向：

- `brand-ready review`
- `shareable case record`
- `evidence readiness`

### Kill This Packaging

如果 `10-15` 个高质量对话后仍然没有人承认：

- exception handling is messy
- evidence retrieval is painful
- brand challenge matters
- onboarding rules across brands is hard

那不是 landing page 问题，是定位问题。

## Final Recommendation

接下来不要说：

- `我们覆盖了 returns 岗位 7 / 11 的核心需求`

要说：

- `我们先解决最难解释、最容易出争议的退货 case。`
- `让每个品牌的 SOP 变成现场可执行的 playbook。`
- `让仓库在面对品牌质疑时，能发出一份完整的 case record。`

一句话收口：

**`先卖高风险 exception 的 brand-ready proof，不要先卖全功能 returns software。`**
