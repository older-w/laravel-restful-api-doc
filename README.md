# laravel-restful-api-doc
a tool for generate api document for laravel

# Installation

1、安装扩展
```
composer require older-w/laravel-restful-api-doc


```
2、发布配置
```
php artisan vendor:publish --provider="OlderW\RestfulDoc\RestfulServiceProvider"
```

# 默认配置

配置中的pusher并不可以直接使用，大家可以创建自己pusher。配置说明如下

```
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
```
 - pusher定义的发布方式，目前的wordpress为个人使用配置的，需要在wordpress安装扩展，没有通用性。大家可以自己创建一个pusher使用，创建一个新的pusher 需要实现OlderW\RestfulDoc\Interfaces\DocPusher的接口
 
 - publisher 定义使用哪个pusher
 
 - formatter 定义使用的格式化，自定义可以实现\OlderW\RestfulDoc\Interfaces\DocFormat接口
 
 - docs 定义了目前支持的类型
  - api 文档
  - error 错误
  - enum 枚举
