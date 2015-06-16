<?php
include_once('./_common.php');
include_once(G5_LIB_PATH.'/mailer.lib.php');

//print_r2($_POST); exit;

if($is_guest)
    alert('회원 로그인 후 이용해 주십시오.');

if(!$setting['de_cash_charge_use']|| !$setting['de_cash_charge_price'])
    alert('캐시 충전이 불가능합니다. 관리자에게 문의해 주십시오.', G5_CONTENTS_URL);

if($cs_settle_case != '무통장' && $setting['de_pg_service'] == 'lg' && !$_POST['LGD_PAYKEY'])
    alert('결제등록 요청 후 주문해 주십시오.');

$i_price = (int)$_POST['cs_temp_price'];
$i_cash  = (int)$_POST['cs_temp_cash'];

// 충전금액 체크
$cash = explode('|', $setting['de_cash_charge_price']);
$cash_count = count($cash);
if(!$cash_count)
    alert('캐시충전 목록이 없습니다. 관리자에게 문의해 주십시오.', G5_CONTENTS_URL);

$ss_cash = get_session('ss_cm_cash_charge_price');
if($ss_cash != $setting['de_cash_charge_price'])
    alert('캐시충전 목록이 변경됐습니다. 캐시충전을 다시 시도해 주십시오.');

for($i=0; $i<$cash_count; $i++) {
    $info = explode(':', $cash[$i]);
    $price = (int)$info[0];
    $charge = (int)$info[1];

    if($price == $i_price) {
        $cs_cash_price = $charge;
        break;
    }
}

if($i_cash !== $cs_cash_price)
    die('Charge Price Error.');

$cs_status = '접수';
if ($cs_settle_case == "무통장")
{
    $cs_receipt_price   = 0;
    $cs_misu            = $i_price - $cs_receipt_price;
    if($cs_misu == 0) {
        $cs_status      = '입금';
        $cs_receipt_time = G5_TIME_YMDHIS;
    }
}
else if ($cs_settle_case == "계좌이체")
{
    switch($setting['de_pg_service']) {
        case 'lg':
            include G5_CONTENTS_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            include G5_CONTENTS_PATH.'/inicis/inipay_result.php';
            break;
        default:
            include G5_CONTENTS_PATH.'/kcp/pp_ax_hub.php';
            $bank_name  = iconv("cp949", "utf-8", $bank_name);
            break;
    }

    $cs_tno             = $tno;
    $cs_receipt_price   = $amount;
    $cs_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $cs_deposit_name    = $pp_name;
    $cs_bank_account    = $bank_name;
    $pg_price           = $amount;
    $cs_misu            = $i_price - $cs_receipt_price;
    if($cs_misu == 0)
        $cs_status = '입금';
}
else if ($cs_settle_case == "가상계좌")
{
    switch($setting['de_pg_service']) {
        case 'lg':
            include G5_CONTENTS_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            include G5_CONTENTS_PATH.'/inicis/inipay_result.php';
            $cs_app_no  = $app_no;
            break;
        default:
            include G5_CONTENTS_PATH.'/kcp/pp_ax_hub.php';
            $bankname   = iconv("cp949", "utf-8", $bankname);
            $depositor  = iconv("cp949", "utf-8", $depositor);
            break;
    }

    $cs_tno             = $tno;
    $cs_receipt_price   = 0;
    $cs_bank_account    = $bankname.' '.$account;
    $cs_deposit_name    = $depositor;
    $pg_price           = $amount;
    $cs_misu            = $i_price - $cs_receipt_price;
}
else if ($cs_settle_case == "휴대폰")
{
    switch($setting['de_pg_service']) {
        case 'lg':
            include G5_CONTENTS_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            include G5_CONTENTS_PATH.'/inicis/inipay_result.php';
            break;
        default:
            include G5_CONTENTS_PATH.'/kcp/pp_ax_hub.php';
            break;
    }

    $cs_tno             = $tno;
    $cs_receipt_price   = $amount;
    $cs_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $cs_bank_account    = $commid.' '.$mobile_no;
    $pg_price           = $amount;
    $cs_misu            = $i_price - $cs_receipt_price;
    if($cs_misu == 0)
        $cs_status = '입금';
}
else if ($cs_settle_case == "신용카드")
{
    switch($setting['de_pg_service']) {
        case 'lg':
            include G5_CONTENTS_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            include G5_CONTENTS_PATH.'/inicis/inipay_result.php';
            break;
        default:
            include G5_CONTENTS_PATH.'/kcp/pp_ax_hub.php';
            $card_name  = iconv("cp949", "utf-8", $card_name);
            break;
    }

    $cs_tno             = $tno;
    $cs_app_no          = $app_no;
    $cs_receipt_price   = $amount;
    $cs_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $cs_bank_account    = $card_name;
    $pg_price           = $amount;
    $cs_misu            = $i_price - $cs_receipt_price;
    if($cs_misu == 0)
        $cs_status = '입금';
}
else
{
    die("cs_settle_case Error!!!");
}

// 주문금액과 결제금액이 일치하는지 체크
if($tno && (int)$i_price !== (int)$pg_price) {
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

    die("Receipt Price Error");
}

// 정보 입력
$cs_id    = get_session('ss_cm_cash_charge_id');
$cs_pg    = $setting['de_pg_service'];
$cs_email = get_email_address($cs_email);

$sql = " insert {$g5['g5_contents_cash_table']}
            set cs_id             = '$cs_id',
                mb_id             = '{$member['mb_id']}',
                cs_name           = '$cs_name',
                cs_email          = '$cs_email',
                cs_hp             = '$cs_hp',
                cs_price          = '$i_price',
                cs_cash_price     = '$cs_cash_price',
                cs_receipt_price  = '$cs_receipt_price',
                cs_bank_account   = '$cs_bank_account',
                cs_receipt_time   = '$cs_receipt_time',
                cs_deposit_name   = '$cs_deposit_name',
                cs_status         = '$cs_status',
                cs_misu           = '$cs_misu',
                cs_mobile         = '0',
                cs_pg             = '$cs_pg',
                cs_tno            = '$cs_tno',
                cs_app_no         = '$cs_app_no',
                cs_time           = '".G5_TIME_YMDHIS."',
                cs_ip             = '$REMOTE_ADDR',
                cs_settle_case    = '$cs_settle_case'
                ";
$result = sql_query($sql, false);

// 결제정보 입력 오류시 결제 취소
if(!$result) {
    $cancel_msg = '결제정보 입력 오류';
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

    die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['PHP_SELF']}");
}

// 히스토리 테이블에 기록
if($cs_misu == 0 && $cs_status = '입금') {
    $ch_memo = $cs_settle_case.'('.$cs_id.') 충전';
    insert_cash($member['mb_id'], $cs_id, $cs_cash_price, $ch_memo);
}

// cashresult 에서 사용하기 위해 session에 넣고
$uid = md5($cs_id.G5_TIME_YMDHIS.$REMOTE_ADDR);
set_session('ss_cm_cashresult_uid', $uid);

// 개인결제번호제거
set_session('ss_cm_cash_charge_id', '');

goto_url(G5_CONTENTS_URL.'/cashresult.php?cs_id='.$cs_id.'&amp;uid='.$uid);
?>

<html>
    <head>
        <title>개인결제정보 기록</title>
        <script>
            // 결제 중 새로고침 방지 샘플 스크립트 (중복결제 방지)
            function noRefresh()
            {
                /* CTRL + N키 막음. */
                if ((event.keyCode == 78) && (event.ctrlKey == true))
                {
                    event.keyCode = 0;
                    return false;
                }
                /* F5 번키 막음. */
                if(event.keyCode == 116)
                {
                    event.keyCode = 0;
                    return false;
                }
            }

            document.onkeydown = noRefresh ;
        </script>
    </head>
</html>