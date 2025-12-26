<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SergiX44\Hydrator\Hydrator;
use SergiX44\Nutgram\Nutgram;

class NutgramServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Hook into Nutgram after it's resolved from the container
        $this->app->resolving(Nutgram::class, function (Nutgram $bot) {
            // For 32-bit systems: Intercept API responses after JSON decoding
            // and attempt to fix integer overflow issues
            $bot->afterApiRequest(function ($json) {
                if (is_object($json) && isset($json->result)) {
                    $this->fixIntegerOverflow($json->result);
                }

                return $json;
            });

            // Additionally, configure a more lenient hydrator
            $this->configureLenientHydrator($bot);
        });
    }

    /**
     * Configure Nutgram to use a more lenient hydrator that allows type coercion.
     */
    protected function configureLenientHydrator(Nutgram $bot): void
    {
        try {
            $reflection = new \ReflectionClass($bot);
            $hydratorProperty = $reflection->getProperty('hydrator');
            $hydratorProperty->setAccessible(true);

            // Create a new hydrator instance
            // Note: The Hydrator already performs type coercion by default (int/float/bool from strings)
            // and handles snake_case attributes automatically
            $lenientHydrator = new Hydrator($this->app);

            $hydratorProperty->setValue($bot, $lenientHydrator);
        } catch (\ReflectionException $e) {
            // Silently fail if reflection doesn't work
            logger()->warning('Failed to configure lenient Nutgram hydrator', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Recursively fix integer overflow in API responses on 32-bit systems.
     * When integers exceed PHP_INT_MAX, they wrap to negative numbers.
     */
    protected function fixIntegerOverflow(&$data): void
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => &$value) {
                if (is_array($value) || is_object($value)) {
                    $this->fixIntegerOverflow($value);
                } elseif ($key === 'id' && is_int($value) && $value < 0 && PHP_INT_SIZE === 4) {
                    // On 32-bit systems, large Telegram IDs wrap to negative
                    // Convert negative integers to string representation
                    if (is_object($data)) {
                        $data->$key = (string) ($value + 4294967296); // Convert back from 32-bit overflow
                    } else {
                        $value = (string) ($value + 4294967296);
                    }
                }
            }
        }
    }
}
