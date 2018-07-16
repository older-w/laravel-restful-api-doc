<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 1:40 PM
 */

namespace OlderW\RestfulDoc\Pusher;

use GuzzleHttp\Client;
use OlderW\RestfulDoc\Interfaces\DocPusher;
use OlderW\RestfulDoc\RestfulDoc;

class Wordpress implements DocPusher
{
    /**
     * @param $type string
     * @param $data string
     */
    public function push($type,$data)
    {
        $id = config(RestfulDoc::$config_path.'.pusher.wordpress.docs.'.$type.'.id');
        $key = config(RestfulDoc::$config_path.'.pusher.wordpress.docs.key','7358812c1388060212389866d5ea9f31');
        $user_id = config(RestfulDoc::$config_path.'.pusher.wordpress.docs.user_id','60');
        $url = config(RestfulDoc::$config_path.'.pusher.wordpress.docs.url','https://doc.wangxutech.com/wp-admin/admin-ajax.php?action=edit_api');
        $client = new Client();
        $res = $client->request(
            'POST',$url,[
            'form_params' => ['content' => $data,'id'=>$id,'hash'=>$user_id.','.md5($user_id.$key)]
        ]);
        $result = trim($res->getBody()->getContents());
        if ($result == 'success')
        {
            echo '发布成功';
            return ;
        }
        else{
            echo '发布失败';
        }
        print_r($res->getBody()->getContents());
    }
}