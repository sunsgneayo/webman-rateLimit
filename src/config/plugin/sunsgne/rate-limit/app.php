<?php
return [
    'enable'  => true,
    'default' => [
        'capacity'    => 60, // The number of requests the "bucket" can hold
        'seconds'     => 60,  // The time it takes the "bucket" to completely refill
        'cost'        => 1, // The number of tokens this action uses.
        'concurrency' => true,
        'customer'    => [
            'class'       => \support\Response::class,
            'constructor' => [
                429,
                array(),
                json_encode(['success' => false, 'msg' => '请求次数太频繁'], 256),
            ],
        ],
    ],

];