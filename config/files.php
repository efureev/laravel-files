<?php

return [
    'table'  => [
        'name' => 'files',
        'id'   => 'uuid',
    ],
    'policy' => \Feugene\Files\Policies\FilePolicy::class,
];
