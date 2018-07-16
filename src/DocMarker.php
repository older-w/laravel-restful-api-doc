<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 10:21 AM
 */

namespace OlderW\RestfulDoc;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

class DocMarker
{
    public static function getUri($class,$method)
    {   //echo $class,$method;echo "\n";
        $all = Route::getRoutes();
        $result = [];if ($method == 'showByEmail'){}
        foreach ($all as $route)
        {

            /**
             * @var $route \Illuminate\Routing\Route::class;
             */
            //print_r($route->getActionName());echo "--";exitexit;
            //print_r($route->getAction());

            $action = $route->getActionName();
            if ($action == 'Closure')
            {
                continue;
            }
            $action = explode('@',$action);
            if (!isset($action[1]))
            {
                print_r($action);exit;
            }
            //echo "\t\t ----\t".$action[0].' '.$action[1]."\n";
            if ($action[0] == $class && $action[1] == $method)
            {
                $uri = $route->uri();
                $methods = $route->methods();
                $md = $methods[0];
                if (strstr($uri,'processesxx')!==false)
                {
                    continue;
                }
                //$prefix = $route->getPrefix();
                $result[] = [
                    'method'=>$md,
                    'uri'=>'/'.$uri,
                    'action'=>$route->getActionName(),
                ];
            }
        }
        if (count($result)>1)
        {
            print_r($result);
            echo $class.' '.$method.'存在多个对应路由';
            exit();
        }
        if (count($result) == 0)
        {
            die($class.' '.$method.' 找不到对应路由');
        }
        return $result[0];

    }

    public static function getEnumDoc($path = '/app/Enum',$base_class='\App\Http\Enum')
    {
        $classes = self::get_classes([
            base_path() . $path,
        ]);
        $docs = [];
        sort($classes);
        foreach ($classes as $class) {
            if (!$doc = DocParser::get_classes_commentdoc($class)) {
                continue;
            }
            if (!is_subclass_of($class,$base_class))
            {
                continue;
            }
            if (isset($doc['ignore']))
            {
                continue;
            }
            if (!isset($doc['intro']))
            {
                die($class."缺少intro");
            }
            $data = DocParser::get_class_const_commentdoc($class);
            $docs[] = ['data'=>$data,'intro'=>$doc['intro'],'class'=>$class];
        }
        return app('OlderW\RestfulDoc\DocFormat')::Enum_markdown($docs);
    }

    /**
     * 生成api文档
     */
    public static function getDoc($path = '/app/Http/Controllers/Api',$base_class = '\App\Http\Controllers\Api')
    {

        if (is_string($path))
        {
            $path = [$path];
        }
        $classes = [];
        foreach ($path as $p)
        {
            $classes = array_merge($classes, self::get_classes([
                base_path() . $p,
            ]));
        }
        $docs = [];
        sort($classes);
        foreach ($classes as $class) {
            if (!is_subclass_of($class,$base_class))
            {
                continue;
            }
            if (!$doc = DocParser::get_classes_commentdoc($class,\Exception::class)) {
                continue;
            }
            if (!isset($doc['module']))
            {
                die($class."缺少module");
            }
            if (isset($doc['ignore']))
            {
                continue;
            }
            $module = $doc['module'];
            if (!isset($docs[$module])) {
                $docs[$module] = [];
            }
            if ($motheds = DocParser::getMethodDocForClass($class))
            {
                if (!is_array($motheds))
                {
                    print_r($motheds);die('date error!!');
                }
                foreach ($motheds as $method => $method_doc) {
                    $uri = self::getUri($class,$method);
                    $docs[$module][$uri['method'].' '.$uri['uri']] = $method_doc;

                }
            }


        }

        return app('OlderW\RestfulDoc\DocFormat')::api_markdown($docs);
    }



    public static function getExceptionDoc($path= '/app/Exceptions',$base_class='\App\Http\Exception')
    {
        $classes = self::get_classes([
            base_path() . $path,
            //other root
        ]);
        $docs = [];//print_r($classes);\Symfony\Component\HttpKernel\Exception\HttpException::class;
        $errnoList = [];
        foreach ($classes as $class) {
            if (substr($class, -9) !== 'Exception') {
                continue;
            }
            if (!is_subclass_of($class,$base_class))
            {
                continue;
            }
            if (!$doc = DocParser::get_classes_commentdoc($class,\Exception::class)) {
                continue;
            }
            if (substr($doc['name'], 0,4) !== 'App\\') {
                //echo $doc['name']."\n\n--".substr($doc['name'], 0,4);
                continue;
            }

            $properties = $doc['properties'];
            unset($doc['properties']);
            if (isset($doc['ignore'])) {
                continue;
            }
            if (!isset($doc['error']) || !trim($doc['error'])) {
                if (isset($properties['message']) && trim($properties['message'])) {
                    $doc['error'] = $properties['message'];
                } else {
                    //die("{$class} {$doc['name']} 缺少 @error 注释\n");
                }
            }
            if (!isset($doc['errno']) || !trim($doc['errno'])) {
                if (isset($properties['code']) && trim($properties['code'])) {
                    $doc['errno'] = $properties['code'];
                } else {
                    //die("{$class} {$doc['name']} 缺少 @errno 注释\n");
                }
            }
            if (!isset($doc['errno']))
            {
                die(" {$doc['name']}中的错误码 无法读取 errno \n");
            }
            if (isset($errnoList[$doc['errno']]) && $doc['errno'] != 500000) {
                //@todo容错处理

                die(" {$doc['name']}中的错误码和 {$errnoList[$doc['errno']]} 中的重复 {$doc['errno']} \n");
            }
            $errnoList[$doc['errno']]=$doc['name'];

            $docs[] = $doc;
        }

        return app('OlderW\RestfulDoc\DocFormat')::exec_markdown($docs);

    }


    public static function get_classes($directory)
    {

        $classes = get_declared_classes();
        self::include_classes($directory);

        return array_diff(get_declared_classes(), $classes);
    }
    public static  function include_classes($directories)
    {
        $directories = (array) $directories;
        foreach ($directories as $directory)
        {
            if (!is_dir($directory))
            {
                continue;
            }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

            foreach ($it as $o) {
                if (!$o->isFile()) {
                    //continue;
                    $file = $o->getFilename();
                    if (($o->isDir()) && $file != '.' && $file != '..') {
                        //   if ($i++ == 0);
                        self::include_classes($o->getRealPath());
                    } else {
                        continue;
                    }
                }

                if (!preg_match('/\.php$/', $o->getPathName())) {
                    continue;
                }

                require_once $o->getPathName();
            }
        }
    }
}