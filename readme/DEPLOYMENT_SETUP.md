# Deployment Setup Guide

This guide explains all the GitHub Secrets and Variables you need to configure for successful deployment to your Raspberry Pi.

## Prerequisites

- PostgreSQL database running on your Raspberry Pi (accessible via `host.docker.internal`)
- Redis server running on your Raspberry Pi (accessible via `host.docker.internal`)
- Docker and Docker Compose installed on your Raspberry Pi
- Self-hosted GitHub Actions runner configured on your Raspberry Pi

---

## GitHub Secrets Configuration

Go to: **Settings → Secrets and variables → Actions → Secrets**

Click **"New repository secret"** for each of the following:

### Required Secrets

| Secret Name | Description | Example |
|------------|-------------|---------|
| `DOCKER_USERNAME` | Your Docker Hub username | `yourusername` |
| `DOCKER_PASSWORD` | Your Docker Hub password or access token | `dckr_pat_xxxxx...` |
| `APP_KEY` | Laravel application encryption key | `base64:xxxxx...` |
| `DB_PASSWORD` | PostgreSQL database password | `your_secure_password` |
| `REDIS_PASSWORD` | Redis server password (use `null` if no auth) | `your_redis_password` or `null` |
| `TELEGRAM_TOKEN` | Telegram bot token from @BotFather | `123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11` |
| `OWNER_ID` | Your Telegram user ID (for admin access) | `123456789` |
| `GEMINI_API_KEY` | Google Gemini API key | `AIzaSyDWq5fW1jHpSSgRn3pzfXiGzvARHWm5VHU` |
| `MAIL_PASSWORD` | Email SMTP password (optional) | `your_email_password` |

### How to Generate APP_KEY

Run this command locally:
```bash
php artisan key:generate --show
```

Copy the output (starts with `base64:`) and paste it as the `APP_KEY` secret.

### How to Get OWNER_ID

1. Message your bot on Telegram
2. Go to `https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates`
3. Find your user ID in the response under `message.from.id`

---

## GitHub Variables Configuration

Go to: **Settings → Secrets and variables → Actions → Variables**

Click **"New repository variable"** for each of the following:

### Required Variables

| Variable Name | Description | Example |
|--------------|-------------|---------|
| `APP_URL` | Your application URL | `http://192.168.1.100:8050` |
| `APP_PORT` | Port to expose on Raspberry Pi | `8050` |
| `DB_DATABASE` | PostgreSQL database name | `unisoul` |
| `DB_USERNAME` | PostgreSQL database user | `unisoul_user` |
| `GEMINI_MODEL` | Gemini AI model to use | `gemini-2.5-flash` |
| `TELEGRAPH_BOT_NAME` | Your bot username (with @) | `@your_bot_username` |

### Optional Variables (for email)

| Variable Name | Description | Example |
|--------------|-------------|---------|
| `MAIL_MAILER` | Mail driver | `smtp` |
| `MAIL_HOST` | SMTP host | `smtp.gmail.com` |
| `MAIL_PORT` | SMTP port | `587` |
| `MAIL_USERNAME` | SMTP username | `your@email.com` |
| `MAIL_FROM_ADDRESS` | From email address | `noreply@yourdomain.com` |

---

## Database Setup on Raspberry Pi

Before deploying, ensure PostgreSQL is running and configured:

```bash
# Install PostgreSQL (if not installed)
sudo apt update
sudo apt install postgresql postgresql-contrib

# Start PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Create database and user
sudo -u postgres psql
```

In PostgreSQL shell:
```sql
-- Create user
CREATE USER unisoul_user WITH PASSWORD 'your_secure_password';

-- Create database
CREATE DATABASE unisoul OWNER unisoul_user;

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE unisoul TO unisoul_user;

-- Exit
\q
```

### Configure PostgreSQL for Docker Access

Edit PostgreSQL config to allow connections from Docker:

```bash
sudo nano /etc/postgresql/15/main/postgresql.conf
```

Add or modify:
```
listen_addresses = 'localhost,172.17.0.1'
```

Edit access control:
```bash
sudo nano /etc/postgresql/15/main/pg_hba.conf
```

Add:
```
host    all             all             172.17.0.0/16           scram-sha-256
```

Restart PostgreSQL:
```bash
sudo systemctl restart postgresql
```

---

## Redis Setup on Raspberry Pi

```bash
# Install Redis (if not installed)
sudo apt update
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
```

Find and modify:
```
# Bind to localhost and Docker bridge
bind 127.0.0.1 172.17.0.1

# Set password (optional but recommended)
requirepass your_redis_password
```

Restart Redis:
```bash
sudo systemctl restart redis
sudo systemctl enable redis
```

---

## Deployment Checklist

Before triggering deployment, verify:

- [ ] All GitHub Secrets are set correctly
- [ ] All GitHub Variables are set correctly
- [ ] PostgreSQL is running and accessible from Docker
- [ ] Redis is running and accessible from Docker
- [ ] Port 8050 (or your APP_PORT) is available
- [ ] Self-hosted GitHub Actions runner is online
- [ ] Docker Hub credentials are valid

---

## Environment Variables Reference

### Complete .env Structure (Generated Automatically)

```env
APP_NAME=UniSoul
APP_ENV=production
APP_KEY=<from secrets>
APP_DEBUG=false
APP_URL=<from variables>
APP_PORT=<from variables>

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=host.docker.internal
DB_PORT=5432
DB_DATABASE=<from variables>
DB_USERNAME=<from variables>
DB_PASSWORD=<from secrets>

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=redis
QUEUE_CONNECTION=redis

REDIS_CLIENT=phpredis
REDIS_HOST=host.docker.internal
REDIS_PASSWORD=<from secrets>
REDIS_PORT=6379

MAIL_MAILER=<from variables>
MAIL_HOST=<from variables>
MAIL_PORT=<from variables>
MAIL_USERNAME=<from variables>
MAIL_PASSWORD=<from secrets>
MAIL_FROM_ADDRESS=<from variables>
MAIL_FROM_NAME="UniSoul"

MODEL_SETTINGS_PERSISTENT=true

SUPPORTED_LANGUAGES=uk,en,ro,de,ka

TELEGRAM_TOKEN=<from secrets>
OWNER_ID=<from secrets>

GEMINI_API_KEY=<from secrets>
GEMINI_MODEL=<from variables>

TELEGRAPH_BOT_NAME=<from variables>
TELEGRAPH_TOKEN=<from secrets>
TELEGRAPH_WEBHOOK_DEBUG=false
```

---

## Testing Database Connection

After PostgreSQL setup, test the connection:

```bash
# From your Raspberry Pi
psql -h localhost -U unisoul_user -d unisoul -W

# From Docker container (after deployment)
docker exec -it unisoul-app php artisan db:show
```

---

## Troubleshooting

### Connection Refused Errors

**Problem**: Can't connect to PostgreSQL or Redis

**Solution**:
1. Check if services are running: `sudo systemctl status postgresql redis`
2. Verify Docker can reach host: `docker run --rm alpine ping -c 3 host.docker.internal`
3. Check firewall rules: `sudo ufw status`

### Authentication Failed

**Problem**: `Access denied for user` or `password authentication failed`

**Solution**:
1. Verify DB_USERNAME and DB_PASSWORD match PostgreSQL user
2. Check `pg_hba.conf` allows connections from Docker network
3. Restart PostgreSQL after config changes

### Port Already in Use

**Problem**: `port 8050 already in use`

**Solution**:
1. Check what's using the port: `sudo lsof -i :8050`
2. Stop the conflicting service or change APP_PORT variable
3. Update APP_URL to match the new port

### Migration Failures

**Problem**: Migrations fail during deployment

**Solution**:
1. Check database exists: `sudo -u postgres psql -l | grep unisoul`
2. Verify database permissions: `GRANT ALL PRIVILEGES ON DATABASE unisoul TO unisoul_user;`
3. Check Laravel can connect: `docker exec -it unisoul-app php artisan migrate:status`

---

## Support

If you encounter issues:

1. Check GitHub Actions logs for detailed error messages
2. View container logs: `docker logs unisoul-app`
3. Check PostgreSQL logs: `sudo tail -f /var/log/postgresql/postgresql-15-main.log`
4. Check Redis logs: `sudo journalctl -u redis -f`

---

## Security Best Practices

- ✅ Use strong, unique passwords for all services
- ✅ Keep your `TELEGRAM_TOKEN` and `GEMINI_API_KEY` secret
- ✅ Regularly rotate `APP_KEY` and database passwords
- ✅ Set up firewall rules to restrict access to PostgreSQL and Redis
- ✅ Use SSL/TLS for production deployments
- ✅ Enable Redis authentication with `requirepass`
- ✅ Regularly update all packages and dependencies
- ✅ Monitor logs for suspicious activity
- ✅ Keep backups of your database

---

**Ready to Deploy?**

Once all secrets and variables are configured, trigger a deployment:
1. Create a new release/tag, or
2. Manually trigger the workflow from the Actions tab
