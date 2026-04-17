# Dossentry Guided Self-Hosted Install SOP

Last updated: 2026-04-17 America/Los_Angeles

## Goal

Use this SOP when you are helping a real customer install the Docker self-hosted package.

Success means:

- the customer runs a `blank` workspace install
- the first owner account can log in
- the customer understands how to add staff and keep backups

## Target Delivery Mode

For real customers, the default path is:

- `blank` workspace
- customer-owned infrastructure
- guided install
- one owner account created during bootstrap

Do not use `demo` mode for formal production delivery.

## Time Estimate

Plan for:

- `15-20 min` preflight
- `20-30 min` install and bootstrap
- `15-20 min` smoke test and handoff

Normal guided install target:

- `60-90 min`

## Files To Have Ready

- [self-hosted-web-delivery-pack.md](/Users/mikezhang/Desktop/projects/6POS/self-hosted-web-delivery-pack.md)
- [dossentry-docker-install-handoff-checklist.md](/Users/mikezhang/Desktop/projects/6POS/dossentry-docker-install-handoff-checklist.md)
- [docker-compose.self-hosted.yml](/Users/mikezhang/Desktop/projects/6POS/docker-compose.self-hosted.yml)
- [.env.self-hosted.example](/Users/mikezhang/Desktop/projects/6POS/.env.self-hosted.example)
- [web-panel/scripts/self-hosted/init.sh](/Users/mikezhang/Desktop/projects/6POS/web-panel/scripts/self-hosted/init.sh)

## Preflight Checklist

Before the install call:

- confirm the customer has Docker available
- confirm the target host/domain
- confirm whether the install is `http` or `https`
- collect the owner email and password
- collect optional ops and inspector accounts
- confirm who is responsible for backups
- confirm who owns future update execution

Do not start the install until you also confirm:

- the chosen port is free
- the customer has generated or can generate a real `APP_KEY_BASE64`

## Recommended Env Review

Review these fields live with the customer:

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

For customer production installs, set:

```env
SELF_HOSTED_BOOTSTRAP_MODE=blank
```

## Guided Install Runbook

### Step 1. Copy the env file

```bash
cp .env.self-hosted.example .env.self-hosted
```

### Step 2. Fill real values

Make sure placeholders are removed, especially:

- `APP_KEY_BASE64`
- passwords
- owner email

### Step 3. Start MySQL and app

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml up -d --build mysql app
```

### Step 4. Run bootstrap

```bash
docker compose --env-file .env.self-hosted -f docker-compose.self-hosted.yml --profile setup up setup
```

Expected success lines:

```text
Blank workspace bootstrapped with customer-owned accounts.
Self-hosted bootstrap finished in mode: blank
```

### Step 5. Run browser smoke test

Confirm:

- `/`
- `/admin/auth/login`
- first admin login

### Step 6. Admin handoff

After login, show:

- main workspace
- `Settings -> Workspace Access`
- how to add or remove staff

## Mandatory Acceptance Test

Do not close the install until all of these pass:

- homepage returns `200`
- login page returns `200`
- owner credentials sign in successfully
- owner reaches the workspace
- customer sees their own owner email in the admin table
- demo guest account is not present in `blank` mode

## Known Failure Modes

### 1. Login page hangs or is unusually slow

Check:

- outbound network policies
- whether the current code includes the self-hosted login-path fix

The current known fix is in:

- [AppServiceProvider.php](/Users/mikezhang/Desktop/projects/6POS/web-panel/app/Providers/AppServiceProvider.php)

### 2. Immediate `500` after install

Check first:

- invalid or placeholder `APP_KEY_BASE64`
- broken DB credentials
- malformed `.env.self-hosted`

### 3. Wrong accounts appear after bootstrap

Check:

- `SELF_HOSTED_BOOTSTRAP_MODE`
- owner email fields
- whether the customer accidentally ran `demo` mode

### 4. Bootstrap finishes but customer cannot log in

Check:

- exact owner email in `.env.self-hosted`
- password typing
- captcha entry
- whether the customer is on the correct host and port

## Post-Install Handoff Script

Before ending the session, tell the customer:

- where the login URL is
- which owner email was created
- where staff accounts are managed
- which Docker volumes must be backed up
- which commands to run for future upgrades

## Commercial Guardrails

Position the offer as:

- guided self-hosted deployment
- customer-owned data
- paid setup plus support window

Do not promise:

- one-click installation
- automatic updates
- zero-touch upgrades

## Internal Rule

Never call a Docker delivery “customer-ready” unless a **brand-new blank install** has proven:

- bootstrap success
- login page load
- real owner login

This is now a release gate, not an optional QA step.
