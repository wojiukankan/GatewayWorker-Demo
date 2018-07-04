<?php

namespace app\push\controller;

use \GatewayWorker\Lib\Gateway;

class Bind {
    public function index() {
        if (request()->isPost()) {
            // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值(ip不能是0.0.0.0)
            Gateway::$registerAddress = '127.0.0.1:1238';
            $client_id = request()->param('client_id');
            $uid = request()->param('name');
            Gateway::bindUid($client_id, $uid);
            $message = json_encode(array('type' => 'success', 'data' => '连接成功！'));
            // 向任意uid的网站页面发送数据
            Gateway::sendToUid($uid, $message);
            // 向任意群组的网站页面发送数据
//            Gateway::sendToGroup($group, $message);
        } else {
            return view();

        }
    }
}