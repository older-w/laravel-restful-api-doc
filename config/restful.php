<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 1:53 PM
 */

return [
    'pusher'=>[
        'wordpress'=>[
            'key'=>'7358812c1388060212389866d5ea9f31',
            'user_id'=>'60',
            'url'=>'https://doc.wangxutech.com/wp-admin/admin-ajax.php?action=edit_api',
            'docs'=>[
                'api'=>['id'=>638],
                'error'=>['id'=>638],
                'enum'=>['id'=>638],
            ]
        ]
    ],
    'docs'=>[
        'api'=>[
            'path'=>'/app/Http/Controllers/Api',
            'base_class'=>\App\Http\Controllers\Api::class,
            'type'=>'api'
        ],
        'error'=>[
            'path'=>'/app/Http/Exceptions',
            'base_class'=>\App\Http\Exception::class,
            'type'=>'error'
        ],
        'enum'=>[
            'path'=>'/app/Http/Controllers/Api',
            'base_class'=>\App\Http\Controllers\Api::class,
            'type'=>'enum'
        ],
    ]
];