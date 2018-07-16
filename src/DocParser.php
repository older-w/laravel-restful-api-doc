<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 10:24 AM
 */

namespace OlderW\RestfulDoc;


class DocParser
{
    public static  function getMethodDocForClass($class)
    {//echo $class."<br>\n";
        $method_doc = [];
        $class = new \ReflectionClass($class);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (in_array(($method->getName()),[
                'middleware',
                'getMiddleware',
                'callAction',
                '__call',
                'authorize',
                'authorizeForUser',
                'authorizeResource',
                'dispatchNow',
                'validateWith',
                'validate',
                'validateWithBag',
                'test',
            ]))
            {
                continue;
            }
            //echo $method;
            $doc = $method->getDocComment();
            if (!$doc) {
                die("{$method->getDeclaringClass()->getName()}::{$method->getName()}() 没有写注释\n");
            }
            $doc = self::parse_commentdoc($doc);
            if (isset($doc['ignore'])) {
                continue;
            }
            foreach (['intro', 'request', 'response'] as $key) {
                if (!isset($doc[$key]) || !trim($doc[$key])) {
                    die("{$method->getDeclaringClass()->getName()}::{$method->getName()}() 缺少 @{$key} 注释\n");
                }
            }
            if (isset($doc['throws']))
            {
                foreach (explode("\n",$doc['throws'] ) as $error)
                {
                    $error = trim($error);
                    $error = trim($error,';');
                    if (!$error)
                    {
                        continue;
                    }
                    $ex_class = '\App\Exceptions\\'.$error;
                    $ex = new $ex_class();
                    if ($ex instanceof  Exception)
                    {
                        $code   = $ex->getCode();
                        $msg   = $ex->getMessage();
                        $doc['errors'][]= [
                            'code'=>$code,
                            'message'=>$msg
                        ];
                    }

                }
            }
            if (0)
            {
                if( isset($method_doc['version']) && $doc['version'] == DOC_VERSION)
                {
                    $method_doc[$method->getName()] = $doc;
                    //$versionFilter = 1;
                }
            }
            else{
                $method_doc[$method->getName()] = $doc;
            }


        }
//        if (DOC_VERSION )
//        {
//            return null;
//        }

        return $method_doc;
    }


    public static function get_classes_commentdoc($class)
    {
        $class = new \ReflectionClass($class);

        if ($class->isAbstract()) {
            return false;
        }
        $doc = $class->getDocComment();
        $class_doc = self::parse_commentdoc($doc);
        $class_doc['properties'] = $class->getDefaultProperties();
        $class_doc['name']= $class->getName();

        return $class_doc;
    }
    public static function get_class_const_commentdoc($class)
    {
        $class = new \ReflectionClass($class);

        if ($class->isAbstract()) {
            return false;
        }
        $docs = [];
        foreach ($class->getReflectionConstants () as $c)
        {
            $comment =  $c->getDocComment();
            $comment = DocParser::trim_commentdoc($comment);
            $comment = implode(' ',$comment);
            $docs[$c->getValue() ] = $comment;//getName

        }
        return $docs;
    }
    /**
     * 解析注释内容.
     *
     * @return [
     *           (string) => (string),
     *           ...
     *           ]
     */
    public static function parse_commentdoc($doc)
    {
        $lines = self::trim_commentdoc($doc);
        $token = '';
        $result = [];
        foreach ($lines as $line) {
            //$line = trim($line);
            if (preg_match('/^@([a-z\_]+)(?:\s(.+))?$/', $line, $match)) {
                $token = $match[1];
//                $result[$token] = [];

                if (isset($match[2]) && $match[2]) {
                    $result[$token][] = $match[2];
                }

                continue;
            }

            if (!$token) {
                continue;
            }
            //          print_r($line); print_r($match);
            $result[$token][] = $line;
        }

        foreach ($result as $token => $lines) {
            $lines = implode("\n", $lines);
            $lines = trim($lines, "\n");

            $result[$token] = $lines;
        }

        return $result;
    }
    /**
     * 删除注释文字的注释符号
     * 拆分为数组，每行为一个元素.
     *
     * @return array
     */
    public static function trim_commentdoc($doc)
    {
        if (!$doc) {
            return [];
        }
        $result = [];
        $lines = preg_split('/\r|\n|\r\n/', $doc);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '*') {
                $result[] = '';
                continue;
            }
            if (preg_match('#^/\*+$#', $line)) {
                continue;
            } elseif (preg_match('#^\*+/$#', $line)) {
                continue;
            }
            $line = preg_replace('/^\*\s?/', '', $line);
            $result[] = $line;
        }
        return $result;
    }
}