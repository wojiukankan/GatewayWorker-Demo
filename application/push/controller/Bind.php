<?php

namespace app\push\controller;

use \GatewayWorker\Lib\Gateway;

class Bind {
    public function index() {
        if (request()->isPost()) {
//            if(cache('uid')){
//
//            }else{
//                cache('uid',1);
//            }
//            $param = request()->param();
//            Gateway::bindUid($param['client_id'], $uid);
        } else {
            return view();
        }
    }
}