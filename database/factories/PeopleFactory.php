<?php

use Faker\Generator as Faker;

$factory->define(App\People::class, function (Faker $faker) {
    return [
        //defines perameters for table fields
        'firstName' => $faker->text(10),
        'lastName' => $faker->text(10),
        'description' => $faker->text(100),
        'code' => $faker->text(5),
        'status' => $faker->randomElement(array('active','inactive'))
    ];
});
