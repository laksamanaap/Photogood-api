<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class KategoriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Kategori::class;

    public function definition(): array
    {
        return [
            'nama_kategori' => $this->faker->word,
            'deskripsi_kategori' => $this->faker->sentence,
        ];
    }
}
