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
    public static function onMessage($client_id, $message) {
        // 向发送人发送
        var_dump($_SESSION);
        $message = json_encode(array('type' => 'success', 'data' => '用户"'.$_SESSION['name'].'":'.$message));
        // 向任意uid的网站页面发送数据
//            Gateway::sendToUid($uid, $message);
        Gateway::sendToAll($message);
//        Gateway::sendToClient($client_id, json_encode(array('type' => 'send', 'data' => '你说:' . $message)));
    }
}