<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 1:53 PM
 */

return [
    /**
     * 定义发布方式和参数
     */
    'pusher'=>[
        'wordpress'=>[
            'key'=>'key',
            'user_id'=>'60',
            'url'=>'https://doc.com/wp-admin/admin-ajax.php?action=edit_api',
            'docs'=>[
                'api'=>['id'=>638],
                'error'=>['id'=>638],
                'enum'=>['id'=>638],
            ]
        ]
    ],
    /**
     * 定义采用的发布方式
     */
    'publisher' =>'OlderW\RestfulDoc\Pusher\Wordpress',
    /**
     * 定义处理格式化的类
     */
    'formatter' =>'OlderW\RestfulDoc\DocFormat',
    /**
     * 定义各个文档类型
     */
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
    ],

];