<?php

use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\Feugene\Files\Models\File::class, function (Faker $faker) {
    return [
        'driver' => 'local',
    ];
});

/*
$factory->state(\Feugene\Files\Models\File::class, 'defined', function (Faker $faker, $data) use ($createFile) {
    return [
        'path'   => trim(config('upload.url'), '/') . '/' . str_random() . '.' . ($ext = $faker->fileExtension),
        'mime'   => $faker->mimeType,
        'ext'    => $ext,
        'driver' => 'local',
        'size'   => mt_rand(100, 30000),
    ];
});

$factory->state(\Feugene\Files\Models\File::class, 'exists', function (Faker $faker, $data) use ($createFile) {
    return $createFile('*', $data);
});

$factory->state(\Feugene\Files\Models\File::class, 'image', function (Faker $faker, $data) use ($createFile) {
    return $createFile('image', $data);
});

$factory->state(\Feugene\Files\Models\File::class, 'document', function (Faker $faker, $data) use ($createFile) {
    return $createFile('document', $data);
});

$factory->state(\Feugene\Files\Models\File::class, 'video', function (Faker $faker, $data) use ($createFile) {
    return $createFile('video', $data);
});

$factory->state(\Feugene\Files\Models\File::class, 'audio', function (Faker $faker, $data) use ($createFile) {
    return $createFile('audio', $data);
});

$factory->state(\Feugene\Files\Models\File::class, 'faker-image', function (Faker $faker, $data) use ($createFile) {
    return $createFile('audio', $data + ['faker' => $faker]);
});*/
