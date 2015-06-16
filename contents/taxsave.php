<?php
include_once('./_common.php');

$g5['title'] = '주문번호 '.$od_id.' 현금영수증 발행';
include_once(G5_PATH.'/head.sub.php');

if($tx == 'cash') {
    $od = sql_fetch(" select * from {$g5['g5_contents_cash_table']} where cs_id = '$od_id' ");
    if (!$od)
        die('<p id="scash_empty">캐시충전 내역이 존재하지 않습니다.</p>');

    $goods_name = $od['cs_cash_price'].'원 캐시충전';
    $amt_tot = (int)$od['cs_receipt_price'];
    $dir = $od['cs_pg'];
    $od_name = $od['cs_name'];
    $od_email = $od['cs_email'];
    $od_tel = $od['cs_hp'];
} else {
    $od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ");
    if (!$od)
        die('<p id="scash_empty">주문서가 존재하지 않습니다.</p>');

    $goods = cm_get_goods($od['od_id']);
    $goods_name = $goods['full_name'];
    $amt_tot = (int)($od['od_receipt_price'] - $od['od_refund_price']);
    $dir = $od['od_pg'];
    $od_name = $od['od_name'];
    $od_email = $od['od_email'];
    $od_tel = $od['od_tel'];
}

$trad_time = date("YmdHis");

$amt_sup = (int)round(($amt_tot * 10) / 11);
$amt_svc = 0;
$amt_tax = (int)($amt_tot - $amt_sup);

// 신청폼
include_once(G5_CONTENTS_PATH.'/'.$dir.'/taxsave_form.php');

include_once(G5_PATH.'/tail.sub.php');
?>
