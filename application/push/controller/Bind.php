<?php

namespace app\push\controller;

use \GatewayWorker\Lib\Gateway;

class Bind {
    public function index() {
        if (request()->isPost()) {
            // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
//            Gateway::$registerAddress = '127.0.0.1:1238';
//            $client_id = request()->param('client_id');
//            $name = request()->param('name');
//            $uid = time().rand(10000,99999);
//            $_SESSION['uid']=$uid;
//            $_SESSION['name']=$name;
//            var_dump($_SESSION);
//            Gateway::bindUid($client_id, $uid);
//            $message = json_encode(array('type' => 'success', 'data' => '用户"'.$name.'"已登录！'));
//            // 向任意uid的网站页面发送数据
////            Gateway::sendToUid($uid, $message);
//            Gateway::sendToAll($message);
//            // 向任意群组的网站页面发送数据
////            Gateway::sendToGroup($group, $message);
        } else {
            return view();


        }
    }

    private $config = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
    public function aaa(){

        $num = request()->param('num');
        $array = array();
        $array1 = array();
        $m = intval($num/count($this->config));
        $n = $num%count($this->config);
        for($i = 0;$i<$m;$i++){
            foreach ($this->config as $k => $item){
                if(isset($this->config[$i-1])){
                    $array[] = $this->config[$i-1].$item;
                }else{
                    $array[] = $item;
                }
            }
        }
        foreach ($this->config as $k => $v){
            if($k<$n){
                if(isset($this->config[$m-1])){
                    $array1[] = $this->config[$m-1].$v;
                }else{
                    $array1[] = $v;
                }
            }else{
                break;
            }
        }
        $data = array_merge($array,$array1);
        dump($data);
    }
}