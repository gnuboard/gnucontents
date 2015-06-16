<?php
if (!defined('_GNUBOARD_')) exit;

// 주문과 장바구니의 상태를 변경한다.
function cm_change_status($od_id, $current_status, $change_status)
{
    global $g5;

    $sql = " update {$g5['g5_contents_order_table']} set od_status = '{$change_status}' where od_id = '{$od_id}' and od_status = '{$current_status}' ";
    sql_query($sql, true);

    $sql = " update {$g5['g5_contents_cart_table']} set ct_status = '{$change_status}' where od_id = '{$od_id}' and ct_status = '{$current_status}' ";
    sql_query($sql, true);
}


// 주문서에 입금시 update
function cm_order_update_receipt($od_id)
{
    global $g5;

    $sql = " update {$g5['g5_contents_order_table']} set od_receipt_price = od_misu, od_misu = 0, od_receipt_time = '".G5_TIME_YMDHIS."' where od_id = '$od_id' and od_status = '입금' ";
    return sql_query($sql);
}


// 처리내용 SMS
function cm_conv_sms_contents($uid, $contents, $type='')
{
    global $g5, $config, $setting;

    $sms_contents = '';

    if ($od_id && $config['cf_sms_use'] == 'icode')
    {
        if($type == 'cash') {
            $sql = " select cs_id as uid, cs_name as name, cs_receipt_price as receipt_price
                        from {$g5['g5_contents_cash_table']} where cs_id = '$uid' ";
        } else {
            $sql = " select od_id as uid, od_name as name, od_receipt_price as receipt_price
                        from {$g5['g5_contents_order_table']} where od_id = '$uid' ";
        }

        $od = sql_fetch($sql);

        $sms_contents = $contents;
        $sms_contents = str_replace("{이름}", $od['name'], $sms_contents);
        $sms_contents = str_replace("{입금액}", number_format($od['receipt_price']), $sms_contents);
        $sms_contents = str_replace("{주문번호}", $od['uid'], $sms_contents);
        $sms_contents = str_replace("{회사명}", $setting['de_admin_company_name'], $sms_contents);
    }

    return iconv("utf-8", "euc-kr", stripslashes($sms_contents));
}
?>