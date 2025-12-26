# 🕊️ UniSoul: Spiritual Guide (Telegram Bot)

## 🚨 PROJECT STATUS: Testing Mode

**Laravel 12 Telegram Bot (PHP 8.2 / Sail Ready)**

This project is a modern Telegram bot implementation built on **Laravel 12** and designed to run easily using **Laravel Sail** (Docker). It leverages the `irazasyed/telegram-bot-sdk` (v3.x) for seamless update handling and messaging.

---

![main](./readme/chat.png)


## 1. Setup and Installation

This guide assumes you have **Docker** and **Docker Compose** installed and running on your system.

### A. Environment Variables

Copy the contents of the provided `.env.additions.txt` into your main `.env` file, ensuring you replace the placeholder with your actual bot token obtained from BotFather.

```env
# .env additions
TELEGRAM_BOT_TOKEN="<YOUR_BOT_TOKEN_FROM_BOTFATHER>"

# Standard MySQL DB Config (Sail default)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
````

### B. Running with Docker Compose (First Run)

To handle the dependencies and setup without relying on the local `./vendor/` directory, we use direct `docker compose` commands.

1.  **Build and Start the Environment:**
    The following command builds the images, starts the necessary services (App, MySQL), and runs `composer install` inside the application container to fetch all dependencies, including the `sail` executable.

    ```bash
    docker compose up -d
    docker compose exec laravel.test composer install
    ```

    *(The application container is typically named `laravel.test` by Laravel Sail.)*

2.  **Run Database Migrations:**
    Execute migrations to create the required tables (`user_configs` and `user_messages`).

    ```bash
    docker compose exec laravel.test php artisan migrate
    ```

### C. Subsequent Commands (Using Sail Helper)

Now that `composer install` has run, the `./vendor/bin/sail` executable is available and can be used for convenience.

* **Standard Up/Down:**
  ```bash
  ./vendor/bin/sail up -d # Start services
  ./vendor/bin/sail down  # Stop services
  ```

-----

## 2\. Bot Setup: Development vs Production

This project uses **Nutgram** for Telegram bot integration, which supports two modes of operation.

### Development Mode (Polling)

For local development, the bot uses **polling mode** to fetch updates from Telegram.

**Start the bot:**
```bash
./vendor/bin/sail artisan nutgram:run
```

The bot will continuously poll Telegram for updates. Keep this command running while developing.

### Production Mode (Webhook)

In production, the bot uses **webhooks** for receiving updates from Telegram. This is more efficient and doesn't require a continuously running process.

#### Environment Configuration

Your `.env` file in production should have:
```env
APP_ENV=production
TELEGRAM_TOKEN=your_actual_bot_token
```

The `safe_mode` in `config/nutgram.php:10` will automatically enable webhook validation in production.

#### Initial Webhook Setup

After deploying your application to production, **run this command ONCE**:

```bash
php artisan nutgram:hook:set https://yourdomain.com/api/telegram/webhook
```

Replace `yourdomain.com` with your actual production domain.

**Note:** The webhook endpoint is already configured at `/api/telegram/webhook` and excludes CSRF protection.

#### Verify Webhook Status

Check if the webhook is properly registered:

```bash
php artisan nutgram:hook:info
```

#### Development vs Production Workflow

| Environment | Mode    | Command                                                      |
|-------------|---------|--------------------------------------------------------------|
| Development | Polling | `./vendor/bin/sail artisan nutgram:run` (run manually)      |
| Production  | Webhook | No command needed after initial setup                        |

**How It Works:**
- **Development:** You manually run `nutgram:run` when working locally. The bot polls Telegram for updates.
- **Production:** Telegram sends updates directly to your webhook URL. No manual command needed after deployment.

#### CI/CD Integration (Optional)

If you want to automate webhook registration in your deployment pipeline, add this to your deployment script:

```bash
if [ "$APP_ENV" = "production" ]; then
    php artisan nutgram:hook:set https://yourdomain.com/api/telegram/webhook
fi
```

#### Remove Webhook

To switch back to polling or remove the webhook:

```bash
php artisan nutgram:hook:remove
```

**Important:** Once the webhook is set in production, you don't need to run any artisan commands after each deploy. The webhook stays active until you explicitly remove it.

### Local Testing with ngrok (Optional)

If you want to test webhook mode locally:

1.  **Start ngrok:**
    ```bash
    ngrok http 80
    ```

2.  **Set the webhook:**
    ```bash
    ./vendor/bin/sail artisan nutgram:hook:set https://<your-ngrok-id>.ngrok-free.app/api/telegram/webhook
    ```

3.  **Test the Bot:**
    Open Telegram, find your bot, and send the `/start` command.

-----

## 3\. Testing the Code

The project includes a feature test to verify webhook functionality and database persistence.

Run the tests inside the Sail container:

```bash
./vendor/bin/sail artisan test
```

This runs `tests/Feature/TelegramWebhookTest.php`, confirming that incoming updates are correctly handled, stored in the database, and that a reply is attempted (by mocking the Telegram API client).

-----

## 4\. Admin Panel (Filament)

The project includes a Filament admin panel for managing users, confessions, bot buttons, and viewing statistics.

**Access the admin panel at:** [http://localhost:8050/management](http://localhost:8050/management)

Available resources:
- **Users** - Manage bot users
- **Confessions** - View and manage user confessions
- **Bot Buttons** - Configure bot keyboard buttons
- **Statistics** - View application statistics

Login credentials should be created using:
```bash
./vendor/bin/sail artisan make:filament-user
```

-----

## 5\. Compatibility Fixes Summary

The following files were replaced/updated to ensure full **Laravel 12** compatibility and **PHP 8.2** type-hinting:

| Outdated File | Laravel 12 Replacement/Change |
| :--- | :--- |
| `app/Http/Middleware/*` | Replaced with modern Laravel 12 versions, ensuring correct namespaces, use statements, and return type declarations. |
| `app/Http/Kernel.php` | Updated to the Laravel 12 structure, including correct middleware registration. `TrustProxies` is configured to trust all proxies (`*`), essential for Docker/Sail and ngrok. |
| `routes/api.php` | The webhook route `POST /telegram/webhook` was added using the modern `[Controller::class, 'method']` array syntax. |
| `app/Http/Middleware/VerifyCsrfToken.php` | The webhook route `/telegram/webhook` was added to the `$except` array to bypass CSRF protection for external API calls. |
| **New Files** | New files like `app/Models/UserConfig.php`, `app/Services/TelegramBotService.php`, and `app/Console/Commands/TelegramSetWebhook.php` were created following Laravel 12 idioms. |

-----

## 6\. Main Flow Examples

The `TelegramBotService` implements the following core user flows:

| User Action | Bot Response | Persistence |
| :--- | :--- | :--- |
| `/start` or "Main Menu" | Greets user, shows main menu keyboard. | Creates `UserConfig` if new. Stores message in `UserMessage`. |
| "1. Ask a question" | Prompts user to type a question. | Stores message in `UserMessage`. |
| "2. My config" | Displays current config (language, notifications). | Stores message in `UserMessage`. |
| Free-text message | Acknowledges the query with a standard reply. | Stores message in `UserMessage`. |
| `set language es` (in config menu) | Updates the user's language preference. | Updates `UserConfig` record. |
| `toggle notifications` (in config menu) | Toggles the notification setting. | Updates `UserConfig` record. |

The core logic is contained within `app/Services/TelegramBotService.php`, which manages update parsing, database records, and sending replies using the injected `Telegram\Bot\Api` client.
