<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/16/18
 * Time: 9:26 AM
 */
namespace OlderW\RestfulDoc\Interfaces;

interface DocFormat
{
    /**
     * @param array $docs
     * @return string
     */
    public static function enum_markdown(array $docs);
    /**
     * @param array $docs
     * @return string
     */
    public static function exec_markdown(array $docs);
    /**
     * @param array $docs
     * @return string
     */
    public static function api_markdown(array $docs);
}