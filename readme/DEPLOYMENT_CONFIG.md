# GitHub Actions Deployment Configuration

This document describes the required GitHub Secrets and Variables for deploying UniSoul to Raspberry Pi.

## How to Configure

1. **Secrets**: Settings → Secrets and variables → Actions → Secrets
2. **Variables**: Settings → Secrets and variables → Actions → Variables

---

## Secrets (Sensitive Data)

These values contain sensitive information and must be stored as **Secrets**:

### Docker Hub
- `DOCKER_USERNAME` - Docker Hub username
- `DOCKER_PASSWORD` - Docker Hub password/token

### Application Security
- `APP_KEY` - Laravel application key (generate with: `php artisan key:generate --show`)

### Database
- `DB_PASSWORD` - MySQL database password

### Redis
- `REDIS_PASSWORD` - Redis authentication password

### Mail
- `MAIL_PASSWORD` - Email account password

### Telegram
- `TELEGRAM_TOKEN` - Telegram bot token from @BotFather

### APIs
- `GEMINI_API_KEY` - Google Gemini API key

---

## Variables (Non-Sensitive Configuration)

These values are non-sensitive and should be stored as **Variables**:

### Application
- `APP_URL` - Application URL (e.g., `http://192.168.0.100` or `https://yourdomain.com`)

### Database
- `DB_DATABASE` - Database name (e.g., `unisoul`)
- `DB_USERNAME` - Database username (e.g., `unisoul`)

### Mail Configuration
- `MAIL_MAILER` - Mail driver (e.g., `smtp`, `sendmail`)
- `MAIL_HOST` - Mail server host (e.g., `smtp.gmail.com`)
- `MAIL_PORT` - Mail server port (e.g., `587`)
- `MAIL_USERNAME` - Mail account username/email
- `MAIL_FROM_ADDRESS` - Email sender address

### Telegram
- `TELEGRAM_BOT_USERNAME` - Telegram bot username (e.g., `@YourBot`)

### Developer Information
- `DEVELOPER_ID` - Developer Telegram ID
- `DEVELOPER_NAME` - Developer full name
- `DEVELOPER_USERNAME` - Developer Telegram username
- `DEVELOPER_EMAIL` - Developer email address
- `DEVELOPER_WEBSITE` - Developer website URL

### Owner Information
- `OWNER_ID` - Owner Telegram ID
- `OWNER_CHANNEL` - Owner Telegram channel
- `OWNER_SUPPORT` - Owner support contact

---

## Summary

| Category | Secrets (Sensitive) | Variables (Non-Sensitive) |
|----------|---------------------|---------------------------|
| Docker   | 2                   | 0                         |
| App      | 1                   | 1                         |
| Database | 1                   | 2                         |
| Redis    | 1                   | 0                         |
| Mail     | 1                   | 5                         |
| Telegram | 1                   | 1                         |
| APIs     | 1                   | 0                         |
| Developer| 0                   | 5                         |
| Owner    | 0                   | 3                         |
| **Total**| **9 Secrets**       | **17 Variables**          |

---

## Validation

After configuring, you can verify the setup by:

1. Going to Actions → Deploy to Raspberry Pi → Run workflow
2. Manually trigger a deployment with tag `latest`
3. Check the deployment logs for any missing configuration errors
