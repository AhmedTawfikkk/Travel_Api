<?php

namespace Database\Factories;

use App\Models\Travel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'travel_id'=>null,
            'name'=>fake()->text(20),
            'starting_date'=>now(),
            'ending_date'=>now()->addDays(rand(1,10)),
            'price'=>fake()->randomFloat(2,10,999)
        ];
    }

    public function fortravel($travel_id){
        return $this->state([
            'travel_id'=>$travel_id
        ]); 
    }
}
