<?php
namespace Modules\Management\BlogManagement\Blog\Database\Seeders;

use Illuminate\Database\Seeder as SeederClass;
use Faker\Factory as Faker;

class Seeder extends SeederClass
{
    /**
     * Run the database seeds.
     php artisan db:seed --class="Modules\Management\BlogManagement\Blog\Database\Seeders\Seeder"
     */
    static $model = \Modules\Management\BlogManagement\Blog\Database\Models\Model::class;

    public function run(): void
    {
        $faker = Faker::create();
        self::$model::truncate();

                $blogCategoryIds = \Modules\Management\BlogManagement\BlogCategory\Database\Models\Model::pluck('id')->toArray();
                $writerIds = \Modules\Management\BlogManagement\BlogWriter\Database\Models\Model::pluck('id')->toArray();

        for ($i = 1; $i <= 100; $i++) {
            self::$model::create([                'blog_category_id' => !empty($blogCategoryIds) ? $blogCategoryIds[array_rand($blogCategoryIds)] : null,
                'writer_id' => !empty($writerIds) ? $writerIds[array_rand($writerIds)] : null,
                'title' => $faker->text(200),
                'short_description' => $faker->paragraph,
                'content' => $faker->paragraph,
                'reading_time' => $faker->randomNumber(5),
                'average_rating' => $faker->randomFloat(2, 0, 1000),
                'publish_date' => $faker->dateTime()->format('Y-m-d H:i:s'),
                'scheduled_at' => $faker->dateTime()->format('Y-m-d H:i:s'),
                'thumbnail_image' => $faker->text(150),
                'gallery' => $faker->word,
                'blog_type' => $faker->randomElement(array (
  0 => 'news',
  1 => 'tutorial',
  2 => 'opinion',
  3 => 'review',
  4 => 'case_study',
)),
                'content_format' => $faker->randomElement(array (
  0 => 'article',
  1 => 'video',
  2 => 'podcast',
  3 => 'infographic',
)),
                'external_url' => $faker->word,
                'show_on_top' => $faker->randomElement(array (
  0 => 'yes',
  1 => 'no',
)),
                'allow_comments' => $faker->randomElement(array (
  0 => 'yes',
  1 => 'no',
)),
                'is_featured' => $faker->boolean,
                'is_published' => $faker->boolean,
                'video_link' => $faker->text(200),
                'meta_title' => $faker->text(200),
                'meta_description' => $faker->paragraph,
                'meta_keywords' => [$faker->word, $faker->word],
            ]);
        }
    }
}