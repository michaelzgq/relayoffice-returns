# Dossentry 当前产品总览

客户版
更新：2026年4月13日

## 执行摘要

Dossentry 是一个面向多品牌退货场景的仓库端异常处理层。

它解决的是这样一个时刻：退货已经回到仓库，团队需要清楚说明：

- 当前适用的是哪一套品牌规则
- 现场拍到了哪些证据
- 实际观察到的商品状态是什么
- 仓库建议的处理动作是什么
- 品牌方或运营下一步应该看什么

Dossentry 不是 shopper-facing returns portal，也不是 WMS 替代品。

它是叠加在现有系统之上的一层，补上大多数团队仍然靠 SOP PDF、共享文件夹和聊天记录处理的那一段：品牌规则、近距离证据采集、case 复核，以及可直接发给品牌方的整洁记录。

## 核心客户收益

- 用团队现有手机就能完成取证
- 把每个品牌的 SOP 变成实时 playbook
- 用一个 Decision Queue 管理高风险或有争议的 case
- 用一个 Brand Review Link 代替截图包和邮件来回解释
- 支持 Docker self-hosted，数据和账号控制在客户自己手里

## 当前已经可用的能力

### 1. 配置品牌 Playbook

仓库或运营团队可以为不同客户创建独立 playbook，定义：

- 允许的商品 condition
- 允许的仓库处理动作
- 每种 condition 的推荐默认动作
- 必拍照片类型
- 最低照片数量
- 是否要求备注、SKU 和序列号
- 默认 decision state

这样同一个仓库就能同时服务多个品牌，而不必让检查员去记住不同 SOP。

### 2. 手机优先检查

检查员可以直接在浏览器里的检查流程完成录入，包括：

- return ID
- brand / client playbook
- SKU 或条码
- 序列号
- condition
- warehouse action
- notes
- 必要照片

当前流程支持：

- 支持的手机浏览器 live camera scan
- 手机 live scan 不稳定时的拍照识别兜底
- USB / 蓝牙扫码枪输入
- 手动输入

这套流程是围绕仓库一线的近景证据采集设计的，而不是围绕固定工位设计的。

### 3. 自动形成结构化 Case Record

每次检查都会生成一个 return case，包含：

- evidence count
- evidence completeness status
- condition 和 disposition
- SKU 与序列号信息
- notes
- timestamps
- timeline events
- SLA age

这样运营负责人不需要事后再重新拼 case 经过。

### 4. 在 Decision Queue 里复核

运营团队可以在专门的 Decision Queue 中管理 case，当前状态包括：

- hold
- ready for brand review
- needs review

这个队列支持：

- 按品牌筛选
- 按缺失证据筛选
- 按 SLA aging 筛选
- case 级复核
- 批量更新 decision
- audit notes

如果证据不完整，case 不会继续往前流转。

### 5. 发送 Brand Review Link

当 case 有争议时，Dossentry 可以生成一个受保护的只读 Brand Review Link，让品牌方或运营查看：

- 照片
- timeline
- playbook 快照
- recommendation
- evidence status

这是当前最核心的对外证明资产。仓库团队不用再发截图、文件夹和 Slack 对话，而是发一个干净的 review record。

### 6. 导出 Brand Defense Pack PDF

团队可以打开或下载一份 PDF case pack，其中包含：

- executive summary
- evidence gallery
- timeline
- decision context
- rule coverage

适合 escalations、客户沟通和内部复核。

### 7. 用一个 Ops Board 管理日常节奏

Ops Board 当前提供的管理视图包括：

- 当日检查量
- awaiting decision review
- ready for brand review
- 超过 48 小时仍卡住的 case
- 各品牌 backlog
- 缺失证据
- recent inspections

这样仓库运营负责人不用额外拉很多报表，也能看到处理节奏和风险点。

## 当前完整流程

当前产品流程是：

1. 创建 client playbook
2. 在手机或浏览器中检查退货商品
3. 拍摄要求的照片，记录 notes、SKU 和序列号
4. 自动形成结构化 case record
5. 在 Decision Queue 中复核
6. 当需要对外解释时生成 Brand Review Link 或 PDF
7. 在 Ops Board 中查看 backlog 和超时 case

用最直白的话说：

Dossentry 帮助仓库团队从“我们应该是按 SOP 做了”走到“这是我们可以直接拿出来给品牌方看的完整记录”。

## 为什么仓库团队会使用 Dossentry

### 让多品牌规则标准化

不同品牌对证据和处理逻辑的要求都不一样。

Dossentry 把这些要求变成实时 playbook，仓库员工不再依赖记忆和分散 SOP。

### 拍到品牌方真正会问的证据

固定工位或通用 inspection app 往往只停留在大图或 checklist 完成。

Dossentry 强调的是近景证据，例如：

- 序列号标签
- 包装损伤
- 盒内状态
- 侧面 condition 细节

### 减少品牌方来回追问

当品牌方质疑退货处理结果时，最难的通常不是打开检查页，而是事后把整个故事重新讲清楚。

Dossentry 把这一步缩短成一份可直接复核的 record。

### 在证据完整前就卡住弱 case

这个产品不只是存照片，它还会判断当前证据是否足够进入下一步。

这样可以避免证据薄弱的 case 继续往前流转。

### 保留现有系统

团队不需要替换现有 WMS 或 shopper-facing returns portal 才能用 Dossentry。

它是叠加在现有系统旁边的仓库端 exception layer。

### 不需要重建仓库工位

Dossentry 是 browser-based、phone-first 的。团队可以直接用现有设备开始，而不是先做一轮固定拍摄工位改造。

## 最适合的客户

Dossentry 当前最适合：

- 多品牌 3PL
- 服务多个 DTC 品牌的运营团队
- 经常处理高风险或有争议退货 case 的仓库团队
- 仍然通过 SOP、表格、文件夹或聊天记录管理规则和证据的团队
- 需要给品牌方提供更清晰 review record 的团队

## Dossentry 不是要替代什么

当前产品并不把自己定位成：

- shopper-facing returns portal
- 运单或换货平台
- 完整 reverse logistics suite
- system-of-record inventory reconciliation 平台
- WMS 替代品

这个边界是刻意保留的。

它最强的定位，是仓库端的 evidence 和 exception-control layer。

## 部署与访问方式

当前产品支持：

- 浏览器端仓库使用
- 在线 web demo
- Docker self-hosted 部署
- 客户自有数据和员工账号控制

这对想快速上手、但不想推进重型系统迁移的团队尤其有价值。

## 当前外部入口

官网：

- https://dossentry.com

对比页：

- https://dossentry.com/compare/generic-inspection-apps

Demo 登录：

- https://demo.dossentry.com/admin/auth/login
