<?php

namespace Database\Factories;

use App\Models\Comic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comic>
 */
class ComicFactory extends Factory
{
    protected $model = Comic::class;

    public function definition(): array
    {
        $title = rtrim($this->faker->unique()->sentence(3), '.');

        return [
            'number'            => $this->faker->unique()->numberBetween(1, 100000),
            'slug'              => $this->faker->unique()->slug(),
            'title'             => $title,
            'alt_text'          => $this->faker->sentence(),
            'caption'           => $this->faker->sentence(),
            'description'       => $this->faker->sentence(),
            'image_path'        => 'comics/2026/01/'.$this->faker->uuid().'.png',
            'og_image_path'     => null,
            'width'             => 1200,
            'height'            => 1200,
            'file_hash'         => $this->faker->unique()->sha256(),
            'original_filename' => $this->faker->word().'.png',
            'file_created_at'   => $this->faker->dateTimeThisYear(),
            'published_at'      => now()->toDateString(),
        ];
    }

    /** Make this comic live (released today). */
    public function published(): static
    {
        return $this->state(fn () => ['published_at' => now()->toDateString()]);
    }

    /** Schedule this comic for the future. */
    public function scheduled(): static
    {
        return $this->state(fn () => ['published_at' => now()->addWeek()->toDateString()]);
    }
}
