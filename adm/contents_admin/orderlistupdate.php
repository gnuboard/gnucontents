<?php
$sub_menu = '600200';
include_once('./_common.php');
include_once('./admin.contents.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');

define("_ORDERMAIL_", true);

//print_r2($_POST); exit;

$sms_count = 0;
if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'])
{
    $SMS = new SMS;
	$SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
}

for ($i=0; $i<count($_POST['chk']); $i++)
{
    // 실제 번호를 넘김
    $k     = $_POST['chk'][$i];
    $od_id = $_POST['od_id'][$k];

    $od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ");
    if (!$od) continue;

    $current_status = $od['od_status'];
    $change_status  = $_POST['od_status'];

    if ($current_status == '주문')
    {
        if ($change_status != '입금') continue;
        if ($od['od_settle_case'] != '무통장') continue;
        cm_change_status($od_id, '주문', '입금');
        cm_order_update_receipt($od_id);

        // 입금인 경우에 상품구입 합계수량을 상품테이블에 저장
        add_item_sale_qty($od_id);

        // SMS
        if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'] && $setting['de_sms_use4']) {
            $sms_contents = cm_conv_sms_contents($od_id, $setting['de_sms_cont4']);
            if($sms_contents) {
                $receive_number = preg_replace("/[^0-9]/", "", $od['od_hp']);	// 수신자번호
                $send_number = preg_replace("/[^0-9]/", "", $setting['de_admin_company_tel']); // 발신자번호

                if($receive_number && $send_number) {
                    $SMS->Add($receive_number, $send_number, $config['cf_icode_id'], $sms_contents, "");
                    $sms_count++;
                }
            }
        }

        // 메일
        if($config['cf_email_use'] && $_POST['od_send_mail'])
            include './ordermail.inc.php';
    }

    // 주문정보
    $info = cm_get_order_info($od_id);
    if(!$info) continue;

    $sql = " update {$g5['g5_contents_order_table']}
                set od_misu = '{$info['od_misu']}'
                where od_id = '$od_id' ";
    sql_query($sql, true);

}

// SMS
if($config['cf_sms_use'] == 'icode' && $_POST['send_sms'] && $sms_count)
{
    $SMS->Send();
}

$qstr  = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search";
$qstr .= "&amp;od_status=".urlencode($od_status);
$qstr .= "&amp;od_settle_case=".urlencode($odr_settle_case);
$qstr .= "&amp;od_misu=$odr_misu";
$qstr .= "&amp;od_refund_price=$odr_refund_price";
$qstr .= "&amp;od_receipt_cash=$odr_receipt_cash";
$qstr .= "&amp;od_receipt_point=$odr_receipt_point";
$qstr .= "&amp;od_coupon=$odr_coupon";
$qstr .= "&amp;fr_date=$odr_fr_date&amp;to_date=$odr_to_date";
$qstr .= "&amp;page=$page";

//exit;

goto_url("./orderlist.php?$qstr");
?>