<?php
include_once('./_common.php');

$content = trim($_POST['content']);
$tel     = preg_replace('/[^0-9]/', '', trim($_POST['tel']));
$name    = trim($_POST['name']);

if(!$content)
    die('문의내용을 입력해 주십시오.');

if(!$tel)
    die('연락처를 입력해 주십시오.');

if(!$name)
    die('성함을 입력해 주십시오.');

if(!$config['cf_sms_use'])
    die('Fail');

// SMS BEGIN --------------------------------------------------------
$is_sms_send = false;

// 충전식일 경우 잔액이 있는지 체크
if($config['cf_icode_id'] && $config['cf_icode_pw']) {
    $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);

    if($userinfo['code'] == 0) {
        if($userinfo['payment'] == 'C') { // 정액제
            $is_sms_send = true;
        } else {
            $minimum_coin = 100;
            if(defined('G5_ICODE_COIN'))
                $minimum_coin = intval(G5_ICODE_COIN);

            if((int)$userinfo['coin'] >= $minimum_coin)
                $is_sms_send = true;
        }
    }
}

if($is_sms_send) {
    include_once(G5_LIB_PATH.'/icode.sms.lib.php');

    $SMS = new SMS; // SMS 연결
    $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);

    $sms_content = $content."\n".$name;
    $recv_number = preg_replace("/[^0-9]/", "", $setting['de_sms_hp']);
    $send_number = preg_replace("/[^0-9]/", "", $tel);

    if($sms_content && $recv_number) {
        $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_content)), "");
        $SMS->Send();
    }

    if(isset($_POST['ajax']) && $_POST['ajax'] == 1)
        die('OK');
    else
        alert('고객님의 문의가 정상적으로 접수되었습니다.', G5_CONTENTS_URL);
} else {
    die('Fail');
}
// SMS END   --------------------------------------------------------
?>