<?php
include_once('./_common.php');

// 세션에 저장된 토큰과 폼으로 넘어온 토큰을 비교하여 틀리면 에러
if ($token && get_session("ss_token") == $token) {
    // 맞으면 세션을 지워 다시 입력폼을 통해서 들어오도록 한다.
    set_session("ss_token", "");
} else {
    alert("토큰 에러");
}

$od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' and mb_id = '{$member['mb_id']}' ");

if (!$od['od_id']) {
    alert("존재하는 주문이 아닙니다.");
}

// 주문상품의 상태가 주문인지 체크
$sql = " select SUM(IF(ct_status = '주문', 1, 0)) as od_count2,
                COUNT(*) as od_count1
            from {$g5['g5_contents_cart_table']}
            where od_id = '$od_id' ";
$ct = sql_fetch($sql);

$uid = md5($od['od_id'].$od['od_time'].$od['od_ip']);

if($od['od_cancel_price'] > 0 || $ct['od_count1'] != $ct['od_count2']) {
    alert("취소할 수 있는 주문이 아닙니다.", G5_CONTENTS_URL."/orderinquiryview.php?od_id=$od_id&amp;uid=$uid");
}

// PG 결제 취소
if($od['od_tno']) {
    switch($od['od_pg']) {
        case 'lg':
            require './settle_lg.inc.php';
            $LGD_TID    = $od['od_tno'];        //LG유플러스으로 부터 내려받은 거래번호(LGD_TID)

            $xpay = new XPay($configPath, $CST_PLATFORM);

            // Mert Key 설정
            $xpay->set_config_value('t'.$LGD_MID, $config['cf_lg_mert_key']);
            $xpay->set_config_value($LGD_MID, $config['cf_lg_mert_key']);
            $xpay->Init_TX($LGD_MID);

            $xpay->Set("LGD_TXNAME", "Cancel");
            $xpay->Set("LGD_TID", $LGD_TID);

            if ($xpay->TX()) {
                //1)결제취소결과 화면처리(성공,실패 결과 처리를 하시기 바랍니다.)
                /*
                echo "결제 취소요청이 완료되었습니다.  <br>";
                echo "TX Response_code = " . $xpay->Response_Code() . "<br>";
                echo "TX Response_msg = " . $xpay->Response_Msg() . "<p>";
                */
            } else {
                //2)API 요청 실패 화면처리
                $msg = "결제 취소요청이 실패하였습니다.\\n";
                $msg .= "TX Response_code = " . $xpay->Response_Code() . "\\n";
                $msg .= "TX Response_msg = " . $xpay->Response_Msg();

                alert($msg);
            }
            break;
        default:
            require './settle_kcp.inc.php';

            $_POST['tno'] = $od['od_tno'];
            $_POST['req_tx'] = 'mod';
            $_POST['mod_type'] = 'STSC';
            $_POST['mod_desc'] = iconv("utf-8", "euc-kr", '주문자 본인 취소-'.$cancel_memo);
            $_POST['site_cd'] = $setting['de_kcp_mid'];

            // 취소내역 한글깨짐방지
            setlocale(LC_CTYPE, 'ko_KR.euc-kr');

            include G5_CONTENTS_PATH.'/kcp/pp_ax_hub.php';

            // locale 설정 초기화
            setlocale(LC_CTYPE, '');
    }
}

// 장바구니 자료 취소
sql_query(" update {$g5['g5_contents_cart_table']} set ct_status = '취소' where od_id = '$od_id' ");

// 주문 취소
$cancel_memo = addslashes($cancel_memo);
$cancel_price = $od['od_cart_price'];

$sql = " update {$g5['g5_contents_order_table']}
            set od_receipt_price = '0',
                od_receipt_point = '0',
                od_receipt_cash  = '0',
                od_misu = '0',
                od_cancel_price = '$cancel_price',
                od_cart_coupon = '0',
                od_coupon = '0',
                od_status = '취소',
                od_shop_memo = concat(od_shop_memo,\"\\n주문자 본인 직접 취소 - ".G5_TIME_YMDHIS." (취소이유 : {$cancel_memo})\")
            where od_id = '$od_id' ";
sql_query($sql);

// 주문취소 회원의 포인트를 되돌려 줌
if ($od['od_receipt_point'] > 0)
    insert_point($member['mb_id'], $od['od_receipt_point'], "컨텐츠몰 주문번호 $od_id 본인 취소");

// 주문취소 회원의 캐시를 되돌려 줌
if ($od['od_receipt_cash'] > 0)
    insert_cash($od['mb_id'], $od['od_id'], $od['od_receipt_cash'], "컨텐츠몰 주문번호 $od_id 본인 취소");

goto_url(G5_CONTENTS_URL."/orderinquiryview.php?od_id=$od_id&amp;uid=$uid");
?>