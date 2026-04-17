# Dossentry Docker Install Handoff Checklist

Last updated: 2026-04-17 America/Los_Angeles

## Purpose

Use this checklist when a customer wants a **self-hosted Docker deployment** of Dossentry.

This package is designed for:

- customer-controlled infrastructure
- browser-based warehouse use
- mobile inspection workflows
- multiple staff accounts inside one workspace

This is **not** a one-click self-service installer.

The current recommended delivery model is:

- customer IT runs the Docker package
- Dossentry guides the first installation
- formal production use starts in `blank` mode

## What The Customer Receives

- compose file: [docker-compose.self-hosted.yml](/Users/mikezhang/Desktop/projects/6POS/docker-compose.self-hosted.yml)
- env template: [.env.self-hosted.example](/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted.example)
- bootstrap script: [web-panel/scripts/self-hosted/init.sh](/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/self-hosted/init.sh)
- delivery overview: [self-hosted-web-delivery-pack.md](/Users/mikezhang/Desktop/projects/6POS/self-hosted-web-delivery-pack.md)
- this checklist

## Prerequisites

The customer should have:

- one Linux host or VM with Docker and Docker Compose available
- one domain or subdomain for the workspace
- outbound internet access for package/image pulls
- a place to store backups
- one owner email for the first admin account

Recommended minimum starting point:

- `2 vCPU`
- `4 GB RAM`
- `20+ GB` free disk

## Required Inputs Before Install

Collect these before the install session starts:

- workspace URL
- whether the install will run on `http` or `https`
- chosen host port
- primary owner first name, last name, email, password
- optional ops manager email/password
- optional inspector email/password
- database passwords
- SMTP values if email notifications are required

## Recommended Production Values

Use `blank` mode for real customer installs.

```env
APP_URL=https://their-domain.example
FORCE_HTTPS=true
APP_HOST_PORT=8080
SELF_HOSTED_BOOTSTRAP_MODE=blank
```

You must also generate a real application key:

```bash
openssl rand -base64 32
```

Put the output into:

```env
APP_KEY_BASE64=...
```

## Installation Steps

### 1. Prepare the env file

From the project root:

```bash
cp .env.self-hosted.example .env.self-hosted
```

Edit `.env.self-hosted` and fill in:

- `APP_URL`
- `FORCE_HTTPS`
- `APP_KEY_BASE64`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `APP_HOST_PORT`
- `SELF_HOSTED_BOOTSTRAP_MODE`
- `SELF_HOSTED_PRIMARY_ADMIN_*`

Optional but recommended:

- `SELF_HOSTED_OPS_ADMIN_*`
- `SELF_HOSTED_INSPECTOR_*`
- SMTP settings if the customer wants email notifications

### 2. Start MySQL and the app

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml up -d --build mysql app
```

### 3. Run the first bootstrap

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml --profile setup up setup
```

Expected success message in `blank` mode:

```text
Blank workspace bootstrapped with customer-owned accounts.
Self-hosted bootstrap finished in mode: blank
```

### 4. Open the workspace

Open:

```text
https://their-domain.example
https://their-domain.example/admin/auth/login
```

If this is a local test install, use the local URL and port instead.

### 5. First login

Sign in with the primary admin credentials from `.env.self-hosted`.

The login page includes a local captcha by default.
That is expected.

## Post-Install Smoke Test

Run this before calling the install complete:

- homepage loads
- `/admin/auth/login` loads
- primary admin can sign in
- admin reaches the main workspace after login
- `Settings -> Workspace Access` opens
- optional ops or inspector account can sign in if they were configured

## Backups

Back up these Docker volumes:

- `mysql-data`
- `app-storage`

They contain:

- database records
- uploaded evidence
- session/file storage

## Upgrade Commands

When shipping an update:

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml up -d --build app
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml --profile setup up setup
```

## Scope Boundaries

This package currently supports:

- guided Docker deployment
- customer-controlled data
- blank workspace bootstrap
- multi-user browser access

It does **not** currently provide:

- self-service installer UI
- automated upgrade channel
- license entitlement automation
- tenant isolation across multiple customer orgs in one install

## Install Acceptance Criteria

The install is complete only when all of these are true:

- services start cleanly
- bootstrap completes without errors
- the login page loads quickly
- the customer owner account can sign in
- the customer confirms the first admin credentials work

## If Something Fails

Check these first:

- `.env.self-hosted` exists and is the file actually being used
- `APP_KEY_BASE64` is a real generated value, not a placeholder
- `SELF_HOSTED_BOOTSTRAP_MODE=blank` for formal customer installs
- Docker port mapping does not conflict with another local service
- MySQL credentials match across `DB_*` and `MYSQL_*`

## Recommended Commercial Framing

The safest way to sell this today is:

- paid setup
- guided installation
- customer-owned deployment
- optional support/updates term

Do **not** position this as:

- one-click self-serve install
- no-support deployment
- automatic upgrade SaaS
