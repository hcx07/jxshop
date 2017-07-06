<?php
/**
 * Created by PhpStorm.
 * User: 周骁
 * Date: 2017/7/3
 * Time: 18:47
 */
namespace frontend\models;

class CURL
{
    public function requestGet($url,$ssl=true)
    {
        // curl初始
        $curl = curl_init();

        //设置curl选项
        //1、URL
        curl_setopt($curl,CURLOPT_URL,$url);
        //2、user_agent,请求代理信息
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36';
        curl_setopt($curl,CURLOPT_USERAGENT,$user_agent);
        //3、referer头，请求来源
        curl_setopt($curl,CURLOPT_AUTOREFERER,true);
        //4、SSL相关
        if($ssl){
            //a.终止从服务器端验证（因为我们已经非常相信微信服务器）
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
            //b.检查服务器SSL证书中是否存在一个公用名(common name)
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,2);
        }
        //5、是否处理响应头
        curl_setopt($curl,CURLOPT_HEADER,false);
        //6、是否返回响应结果
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

        //发出请求
        $response = curl_exec($curl);
        if ($response === false){
            echo '<br />',curl_error($curl),'<br />';
            return false;
        }
        return $response;
    }

    public function requestPost($url,$data,$ssl=true)
    {
        // curl初始
        $curl = curl_init();

        //设置curl选项
        //1、URL
        curl_setopt($curl,CURLOPT_URL,$url);
        //2、user_agent,请求代理信息
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36';
        curl_setopt($curl,CURLOPT_USERAGENT,$user_agent);
        //3、referer头，请求来源
        curl_setopt($curl,CURLOPT_AUTOREFERER,true);
        //设置超时时间
        curl_setopt($curl,CURLOPT_TIMEOUT,10);
        //4、SSL相关
        if($ssl){
            //a.终止从服务器端验证（因为我们已经非常相信微信服务器）
            curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
            //b.检查服务器SSL证书中是否存在一个公用名(common name)
            curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,2);
            //php5.6版本https请求必须加上这段
            curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, false);
        }
        //处理POST请求相关
        curl_setopt($curl,CURLOPT_POST,true); //是否为post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data); //处理请求数据
        //处理响应结果
        curl_setopt($curl,CURLOPT_HEADER,false);//是否处理响应头
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);//curl_exec()
        //是否返回响应结果
        //发出请求
        $response = curl_exec($curl);
        if ($response === false){
            echo '<br />',curl_error($curl),'<br />';
            return false;
        }
        return $response;
    }
}