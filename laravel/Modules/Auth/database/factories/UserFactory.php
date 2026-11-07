<?php

namespace Modules\Auth\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
 protected $model = \Modules\Auth\Model\Users\Model::class;

    /**
     * Define the model's default state.
     */
   public function definition(): array
{
    return [ 
        'name' => fake()->userName(), 
        'email' => fake()->unique()->safeEmail(),
        'first_name' => fake()->firstName(), 
        'last_name' => fake()->lastName(),
        'phone' => '+77013955928', 
        //'blocked' => 0,
         'password' => '12345678', 
        //'email_verified_at' => now(),
        'remember_token' => \Illuminate\Support\Str::random(10),
    ];
}

}

