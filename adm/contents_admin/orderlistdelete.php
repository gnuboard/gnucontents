<?php
$sub_menu = '600200';
include_once('./_common.php');

//print_r2($_POST); exit;

for ($i=0; $i<count($_POST['chk']); $i++)
{
    // 실제 번호를 넘김
    $k     = $_POST['chk'][$i];
    $od_id = $_POST['od_id'][$k];

    $od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ");
    if (!$od) continue;

    $data = serialize($od);

    $sql = " insert {$g5['g5_contents_order_delete_table']} set de_key = '$od_id', de_data = '".addslashes($data)."', mb_id = '{$member['mb_id']}', de_ip = '{$_SERVER['REMOTE_ADDR']}', de_datetime = '".G5_TIME_YMDHIS."' ";
    sql_query($sql, true);

    $sql = " delete from {$g5['g5_contents_order_table']} where od_id = '$od_id' ";
    sql_query($sql);
}

$qstr  = "sort1=$sort1&amp;sort2=$sort2&amp;sel_field=$sel_field&amp;search=$search";
$qstr .= "&amp;od_status=".urlencode($odr_status);
$qstr .= "&amp;od_settle_case=".urlencode($odr_settle_case);
$qstr .= "&amp;od_misu=$odr_misu";
$qstr .= "&amp;od_refund_price=$odr_refund_price";
$qstr .= "&amp;od_receipt_cash=$odr_receipt_cash";
$qstr .= "&amp;od_receipt_point=$odr_receipt_point";
$qstr .= "&amp;od_coupon=$odr_coupon";
$qstr .= "&amp;fr_date=$odr_fr_date&amp;to_date=$odr_to_date";
$qstr .= "&amp;page=$page";

goto_url("./orderlist.php?$qstr");
?>