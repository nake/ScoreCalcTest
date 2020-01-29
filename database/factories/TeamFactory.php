<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Team;
use Faker\Generator as Faker;

$factory->define(Team::class, function (Faker $faker) {
    return [
        'name' => substr($faker->sentence(2), 0, -1),
        'power' => rand(0, 100),
    ];
});
