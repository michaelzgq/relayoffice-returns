# Returns AI Strategy Report

最后更新时间：2026-04-06 America/Los_Angeles

## Goal

回答一个非常具体的问题：

**当前这套 multi-brand 3PL returns 产品，是否应该加 AI，如果要加，先加什么，什么时候加，怎么加才不会把产品带偏。**

本报告的目标不是证明 “AI 很重要”，而是做一个更残酷的取舍：

- 什么 AI 现在值得做
- 什么 AI 现在不值得做
- 什么 AI 会增强你现有优势
- 什么 AI 会把你拖回功能战

## Executive Verdict

### 最终结论

**要加 AI，但现在不应该先做 `AI 自动 grading / 自动 disposition`。**

当前最合理的顺序是：

1. `先卖出第一个付费 review / pilot`
2. `先把 no hardware / no integration / fast setup 这个差异打穿`
3. `先做 AI Brand Rule Suggester`
4. `先做 AI Evidence Summary`
5. `再做 AI Photo / Evidence QA`
6. `最后才考虑 AI Condition / Disposition Suggestion`

### 一句话版本

**AI 应该先强化你已经强的地方：证据质量、证据完整性、输出效率；不要先替代人做最终判断。**

## Current Product Context

以下是当前产品已经成立的能力，来自本地代码与已完成的实现。

### 已有基础能力

- `Client Playbooks`
- `Responsive Inspection Flow`
- `Evidence Completeness Gate`
- `Refund Queue`
- `Case Timeline`
- `Evidence Export`
- `Condition -> Recommended Disposition` 的非 AI 规则建议

### 关键文件

- [BrandRuleProfile.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Models/BrandRuleProfile.php)
- [InspectionController.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Controllers/Admin/InspectionController.php)
- [StoreReturnInspectionRequest.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Http/Requests/Admin/Returns/StoreReturnInspectionRequest.php)
- [inspect.blade.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/inspect.blade.php)
- [show.blade.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/show.blade.php)

### 当前最强卖点

这套产品当前最强的不是“图像理解”，而是：

- 每个品牌不同的 returns playbook
- 证据不齐时不能顺利进入 release 流
- 手机浏览器可用
- case 可以形成 timeline 和 evidence pack

因此，AI 的第一原则应该是：

**优先增强 evidence workflow，而不是优先替代 inspection judgement。**

## Go-to-Market Differentiators

这一节不是 AI 功能本身，但它决定 AI 应该服务什么方向。

### 1. Flat-Rate Per 3PL

[Inference] 你最值得测试的定价差异，不是按品牌收费，而是按 `3PL workspace` 收费。

但这里要和之前战略保持一致：

**现在更稳的公开方式不是 `无限一切`，而是 `Founding 3PL Plan`。**

建议测试版本：

- `$199/month`
- `unlimited brands`
- `unlimited inspectors`
- `up to a defined monthly case limit`
- `month-to-month`

它的作用不是证明你更便宜，而是证明：

**3PL 自己就能买，不需要一个个品牌跟着买。**

### 2. Quick-Start Rule Templates

这是当前最缺、也最该补的 onboarding 杠杆。

最低应有：

- `Apparel`
- `Electronics`
- `Beauty`
- `General`

作用：

- 把配置从填空题变成选择题
- 支撑更快 onboarding
- 为 AI Brand Rule Suggester 提供可靠骨架

### 3. 30-Minute Onboarding Target

这里要非常谨慎：

[Inference] 这是一个**产品目标**，不是今天就能公开承诺的卖点。  

因为当前系统还没有：

- self-serve signup
- onboarding wizard
- template-first setup flow
- 完整 mobile onboarding QA

所以更稳的写法应该是：

**`30-minute self-serve setup target`**

而不是：

**`30-minute onboarding promise`**

### 4. Setup Service Bundle

这是你现在最真实、最容易成交的差异化：

- 产品
- 模板配置
- 首批品牌 playbook setup
- inspector training
- first evidence pack walkthrough

因此 AI 的第一批功能也应该服务：

- 更快 setup
- 更快 evidence handling

而不是优先服务 fancy grading demo

## External Evidence

### Renow

[Fact] Renow 官方当前明确把 AI 放在 inspection / grading 主叙事里。  
[Fact] 官方公开写法包括：

- `AI based recommerce engine inspects the product instantly, gives return a grade and approves or declines the return`
- `AI-enhanced product grading`
- `human oversight` 风格的仓库分级流程

来源：

- [Renow Recommerce Platform](https://renow.ai/renow-recommerce-platform/)
- [Renow Returns Solution](https://renow.ai/returns-solution/)
- [Renow Inspection App announcement](https://renow.ai/renow-revolutionizes-time-consuming-warehouse-inspections-with-ai-powered-inspection-app-at-shoptalk-barcelona/)

结论：

**AI grading 已经不是科幻功能，而是竞品正在公开售卖的能力。**

### Two Boxes

[Fact] Two Boxes 当前公开对 3PL 的核心卖点是：

- `Process any return, from any brand, in seconds`
- `Digitize your brands' SOPs`
- `We provide all the hardware and software you need`
- `get up and running on day 1`
- `train your warehouse associates ... within a day`

来源：

- [Two Boxes](https://www.twoboxes.com/)

[Inference] Two Boxes 当前公开叙事不是 AI-first，而是 SOP digitization + hardware/software + operational rollout。

结论：

**你的窗口不是“AI 比 Two Boxes 多”，而是“更轻、更 browser-native、更 self-serve”。**

### OpenAI Cost / Capability

[Fact] OpenAI 官方模型页写明 `GPT-4o` 支持 `image input`。  
[Fact] OpenAI 官方价格页写明：

- `GPT-4o` 文本 token 定价公开透明
- image input 计费存在
- `Batch API` 可节省 `50%` 输入/输出成本

来源：

- [GPT-4o model docs](https://platform.openai.com/docs/models/gpt-4o)
- [OpenAI API pricing](https://openai.com/api/pricing/)

结论：

**做 AI-assisted evidence or suggestion 在技术上是可行的，且推理成本可以控制，但真实成本必须根据图片大小、张数、prompt 和频率实测。**

## The Real Strategic Question

问题不是：

`能不能接一个 vision API`

真正的问题是：

`哪一种 AI 会增强你现在已经能卖的价值，而不是把你拉回和更强竞品打功能战。`

## Decision Framework

### 判断标准

每个 AI 候选功能只看这 5 件事：

1. 是否增强当前最强卖点
2. 是否需要大量历史标注数据
3. 是否会增加错误责任风险
4. 是否能在现有流程中低摩擦落地
5. 是否能在销售对话里讲清楚

## AI Options Matrix

| AI 功能 | 现在值不值得做 | 风险 | 为什么 |
|---|---|---|---|
| `AI Brand Rule Suggester` | `🟢 Yes` | 低 | 直接服务 onboarding 和 setup，增强 fast-start 差异 |
| `AI Evidence Summary` | `🟢 Yes` | 低 | 直接增强 export 和对外沟通，价值感知强 |
| `AI Photo / Evidence QA` | `🟢 Yes` | 低 | 直接增强 evidence completeness，但更像体验增强而不是强差异化 |
| `AI Condition Suggestion` | `🟡 Later` | 中 | 有价值，但容易误判，且会被当成主能力比较 |
| `AI Disposition Suggestion` | `🟡 Later` | 中 | 必须依赖品牌规则和人类确认 |
| `AI Fraud / Wrong Item Detection` | `🟡 Later` | 高 | 真有价值，但需要 SKU reference image / 更强数据基础 |
| `Fully Automatic Grading` | `🔴 No` | 很高 | 责任大、可信度要求高、会伤害早期信任 |
| `AI Fraud Pattern Prediction` | `🔴 No` | 很高 | 需要大量历史数据，当前完全不该做 |
| `Automatic Resale Listing AI` | `🔴 No` | 很高 | 会把产品方向带向 Renow/ resale 平台 |

## Recommended AI V1

### First AI Feature

**`AI Brand Rule Suggester + AI Evidence Summary`**

它包含两个最小能力：

1. `Brand Rule Suggester`
2. `Evidence Summary`

### 1. Brand Rule Suggester

这个功能最适合放在 playbook 创建流程里，而不是 inspection 末端。

最简单版本不需要神奇模型，只要：

1. 询问品类
2. 询问是否需要 serial
3. 询问退货窗口或严格程度

然后自动生成一套 playbook 草稿：

- allowed conditions
- allowed dispositions
- required photo types
- required photo count
- notes required
- sku / serial required
- default refund status
- recommended dispositions

它的商业价值非常直接：

- 把新品牌配置从空白表单变成草稿微调
- 支撑更快 setup
- 支撑 `fast-start` 差异
- 强化你不是项目制实施工具这个定位

### 2. Evidence Summary

AI 读取：

- case metadata
- selected condition
- selected disposition
- notes
- uploaded photos
- required photo types

生成一段给 ops / brand 可复用的摘要，例如：

`Item received with visible packaging crush and front-corner damage. Required evidence set is complete, including front, back, packaging, and label photos. Inspector marked condition as opened_damaged and warehouse action as refurb. Refund remains on hold pending ops review.`

这个 summary 最适合挂在：

- case detail
- evidence export
- refund queue side panel

### Why Evidence Summary Comes Before Photo QA

[Inference] `Photo QA` 很有用，但更像基础体验增强；`Evidence Summary` 更像一个看得见、能立刻发给品牌的输出物。

对早期销售和 demo 来说，后者更容易产生：

**“原来这个工具真的能帮我省掉一段运营沟通工作。”**

这比：

**“系统说这张图有点模糊。”**

更容易形成感知价值。

### Next In V1.5: Evidence QA

AI 在 inspector 上传照片后检查：

- 图片是否模糊
- 图片是否过暗 / 过曝
- serial / label 是否可读
- 是否疑似缺少某个必需视角
- 是否建议补拍

这不直接决定：

- 最终 condition
- 最终 disposition
- 最终 refund movement

它只回答：

**“你现在这组照片够不够形成合格证据？”**

它和现有 evidence gate 高度一致，但在销售感知上不如 summary 强，所以更适合放在 `V1.5`。

## Why AI Grading Should Not Be First

### 1. It attacks the wrong layer first

你当前卖得出去的不是：

- “我的图像模型更强”

而是：

- “我的 evidence 和 refund gate 更可控”

如果先做 AI grading，你是在最薄弱的地方先跟更强竞品对打。

### 2. It creates trust risk before product trust exists

一个早期客户会原谅：

- AI 建议补拍错一次

但不会轻易原谅：

- AI 把 condition 建议错
- AI 导致 disposition 错
- AI 影响 refund 决策

### 3. It is easy to demo, hard to trust

[Inference] `3-5 天` 接一个 AI grading demo 有可能。  
但 `可卖` 和 `可 demo` 不是一回事。

真正难的是：

- 品类差异
- 光照差异
- 包装差异
- 客户标准差异
- 责任归属

## Recommended Product Sequence

### Phase 0: No AI Yet

目标：

- 拿第一个付费 review / pilot
- 验证客户是不是真的愿意为这条 workflow 付钱

退出标准：

- 至少 `1` 个 3PL 支付以下任一项：
  - `$199/month` founding subscription
  - `$300-$500` one-time setup / review
- 且该客户在 `14` 天内提交 `>= 5` 个真实 case

没有真实使用量，只有付款，不算完成 Phase 0。

### Phase 1: AI Setup + Summary

加的功能：

- AI Brand Rule Suggester
- evidence summary generation
- quick-start template-assisted setup

目标：

- 缩短新品牌配置时间
- 提高 setup 完成率
- 降低 ops 手写 case summary 时间
- 支撑更快 onboarding

### Phase 1.5: AI Evidence QA

加的功能：

- photo quality check
- serial / label readability hints
- missing-shot guidance

目标：

- 提高 evidence completeness
- 降低 inspector 补拍成本
- 降低 evidence 被打回概率

### Phase 2: AI Condition Suggestion

只做：

- condition suggestion
- confidence
- human confirm / override

不做：

- auto-release
- auto-disposition without human

目标：

- 提升判断一致性
- 缩短新员工熟悉时间

### Phase 3: Fraud / Wrong-Item Assistance

前提：

- 你已经有 SKU reference images
- 你已经积累 enough reviewed cases
- 你已经有 human override 数据

## Recommended AI UX Rules

### Rule 1

**AI 永远先做 assistant，不先做 decider。**

### Rule 2

**AI 输出必须可解释。**

不能只说：

- `Suggested condition: fair`

应该至少能说明：

- `Packaging damage visible`
- `Serial label is unreadable`
- `Required side-angle image may be missing`

### Rule 3

**AI 必须留下审计记录。**

每次 AI 运行建议至少记录：

- model
- prompt_version
- case_id
- input_image_count
- output_type
- suggestion
- confidence
- accepted_by_human
- final_human_decision

### Rule 4

**任何 AI 都不能直接绕过 evidence completeness gate。**

也就是：

- AI 可以说“看起来没问题”
- 但如果 required evidence 没齐，系统仍不能自动放行

## Retention Mechanics

AI 不应该只服务获客，还要服务留存。

### 1. Evidence History Switching Cost

随着 case 积累，系统会越来越像一个历史证据库：

- 过去处理过哪些 exception
- 当时收集了哪些图片
- 最终如何放款或 hold
- 哪类 case 最容易被 challenge

[Inference] 这是天然 switching cost。  
换工具，3PL 就失去可对比的历史 case 轨迹。

### 2. Inspector Consistency Score

这个功能现在不一定要做成 AI，但它应该进 roadmap。

它可以衡量：

- 哪些 inspector 的 case 更容易被补证据
- 哪些 inspector 的判断更常被 ops 覆写
- 哪些品牌的 case 最容易出错

对 3PL owner 来说，这不是 fancy analytics，而是：

**“我的团队到底有没有越来越稳定。”**

### 3. Weekly Ops Brief

系统可以每周汇总：

- processed cases
- average inspection time
- evidence completeness rate
- most problematic brands
- cases returned for rework

这会让产品从单次工具，变成持续运营面板。

## Star Product Definition

“明星产品时刻”不是：

- 用户说 AI 很酷

而是：

**某个 3PL 用你的 evidence pack 和 timeline，成功解释了一次真实品牌纠纷、chargeback dispute 或高风险 return 决策。**

这才是最值得设计的传播时刻。

### The First Star Moment

最关键的第一个口碑瞬间通常是：

1. case 进入争议
2. ops 导出 evidence pack
3. 品牌或内部团队接受这份说明
4. 团队意识到“这次不是靠记忆和散落照片在解释”

### Product Implication

所以比任何早期 AI grading 更值得投资的是：

- evidence export 质量
- summary 可读性
- timeline 的可解释性
- dispute-ready presentation

### Recommendation

如果必须在 `AI grading` 和 `Evidence Pack quality` 之间二选一：

**先投 Evidence Pack quality。**

## Best AI V1 Integration Points

### In Playbooks

页面：

- [index.blade.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/rules/index.blade.php)

最适合加：

- `AI brand rule draft`
- `industry quick-start + AI refinement`
- `new brand setup assistant`

### In Inspect Return

页面：

- [inspect.blade.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/inspect.blade.php)

最适合加：

- 上传照片后显示 `AI evidence check`
- 提交前提示 `missing evidence risk`
- 低质量图片直接建议重拍

### In Case Detail

页面：

- [show.blade.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/resources/views/admin-views/returns/cases/show.blade.php)

最适合加：

- `AI evidence summary`
- `AI flagged concerns`

### In Export

最适合加：

- 一段可复制的 summary
- 不是替代原始证据
- 而是让 ops 少写一段说明

## Data Requirements

### AI Evidence Assistant 所需数据

最低要求：

- case id
- brand playbook
- required photo types
- uploaded images
- notes

### AI Brand Rule Suggester 所需数据

最低要求：

- selected industry template
- serial requirement
- refund strictness or review posture
- optional return-window policy
- optional brand notes
- selected condition / disposition

### AI Grading 所需数据

要做得像样，额外至少需要：

- 品类标准
- 品牌级 grading 差异
- 更多人工标注结果
- override 反馈

这就是为什么它不该是第一步。

## Cost View

### What Is Known

[Fact] OpenAI 官方公开了 image input 与 token 计费，并提供 `Batch API` 的 `50%` 折扣。  

### What Is Unknown

当前还不知道：

- 平均每单几张图
- 图片分辨率是否要压缩
- 每次 summary prompt 多长
- 每月处理量区间

### Safe Conclusion

**现在可以判断“技术上负担得起”，但不能在没有实测的情况下公开承诺“每单 AI 成本只有多少”。**

正确做法是：

1. 先做内部 prototype
2. 记录 `100` 个 sample cases 的真实 token / image 成本
3. 再决定是否内含在订阅里

## Commercial Impact

### AI Evidence Assistant 的销售价值

它最适合支持以下销售句子：

- `The system helps your team capture complete, usable evidence before refund moves forward.`
- `Inspectors get guidance when photos are weak or incomplete.`
- `Ops gets a ready-to-send case summary instead of writing it from scratch.`

### Why This Is Better Than AI Grading As First Pitch

因为它更可信，更贴近 ops 成本，也更不容易引发：

- “如果 AI 看错了怎么办？”
- “谁为错误 grading 负责？”

## Final Recommendation

### Do Now

- 不把 AI 作为当前对外主卖点
- 继续卖 `no hardware / no integration / setup support`
- 先拿真实客户

### Build First

- `AI Evidence Assistant`
  - photo quality QA
  - missing-evidence guidance
  - evidence summary generation

### Build Later

- `AI Condition Suggestion`
- `AI Disposition Suggestion`
- `AI Fraud Flag`

### Do Not Build Yet

- fully automatic grading
- AI auto-release logic
- resale listing automation
- fraud pattern prediction engine

## Final One-Line Verdict

**AI 有必要，但现在最值得加的不是“AI 替你做判断”，而是“AI 帮你把证据做对、把说明写好”。**

## Sources

- Renow AI returns solution: [https://renow.ai/returns-solution/](https://renow.ai/returns-solution/)
- Renow recommerce platform: [https://renow.ai/renow-recommerce-platform/](https://renow.ai/renow-recommerce-platform/)
- Renow inspection app announcement: [https://renow.ai/renow-revolutionizes-time-consuming-warehouse-inspections-with-ai-powered-inspection-app-at-shoptalk-barcelona/](https://renow.ai/renow-revolutionizes-time-consuming-warehouse-inspections-with-ai-powered-inspection-app-at-shoptalk-barcelona/)
- Two Boxes official site: [https://www.twoboxes.com/](https://www.twoboxes.com/)
- OpenAI GPT-4o model docs: [https://platform.openai.com/docs/models/gpt-4o](https://platform.openai.com/docs/models/gpt-4o)
- OpenAI API pricing: [https://openai.com/api/pricing/](https://openai.com/api/pricing/)
