<?php
$sub_menu = '600200';
include_once('./_common.php');
include_once('./admin.contents.lib.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');

auth_check($auth[$sub_menu], "w");

$sql = " select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ";
$od  = sql_fetch($sql);
if(!$od['od_id'])
    alert('주문자료가 존재하지 않습니다.');

if ($od_receipt_time) {
    if (cm_check_datetime($od_receipt_time) == false)
        alert('결제일시 오류입니다.');
}

// 결제정보 반영
$sql = " update {$g5['g5_contents_order_table']}
            set od_deposit_name    = '{$_POST['od_deposit_name']}',
                od_bank_account    = '{$_POST['od_bank_account']}',
                od_receipt_time    = '{$_POST['od_receipt_time']}',
                od_receipt_price   = '{$_POST['od_receipt_price']}',
                od_receipt_cash    = '{$_POST['od_receipt_cash']}',
                od_receipt_point   = '{$_POST['od_receipt_point']}',
                od_refund_price    = '{$_POST['od_refund_price']}'
            where od_id = '$od_id' ";
sql_query($sql);

// 주문정보
$info = cm_get_order_info($od_id);
if(!$info)
    alert('주문자료가 존재하지 않습니다.');

$od_status = $od['od_status'];
$cart_status = false;

// 미수가 0이고 상태가 주문이었다면 입금으로 변경
if($info['od_misu'] == 0 && $od['od_status'] == '주문')
{
    $od_status = '입금';
    $cart_status = true;
}

// 미수금액
$od_misu = ( $od['od_cart_price'] - $od['od_cancel_price'] )
           - ( $od['od_cart_coupon'] + $od['od_coupon'] )
           - ( $_POST['od_receipt_price']  + $_POST['od_receipt_cash'] + $_POST['od_receipt_point'] - $_POST['od_refund_price'] );

// 미수금 정보 등 반영
$sql = " update {$g5['g5_contents_order_table']}
            set od_misu         = '$od_misu',
                od_status       = '$od_status'
            where od_id = '$od_id' ";
sql_query($sql);

// 장바구니 상태 변경
if($cart_status) {
    $sql = " update {$g5['g5_contents_cart_table']}
                set ct_status = '$od_status'
                where od_id = '$od_id' ";

    switch($od_status) {
        case '입금':
            $sql .= " and ct_status = '주문' ";
            break;
        default:
            ;
    }

    sql_query($sql);

    // 주문에서 입금으로 변경시 판매수량 증가
    if($od_status == '입금')
        add_item_sale_qty($od_id);
}


// 메일발송
define("_ORDERMAIL_", true);
include "./ordermail.inc.php";


// SMS 문자전송
define("_ORDERSMS_", true);
include "./ordersms.inc.php";


$qstr1 = "od_status=".urlencode($odr_status)."&amp;od_settle_case=".urlencode($odr_settle_case)."&amp;od_misu=$odr_misu&amp;od_refund_price=$odr_refund_price&amp;od_receipt_cash=$odr_receipt_cash&amp;od_receipt_point=$odr_receipt_point&amp;od_coupon=$odr_coupon&amp;fr_date=$odr_fr_date&amp;to_date=$odr_to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

goto_url("./orderform.php?od_id=$od_id&amp;$qstr");
?>
