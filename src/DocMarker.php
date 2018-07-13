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
            //print_r($route->getActionName());echo "--";exit;
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
            echo $class.' '.$method.'存在多个对应';
            exit();
        }
        if (count($result) == 0)
        {
            die($class.' '.$method.' 找不到对应');
        }
        return $result[0];

    }

    public static function getEnumDoc($path = '/app/Enum')
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
        self::to_menu_markdown($docs);
    }
    public static function to_menu_markdown($docs)
    {
        foreach ($docs as $doc)
        {
            echo $doc['intro']."\n";
            echo "```\n";
            foreach ($doc['data'] as $val => $comment)
            {
                echo $val."    ".$comment."\n";
            }
            echo "```\n";
        }
    }
    /**
     * 生成api文档
     */
    public static function getDoc($path = '/app/Http/Controllers/Api')
    {


        $classes = self::get_classes([
            base_path() . $path,
        ]);
        $docs = [];
        sort($classes);
        foreach ($classes as $class) {
            if (!is_subclass_of($class,ApiController::class))
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
            //echo get_class(app('url'));exit;
            if ($motheds = DocParser::getMethodDocForClass($class))
            {
                if (!is_array($motheds))
                {
                    print_r($motheds);die('date error!!');
                }
                foreach ($motheds as $method => $method_doc) {//print_r($method_doc);exit;
                    //app('url')->route($name, $parameters, $absolute);
//                $url = route($method_doc['route']);
//                $url = (parse_url($url));
//                $path = $url['path'];
//                $method = self::getMotehd($method);
//                $api = sprintf('%s %s', $method, $path);
                    //$api = $method_doc['route'];
                    $uri = self::getUri($class,$method);
//                    if ($uri['method'].' '.$uri['uri']!=$method_doc['route'])
//                    {
//                        die($uri['method'].' '.$uri['uri'].'----------'.$method_doc['route']);
//                    }

                    $docs[$module][$uri['method'].' '.$uri['uri']] = $method_doc;
                    //break;
                }
            }


        }
        return self::to_doc_markdown($docs);
    }
    public static function getMotehd($m)
    {
        if ($m == 'store')
        {
            return 'POST';
        }elseif ($m =='update')
        {
            return 'PUT';
        }elseif ($m =='show')
        {
            return 'GET';
        }
        return $m;
    }

    public static function to_doc_markdown($docs)
    {
        $toc = [];
        $line =[];
        ob_start();
        foreach ($docs as $module=>$apis)
        {

            $toc[] = "\n".'['.$module.'](#'.$module.')'."\n\n |  |\n | ------------------------ |";
            $line[] = '<br><br>';
            $line[] ="### <div id='".$module."'  style='border: 1px solid #ddd;padding: 6px 13px;background: #0088cc;color:#fff'>".$module."</div>";
            //$line[] = '---';

            foreach ($apis as $url =>$doc)
            {

                $anchor = str_replace(' ','',$url);
                $toc[] ='| ['.$doc['intro'].'](#'.$anchor.') |';
                $line[] = "### <span id='".$anchor."'>".$url." ".$doc['intro']."</span>";
                if (isset($doc['desc']))
                {
                    $line[] = $doc['desc'];
                }
                $line[] =  "```";
                $line[] =  "request\n".$doc['request'];
                $line[] =  "response\n".$doc['response'];
                if (isset($doc['errors']))
                {
                    $err_str = [];
                    foreach ($doc['errors'] as $error)
                    {
                        $err_str[] = '    '.$error['code'].'     '.$error['message'];
                    }
                    $line[] =  "throws\n{\n".implode("\n",$err_str)."\n}\n";
                }
                //print_r($doc);exit;
                $line[] =  "```";
            }

        }
        //print_r($toc);exit;
        //$toc = ;
        print_r(implode("\n",$toc));
        echo "\n\n---\n\n";
        print_r(implode("\n",$line));
        //exit;
        //exit;
        $out1 = ob_get_contents();
        ob_end_clean();
        return $out1;
    }
    public static function getExceptionDoc()
    {
        $classes = self::get_classes([
            base_path() . '/app/Exceptions',
            //other root
        ]);
        $docs = [];//print_r($classes);\Symfony\Component\HttpKernel\Exception\HttpException::class;
        $errnoList = [];
        foreach ($classes as $class) {
            if (substr($class, -9) !== 'Exception') {
                continue;
            }
            if ($class == Exception::class)
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
            if (isset($errnoList[$doc['errno']]) && $doc['errno'] != 500000) {
                //@todo容错处理

                die(" {$doc['name']}中的错误码和 {$errnoList[$doc['errno']]} 中的重复 {$doc['errno']} \n");
            }
            $errnoList[$doc['errno']]=$doc['name'];

            $docs[] = $doc;
        }

        return self::to_exec_markdown($docs);

    }
    public static function to_exec_markdown($docs)
    {
        $str = "## 错误码说明\n";
        array_multisort(array_column($docs,'errno'),SORT_DESC,$docs);
        foreach ($docs as $doc)
        {
            $str.= '- '.$doc['errno'].' '.$doc['error']."\n";
        }
        return $str;
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