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

## 2\. Webhook Setup and Local Testing (via ngrok)

For the Telegram bot to receive updates, you must register a publicly accessible HTTPS URL with the Telegram API.

\*\*

[Image of Main Page]
\*\*

1.  **Start ngrok:**
    Expose your local Sail environment (which runs on port 80) to the public internet:

    ```bash
    ngrok http 80
    ```

    Copy the public **HTTPS URL** (e.g., `https://<your-ngrok-id>.ngrok-free.app`).

2.  **Set the Webhook:**
    Use the custom Artisan command, replacing the URL placeholder with your ngrok HTTPS link:

    ```bash
    ./vendor/bin/sail artisan telegram:set-webhook --url=https://<your-ngrok-id>.ngrok-free.app/telegram/webhook
    ```

    A confirmation message from the Telegram API confirms success.

3.  **Test the Bot:**
    Open Telegram, find your bot, and send the `/start` command. You should receive the main menu reply.

-----

## 3\. Testing the Code

The project includes a feature test to verify webhook functionality and database persistence.

Run the tests inside the Sail container:

```bash
./vendor/bin/sail artisan test
```

This runs `tests/Feature/TelegramWebhookTest.php`, confirming that incoming updates are correctly handled, stored in the database, and that a reply is attempted (by mocking the Telegram API client).

-----

## 4\. Compatibility Fixes Summary

The following files were replaced/updated to ensure full **Laravel 12** compatibility and **PHP 8.2** type-hinting:

| Outdated File | Laravel 12 Replacement/Change |
| :--- | :--- |
| `app/Http/Middleware/*` | Replaced with modern Laravel 12 versions, ensuring correct namespaces, use statements, and return type declarations. |
| `app/Http/Kernel.php` | Updated to the Laravel 12 structure, including correct middleware registration. `TrustProxies` is configured to trust all proxies (`*`), essential for Docker/Sail and ngrok. |
| `routes/api.php` | The webhook route `POST /telegram/webhook` was added using the modern `[Controller::class, 'method']` array syntax. |
| `app/Http/Middleware/VerifyCsrfToken.php` | The webhook route `/telegram/webhook` was added to the `$except` array to bypass CSRF protection for external API calls. |
| **New Files** | New files like `app/Models/UserConfig.php`, `app/Services/TelegramBotService.php`, and `app/Console/Commands/TelegramSetWebhook.php` were created following Laravel 12 idioms. |

-----

## 5\. Main Flow Examples

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
