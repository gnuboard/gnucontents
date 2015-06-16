<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
if (!defined("_ORDERSMS_")) exit;

$receive_number = preg_replace("/[^0-9]/", "", $od_hp);	// 수신자번호 (받는사람 핸드폰번호 ... 여기서는 주문자님의 핸드폰번호임)
$send_number = preg_replace("/[^0-9]/", "", $setting['de_admin_company_tel']); // 발신자번호

if ($config['cf_sms_use']) {
    if ($od_sms_ipgum_check && $setting['de_sms_use4'])
    {
        if ($od_bank_account && $od_receipt_price && $od_deposit_name)
        {
            $sms_contents = cm_conv_sms_contents($od_id, $setting['de_sms_cont4']);

            $SMS = new SMS;
            $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
            $SMS->Add($receive_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_contents)), "");
            $SMS->Send();
        }
    }
}
?>
