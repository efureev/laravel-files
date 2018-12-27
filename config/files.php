<?php

return [
    'table'  => [
        'name' => 'files_upload',
        'id'   => 'uuid',
    ],
    'policy' => \Feugene\Files\Policies\FilePolicy::class,
];
