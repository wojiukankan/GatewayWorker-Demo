<?php

use \GatewayWorker\Lib\Gateway;

class Events {

    // 当有客户端连接时，将client_id返回，让mvc框架判断当前uid并执行绑定
    public static function onConnect($client_id) {
        Gateway::sendToClient($client_id, json_encode(array(
            'type' => 'init',
            'client_id' => $client_id
        )));
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param string $message 具体消息
     */
    public static function onMessage($client_id, $param) {
        // 向发送人发送
        $data = json_decode($param,true);
        if($data['type'] == 'init'){
            $uid = time().rand(10000,99999);
            Gateway::bindUid($client_id, $uid);
            $_SESSION['uid'] =$uid;
            $data['name'] = $data['name']?$data['name']:'匿名';
            $_SESSION['name']=$data['name'];
            $message = json_encode(array('type' => 'login', 'data' => '用户"'.$data['name'].'"已登录！'));
            Gateway::sendToAll($message);
        }else{
            $message = json_encode(array('type' => 'message', 'data' => $_SESSION['name'].':'.$data['value']));
            Gateway::sendToAll($message);
        }
    }
}