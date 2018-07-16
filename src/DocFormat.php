<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 3:19 PM
 */
namespace OlderW\RestfulDoc;

class DocFormat implements \OlderW\RestfulDoc\Interfaces\DocFormat
{
    /**
     * @param array $docs
     * @return string
     */
    public static function enum_markdown(array $docs)
    {
        $str = '';
        foreach ($docs as $doc)
        {
            $str .= $doc['intro']."\n";
            $str .=  "```\n";
            foreach ($doc['data'] as $val => $comment)
            {
                $str .=  $val."    ".$comment."\n";
            }
            $str .=  "```\n";
        }
        return $str;
    }
    /**
     * @param array $docs
     * @return string
     */
    public static function exec_markdown(array $docs)
    {
        $str = "## 错误码说明\n";
        array_multisort(array_column($docs,'errno'),SORT_DESC,$docs);
        foreach ($docs as $doc)
        {
            $str.= '- '.$doc['errno'].' '.$doc['error']."\n";
        }
        return $str;
    }
    /**
     * @param array $docs
     * @return string
     */
    public static function api_markdown(array $docs)
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
}