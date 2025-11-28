<?php

namespace Database\Seeders;

use App\Models\Confession;
use App\Models\Donation;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $confessions = Confession::all();

        if ($confessions->isEmpty()) {
            $this->command->warn('No confessions found. Please seed confessions first.');

            return;
        }

        $donationTypes = [
            [
                'name' => [
                    'en' => 'Light a Candle',
                    'uk' => 'Поставити свічку',
                    'ru' => 'Поставить свечу',
                    'ro' => 'Aprinde o lumânare',
                    'ka' => 'სანთლის დანთება',
                    'de' => 'Kerze anzünden',
                ],
                'description' => [
                    'en' => 'Light a candle for your loved ones',
                    'uk' => 'Поставте свічку за здоров\'я близьких',
                    'ru' => 'Поставьте свечу за здоровье близких',
                    'ro' => 'Aprinde o lumânare pentru cei dragi',
                    'ka' => 'აანთეთ სანთელი თქვენი საყვარელი ადამიანებისთვის',
                    'de' => 'Zünde eine Kerze für deine Lieben an',
                ],
                'purpose' => 'candle',
                'emoji' => '🕯️',
                'min_amount' => 1,
                'max_amount' => 100,
                'order' => 1,
            ],
            [
                'name' => [
                    'en' => 'Sorokoust',
                    'uk' => 'Сорокоуст',
                    'ru' => 'Сорокоуст',
                    'ro' => 'Sorokoust',
                    'ka' => 'სოროკოუსტი',
                    'de' => 'Sorokoust',
                ],
                'description' => [
                    'en' => 'Order a 40-day prayer service',
                    'uk' => 'Замовити сорокоуст за здоров\'я або за упокій',
                    'ru' => 'Заказать сорокоуст о здравии или за упокой',
                    'ro' => 'Comandă o slujbă de rugăciune de 40 de zile',
                    'ka' => '40-დღიანი ლოცვის მომსახურება',
                    'de' => 'Bestelle einen 40-Tage-Gebetsdienst',
                ],
                'purpose' => 'sorokoust',
                'emoji' => '🙏',
                'min_amount' => 10,
                'max_amount' => 500,
                'order' => 2,
            ],
            [
                'name' => [
                    'en' => 'General Support',
                    'uk' => 'Загальна підтримка',
                    'ru' => 'Общая поддержка',
                    'ro' => 'Sprijin general',
                    'ka' => 'ზოგადი მხარდაჭერა',
                    'de' => 'Allgemeine Unterstützung',
                ],
                'description' => [
                    'en' => 'Support the church and its activities',
                    'uk' => 'Підтримайте церкву та її діяльність',
                    'ru' => 'Поддержите церковь и её деятельность',
                    'ro' => 'Sprijină biserica și activitățile sale',
                    'ka' => 'მხარი დაუჭირეთ ეკლესიას და მის საქმიანობას',
                    'de' => 'Unterstütze die Kirche und ihre Aktivitäten',
                ],
                'purpose' => 'general',
                'emoji' => '⛪',
                'min_amount' => 1,
                'max_amount' => null,
                'order' => 3,
            ],
            [
                'name' => [
                    'en' => 'Memorial Service',
                    'uk' => 'Панахида',
                    'ru' => 'Панихида',
                    'ro' => 'Serviciu memorial',
                    'ka' => 'მემორიალური მსახურება',
                    'de' => 'Gedenkgottesdienst',
                ],
                'description' => [
                    'en' => 'Order a memorial service for the departed',
                    'uk' => 'Замовити панахиду за упокій',
                    'ru' => 'Заказать панихиду за упокой',
                    'ro' => 'Comandă un serviciu memorial pentru cei plecați',
                    'ka' => 'მემორიალური მსახურება გარდაცვლილთათვის',
                    'de' => 'Bestelle einen Gedenkgottesdienst für Verstorbene',
                ],
                'purpose' => 'memorial',
                'emoji' => '✝️',
                'min_amount' => 5,
                'max_amount' => 300,
                'order' => 4,
            ],
            [
                'name' => [
                    'en' => 'Unceasing Psalter',
                    'uk' => 'Неусипний Псалтир',
                    'ru' => 'Неусыпный Псалтирь',
                    'ro' => 'Psalter neîntrerupt',
                    'ka' => 'უწყვეტი ფსალმუნი',
                    'de' => 'Unaufhörlicher Psalter',
                ],
                'description' => [
                    'en' => 'Add names to the unceasing psalter reading',
                    'uk' => 'Додати імена до читання неусипного псалтиря',
                    'ru' => 'Добавить имена к чтению неусыпного псалтиря',
                    'ro' => 'Adaugă nume la citirea neîntreruptă a psalterului',
                    'ka' => 'დაამატეთ სახელები უწყვეტ ფსალმუნის კითხვაში',
                    'de' => 'Füge Namen zur unaufhörlichen Psalterlesung hinzu',
                ],
                'purpose' => 'psalter',
                'emoji' => '📿',
                'min_amount' => 5,
                'max_amount' => 200,
                'order' => 5,
            ],
        ];

        foreach ($confessions as $confession) {
            foreach ($donationTypes as $donationType) {
                Donation::create([
                    'confession_id' => $confession->id,
                    'branch_id' => null,
                    'name' => $donationType['name'],
                    'description' => $donationType['description'],
                    'purpose' => $donationType['purpose'],
                    'emoji' => $donationType['emoji'],
                    'min_amount' => $donationType['min_amount'],
                    'max_amount' => $donationType['max_amount'],
                    'currency' => 'XTR',
                    'active' => true,
                    'order' => $donationType['order'],
                ]);
            }
        }

        $this->command->info('Donations seeded successfully!');
    }
}
