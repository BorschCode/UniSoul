# UniSoul - Raspberry Pi Deployment Guide

This guide explains how to deploy the UniSoul Laravel application to a Raspberry Pi using Docker and GitHub Actions.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Raspberry Pi Setup](#raspberry-pi-setup)
3. [GitHub Secrets Configuration](#github-secrets-configuration)
4. [Deployment Process](#deployment-process)
5. [Manual Deployment](#manual-deployment)
6. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### On Raspberry Pi

- **Raspberry Pi 4 or newer** (with ARM64 architecture)
- **Ubuntu 24.04 Server** or Raspberry Pi OS (64-bit)
- **Docker** and **Docker Compose** installed
- **MySQL/MariaDB** or **PostgreSQL** database running on host
- **Redis** server running on host (optional but recommended)
- Minimum **4GB RAM** recommended
- At least **16GB** free disk space

### On GitHub

- GitHub repository with admin access
- GitHub Actions enabled
- Self-hosted runner configured on Raspberry Pi

---

## Raspberry Pi Setup

### 1. Install Docker

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add your user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Verify installation
docker --version
docker compose version
```

### 2. Install and Configure Database

#### For MySQL/MariaDB:

```bash
# Install MySQL
sudo apt install mysql-server -y

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -e "CREATE DATABASE unisoul;"
sudo mysql -e "CREATE USER 'unisoul'@'%' IDENTIFIED BY 'your_secure_password';"
sudo mysql -e "GRANT ALL PRIVILEGES ON unisoul.* TO 'unisoul'@'%';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

#### For PostgreSQL:

```bash
# Install PostgreSQL
sudo apt install postgresql postgresql-contrib -y

# Create database and user
sudo -u postgres psql -c "CREATE DATABASE unisoul;"
sudo -u postgres psql -c "CREATE USER unisoul WITH PASSWORD 'your_secure_password';"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE unisoul TO unisoul;"

# Allow connections from Docker containers
sudo nano /etc/postgresql/*/main/postgresql.conf
# Add: listen_addresses = '*'

sudo nano /etc/postgresql/*/main/pg_hba.conf
# Add: host all all 172.16.0.0/12 md5

sudo systemctl restart postgresql
```

### 3. Install and Configure Redis

```bash
# Install Redis
sudo apt install redis-server -y

# Configure Redis to accept connections from Docker
sudo nano /etc/redis/redis.conf
# Change: bind 127.0.0.1 ::1
# To: bind 0.0.0.0

# Set password (optional but recommended)
# Add: requirepass your_redis_password

# Restart Redis
sudo systemctl restart redis-server

# Verify Redis is running
redis-cli ping
```

### 4. Configure GitHub Actions Self-Hosted Runner

```bash
# Create a directory for the runner
mkdir actions-runner && cd actions-runner

# Download the latest runner package (check GitHub for latest version)
curl -o actions-runner-linux-arm64-2.311.0.tar.gz -L \
  https://github.com/actions/runner/releases/download/v2.311.0/actions-runner-linux-arm64-2.311.0.tar.gz

# Extract the installer
tar xzf ./actions-runner-linux-arm64-2.311.0.tar.gz

# Configure the runner
# Go to: GitHub Repo → Settings → Actions → Runners → New self-hosted runner
# Copy and run the configuration command shown
./config.sh --url https://github.com/YOUR_USERNAME/UniSoul --token YOUR_TOKEN

# Add labels during configuration
# Labels: self-hosted,Linux,ARM64

# Install as a service
sudo ./svc.sh install
sudo ./svc.sh start

# Verify runner status
sudo ./svc.sh status
```

---

## GitHub Secrets Configuration

Go to your GitHub repository: **Settings → Secrets and variables → Actions → New repository secret**

Add the following secrets:

### Docker Registry Secrets

- `DOCKER_USERNAME` - Your Docker Hub username
- `DOCKER_PASSWORD` - Your Docker Hub password or access token

### Application Secrets

- `APP_KEY` - Laravel app key (generate with: `php artisan key:generate --show`)
- `APP_URL` - Your application URL (e.g., `http://your-raspberry-pi-ip`)

### Database Secrets

- `DB_DATABASE` - Database name (e.g., `unisoul`)
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

### Redis Secrets

- `REDIS_PASSWORD` - Redis password (if configured)

### Mail Secrets

- `MAIL_MAILER` - Mail driver (e.g., `smtp`, `mailgun`, `log`)
- `MAIL_HOST` - SMTP host
- `MAIL_PORT` - SMTP port
- `MAIL_USERNAME` - SMTP username
- `MAIL_PASSWORD` - SMTP password
- `MAIL_FROM_ADDRESS` - From email address

### Telegram Bot Secrets

- `TELEGRAM_TOKEN` - Your Telegram bot token from BotFather

### AI Service Secrets

- `GEMINI_API_KEY` - Your Google Gemini API key

---

## Deployment Process

### Automated Deployment (Recommended)

#### 1. Build and Push Docker Image

Push code to the `main` branch or manually trigger the workflow:

```bash
# Commit and push your changes
git add .
git commit -m "Your commit message"
git push origin main
```

Or trigger manually:
- Go to **Actions** tab
- Select **Build and Push Docker Image**
- Click **Run workflow**

This will:
- Build a multi-platform Docker image (ARM64 + AMD64)
- Push to Docker Hub with appropriate tags

#### 2. Deploy to Raspberry Pi

After the image is built, trigger the deployment:

- Go to **Actions** tab
- Select **Deploy to Raspberry Pi**
- Click **Run workflow**
- Enter the image tag (e.g., `latest`, `main`, `v1.0.0`)

This will:
- Pull the latest image from Docker Hub
- Stop the old container
- Deploy the new version
- Run database migrations
- Optimize the application
- Verify health and connectivity

---

## Manual Deployment

If you prefer to deploy manually on your Raspberry Pi:

### 1. Clone the Repository

```bash
cd /home/your-user
git clone https://github.com/YOUR_USERNAME/UniSoul.git
cd UniSoul
```

### 2. Create .env File

```bash
cp .env.example .env
nano .env
```

Update the environment variables with your configuration.

### 3. Pull the Docker Image

```bash
# Login to Docker Hub
docker login

# Pull the image
docker pull YOUR_DOCKER_USERNAME/unisoul:latest
```

### 4. Deploy with Docker Compose

```bash
# Start the application
docker compose -f compose.override.raspberry.yml up -d

# Run migrations
docker compose -f compose.override.raspberry.yml exec app php artisan migrate --force

# Optimize application
docker compose -f compose.override.raspberry.yml exec app php artisan config:cache
docker compose -f compose.override.raspberry.yml exec app php artisan route:cache
docker compose -f compose.override.raspberry.yml exec app php artisan view:cache

# Check status
docker compose -f compose.override.raspberry.yml ps
```

### 5. Verify Deployment

```bash
# Check logs
docker compose -f compose.override.raspberry.yml logs -f

# Test the application
curl http://localhost

# Check database connection
docker compose -f compose.override.raspberry.yml exec app php artisan db:show
```

---

## Troubleshooting

### Container Won't Start

```bash
# Check container logs
docker compose -f compose.override.raspberry.yml logs app

# Check if ports are available
sudo netstat -tulpn | grep :80

# Restart the container
docker compose -f compose.override.raspberry.yml restart app
```

### Database Connection Issues

```bash
# Test database connectivity from host
mysql -h 127.0.0.1 -u unisoul -p unisoul
# OR
psql -h 127.0.0.1 -U unisoul -d unisoul

# Check if database is accessible from container
docker compose -f compose.override.raspberry.yml exec app ping host.docker.internal

# Verify environment variables
docker compose -f compose.override.raspberry.yml exec app env | grep DB_
```

### Redis Connection Issues

```bash
# Test Redis from host
redis-cli ping

# Test from container
docker compose -f compose.override.raspberry.yml exec app php artisan tinker
# In tinker:
Redis::ping();

# Check Redis logs
sudo tail -f /var/log/redis/redis-server.log
```

### Permission Issues

```bash
# Fix storage permissions
docker compose -f compose.override.raspberry.yml exec app chmod -R 775 storage bootstrap/cache
docker compose -f compose.override.raspberry.yml exec app chown -R www-data:www-data storage bootstrap/cache
```

### Performance Issues

```bash
# Check system resources
htop

# Check Docker stats
docker stats

# Reduce PHP-FPM workers if needed (edit Dockerfile.production)
# Consider using swap if RAM is limited:
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

### GitHub Actions Runner Issues

```bash
# Check runner status
cd ~/actions-runner
sudo ./svc.sh status

# View runner logs
sudo journalctl -u actions.runner.* -f

# Restart runner
sudo ./svc.sh restart
```

### Clean Up Old Images

```bash
# Remove unused images
docker image prune -af

# Remove old containers
docker container prune -f

# Full cleanup (careful!)
docker system prune -af --volumes
```

---

## Maintenance

### Update Application

```bash
# Pull latest changes
cd ~/UniSoul
git pull

# Rebuild and redeploy (if not using automated deployment)
docker compose -f compose.override.raspberry.yml down
docker compose -f compose.override.raspberry.yml pull
docker compose -f compose.override.raspberry.yml up -d
```

### Backup Database

```bash
# MySQL backup
docker exec -i mysql mysqldump -u unisoul -p unisoul > backup_$(date +%Y%m%d).sql

# PostgreSQL backup
docker exec -i postgres pg_dump -U unisoul unisoul > backup_$(date +%Y%m%d).sql
```

### Monitor Logs

```bash
# Application logs
docker compose -f compose.override.raspberry.yml logs -f app

# All services
docker compose -f compose.override.raspberry.yml logs -f
```

---

## Security Recommendations

1. **Use strong passwords** for all services
2. **Enable firewall** and only open necessary ports
3. **Keep system updated**: `sudo apt update && sudo apt upgrade -y`
4. **Use HTTPS** with Let's Encrypt (consider using Nginx reverse proxy)
5. **Regularly backup** your database and `.env` file
6. **Monitor logs** for suspicious activity
7. **Disable debug mode** in production (`APP_DEBUG=false`)

---

## Support

For issues and questions:
- GitHub Issues: https://github.com/YOUR_USERNAME/UniSoul/issues

---

## License

This project is licensed under the MIT License.
