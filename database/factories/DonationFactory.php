<?php

namespace Database\Factories;

use App\Models\Confession;
use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purposes = ['candle', 'sorokoust', 'general', 'memorial', 'psalter', 'akathist'];
        $emojis = ['🕯️', '🙏', '❤️', '✝️', '⛪', '📿'];

        return [
            'confession_id' => Confession::factory(),
            'branch_id' => null,
            'name' => [
                'en' => $this->faker->words(3, true),
                'uk' => $this->faker->words(3, true),
                'ru' => $this->faker->words(3, true),
            ],
            'description' => [
                'en' => $this->faker->sentence(),
                'uk' => $this->faker->sentence(),
                'ru' => $this->faker->sentence(),
            ],
            'purpose' => $this->faker->randomElement($purposes),
            'min_amount' => $this->faker->randomElement([1, 5, 10, 50]),
            'max_amount' => $this->faker->optional()->randomElement([100, 500, 1000, 5000]),
            'currency' => 'XTR',
            'emoji' => $this->faker->randomElement($emojis),
            'active' => true,
            'order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
