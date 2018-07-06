<?php

use \GatewayWorker\Lib\Gateway;

global $global_uid, $global_i, $global_j;
$global_uid = 0;
//棋盘大小，默认15行15列
$global_i = 15;//棋盘行
$global_j = 15;//棋盘列

class Events {

    // 当有客户端连接时，将client_id返回，让mvc框架判断当前uid并执行绑定
    public static function onConnect($client_id) {

        global $global_uid, $global_i, $global_j;
        // 为这个链接分配一个uid
        Gateway::bindUid($client_id,++$global_uid);
        $_SESSION = array(
            'playing' => 0,
            'uid'     => $global_uid,
            'name'    => 'player' . $global_uid,
            'qipan'   => array(),
            'type'    => 0,
            'move'    => 0,
        );
        $json = array('status' => 1, 'msg' => '', 'data' => array());
        $json['data']['name'] = $_SESSION['name'];
        Gateway::sendToClient($client_id, json_encode($json));
        echo "player {$global_uid} connected!\n";

        //分配对手
        foreach (Gateway::getAllClientSessions() as $k => $val) {
            if ($val['playing'] == 0 && $k != $client_id) {
                //初始化棋盘
                $init_data = array();
                for ($i = 0; $i <= $global_i; $i++) {
                    for ($j = 0; $j <= $global_j; $j++) {
                        $init_data[$i][$j] = 0;
                    }
                }
                //分配红黑方
                $_SESSION['qipan'] = $init_data;
                $_SESSION['playing'] = $k;
                $_SESSION['type'] = 2;
                $_SESSION['move'] = 0;
                Gateway::updateSession($k, array('qipan'=>$init_data,'playing'=>$client_id,'type'=>1,'move'=>1));
                $json = array('status' => 0, 'msg' => '初始化棋盘...', 'data' => array());
                $json['data']['qipan'] = $init_data;
                $json['data']['text'] = "为你匹配到对手{$_SESSION['name']}!";
                Gateway::sendToClient($k, json_encode($json));
                $json['data']['text'] = "为你匹配到对手{$val['name']}!";
                Gateway::sendToClient($client_id, json_encode($json));
                break;
            }
        }
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param string $message 具体消息
     */
    public static function onMessage($client_id, $param) {
        $data = json_decode($param, true);
        if ($data['status'] == 2 && $_SESSION['playing'] > 0 && $_SESSION['move'] == 1) {
            $qipan = $_SESSION['qipan'];
            $press = explode('_', $data['data']);//取到本次落下的子
            if (isset($press[0])&&isset($press[1])) {
                if (isset($qipan[$press[0]][$press[1]])&&$qipan[$press[0]][$press[1]]) {
                    return;
                } else {
                    $qipan[$press[0]][$press[1]] = $_SESSION['type'];//更新棋盘
                    //跟新自己/对手数据
                    $_SESSION['qipan'] = $qipan;
                    $_SESSION['move'] = 0;
                    Gateway::updateSession($_SESSION['playing'], array('qipan'=>$qipan,'move'=>1));
                    //发送本次落下的子
                    $json = array('status' => 2, 'msg' => '', 'data' => array());
                    $json['data']['type'] = $_SESSION['type'];
                    $json['data']['press_i'] = $press[0];
                    $json['data']['press_j'] = $press[1];
                    Gateway::sendToClient($client_id, json_encode($json));
                    Gateway::sendToClient($_SESSION['playing'], json_encode($json));
                    $count = self::get_who_win($qipan, $press[0], $press[1], $_SESSION['type']);
                    file_put_contents('./qipan', json_encode($qipan));
                    if ($count >= 5) { //分出胜负
                        $json = array('status' => 3, 'msg' => $_SESSION['name'] . ' Win !', 'data' => array());
                        $json['data']['type'] = $_SESSION['type'];
                        Gateway::sendToClient($client_id, json_encode($json));
                        Gateway::sendToClient($_SESSION['playing'], json_encode($json));
                    }
                }
            }
        }
    }

    static function get_who_win($qipan = array(), $i = -1, $j = -1, $type = 0) {

        global $global_i, $global_j;
        $count = 0;
        $temp_type = $type;
        if (empty($qipan) || $i < 0 || $j < 0 || $type <= 0) {
            return $count;
        }
        echo json_encode($qipan) . "\n";
        echo 'i=' . $i . '|j=' . $j . '|type=' . $type . '|gi=' . $global_i . '|gj=' . $global_j . "\n";
        /*左右上下的判断*/
        $count = 1;
        $a = array(
            0 => array('index' => $j, 'border' => $global_j), //,
            1 => array('index' => $i, 'border' => $global_i) //上下
        );
        for ($round = 0; $round < 1; $round++) {
            $mov1_num = 1;
            $mov2_num = 1;
            while (true) {
                $mov1 = $a[$round]['index'] + $mov1_num;
                $mov2 = $a[$round]['index'] - $mov2_num;
                echo "'cs'$mov1,$mov2,$count\n";
                $temp_mov1 = $temp_mov2 = -1;
                if ($mov1_num > 0) {
                    if ($round == 0 && $mov1 <= $global_j) {
                        $temp_mov1 = $qipan[$i][$mov1];
                        var_dump($i . ',' . $mov1 . ',' . $temp_mov1);
                    } elseif ($round == 1 && $mov1 <= $global_i) {
                        $temp_mov1 = $qipan[$mov1][$j];
                    }

                    if ($temp_mov1 == $temp_type) {
                        $count++;
                        var_dump('count=' . $count);
                        $mov1_num++;
                    } else {
                        $mov1_num = 0;
                    }

                } else {
                    $mov1_num = 0;
                }

                if ($mov2 >= 0 && $mov2_num > 0) {
                    if ($round == 0) {
                        $temp_mov2 = $qipan[$i][$mov2];
                        var_dump($i . ',' . $mov2 . ',' . $temp_mov1);
                    } elseif ($round == 1) {
                        $temp_mov2 = $qipan[$mov2][$j];
                    }
                    if ($temp_mov2 == $temp_type) {
                        $count++;
                        $mov2_num++;
                    } else {
                        $mov2_num = 0;
                    }
                } else {
                    $mov2_num = 0;
                }
                if ($count >= 5) {
                    return $count;
                }
                if (($mov1_num == 0 && $mov2_num == 0)) {
                    break;
                }
            }
        }

        /*斜角的判断*/
        $count = 1;
        for ($round = 0; $round <= 1; $round++) {
            $mov1_num = 1;
            $mov2_num = 1;
            while (true) {
                $temp_mov1 = $temp_mov2 = -1;
                if (($i - $mov1_num) >= 0 && ($j - $mov1_num) >= 0 && ($j + $mov1_num) <= $global_j && $mov1_num > 0) {
                    if ($round == 0) {
                        $temp_mov1 = $qipan[$i - $mov1_num][$j + $mov1_num];
                    } elseif ($round == 1) {
                        $temp_mov1 = $qipan[$i - $mov1_num][$j - $mov1_num];
                    }

                    if ($temp_mov1 == $temp_type) {
                        $count++;
                        $mov1_num++;
                    } else {
                        $mov1_num = 0;
                    }

                } else {
                    $mov1_num = 0;
                }

                if (($i + $mov2_num) <= $global_i && ($j - $mov2_num) >= 0 && ($j + $mov2_num) <= $global_j && $mov2_num > 0) {
                    if ($round == 0) {
                        $temp_mov2 = $qipan[$i + $mov2_num][$j - $mov2_num];
                    } elseif ($round == 1) {
                        $temp_mov2 = $qipan[$i + $mov2_num][$j + $mov2_num];
                    }
                    if ($temp_mov2 == $temp_type) {
                        $count++;
                        $mov2_num++;
                    } else {
                        $mov2_num = 0;
                    }
                } else {
                    $mov2_num = 0;
                }
                if ($count >= 5) {
                    return $count;
                }
                if (($mov1_num == 0 && $mov2_num == 0)) {
                    break;
                }

            }
        }
        return $count;
    }
}