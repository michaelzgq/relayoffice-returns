# Render 部署方案：`Dossentry`

最后更新时间：2026-04-07 America/Los_Angeles

## 1. 目标

把当前 `Dossentry` returns-only V0 部署到你自己的线上域名。

部署平台：

- `Render`

原因：

- 对当前 `Laravel + MySQL + 文件上传` 结构最省心
- 比自己管 VPS 更快
- 比 Railway 的常驻成本更好预测

## 2. 这次已经准备好的文件

### Render blueprint

- [render.yaml](/Users/mikezhang/Desktop/projects/6POS/render.yaml)
- MySQL builder image: [docker/mysql/Dockerfile](/Users/mikezhang/Desktop/projects/6POS/docker/mysql/Dockerfile)

### 生产 Docker

- [Dockerfile.render](/Users/mikezhang/Desktop/projects/6POS/web-panel/Dockerfile.render)
- [nginx.conf](/Users/mikezhang/Desktop/projects/6POS/web-panel/docker/render/nginx.conf)
- [site.conf.template](/Users/mikezhang/Desktop/projects/6POS/web-panel/docker/render/site.conf.template)
- [supervisord.conf](/Users/mikezhang/Desktop/projects/6POS/web-panel/docker/render/supervisord.conf)

### 启动与发布脚本

- [bootstrap-app-env.sh](/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/bootstrap-app-env.sh)
- [predeploy.sh](/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/predeploy.sh)
- [start.sh](/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/render/start.sh)

### 运行时补充

- 健康检查路由：[web.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/routes/web.php)
- HTTPS 强制：[AppServiceProvider.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Providers/AppServiceProvider.php)

### 本地已验证结果

- `render.yaml` 已通过 YAML 解析检查
- Render 生产镜像已成功 `docker build`
- 本地容器烟雾测试已通过：
  - `GET /healthz -> 200`
- 生产镜像当前使用：
  - `PHP 8.4`

这一点很重要：

- 当前 `composer.lock` 已锁到需要 `PHP 8.4` 的包，所以 Render 生产镜像不能再用 `PHP 8.2`

## 3. 部署前你还需要做的事

### 必做

1. 在 Render 新建 Blueprint 部署  
选择这个 repo，Render 会读取根目录的 `render.yaml`。

2. 先部署，再绑域名  
域名建议绑定到 web service：
- `demo.yourdomain.com`

### 最短路径

1. Render 里点 `New + > Blueprint`
2. 选当前 GitHub 仓库
3. `Blueprint Name` 填 `dossentry-demo`
4. `Branch` 选 `main`
5. `Blueprint Path` 填 `render.yaml`
6. 等第一次部署完成
7. 再绑定你的 demo 子域名

### 如果 Blueprint 页面报 MySQL image 错误

说明 Render 还没拉到最新仓库内容。

这时只做 2 件事：

1. 确认 GitHub 上根目录的 [render.yaml](/Users/mikezhang/Desktop/projects/6POS/render.yaml) 里 MySQL service 使用的是：
   - `env: docker`
   - `dockerContext: ./docker/mysql`
   - `dockerfilePath: ./docker/mysql/Dockerfile`
2. 回 Render 刷新 Blueprint 页面

## 4. DNS 建议

不要先把根域名直接给应用。

建议：

- `yourdomain.com` 留给 landing page
- `demo.yourdomain.com` 给当前产品 demo

### DNS 记录

在 Render 添加自定义域名后，按它给出的目标值配置：

- `CNAME`
- 名称：`demo`
- 值：Render 提供的目标域名

不要现在就自己猜值，Render 创建域名时会给你准确记录。

### 为什么不用根域名

根域名直接绑应用不是不行，但不够省事。

当前最方便的做法是：

- `yourdomain.com` 留给 landing page
- `demo.yourdomain.com` 直接指向 Render app

这样不需要先处理 apex 记录、官网和应用拆分也更清楚。

## 5. 首次部署后的必须操作

Render 第一次部署完成后，打开 web service 的 Shell，执行：

```bash
php artisan db:seed --class=AdminTableSeeder --force
php artisan returns:reset-demo --force --bootstrap
```

这两步的作用：

- 创建默认后台账号和角色
- 生成 canonical demo 数据与证据图片

## 6. 首次部署后的登录信息

默认账号：

- `admin@admin.com / 12345678`
- `ops@admin.com / 12345678`
- `inspector@admin.com / 12345678`

### 但上线后必须马上做

1. 先登录管理员账号
2. 立刻改密码
3. 如果准备给外部客户演示，再单独创建 demo 账号

## 7. 上线后 QA 顺序

按这个顺序验证：

1. `https://demo.yourdomain.com/healthz`
2. `https://demo.yourdomain.com/admin/auth/login`
3. 用 `admin` 登录
4. 打开 `Ops Board`
5. 打开 `Inspect`
6. 提交一个新 case
7. 打开 `Cases`
8. 打开 `Queue`
9. 打开某个 case 的 `Brand Defense Pack`
10. 下载 PDF

### 预期

- 第 `1` 步应该直接返回 JSON：
  - `{"status":"ok", ...}`
- 如果第 `1` 步不通，不要继续点后台页面，先回 Render 看：
  - build log
  - predeploy log
  - web service log

## 8. 当前大致成本

### Render 资源

- Web service: `Starter`
- MySQL private service: `Starter`
- MySQL disk: `10GB`
- App public storage disk: `5GB`

### 粗略月成本

大约：

- `$16 - $20+ / month`

取决于磁盘和 Render 当月价格，但对当前 demo/pilot 已经足够。

## 9. 当前不是正式 SaaS 的原因

这次部署目标是：

**`pilot cloud demo`**

不是：

**公开自助注册 SaaS**

因为现在还没有：

- 自助注册
- 订阅/计费
- 密码重置
- 多租户隔离
- 正式生产监控和备份策略

## 10. 一句话执行建议

### 现在最对的顺序

1. 先创建并推 GitHub repo
2. Render 导入 `render.yaml`
3. 绑定你的 demo 子域名
4. 首次 seed
5. 跑 QA
6. 再开始外部 demo
