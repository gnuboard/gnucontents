<?php
$sub_menu = '600200';
include_once('./_common.php');

if($_POST['mod_type'] == 'info') {
    $sql = " update {$g5['g5_contents_order_table']}
                set od_name = '$od_name',
                    od_tel = '$od_tel',
                    od_hp = '$od_hp',
                    od_email = '$od_email' ";
} else {
    $sql = "update {$g5['g5_contents_order_table']}
                set od_shop_memo = '$od_shop_memo' ";
}
$sql .= " where od_id = '$od_id' ";
sql_query($sql);

$qstr1 = "od_status=".urlencode($odr_status)."&amp;od_settle_case=".urlencode($odr_settle_case)."&amp;od_misu=$odr_misu&amp;od_refund_price=$odr_refund_price&amp;od_receipt_cash=$odr_receipt_cash&amp;od_receipt_point=$odr_receipt_point&amp;od_coupon=$odr_coupon&amp;fr_date=$odr_fr_date&amp;to_date=$odr_to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

goto_url("./orderform.php?od_id=$od_id&amp;$qstr");
?>
