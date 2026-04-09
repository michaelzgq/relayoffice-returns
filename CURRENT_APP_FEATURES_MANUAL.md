# Dossentry 当前应用功能使用说明书

## 1. 文档范围

这份文档描述的是当前可运行的 **Dossentry Web 管理后台**，地址是：

- `http://localhost:8000/admin`

这份说明书覆盖：

- `/Users/mikezhang/Desktop/projects/6POS/web-panel` 里的 Laravel 后台
- 当前已经接入的 `Returns Demo P0`
- 当前默认启用的 `returns-only workspace` 界面

这份说明书不覆盖：

- Flutter 移动端
- 单独作为产品使用的 API

## 2. 登录信息

- 登录地址：`http://localhost:8000/admin/auth/login`
- 默认管理员账号：`admin@admin.com`
- 默认密码：`12345678`
- Ops Manager 测试账号：`ops@admin.com`
- Ops Manager 密码：`12345678`
- Inspector 测试账号：`inspector@admin.com`
- Inspector 密码：`12345678`

## 3. 当前验证状态

### 已完整回归验证

- 登录流程
- returns-only 工作台默认导航
  - Ops Board
  - Inspect
  - Cases / My Cases
  - Queue
  - Playbooks
- Returns 模块全套流程
  - Client playbooks
  - Inspect return
  - Return cases
  - Refund queue
  - Ops board
  - Evidence pack export
- 角色落地行为
  - `Master Admin / Ops Manager` 登录后进入 `Ops Board`
  - `Inspector` 登录后直接进入 `Inspect Return`
  - `Inspector` 只能看自己的 case，看不到 Queue / Playbooks

### 当前 UI 默认隐藏但代码仍保留的旧模块

- POS
- Orders
- Category / Subcategory
- Brand
- Unit
- Product 管理
- Stock limit
- Coupons
- Account management
- Employee / Role
- Customer
- Supplier
- Shop settings
- Counter setup

说明：

- 这些模块当前不是默认产品体验的一部分。
- 现在默认展示的是一个 `3PL returns / refund control` 工作台，而不是零售 POS。
- 如果你只是做 returns demo、销售演示、pilot，不需要进入这些旧模块。

## 4. 第一次使用的推荐顺序

如果你准备按当前产品方向去试用，推荐按这个顺序操作：

1. `Playbooks`
2. `Inspect`
3. `Cases`
4. `Queue`
5. `Ops Board`

原因：

- 先定义每个品牌/客户的检查规则
- 再让仓库检查员提交 inspection
- 然后在 cases 和 queue 里做追踪与放款控制
- 最后在 ops board 看积压、证据缺口和队列状态

## 5. 后台通用操作逻辑

大多数模块都遵循同一套后台模式：

- `列表页`：搜索、筛选、分页、导出
- `新增页`：填写表单并保存
- `编辑页`：更新已有记录
- `状态开关`：启用 / 停用
- `详情页`：看关联数据、时间线、历史记录、导出

你上手时可以先按这个心智模型理解：  
当前默认产品体验已经不是 `POS 后台`，而是一个 `warehouse inspection + evidence + refund release control` 工作台。

## 6. 一级功能说明

## 6.1 Ops Board

### 作用

后台首页，用来快速确认当天检查量、待放款队列、超 SLA case 和证据缺口。

### 入口

- `Ops Board`

### 如何使用

1. `Master Admin / Ops Manager` 登录后默认进入这里。
2. 查看 `Inspections today / Awaiting refund action / Ready to release / Over 48h stuck`。
3. 点击卡片进入对应 queue 或 cases。
4. 查看 backlog brand 和 missing evidence 列表。

### 有什么用

- 作为 ops manager 的日常首页
- 作为销售 demo 的第一屏
- 一眼确认积压和放款风险

## 6.2 POS：New Sale

### 作用

在后台直接完成收银、建单、下单。

### 入口

- `Pos section > New Sale`

### 使用前提

- 已创建商品
- 商品有库存
- 最好至少有一个 active 的 counter
- 已配置支付账户

### 如何使用

1. 打开 `New Sale`。
2. 按分类、子分类、品牌、价格区间筛选商品。
3. 把商品加入购物车。
4. 选择客户，或者保持 walk-in。
5. 设置税、额外折扣、优惠券。
6. 选择支付方式 / 支付账户。
7. 提交订单。

### 有什么用

- 门店收银
- 后台人工建单
- 生成后续 Orders 模块可追踪的订单

### 关键说明

- POS 页面只展示有库存的 active 商品
- 系统会优先使用第一个 active counter 作为默认 counter
- 支持 held cart / 切换购物车

## 6.3 Orders

### 作用

订单总表，查看已完成订单和已退款订单。

### 入口

- `Order section > Orders`

### 可以做什么

- 查看全部订单
- 按状态筛选：`all / completed / refunded`
- 按日期、客户、支付方式、counter 筛选
- 按订单号 / 客户 / counter 搜索
- 打开订单详情
- 导出订单数据
- 从订单详情发起退款

### 如何使用

1. 打开 `Orders`。
2. 选择状态标签。
3. 用筛选条件缩小范围。
4. 打开订单详情。
5. 在详情页查看商品明细、支付账户、counter、退款信息。

### 有什么用

- 订单查单
- 退款处理
- 对账和导出

## 6.4 Returns

这是当前最值得重点使用的自定义模块。

## 6.4.1 Brand rules

### 作用

给每个品牌定义退货检查规则，控制检查员必须提交什么、允许怎么判定。

### 入口

- `Returns > Brand rules`

### 可以配置什么

- profile 名称
- 允许的 condition
- 允许的 disposition
- 必须上传的证据照片类型
- 最少照片数量
- notes 是否必填
- SKU 是否必填
- serial 是否必填
- 默认 refund status
- active / inactive

### 如何使用

1. 打开 `Brand rules`。
2. 选择品牌。
3. 创建或编辑规则 profile。
4. 保存规则。

### 有什么用

- 统一不同品牌的 returns 检查标准
- 避免检查提交不完整
- 让 refund release 有规则依据

## 6.4.2 Inspect return

### 作用

通过一个响应式检查表单创建退货 case。

### 入口

- `Returns > Inspect return`

### 表单会收集什么

- return id
- brand
- SKU
- serial number
- condition
- disposition
- refund status
- received time
- notes
- evidence photos

### 如何使用

1. 打开 `Inspect return`。
2. 输入 return id。
3. 选择品牌和商品信息。
4. 填 condition / disposition。
5. 上传证据照片。
6. 提交检查结果。

### 有什么用

- 创建标准化 return case
- 在 refund 前收集证据
- 适合作为仓库检查入口

### 关键说明

- 表单会按品牌规则校验
- serial、notes、condition、disposition、photo count 不满足规则时会被服务端拦住

## 6.4.3 Return cases

### 作用

所有退货 case 的总表。

### 入口

- `Returns > Return cases`

### 可以做什么

- 浏览所有 case
- 按状态和 SLA 条件筛选
- 打开 case 详情
- 查看 evidence 是否完整
- 查看 timeline
- 打开 evidence pack 导出

### 如何使用

1. 打开 `Return cases`。
2. 搜索或筛选 case。
3. 进入 case 详情查看证据、notes、timeline、refund 状态。

### 有什么用

- case 管理
- 财务和运营复核
- 退货问题排查

## 6.4.4 Refund queue

### 作用

退款决策队列，集中处理等待放款、等待人工复核的 cases。

### 入口

- `Returns > Refund queue`

### 可以做什么

- 按 refund status 过滤 case
- 单条修改 refund 状态
- 批量修改 refund 状态
- 在操作前识别 evidence 是否完整

### 如何使用

1. 打开 `Refund queue`。
2. 按 `hold / ready_to_release / needs_review` 筛选。
3. 单条处理，或批量勾选多个 case。
4. 填 decision note。
5. 推进到下一个 refund 状态。

### 有什么用

- 财务放款流程
- 人工复核队列
- 避免证据不完整就错误放款

### 关键说明

- evidence 不完整的 case 不能推进到 `ready_to_release` 或 `released`
- 批量动作会写入 timeline

## 6.4.5 SLA board

### 作用

Returns 运营看板，用来查看 aging、积压、缺失证据等问题。

### 入口

- `Returns > Sla board`

### 可以做什么

- 看超过 24h / 48h 的 case
- 看 missing evidence backlog
- 看按 brand 聚合的 backlog
- 点击卡片 drill-down 到真实筛选后的 queue

### 有什么用

- 运营管理
- case 积压发现
- 升级和人力调度

## 6.4.6 Evidence pack export

### 作用

导出一个 case 的证据包，用于财务复核、品牌升级、内部审计。

### 进入方式

- 打开某个 return case
- 进入 evidence pack / export 页面

### 证据包内容

- case snapshot
- operator summary
- evidence coverage checklist
- coverage gaps / 风险提示
- 证据图片区
- timeline audit

### 有什么用

- refund review
- brand escalation
- 类 chargeback 证据包
- 内部留档

## 6.4.7 Returns demo reset

### 作用

当你把 returns demo 数据点乱后，一键恢复为标准 demo 数据。

### 命令

```bash
docker compose exec app php artisan returns:reset-demo --force
```

### 有什么用

- 演示前重置环境
- 手工测试后恢复标准 case 集

## 6.5 Category / Subcategory

### 作用

维护商品分类树，POS 和商品模块都会用到。

### 入口

- `Product section > Category > Category`
- `Product section > Category > Sub category`

### 如何使用

1. 先建主分类。
2. 再在主分类下建子分类。
3. 建商品时挂到对应分类。

### 有什么用

- 商品目录结构化
- POS 端筛选更清晰
- 方便后续库存和商品管理

## 6.6 Brand

### 作用

维护商品品牌。Returns 模块也用品牌作为规则挂载点。

### 入口

- `Product section > Brand`

### 如何使用

1. 创建品牌。
2. 建商品时选择品牌。
3. 在 returns 模块里给品牌绑定 rule profile。

### 有什么用

- 商品目录组织
- POS 品牌筛选
- returns 品牌政策管理

## 6.7 Unit

### 作用

维护商品单位。

### 入口

- `Product section > Unit`

### 如何使用

1. 新建单位，例如 `pcs`、`box` 等。
2. 商品创建时选择单位。

### 有什么用

- 统一商品计量方式
- 让订单明细更清楚

## 6.8 Product management

### 入口

- `Product section > Product > Add new`
- `Product section > Product > List`
- `Product section > Product > Bulk import`
- `Product section > Product > Bulk export`

### 可以做什么

- 新增商品
- 编辑商品
- 查看商品详情
- 设置 purchase price / selling price / discount / tax
- 设置 quantity / reorder level
- 绑定 category / subcategory / brand / supplier / unit
- 上传商品图片
- 生成 barcode
- 批量导入 / 导出

### 如何使用

1. 先准备好 category、brand、supplier、unit。
2. 在 `Add new` 填写商品信息。
3. 保存后在 `List` 中维护。
4. 多 SKU 时使用 bulk import / export。

### 有什么用

- 搭建可销售商品目录
- 给 POS 供货
- 做库存和补货管理

## 6.9 Stock limit products

### 作用

显示低库存商品，也就是库存低于或等于 reorder level 的商品。

### 入口

- `Product section > Stock limit products`

### 可以做什么

- 看低库存商品列表
- 按分类、品牌、供应商、价格等筛选
- 直接修改商品数量
- 导出低库存报表

### 有什么用

- 补货计划
- 提前发现缺货风险

## 6.10 Coupons

### 作用

管理 POS 可用的优惠券。

### 入口

- `Business section > Coupons`

### 可以配置什么

- title
- code
- coupon type
- user limit
- 生效时间 / 失效时间
- minimum purchase
- maximum discount
- discount type / value
- active / inactive

### 有什么用

- 促销活动
- POS 优惠券减免

## 6.11 Account management

### 入口

- `Business section > Account management > Add new account`
- `Business section > Account management > Accounts`
- `Business section > Account management > New expense`
- `Business section > Account management > New income`
- `Business section > Account management > New transfer`
- `Business section > Account management > Transaction list`

### 作用

这是后台里的基础财务工作台，用来维护账户和现金流动。

### 可以做什么

- 创建账户
- 查看账户余额
- 记录收入
- 记录支出
- 账户间转账
- 查看交易流水
- 导出账户数据

### 有什么用

- POS 结算支撑
- 供应商 / 客户相关财务动作支撑
- 基础内部对账

### 建议使用方式

1. 先建账户。
2. POS、supplier、customer 相关流程都尽量使用这些账户。
3. 定期查看 transaction list。

## 6.12 高级财务：Payable / Receivable

这两个入口当前不是左侧菜单一级入口，但路由是存在的。

### 隐藏入口

- Payable：`/admin/account/add-payable`
- Receivable：`/admin/account/add-receivable`

### 作用

- `Payable`：登记应付项，并从支付账户中结清
- `Receivable`：登记应收项，并在到账后转入收款账户

### 有什么用

- 内部财务记账
- 管理 POS 之外的应收应付

## 6.13 Employee role / Employee

### 入口

- `Employee Section > Employee Role`
- `Employee Section > Add Employee`

### 可以做什么

- 创建自定义 admin 角色
- 配置模块级权限
- 新增员工账号
- 编辑员工资料
- 启用 / 停用员工
- 导出员工数据

### 有什么用

- 多管理员协作
- 权限控制

## 6.14 Customer management

### 入口

- `Customer section > Add customer`
- `Customer section > Customer list`

### 可以做什么

- 新增客户
- 编辑客户资料
- 维护联系方式和地址
- 设置 opening balance
- 查看客户详情
- 查看客户订单
- 查看客户交易流水
- 更新客户余额
- 导出客户数据

### 有什么用

- 把 POS 订单绑定到真实客户
- 跟踪客户消费和余额
- 快速查某个客户的订单历史

## 6.15 Supplier management

### 入口

- `Supplier section > Add supplier`
- `Supplier section > Supplier list`

### 可以做什么

- 新增供应商
- 编辑供应商资料
- 查看供应商名下商品
- 查看供应商交易记录
- 新增采购记录
- 支付供应商欠款
- 导出供应商数据

### 有什么用

- 供应商管理
- 采购与应付跟踪
- 把商品与供应商绑定

## 6.16 Shop settings

### 入口

- `Shop setting section > Settings > Shop setup`
- `Shop setting section > Settings > Recaptcha setup`

### 可以做什么

- 设置 shop 基础信息
- 上传 shop logo
- 上传 favicon
- 配置 reCAPTCHA 状态和 key

### 有什么用

- 品牌化后台和登录页
- 控制登录验证码行为

### 注意

- reCAPTCHA key 配错会让本地登录测试变麻烦

## 6.17 Counter setup

### 作用

Counter 可以理解成收银台 / 终端 / cashier station。

### 入口

- `Counter section > Counter setup`

### 可以做什么

- 新建 counter
- 编辑 counter
- 启用 / 停用 counter
- 看某个 counter 的订单详情
- 导出 counter 列表
- 导出某个 counter 的订单明细

### 有什么用

- 管理门店终端
- 让 POS 订单能追溯到具体收银台

## 6.18 管理员资料与密码

这不是左侧菜单模块，但也是当前可用功能的一部分。

### 入口

- 右上角 Settings / Profile
- 路由：
  - `/admin/settings`
  - `/admin/settings-password`

### 可以做什么

- 修改管理员姓名
- 修改邮箱和手机号
- 上传头像
- 修改密码

## 7. 详情页里的二级功能

这些不是一级菜单，但实际能用，而且很重要。

- `Orders`
  - 订单详情
  - 订单商品弹窗
  - 退款
  - 导出
- `Products`
  - 编辑
  - 详情页
  - barcode 生成
- `Customers`
  - 客户详情
  - 订单历史
  - 交易流水
  - 余额更新
- `Suppliers`
  - 供应商详情
  - 商品列表
  - 交易流水
  - 新增采购
  - 支付欠款
- `Stock`
  - 修改商品数量弹窗
- `Counters`
  - counter 详情
  - counter 级订单导出
- `Returns`
  - case 详情
  - 单条 refund decision 更新
  - 批量 refund decision 更新
  - evidence pack 导出

## 8. 三个最实用的使用流程

## 8.1 基础零售操作流

1. 做好 `Shop setup`
2. 创建 `Counter`
3. 创建 `Category / Brand / Unit`
4. 创建 `Supplier`
5. 创建 `Product` 并设置库存
6. 创建 `Customer`
7. 创建 `Account`
8. 进入 `POS > New Sale`
9. 订单完成后去 `Orders` 查看

## 8.2 Returns 演示流

1. 确认 `Brand rules`
2. 打开 `Inspect return`
3. 创建 return case 并上传证据
4. 在 `Return cases` 查看详情
5. 在 `Refund queue` 推进 refund 状态
6. 在 `Sla board` 看 backlog
7. 导出 `Evidence Pack`

## 8.3 Supplier / Finance 流

1. 创建 supplier
2. 把 product 绑定 supplier
3. 在 supplier 详情里记录 purchase / pay due
4. 在 account management 里追踪资金流
5. 在 transaction list 里复核流水

## 9. 当前环境的几个现实说明

- 当前应用最强的部分是：`Web admin + Returns demo workspace`
- Returns 模块是当前最深度验证的一块
- 原始 vendor 模块已经存在并可进入，但在真正当生产系统前，建议你先做一次手工 smoke test
- `/Users/mikezhang/Desktop/projects/6POS/web-panel` 现在还不是 git repo，当前改动还没有版本管理

## 10. 常用本地命令

### 重置 returns demo 数据

```bash
docker compose exec app php artisan returns:reset-demo --force
```

### 跑 returns 自动化测试

```bash
docker compose exec app php artisan test tests/Feature/Returns
```

## 11. 后续最值得继续补的文档

如果你后面要把这份说明书继续做深，我建议下一批文档直接写这 4 份：

- `POS_OPERATING_SOP.md`
- `RETURNS_OPERATOR_SOP.md`
- `FINANCE_WORKFLOW_SOP.md`
- `DEMO_SCRIPT.md`
