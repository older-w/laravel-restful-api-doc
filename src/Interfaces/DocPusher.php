<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/16/18
 * Time: 9:30 AM
 */

namespace OlderW\RestfulDoc\Interfaces;


interface DocPusher
{
    /**
     * @param $type string
     * @param $data string
     */
    public function push($type,$data);
}