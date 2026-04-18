# Returns Outreach Execution Pack

最后更新时间：2026-04-13 America/Los_Angeles

## Goal

这份文件是当前版本的实际执行手册，不是定位讨论稿。

未来 `7` 天只验证一件事：

**多品牌 3PL / warehouse ops buyer 会不会对 `Brand Review Link + disputed return evidence` 这套卖法产生真实回应。**

当前阶段不要假装已经验证了 PMF。
目标只是：

- 拿到 `3-5` 个有效 discovery calls
- 拿到 `1` 个明确愿意继续看的强信号
- 用真实点击和回复语言验证 message

## Current Asset Stack

当前已经上线、可以拿去卖的资产：

- 官网首页：[https://dossentry.com](https://dossentry.com)
- compare 页面：[https://dossentry.com/compare/generic-inspection-apps](https://dossentry.com/compare/generic-inspection-apps)
- guest demo 登录：[https://demo.dossentry.com/admin/auth/login](https://demo.dossentry.com/admin/auth/login)
- sample Brand Review Link：由首页和 compare 页主 CTA 承接
- first-party CTA tracking：在 admin `Review Requests` 页面看

点击数据查看入口：

- [https://demo.dossentry.com/admin/returns/review-requests](https://demo.dossentry.com/admin/returns/review-requests)

这个页面会显示：

- `Last 7 days`
- `Last 30 days`
- `Unique clients (30d)`
- `Top CTA combinations (30d)`
- `Recent CTA clicks`

## What To Sell

现在不要卖：

- `another inspection app`
- `another barcode workflow`
- `another returns portal`
- `full reverse logistics platform`
- `WMS replacement`

现在只卖：

**high-risk return exception control for multi-brand warehouse teams**

一句话版本：

`When a brand challenges how your warehouse handled a return, Dossentry helps your team send one brand-ready review record instead of reconstructing the case from SOPs, Slack threads, and photo folders.`

## Offer Ladder

### Offer 1: Low-Friction Entry

`View Sample Brand Review Link`

用途：

- 先让对方看到“你到底交付什么”
- 不要求他们先理解整个 product

### Offer 2: Conversation CTA

`Request Workflow Review`

用途：

- 把第一次互动收成一个低摩擦的 discovery / audit 对话
- 避免直接把自己卖成“大软件采购”

### Offer 3: Paid Entry

`Warehouse Return Workflow Review`

建议暂时保持窄范围：

- 一个仓
- 一个 exception lane
- 一个品牌或一组相似品牌

价格现在先别在公开页面写死。
如果需要口头报价，可以继续用：

- `pilot review: $300`

但只有在对方已经承认痛点后再提。

## Channel Priority

未来 `7` 天只做下面这 `5` 类，不扩散。

### 1. Warm Intros

优先级最高。

来源：

- 你现有朋友
- 认识 3PL / DTC / warehouse ops 的人
- 能介绍 owner / ops manager 的二度关系

原因：

- 最快拿到第一批真实对话
- 不需要先靠品牌信任背书

CTA：

- 先约 `20-minute workflow review`

### 2. Founder-Led LinkedIn Outbound

目标人群：

- `Owner`
- `Founder`
- `COO`
- `Operations Manager`
- `Warehouse Operations Manager`
- `Returns Manager`
- `Client Success Lead`

原因：

- 最容易打到多品牌 3PL 和 warehouse-side buyer
- 适合 problem-first DM，不需要先发长材料

默认链接：

- compare page

### 3. Cold Email

用途：

- 给 LinkedIn 不回的人第二触点
- 给网站访问和名单研究形成闭环

默认链接：

- compare page

### 4. Directory Presence

建议：

- Fulfill / 3PL 工具目录先占坑

用途：

- 不追求大流量
- 只接高 intent

默认链接：

- 官网首页

### 5. Problem-First Community Posts

渠道例子：

- LinkedIn 发 problem-first 帖子
- Reddit / 社区发研究型问题帖

规则：

- 先讨论 workflow，不先贴产品
- 不做广告帖

## What Not To Do This Week

先不要做：

- Google Ads
- Meta Ads
- Product Hunt
- 大范围 SEO
- 大规模 automation 冷启动
- 一上来就推 guest demo
- 一上来就发 sample signed link

原因：

- 现在最该验证的是 message，不是 scale
- `guest demo` 不是第一卖点
- sample signed link 不适合作为第一层 UTM 入口

## UTM Rules

### Core Rule

**所有第一触点外链都先指向 `landing` 或 `compare`，不要直接把 UTM 加到 signed Brand Review Link 上。**

原因：

- sample review 是 signed URL
- 额外 query params 会增加签名失效风险
- 你真正要测的是：对方是否愿意在 compare / landing 里继续点 `sample_review`

### Canonical Pattern

```text
?utm_source={{source}}&utm_medium={{medium}}&utm_campaign={{campaign}}&utm_content={{content}}&utm_term={{term}}
```

### Recommended Values

#### `utm_source`

- `linkedin`
- `email`
- `fulfill`
- `reddit`
- `warm_intro`

#### `utm_medium`

- `dm`
- `cold_email`
- `directory`
- `comment`
- `intro`
- `post`

#### `utm_campaign`

固定一周一个名字，不要乱起。

第一周建议：

- `2026q2_wk1_message_validation`

#### `utm_content`

用来区分你发的具体话术和落点。

例子：

- `compare_msg_a`
- `compare_msg_b`
- `landing_msg_a`
- `followup_1`
- `followup_2`

#### `utm_term`

只拿来标记 segment 或 account slug。

例子：

- `multi_brand_3pl`
- `warehouse_ops`
- `acct_shipbob_like`

### Link Mapping Rules

#### First outbound touch

优先发：

- compare page

示例：

```text
https://dossentry.com/compare/generic-inspection-apps?utm_source=linkedin&utm_medium=dm&utm_campaign=2026q2_wk1_message_validation&utm_content=compare_msg_a&utm_term=multi_brand_3pl
```

#### Warm reply / clear pain acknowledged

再发：

- sample Brand Review Link

规则：

- 不加额外 UTM
- 让 tracking 发生在前一层 compare / landing click

#### Wants to “see the product”

再发：

- guest demo

规则：

- 不把 guest demo 当第一触点
- 先让对方理解你卖的不是 generic inspection

## First Batch Channel List

### Batch A: 15 Best-Fit Accounts

只找这些特征：

- 多品牌 3PL
- 服务 `5+` 个 DTC 品牌
- 网站明确提到 returns management / reverse logistics / ecommerce fulfillment
- 过去看起来有 warehouse-side ops 复杂度

### Batch B: 10 Adjacent Accounts

只找这些特征：

- 多品牌 operator / aggregator
- 自营仓
- 有高价值 SKU 或争议率高品类

### Channel Mix For Week 1

- `10` 个 LinkedIn 连接请求
- `10` 个 LinkedIn DM
- `10` 封冷邮件
- `5` 个 follow-up
- `1-2` 条 problem-first 内容帖

不要超过这个量。
现在不是比量，是比 message quality。

## Outreach Copy

### LinkedIn Connection Note

```text
Looking at one narrow warehouse-side returns problem for multi-brand 3PLs: what happens when a brand challenges the handling of a returned item after it reaches the warehouse. Would be good to connect.
```

### LinkedIn DM A

```text
Quick question: when a client brand questions how your warehouse handled a return, what does your team send back today?

I’m looking specifically at the evidence/review layer after the item is physically back, not shopper portals or labels.
```

### LinkedIn DM B

```text
I’m looking at a very narrow problem for multi-brand warehouse teams:
how brand-specific return evidence gets captured and shared when a case becomes disputed.

Not trying to replace a WMS or returns portal.
Just the part where ops needs one clean review record instead of SOP PDFs, Slack threads, and photo folders.

If relevant, I can send a short compare page that shows the workflow.
```

### LinkedIn Follow-Up With Link

```text
This is the narrow workflow I mean:
{{compare_link}}

The wedge is not generic inspection.
It is giving warehouse ops one brand-ready review record when a return gets challenged.
```

### Cold Email A

```text
Subject: Quick question about disputed return handling

Hi {{first_name}},

I’m looking at one specific problem for multi-brand warehouse teams:
what happens when a client brand questions how a returned item was handled after it is physically back in the warehouse.

Not labels.
Not shopper portals.
Not WMS replacement.

The narrow issue is whether the team can send one clean review record with the right photos, rule context, timeline, and recommendation, instead of rebuilding the story from SOP docs and photo folders.

This page shows the exact wedge:
{{compare_link}}

Is this a problem your team deals with today?
```

### Cold Email B

```text
Subject: How do you show return evidence back to client brands?

Hi {{first_name}},

Quick question:
when a returned item becomes disputed, how does your warehouse team show the evidence back to the client brand?

I’m not talking about customer-facing returns tools.
I’m looking at the warehouse-side evidence and review layer after the item reaches the floor.

This explains the workflow more clearly than a long email:
{{compare_link}}

If this is relevant, happy to compare notes for 20 minutes.
```

### Follow-Up 1

```text
Following up because this is a narrow ops problem, not a broad software pitch.

If your team has ever had to answer:

- did we capture enough proof?
- which brand rule applied?
- why was this disposition recommended?
- what do we send back when the client pushes back?

then this is the workflow I’m looking at.
```

### Follow-Up 2

```text
Totally fine if this is not a priority right now.

If disputed returns across brands are messy, I’m happy to send the sample review record so you can see the exact output I’m focused on.
```

### Reply When They Say “Send Me More”

```text
Happy to.

Start here:
{{compare_link}}

If that looks close to the problem you have, I can also send a sample Brand Review Link and walk through the workflow in 20 minutes.
```

## Weekly Validation Table

这些数字是**执行目标和判断阈值**，不是市场基准事实。

| Metric | Week 1 target | Green signal | Yellow signal | Red signal |
|---|---:|---|---|---|
| Target accounts researched | 25 | `25+` | `15-24` | `<15` |
| Personalized first touches sent | 30 | `30+` | `20-29` | `<20` |
| Replies | 5 | `5+` | `2-4` | `0-1` |
| Discovery calls booked | 3 | `3+` | `1-2` | `0` |
| Workflow review requests | 1 | `1+` | `0` but good replies | `0` and weak replies |
| Sample / compare CTA activity | visible in tracking | clicks + replies align | clicks but no replies | no clicks + no replies |

## Daily Operating Rhythm

### Monday

- finalize target list
- send first `8-10` touches
- publish `1` problem-first post

### Tuesday

- send `5-7` more touches
- follow up on Monday opens/replies
- log exact objections

### Wednesday

- run any booked calls
- send next `5-7` touches
- tighten copy based on replies

### Thursday

- second follow-up wave
- send sample review only to warm prospects
- update tracker

### Friday

- review click data
- review replies
- decide:
  - keep message
  - tighten ICP
  - rewrite opening line

## Weekly Decision Rules

### Keep current positioning

如果同时出现：

- `3+` discovery calls
- 至少 `1` 个 prospect 明确承认 brand challenge / evidence pain
- compare 或 sample review 有实际点击

### Keep ICP, rewrite opening line

如果出现：

- 有点击
- 有页面停留/兴趣
- 但没有回复或没有 call

这通常说明：

- problem 方向可能对
- 第一条 DM / email opening 不够锋利

### Tighten ICP

如果出现：

- 回复很多，但都是“我们主要看 shopper portal / labels / WMS”

这说明你打的人太宽了。

### Pause broader outreach and re-check the wedge

如果一周后出现：

- `30+` personalized touches
- `0-1` 回复
- `0` calls
- tracking 也几乎没点击

这时不要继续机械加量。
先重看：

- ICP
- opening line
- 你是不是还是被理解成了 generic inspection tool

## Minimal Tracking Sheet

表头只保留这些：

- `company`
- `contact`
- `role`
- `channel`
- `utm_link_used`
- `date_sent`
- `reply_status`
- `call_booked`
- `pain_signal`
- `next_step`

## Rules To Protect Focus

- 每条外联只卖一个问题，不解释全产品
- 第一触点默认发 compare，不默认发 demo
- sample review 只发给已经表现出兴趣的人
- 不要把 inventory sync 说成现成功能
- 不要把自己卖成 full returns suite
- 不要在第一周同时测试太多 ICP

## This Week's Default Recommendation

最稳的起手式是：

1. 先打 `multi-brand 3PL owner / ops manager`
2. 第一条信息只讲 `brand challenge + evidence reconstruction`
3. 第一层链接发 compare page
4. 有回应后再发 sample Brand Review Link
5. 只有明确想看 product 时才发 guest demo
