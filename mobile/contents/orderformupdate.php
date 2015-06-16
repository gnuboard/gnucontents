<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

//print_r2($_POST); exit;

if($is_guest)
    alert('회원 로그인 후 이용해 주십시오.', G5_CONTENTS_URL);

$page_return_url = G5_CONTENTS_URL.'/orderform.php';
if(get_session('ss_cm_direct'))
    $page_return_url .= '?sw_direct=1';

// 결제등록 완료 체크
if($order_price > 0 && $od_settle_case != '무통장') {
    if($setting['de_pg_service'] == 'kcp' && ($_POST['tran_cd'] == '' || $_POST['enc_info'] == '' || $_POST['enc_data'] == ''))
        alert('결제등록 요청 후 주문해 주십시오.', $page_return_url);

    if($setting['de_pg_service'] == 'lg' && !$_POST['LGD_PAYKEY'])
        alert('결제등록 요청 후 주문해 주십시오.', $page_return_url);

    if($setting['de_pg_service'] == 'inicis' && !$_POST['P_HASH'])
        alert('결제등록 요청 후 주문해 주십시오.', $page_return_url);
}

// 장바구니가 비어있는가?
if (get_session("ss_cm_direct"))
    $tmp_cart_id = get_session('ss_cm_cart_direct');
else
    $tmp_cart_id = get_session('ss_cm_cart_id');

if (cm_get_cart_count($tmp_cart_id) == 0)// 장바구니에 담기
    alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', G5_CONTENTS_URL.'/cart.php');

$i_price      = (int)$_POST['od_price'];
$i_temp_point = (int)$_POST['od_temp_point'];
$i_temp_cash  = (int)$_POST['od_temp_cash'];


// 주문금액이 상이함
$sql = " select SUM((ct_price + io_price) * ct_qty) as od_price,
              COUNT(distinct it_id) as cart_count
            from {$g5['g5_contents_cart_table']} where od_id = '$tmp_cart_id' and ct_select = '1' ";
$row = sql_fetch($sql);
$tot_ct_price = $row['od_price'];
$cart_count = $row['cart_count'];
$tot_od_price = $tot_ct_price;

// 쿠폰금액계산
$tot_cp_price = 0;
if($is_member) {
    // 상품쿠폰
    $tot_it_cp_price = $tot_od_cp_price = 0;
    $it_cp_cnt = count($_POST['cp_id']);
    $arr_it_cp_prc = array();
    for($i=0; $i<$it_cp_cnt; $i++) {
        $cid = $_POST['cp_id'][$i];
        $it_id = $_POST['it_id'][$i];
        $sql = " select cp_id, cp_method, cp_target, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$g5['g5_contents_coupon_table']}
                    where cp_id = '$cid'
                      and mb_id IN ( '{$member['mb_id']}', '전체회원' )
                      and cp_start <= '".G5_TIME_YMD."'
                      and cp_end >= '".G5_TIME_YMD."'
                      and cp_method IN ( 0, 1 ) ";
        $cp = sql_fetch($sql);
        if(!$cp['cp_id'])
            continue;

        // 사용한 쿠폰인지
        if(cm_is_used_coupon($member['mb_id'], $cp['cp_id']))
            continue;

        // 분류할인인지
        if($cp['cp_method']) {
            $sql2 = " select it_id, ca_id, ca_id2, ca_id3
                        from {$g5['g5_contents_item_table']}
                        where it_id = '$it_id' ";
            $row2 = sql_fetch($sql2);

            if(!$row2['it_id'])
                continue;

            if($row2['ca_id'] != $cp['cp_target'] && $row2['ca_id2'] != $cp['cp_target'] && $row2['ca_id3'] != $cp['cp_target'])
                continue;
        } else {
            if($cp['cp_target'] != $it_id)
                continue;
        }

        // 상품금액
        $sql = " select SUM((ct_price + io_price) * ct_qty) as sum_price
                    from {$g5['g5_contents_cart_table']}
                    where od_id = '$tmp_cart_id'
                      and it_id = '$it_id'
                      and ct_select = '1' ";
        $ct = sql_fetch($sql);
        $item_price = $ct['sum_price'];

        if($cp['cp_minimum'] > $item_price)
            continue;

        $dc = 0;
        if($cp['cp_type']) {
            $dc = floor(($item_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
        } else {
            $dc = $cp['cp_price'];
        }

        if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
            $dc = $cp['cp_maximum'];

        if($item_price < $dc)
            continue;

        $tot_it_cp_price += $dc;
        $arr_it_cp_prc[$it_id] = $dc;
    }

    $tot_od_price -= $tot_it_cp_price;

    // 주문쿠폰
    if($_POST['od_cp_id']) {
        $sql = " select cp_id, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$g5['g5_contents_coupon_table']}
                    where cp_id = '{$_POST['od_cp_id']}'
                      and mb_id IN ( '{$member['mb_id']}', '전체회원' )
                      and cp_start <= '".G5_TIME_YMD."'
                      and cp_end >= '".G5_TIME_YMD."'
                      and cp_method = '2' ";
        $cp = sql_fetch($sql);

        // 사용한 쿠폰인지
        $cp_used = cm_is_used_coupon($member['mb_id'], $cp['cp_id']);

        $dc = 0;
        if(!$cp_used && $cp['cp_id'] && ($cp['cp_minimum'] <= $tot_od_price)) {
            if($cp['cp_type']) {
                $dc = floor(($tot_od_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
            } else {
                $dc = $cp['cp_price'];
            }

            if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
                $dc = $cp['cp_maximum'];

            if($tot_od_price < $dc)
                die('Order coupon error.');

            $tot_od_cp_price = $dc;
            $tot_od_price -= $tot_od_cp_price;
        }
    }

    $tot_cp_price = $tot_it_cp_price + $tot_od_cp_price;
}

if ((int)($row['od_price'] - $tot_cp_price) !== $i_price) {
    die("Error.");
}

// 결제포인트가 상이함
// 회원이면서 포인트사용이면
$temp_point = 0;
if ($is_member && $config['cf_use_point'])
{
    if($member['mb_point'] >= $setting['de_settle_min_point']) {
        $temp_point = (int)$setting['de_settle_max_point'];

        if($temp_point > (int)$tot_od_price)
            $temp_point = (int)$tot_od_price;

        if($temp_point > (int)$member['mb_point'])
            $temp_point = (int)$member['mb_point'];

        $point_unit = (int)$setting['de_settle_point_unit'];
        $temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
    }
}

if (($i_temp_point > (int)$temp_point || $i_temp_point < 0) && $config['cf_use_point'])
    die("Error..");

if ($od_temp_point)
{
    if ($member['mb_point'] < $od_temp_point)
        alert('회원님의 포인트가 부족하여 포인트로 결제 할 수 없습니다.');
}

$i_price -= $i_temp_point;
$order_price = $tot_od_price - $od_temp_point;

// 결제캐시가 상이함
// 회원이면서 캐시결제사용이면
$temp_cash = 0;
$mb_cash = 0;
if ($is_member && $setting['de_cash_use'])
{
    $mb_cash = get_member_cash($member['mb_id']);
    $tot_od_price -= $i_temp_point;

    if($mb_cash > 0) {
        $temp_cash = (int)$mb_cash;

        if($temp_cash > (int)$tot_od_price)
            $temp_cash = (int)$tot_od_price;
    }
}

if (($i_temp_cash > (int)$temp_cash || $i_temp_cash < 0) && $setting['de_cash_use'])
    die("Error...");

if ($od_temp_cash)
{
    if ($mb_cash < $od_temp_cash)
        alert('회원님의 보유캐시가 부족하여 캐시로 결제 할 수 없습니다.');
}

$i_price -= $i_temp_cash;
$order_price -= $od_temp_cash;

if($order_price == 0) {
    $od_receipt_point = $i_temp_point;
    $od_receipt_cash  = $i_temp_cash;
    $od_status        = '입금';
    $od_receipt_time  = G5_TIME_YMDHIS;
    $od_settle_case   = '';
} else {
    $od_status = '주문';
    if ($od_settle_case == "무통장")
    {
        $od_receipt_point   = $i_temp_point;
        $od_receipt_cash    = $i_temp_cash;
        $od_receipt_price   = 0;
        $od_misu            = $i_price - $od_receipt_price;
        if($od_misu == 0) {
            $od_status      = '입금';
            $od_receipt_time = G5_TIME_YMDHIS;
        }
    }
    else if ($od_settle_case == "계좌이체")
    {
        switch($setting['de_pg_service']) {
            case 'lg':
                include G5_CONTENTS_PATH.'/lg/xpay_result.php';
                break;
            case 'inicis':
                include G5_MCONTENTS_PATH.'/inicis/pay_result.php';
                break;
            default:
                include G5_MCONTENTS_PATH.'/kcp/pp_ax_hub.php';
                $bank_name  = iconv("cp949", "utf-8", $bank_name);
                break;
        }

        $od_tno             = $tno;
        $od_receipt_price   = $amount;
        $od_receipt_point   = $i_temp_point;
        $od_receipt_cash    = $i_temp_cash;
        $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
        $od_bank_account    = $od_settle_case;
        $od_deposit_name    = $od_name;
        $od_bank_account    = $bank_name;
        $pg_price           = $amount;
        $od_misu            = $i_price - $od_receipt_price;
        if($od_misu == 0)
            $od_status      = '입금';
    }
    else if ($od_settle_case == "가상계좌")
    {
        switch($setting['de_pg_service']) {
            case 'lg':
                include G5_CONTENTS_PATH.'/lg/xpay_result.php';
                break;
            case 'inicis':
                include G5_MCONTENTS_PATH.'/inicis/pay_result.php';
                $od_app_no = $app_no;
                break;
            default:
                include G5_MCONTENTS_PATH.'/kcp/pp_ax_hub.php';
                $bankname   = iconv("cp949", "utf-8", $bankname);
                $depositor  = iconv("cp949", "utf-8", $depositor);
                break;
        }

        $od_receipt_point   = $i_temp_point;
        $od_receipt_cash    = $i_temp_cash;
        $od_tno             = $tno;
        $od_receipt_price   = 0;
        $od_bank_account    = $bankname.' '.$account;
        $od_deposit_name    = $depositor;
        $pg_price           = $amount;
        $od_misu            = $i_price - $od_receipt_price;
    }
    else if ($od_settle_case == "휴대폰")
    {
        switch($setting['de_pg_service']) {
            case 'lg':
                include G5_CONTENTS_PATH.'/lg/xpay_result.php';
                break;
            case 'inicis':
                include G5_MCONTENTS_PATH.'/inicis/pay_result.php';
                break;
            default:
                include G5_MCONTENTS_PATH.'/kcp/pp_ax_hub.php';
                break;
        }

        $od_tno             = $tno;
        $od_receipt_price   = $amount;
        $od_receipt_point   = $i_temp_point;
        $od_receipt_cash    = $i_temp_cash;
        $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
        $od_bank_account    = $commid.' '.$mobile_no;
        $pg_price           = $amount;
        $od_misu            = $i_price - $od_receipt_price;
        if($od_misu == 0)
            $od_status      = '입금';
    }
    else if ($od_settle_case == "신용카드")
    {
        switch($setting['de_pg_service']) {
            case 'lg':
                include G5_CONTENTS_PATH.'/lg/xpay_result.php';
                break;
            case 'inicis':
                include G5_MCONTENTS_PATH.'/inicis/pay_result.php';
                break;
            default:
                include G5_MCONTENTS_PATH.'/kcp/pp_ax_hub.php';
                $card_name  = iconv("cp949", "utf-8", $card_name);
                break;
        }

        $od_tno             = $tno;
        $od_app_no          = $app_no;
        $od_receipt_price   = $amount;
        $od_receipt_point   = $i_temp_point;
        $od_receipt_cash    = $i_temp_cash;
        $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
        $od_bank_account    = $card_name;
        $pg_price           = $amount;
        $od_misu            = $i_price - $od_receipt_price;
        if($od_misu == 0)
            $od_status      = '입금';
    }
    else
    {
        die("od_settle_case Error!!!");
    }

    // 주문금액과 결제금액이 일치하는지 체크
    if($tno) {
        if((int)$i_price !== (int)$pg_price) {
            $cancel_msg = '결제금액 불일치';
            switch($setting['de_pg_service']) {
                case 'lg':
                    include G5_CONTENTS_PATH.'/lg/xpay_cancel.php';
                    break;
                case 'inicis':
                    include G5_CONTENTS_PATH.'/inicis/inipay_cancel.php';
                    break;
                default:
                    include G5_CONTENTS_PATH.'/kcp/pp_ax_hub_cancel.php';
                    break;
            }

            die("Receipt Amount Error");
        }
    }
}

// 주문번호를 얻는다.
$od_id = get_session('ss_cm_order_id');

$od_pg = $setting['de_pg_service'];
$od_email = get_email_address($od_email);
$od_pwd = $member['mb_password'];

// 주문서에 입력
$sql = " insert {$g5['g5_contents_order_table']}
            set od_id             = '$od_id',
                mb_id             = '{$member['mb_id']}',
                od_pwd            = '$od_pwd',
                od_name           = '$od_name',
                od_email          = '$od_email',
                od_tel            = '$od_tel',
                od_hp             = '$od_hp',
                od_deposit_name   = '$od_deposit_name',
                od_memo           = '$od_memo',
                od_cart_count     = '$cart_count',
                od_cart_price     = '$tot_ct_price',
                od_cart_coupon    = '$tot_it_cp_price',
                od_coupon         = '$tot_od_cp_price',
                od_receipt_price  = '$od_receipt_price',
                od_receipt_cash   = '$od_receipt_cash',
                od_receipt_point  = '$od_receipt_point',
                od_bank_account   = '$od_bank_account',
                od_receipt_time   = '$od_receipt_time',
                od_misu           = '$od_misu',
                od_pg             = '$od_pg',
                od_tno            = '$od_tno',
                od_app_no         = '$od_app_no',
                od_status         = '$od_status',
                od_shop_memo      = '',
                od_time           = '".G5_TIME_YMDHIS."',
                od_ip             = '$REMOTE_ADDR',
                od_settle_case    = '$od_settle_case'
                ";
$result = sql_query($sql, false);

// 주문정보 입력 오류시 결제 취소
if(!$result) {
    if($tno) {
        $cancel_msg = '주문정보 입력 오류';
        switch($setting['de_pg_service']) {
            case 'lg':
                include G5_CONTENTS_PATH.'/lg/xpay_cancel.php';
                break;
            case 'inicis':
                include G5_CONTENTS_PATH.'/inicis/inipay_cancel.php';
                break;
            default:
                include G5_CONTENTS_PATH.'/kcp/pp_ax_hub_cancel.php';
                break;
        }
    }

    // 관리자에게 오류 알림 메일발송
    $error = 'order';
    include G5_CONTENTS_PATH.'/ordererrormail.php';

    die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>'.strtoupper($setting['de_pg_service']).'를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
}

// 장바구니 상태변경
// 신용카드로 주문하면서 신용카드 포인트 사용하지 않는다면 포인트 부여하지 않음
$cart_status = $od_status;
$sql_card_point = "";
if ($od_receipt_price > 0 && !$setting['de_card_point']) {
    $sql_card_point = " , ct_point = '0' ";
}
$sql = "update {$g5['g5_contents_cart_table']}
           set od_id = '$od_id',
               ct_status = '$cart_status'
               $sql_card_point
         where od_id = '$tmp_cart_id'
           and ct_select = '1' ";
$result = sql_query($sql, false);

// 주문정보 입력 오류시 결제 취소
if(!$result) {
    if($tno) {
        $cancel_msg = '주문상태 변경 오류';
        switch($setting['de_pg_service']) {
            case 'lg':
                include G5_CONTENTS_PATH.'/lg/xpay_cancel.php';
                break;
            case 'inicis':
                include G5_CONTENTS_PATH.'/inicis/inipay_cancel.php';
                break;
            default:
                include G5_CONTENTS_PATH.'/kcp/pp_ax_hub_cancel.php';
                break;
        }
    }

    // 관리자에게 오류 알림 메일발송
    $error = 'status';
    include G5_CONTENTS_PATH.'/ordererrormail.php';

    // 주문삭제
    sql_query(" delete from {$g5['g5_contents_order_table']} where od_id = '$od_id' ");

    die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>'.strtoupper($setting['de_pg_service']).'를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
}

// 입금인 경우에 상품구입 합계수량을 상품테이블에 저장
if($cart_status == '입금')
    add_item_sale_qty($od_id);

// 회원이면서 포인트를 사용했다면 테이블에 사용을 추가
if ($is_member && $od_receipt_point)
    insert_point($member['mb_id'], (-1) * $od_receipt_point, "컨텐츠몰 주문번호 $od_id 결제");

// 회원이면서 캐시를 사용했다면 테이블에 사용을 추가
if ($is_member && $od_receipt_cash)
    insert_cash($member['mb_id'], $od_id, (-1) * $od_receipt_cash, "컨텐츠몰 주문번호 $od_id 결제");

$od_memo = nl2br(htmlspecialchars2(stripslashes($od_memo))) . "&nbsp;";


// 쿠폰사용내역기록
if($is_member) {
    $it_cp_cnt = count($_POST['cp_id']);
    for($i=0; $i<$it_cp_cnt; $i++) {
        $cid = $_POST['cp_id'][$i];
        $cp_it_id = $_POST['it_id'][$i];
        $cp_prc = (int)$arr_it_cp_prc[$cp_it_id];

        if(trim($cid)) {
            $sql = " insert into {$g5['g5_contents_coupon_log_table']}
                        set cp_id       = '$cid',
                            mb_id       = '{$member['mb_id']}',
                            od_id       = '$od_id',
                            cp_price    = '$cp_prc',
                            cl_datetime = '".G5_TIME_YMDHIS."' ";
            sql_query($sql);
        }

        // 쿠폰사용금액 cart에 기록
        $cp_prc = (int)$arr_it_cp_prc[$cp_it_id];
        $sql = " update {$g5['g5_contents_cart_table']}
                    set cp_price = '$cp_prc'
                    where od_id = '$od_id'
                      and it_id = '$cp_it_id'
                      and ct_select = '1'
                    order by ct_id asc
                    limit 1 ";
        sql_query($sql);
    }

    if($_POST['od_cp_id']) {
        $sql = " insert into {$g5['g5_contents_coupon_log_table']}
                    set cp_id       = '{$_POST['od_cp_id']}',
                        mb_id       = '{$member['mb_id']}',
                        od_id       = '$od_id',
                        cp_price    = '$tot_od_cp_price',
                        cl_datetime = '".G5_TIME_YMDHIS."' ";
        sql_query($sql);
    }
}


include_once(G5_CONTENTS_PATH.'/ordermail1.inc.php');
include_once(G5_CONTENTS_PATH.'/ordermail2.inc.php');

// SMS BEGIN --------------------------------------------------------
// 주문고객과 쇼핑몰관리자에게 SMS 전송
if($config['cf_sms_use'] && ($setting['de_sms_use2'] || $setting['de_sms_use3'])) {
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
        $sms_contents = array($setting['de_sms_cont2'], $setting['de_sms_cont3']);
        $recv_numbers = array($od_hp, $setting['de_sms_hp']);
        $send_numbers = array($setting['de_admin_company_tel'], $od_hp);

        include_once(G5_LIB_PATH.'/icode.sms.lib.php');

        $SMS = new SMS; // SMS 연결
        $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
        $sms_count = 0;

        for($s=0; $s<count($sms_contents); $s++) {
            $sms_content = $sms_contents[$s];
            $recv_number = preg_replace("/[^0-9]/", "", $recv_numbers[$s]);
            $send_number = preg_replace("/[^0-9]/", "", $send_numbers[$s]);

            $sms_content = str_replace("{이름}", $od_name, $sms_content);
            $sms_content = str_replace("{보낸분}", $od_name, $sms_content);
            $sms_content = str_replace("{받는분}", $od_b_name, $sms_content);
            $sms_content = str_replace("{주문번호}", $od_id, $sms_content);
            $sms_content = str_replace("{주문금액}", number_format($tot_ct_price), $sms_content);
            $sms_content = str_replace("{회원아이디}", $member['mb_id'], $sms_content);
            $sms_content = str_replace("{회사명}", $setting['de_admin_company_name'], $sms_content);

            $idx = 'de_sms_use'.($s + 2);

            if($setting[$idx] && $recv_number) {
                $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_content)), "");
                $sms_count++;
            }
        }

        // 무통장 입금 때 고객에게 계좌정보 보냄
        if($od_settle_case == '무통장' && $setting['de_sms_use2'] && $od_misu > 0) {
            $sms_content = $od_name."님의 입금계좌입니다.\n금액:".number_format($od_misu)."원\n계좌:".$od_bank_account."\n".$setting['de_admin_company_name'];

            $recv_number = preg_replace("/[^0-9]/", "", $od_hp);
            $send_number = preg_replace("/[^0-9]/", "", $setting['de_admin_company_tel']);
            $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", $sms_content), "");
            $sms_count++;
        }

        if($sms_count > 0)
            $SMS->Send();
    }
}
// SMS END   --------------------------------------------------------


// 주문 정보 임시 데이터 삭제
$sql = " delete from {$g5['g5_contents_order_data_table']} where od_id = '$od_id' and dt_pg = '$od_pg' ";
sql_query($sql);

// 주문번호제거
set_session('ss_cm_order_id', '');

// 기존자료 세션에서 제거
if (get_session('ss_cm_direct'))
    set_session('ss_cm_cart_direct', '');

goto_url(G5_CONTENTS_URL.'/orderinquiryview.php?od_id='.$od_id);
?>
