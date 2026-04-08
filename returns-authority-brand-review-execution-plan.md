# Returns Authority And Brand Review Execution Plan

最后更新时间：2026-04-08 America/Los_Angeles

## Goal

把当前产品从一份“竞争定位总结”推进成未来 `30` 天可执行的验证计划。

这份文档只解决 5 个问题：

1. 如果 `refund authority` 验证结果不同，产品怎么分叉
2. `Shareable Case Link` 如何变成真实商业杠杆
3. 定价如何先作为假设运行，而不是过早锁死
4. 前 `14` 天去哪里找人、怎么找到人
5. 第一个客户如何同时承担 `验证 + 口碑素材` 两个作用

## Core Principle

现在最该验证的不是“谁是最大竞品”，而是：

**`退款决定到底在谁手里？`**

在这个问题没得到真实回答前，不要把：

- `refund gate`
- `hold / release`
- `refund release control`

当成唯一核心卖点。

## Decision Tree

### Path A: 3PL Controls The Refund

#### Product Definition

产品保持为：

- `Decision Queue`
- `recommended_hold`
- `recommended_release`
- `evidence gate before release`

#### Core Pitch

`Your team should not be pressured into releasing refunds without complete evidence. We standardize the review, capture the proof, and let ops hold the decision until the case is ready.`

中文：

`你的团队不应该在证据不完整的情况下被迫放款。我们把检查、留证和决策队列标准化，让 ops 可以在 case 真正准备好之前先 hold。`

#### Best Buyer

- 独立 3PL owner
- 拥有较强决策权的 ops lead
- 有能力代表品牌执行 hold / release 的团队

#### Demo Focus

- Inspect Return
- Decision Queue
- evidence completeness blocker
- Brand Defense Pack / Case Link

#### Most Important KPI

- missing-evidence cases blocked before release

### Path B: Brand Controls The Refund

#### Product Definition

产品需要改成：

- `Evidence Readiness Board`
- `ready_for_brand_review`
- `evidence_missing`
- `ops_reviewed`
- `recommended_action`

#### Core Pitch

`When a brand challenges how your warehouse handled a return, your team should be able to send one link with the evidence, the timeline, and the recommendation instead of digging through Slack, spreadsheets, and folders.`

中文：

`当品牌挑战你的退货处理决定时，你的团队应该能直接发一个链接过去，里面有证据、时间线和建议动作，而不是再去翻 Slack、表格和照片文件夹。`

#### Best Buyer

- 被品牌 challenge 过的 3PL ops manager
- client success / account lead
- 需要“可辩护性”而不是“最终控制权”的团队

#### Demo Focus

- Inspect Return
- Case detail
- Shareable Case Link
- evidence completeness
- recommendation sent to brand

#### Most Important KPI

- time to respond to brand dispute

## Product Implication

### This Is Not Just Copy

这不是只改 landing page 文案。

它会直接影响：

- 队列命名
- 状态机
- 详情页字段文案
- export / link 的呈现方式
- 谁是主要用户

### Recommended Safe Default

在真实电话验证前，先用更中性的产品语言：

- `Decision Queue`
- `evidence readiness`
- `recommended action`
- `brand-ready review`

避免现在就把所有产品语义钉死在：

- `refund release`

## Shareable Case Link

### Why This Is The Highest-Leverage Feature

它不是一个“导出方式”。

它是产品从内部工具走向外部价值的第一步。

如果它成立，产品会从：

- 仓库内部工具

变成：

- 3PL 和品牌之间的信息接口

### Commercial Value

#### Internal Value

给 ops manager：

- 不需要翻 Slack
- 不需要拼 PDF
- 不需要重新讲一遍 case

#### External Value

给品牌客户：

- 一个统一可读的 case record
- 照片、时间线、规则快照、建议动作都在一处
- 不需要登录 3PL 内部后台

#### Growth Value

这是最可能带来自然传播的功能。

因为品牌第一次体验价值时，不是在你的官网，而是在 3PL 发出去的 case link 里。

### V1 Scope

#### Internal Link

给内部 ops / manager 使用：

- 所有字段可见
- 可看到 internal notes
- 可更新 case 状态
- 可触发 PDF 导出

#### Brand Review Link

给外部品牌客户使用：

- signed read-only link
- 可设置过期时间
- 只显示 external-safe 字段
- 显示 photos
- 显示 timeline
- 显示 rule snapshot
- 显示 evidence completeness
- 显示 recommendation
- 可下载 PDF

### V1 Explicitly Out Of Scope

第一版不要做：

- `Accept / Dispute` buttons
- comment threads
- external login
- email notifications engine
- multi-party approvals

原因：

一旦品牌能点击动作，你就进入跨组织 workflow，复杂度会上升太快。

## Pricing Hypothesis

### Principle

现阶段定价应当是：

- 销售测试工具
- 价值验证工具

而不是永久承诺。

### Recommended Hypothesis

#### `Founding Self-Serve`

- `$199/month`
- self-serve playbooks
- internal case workflow
- basic shareable case link
- no heavy integration

#### `Founding Pro`

- `$349/month`
- Brand Review Link
- unlimited external sharing
- priority support
- simple API / webhook / CSV write-back

#### `Managed Onboarding`

- `$499` one-time
- `3` 个品牌规则配置
- `1` 次 inspector 培训
- `1` 次 first evidence walkthrough

### What This Pricing Structure Does

- 给销售一个升级路径
- 让 Brand Review Link 成为真实 upsell
- 避免所有价值都挤在 `$199`

### Important Rule

在拿到 `3` 个真实对话之前，不要把这组价格当最终价格页。

## Buyer Map

### 1. Ops Manager

- 日常 champion
- 最懂实际痛点
- 会推动试用

### 2. Owner / GM

- 预算批准者
- 在意风险、客户关系、差错成本

### 3. Client Success / Account Lead

- 最可能把 case link 发给品牌
- 最接近“品牌挑战”这个场景

### 4. Brand-Side Reviewer

- 不一定付钱
- 但会影响是否续用
- 会影响口碑传播

## Channel Plan

### Goal For Next 14 Days

约到第一批 `6` 个有效对话：

- `3` 个传统 3PL
- `3` 个 multi-brand operator / aggregator

### Channel 1: LinkedIn

文章主题：

- `3PL Returns Disputes: What Your WMS Won't Save You From`

不要先卖产品。先讲：

- WMS 记录回库
- 但不解决 evidence dispute
- 也不解决谁来承担处理决定的可信度

### Channel 2: Reddit

目标社区：

- `r/fulfillment`
- `r/3PL`

形式：

- 提问题
- 不打广告
- 让对方自己暴露当前流程

### Channel 3: 目录 + 主动外联

目标：

- California / Texas
- 服务 `5+` DTC brands
- returns-heavy 3PL

候选来源：

- Fulfill.com
- 3PL network directories
- LinkedIn 搜索

## Discovery Call Script

### Must-Ask Questions

1. `When a high-risk return is inspected, who actually makes the refund decision today?`
2. `Does your team have authority to hold that refund, or only recommend a decision?`
3. `Has a brand ever challenged your grading or asked for proof after the fact?`
4. `Would a shareable case record help that conversation, or do they expect something else?`

### Best Qualification Signal

不是规模。

而是：

**过去 `6` 个月内，是否真的被品牌 challenge 过一次以上。**

## First-Customer Learning Protocol

### Principle

第一个客户不只是收入。

它同时必须产出：

- authority 结论
- workflow 真相
- 产品语言校正
- testimonial / case study 素材

### Recommended Pilot Length

`2-3 weeks`

不要先拉成 `8 weeks`。

现在更重要的是方向验证，不是长期 adoption data。

### Week 1

- 只上 `Inspect + Case Record`
- 观察真实 case 长什么样
- 不强推复杂队列逻辑

### Week 2

- 引入 `Decision Queue` 或 `Evidence Readiness Board`
- 观察谁在用、谁不用
- 引入 `Shareable Case Link`

### Week 3

- 复盘 4 个 discovery questions
- 跑一次真实或模拟的 brand challenge walkthrough
- 要一段可引用的话

### Required Output

- one authority answer
- one workflow map
- one case-link usage story
- one testimonial candidate

## Metrics To Track

产品如果真要卖成“保护层”，必须开始追这 5 个指标：

1. `evidence completeness rate`
2. `time to case readiness`
3. `time to answer brand challenge`
4. `cases with missing proof`
5. `recommendation override rate`

## What Not To Do Next

未来 `30` 天不要优先做：

- official WMS plugin
- video-first capture
- external approval buttons
- complex portal permissions
- heavy automation based on unvalidated refund authority

## 14-Day Execution Plan

| Day Range | Objective | Output | Exit Criteria |
|---|---|---|---|
| `1-2` | 写两套 authority 分叉话术 | `A/B pitch` 文档 | 两套 pitch 都可直接用于电话 |
| `1-4` | 定义 Brand Review Link v1 | 简化 spec | 明确做什么 / 不做什么 |
| `3-10` | 跑 `6` 个 discovery calls | 通话笔记 | 至少拿到 `3` 个明确 authority 回答 |
| `7-12` | 试报价 `$199 / $349 / $499` | 价格反馈 | 知道哪个档位被接受/被质疑 |
| `10-14` | 选择主 ICP | 聚焦结论 | 决定先打 3PL 还是 multi-brand operator |

## Final Recommendation

### Most Important Strategic Move

把产品先定义成：

**`evidence + recommendation + brand-ready review`**

不要急着把它定义成：

**`refund release control`**

### Most Important Build Move

先做：

**`Brand Review Link`**

不要先做：

- 视频
- 官方插件
- 更复杂的外部协作

### Most Important GTM Move

本周先打 `6` 个电话，把 `refund authority` 问清楚。

在这个问题明确之前，任何更大的产品定义都不够稳。
