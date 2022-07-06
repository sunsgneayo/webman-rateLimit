<?php
return [
    'enable'  => true,
    'default' => [
        /** The number of requests the "bucket" can hold || 每一个请求在redis的最大限度 */
        'capacity'    => 100,
        /** The time it takes the "bucket" to completely refill || redis key的过期时间  */
        'seconds'     => 60,
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