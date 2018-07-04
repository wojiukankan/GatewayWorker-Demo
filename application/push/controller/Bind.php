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
}