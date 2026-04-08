# Self-Hosted Web Delivery Pack

最后更新时间：2026-04-08 America/Los_Angeles

## Goal

给客户一个：

- 数据保存在自己环境里
- 不需要长期使用你的托管服务器
- 仍然保留浏览器访问、手机检查、多人使用能力

的交付版本。

这不是桌面单机版。

这是：

**`self-hosted web`**

## What This Package Includes

- 一键部署 compose: [docker-compose.self-hosted.yml](/Users/mikezhang/Desktop/projects/6POS/docker-compose.self-hosted.yml)
- 环境模板: [.env.self-hosted.example](/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted.example)
- 初始化脚本: [init.sh](/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/self-hosted/init.sh)
- 生产镜像: [Dockerfile.render](/Users/mikezhang/Desktop/projects/6POS/web-panel/Dockerfile.render)

## Why This Is Better Than A Desktop Rewrite Right Now

- 保留当前浏览器工作流
- 保留 inspector 手机端使用方式
- 保留多人角色
- 后面仍然能做外部 `Brand Review Link`
- 你不需要现在就重写桌面架构

## Two Deployment Modes

### 1. Blank Workspace

适合真实客户环境。

会初始化：

- admin accounts
- 基础 business settings

不会初始化：

- demo return cases
- demo evidence images

### 2. Demo Workspace

适合销售演示和试单环境。

会初始化：

- admin accounts
- business settings
- canonical returns demo data
- demo evidence images

## Installation Steps

### Step 1

在项目根目录复制环境文件：

```bash
cp .env.self-hosted.example .env.self-hosted
```

### Step 2

生成一个新的 app key base64 值：

```bash
openssl rand -base64 32
```

把生成结果填进：

- `APP_KEY_BASE64=...`

### Step 3

编辑 [.env.self-hosted.example](/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted.example) 对应的这些值：

- `APP_URL`
- `FORCE_HTTPS`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `APP_HOST_PORT`
- `SELF_HOSTED_BOOTSTRAP_MODE`

### Recommended Defaults

本地试跑：

```env
APP_URL=http://localhost:8080
FORCE_HTTPS=false
APP_HOST_PORT=8080
SELF_HOSTED_BOOTSTRAP_MODE=demo
```

客户正式部署：

```env
APP_URL=https://their-domain.example
FORCE_HTTPS=true
APP_HOST_PORT=8080
SELF_HOSTED_BOOTSTRAP_MODE=blank
```

### Step 4

启动数据库和应用：

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml up -d --build mysql app
```

### Step 5

跑初始化：

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml --profile setup up setup
```

初始化模式由：

- `SELF_HOSTED_BOOTSTRAP_MODE=blank`
- 或 `SELF_HOSTED_BOOTSTRAP_MODE=demo`

控制。

### Step 6

访问：

```text
http://localhost:8080
http://localhost:8080/admin/auth/login
```

## Default Login Accounts

- `admin@admin.com / 12345678`
- `ops@admin.com / 12345678`
- `inspector@admin.com / 12345678`

登录页当前默认会显示一个本地图形验证码。  
这不是部署异常，是后台默认登录保护流程的一部分。

第一次登录后必须立刻改密码。

## Upgrade Flow

### When Shipping An Update

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml up -d --build app
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml --profile setup up setup
```

如果客户环境是 `blank` 模式，第二条命令会重新跑 migration，并重新确保基础账号和配置存在。

## Backups

两个 volume 需要备份：

- `mysql-data`
- `app-storage`

前者保存数据库。  
后者保存上传文件、evidence、session/file storage。

## Scope Boundaries

这个交付包当前解决的是：

- self-hosted deployment
- customer-owned data
- no always-on vendor hosting requirement

它还没有解决：

- license activation
- paid update entitlement
- automated upgrade channel
- self-service installer
- tenant isolation

## Pricing Recommendation

这套交付方式更适合：

- 一次性 license
- 含 `12` 个月更新 / 支持
- optional setup fee

而不是：

- 终身无限更新支持

## Final Recommendation

如果你这段时间想尽快上线并开始卖：

1. 继续保留 Render 版做 demo
2. 用这套 self-hosted pack 做客户生产交付
3. 不要现在转桌面版
