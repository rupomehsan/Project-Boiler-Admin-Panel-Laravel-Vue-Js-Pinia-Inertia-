<?php
namespace Modules\Management\BlogManagement\BlogWriter\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\BlogManagement\BlogWriter\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\BlogManagement\BlogWriter\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();


        for ($i = 1; $i <= 100; $i++) {
            self::$model::create([                'name' => $faker->text(150),
                'email' => $faker->text(150),
                'phone' => $faker->text(20),
                'bio' => $faker->paragraph,
                'avatar' => $faker->text(150),
                'website' => $faker->word,
                'facebook_url' => $faker->word,
                'twitter_url' => $faker->word,
                'linkedin_url' => $faker->word,
                'is_active' => $faker->boolean,
            ]);
        }
    }
}