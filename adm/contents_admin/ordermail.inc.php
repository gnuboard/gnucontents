<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// 제대로된 include 시에만 실행
if (!defined("_ORDERMAIL_")) exit;

// 주문자님께 메일발송 체크를 했다면
if ($od_send_mail)
{
    $od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ");

    $addmemo = nl2br(stripslashes($addmemo));

    unset($cart_list);
    unset($card_list);
    unset($bank_list);
    unset($cash_list);
    unset($point_list);

    $sql = " select *
               from {$g5['g5_contents_cart_table']}
              where od_id = '{$od['od_id']}'
              order by ct_id ";
    $result = sql_query($sql);
    for ($j=0; $ct=mysql_fetch_array($result); $j++) {
        $cart_list[$j]['it_id']   = $ct['it_id'];
        $cart_list[$j]['it_name'] = $ct['it_name'];
        $cart_list[$j]['it_opt']  = $ct['ct_option'];

        $ct_status = $ct['ct_status'];
        $cart_list[$j]['ct_status'] = $ct_status;
        $cart_list[$j]['ct_qty']    = $ct['ct_qty'];
    }


    /*
    ** 입금정보
    */
    $is_receipt = false;

    // 신용카드 입금
    if ($od['od_receipt_price'] > 0 && $od['od_settle_case'] == '신용카드') {
        $card_list['od_receipt_time'] = $od['od_receipt_time'];
        $card_list['od_receipt_price'] = cm_display_price($od['od_receipt_price']);

        $is_receipt = true;
    }

    // 무통장 입금
    if ($od['od_receipt_price'] > 0 && $od['od_settle_case'] == '무통장') {
        $bank_list['od_receipt_time']    = $od['od_receipt_time'];
        $bank_list['od_receipt_price'] = cm_display_price($od['od_receipt_price']);
        $bank_list['od_deposit_name'] = $od['od_deposit_name'];

        $is_receipt = true;
    }

    // 캐시 입금
    if ($od['od_receipt_cash'] > 0) {
        $cash_list['od_time']         = $od['od_time'];
        $cash_list['od_receipt_cash'] = cm_display_price($od['od_receipt_cash']);

        $is_receipt = true;
    }

    // 포인트 입금
    if ($od['od_receipt_point'] > 0) {
        $point_list['od_time']          = $od['od_time'];
        $point_list['od_receipt_point'] = cm_display_point($od['od_receipt_point']);

        $is_receipt = true;
    }

    // 입금이 있다면 메일 발송
    if ($is_receipt)
    {
        ob_start();
        include G5_CONTENTS_PATH.'/mail/ordermail.mail.php';
        $content = ob_get_contents();
        ob_end_clean();

        $title = $config['cf_title'].' - '.$od['od_name'].'님 주문 처리 내역 안내';
        $email = $od['od_email'];

        // 메일 보낸 내역 상점메모에 update
        $od_shop_memo = G5_TIME_YMDHIS.' - 결제내역 메일발송\n' . $od['od_shop_memo'];

        sql_query(" update {$g5['g5_contents_order_table']} set od_shop_memo = '$od_shop_memo' where od_id = '$od_id' ");

        mailer($config['cf_admin_email_name'], $config['cf_admin_email'], $email, $title, $content, 1);
    }
}
?>
