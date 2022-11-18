<?php

return [
    /**
     * Control if the seeder should create a user per role while seeding the data.
     */
    'create_users' => false,

    /**
     * Control if all the laratrust tables should be truncated before running the seeder.
     */
    'truncate_tables' => true,

    'roles_structure' => [
        'super_admin' => [
            'main'               => 'r',
            'users'              => 'c,r,u,d',
            'clinics'            => 'c,r,u,d',
            'customers'          => 'c,r,u,d',
            'requests'           => 'c,r,u,d',
            'cats'               => 'c,r,u,d',
            'subcats'            => 'c,r,u,d',
            'pets'               => 'c,r,u,d',
            'orders'             => 'c,r,u,d',
            'products'           => 'c,r,u,d',
            'coupons'            => 'c,r,u,d',
            'terms'              => 'r,u',
            'stting'             => 'c,r,u,d',
            'rate'               => 'r,d',
            'withdraw_requests'  => 'c,r,u,d',
            'vendors'            => 'c,r,u,d',
            'banks'              => 'c,r,u,d',


        ],
        'admin' => [],
    ],

    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete'
    ]
];
