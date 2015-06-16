<?php
include_once('./_common.php');
include_once(G5_CONTENTS_PATH.'/settle_inicis.inc.php');

/* INIreceipt.php
 *
 * 현금결제(실시간 은행계좌이체, 무통장입금)에 대한 현금결제 영수증 발행 요청한다.
 *
 *
 * http://www.inicis.com
 * http://support.inicis.com
 * Copyright (C) 2006 Inicis, Co. All rights reserved.
 */

if($tx == 'cash') {
    $sql = " select * from {$g5['g5_contents_cash_table']} where cs_id = '{$_POST['od_id']}' ";
    $row = sql_fetch($sql);

    if(!$row)
        alert_close('캐시충전 내역이 존재하지 않습니다.');

    if($row['cs_taxsave'])
        alert_close('이미 등록된 현금영수증 입니다.');

    $buyername = $row['cs_name'];
    $goodname  = $row['cs_cash_price'].'원 캐시충전';
    $amt_tot   = (int)$row['cs_receipt_price'];
    $amt_sup   = (int)round(($amt_tot * 10) / 11);
    $amt_svc   = 0;
    $amt_tax   = (int)($amt_tot - $amt_sup);
} else {
    $sql = " select * from {$g5['g5_contents_order_table']} where od_id = '{$_POST['od_id']}' ";
    $row = sql_fetch($sql);

    if(!$row)
        alert_close('주문서가 존재하지 않습니다.');

    if($row['od_cash'])
        alert_close('이미 등록된 현금영수증 입니다.');

    $buyername = $row['od_name'];
    $goods     = cm_get_goods($row['od_id']);
    $goodname  = $goods['full_name'];
    $amt_tot   = (int)$row['od_receipt_price'];
    $amt_sup   = (int)round(($amt_tot * 10) / 11);
    $amt_svc   = 0;
    $amt_tax   = (int)($amt_tot - $amt_sup);
}


$reg_num  = $id_info;
$useopt   = $tr_code;
$currency = 'WON';

/*********************
 * 3. 발급 정보 설정 *
 *********************/
$inipay->SetField("type"          ,"receipt");    // 고정
$inipay->SetField("pgid"          ,"INIphpRECP"); // 고정
$inipay->SetField("paymethod"     ,"CASH");       // 고정 (요청분류)
$inipay->SetField("currency"      ,$currency);    // 화폐단위 (고정)
/**************************************************************************************************
* admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
* 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
* 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
* 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
**************************************************************************************************/
$inipay->SetField("admin"         ,$setting['de_inicis_admin_key']); // 키패스워드(상점아이디에 따라 변경)
$inipay->SetField("mid"           ,$setting['de_inicis_mid']);       // 상점아이디
$inipay->SetField("goodname"      ,iconv_euckr($goodname));          // 상품명
$inipay->SetField("cr_price"      ,$amt_tot);                        // 총 현금결제 금액
$inipay->SetField("sup_price"     ,$amt_sup);                        // 공급가액
$inipay->SetField("tax"           ,$amt_tax);                        // 부가세
$inipay->SetField("srvc_price"    ,$amt_svc);                        // 봉사료
$inipay->SetField("buyername"     ,iconv_euckr($buyername));         // 구매자 성명
$inipay->SetField("buyeremail"    ,$buyeremail);                     // 구매자 이메일 주소
$inipay->SetField("buyertel"      ,$buyertel);                       // 구매자 전화번호
$inipay->SetField("reg_num"       ,$reg_num);                        // 현금결제자 주민등록번호
$inipay->SetField("useopt"        ,$useopt);                         // 현금영수증 발행용도 ("1" - 소비자 소득공제용, "2" - 사업자 지출증빙용)
$inipay->SetField("companynumber" ,$companynumber);                  // 서브몰 사업자번호


/****************
 * 4. 발급 요청 *
 ****************/
$inipay->startAction();


/********************************************************************************
 * 5. 발급 결과                                                     *
 *                                                                  *
 * 결과코드 : $inipay->GetResult('ResultCode') ("00" 이면 발행 성공)          *
 * 승인번호 : $inipay->GetResult('ApplNum') (현금영수증 발행 승인번호)         *
 * 승인날짜 : $inipay->GetResult('ApplDate') (YYYYMMDD)                         *
 * 승인시각 : $inipay->GetResult('ApplTime') (HHMMSS)                           *
 * 거래번호 : $inipay->GetResult('TID')                             *
 * 총현금결제 금액 : $inipay->GetResult('CSHR_ApplPrice')                          *
 * 공급가액 : $inipay->GetResult('CSHR_SupplyPrice')                                *
 * 부가세 : $inipay->GetResult('CSHR_Tax')                             *
 * 봉사료 : $inipay->GetResult('CSHR_ServicePrice')                            *
 * 사용구분 : $inipay->GetResult('CSHR_Type')                                       *
 ********************************************************************************/


// DB 반영
if($inipay->GetResult('ResultCode') == '00') {
    $cash_no = $inipay->GetResult('ApplNum');

    $cash = array();
    $cash['TID']       = $inipay->GetResult('TID');
    $cash['ApplNum']   = $inipay->GetResult('ApplNum');
    $cash['ApplDate']  = $inipay->GetResult('ApplDate');
    $cash['ApplTime']  = $inipay->GetResult('ApplTime');
    $cash['CSHR_Type'] = $inipay->GetResult('CSHR_Type');
    $cash_info = serialize($cash);

    if($tx == 'cash') {
        $sql = " update {$g5['g5_contents_cash_table']}
                    set cs_taxsave = '1',
                        cs_taxsave_no = '$cash_no',
                        cs_taxsave_info = '$cash_info'
                  where cs_id = '$od_id' ";
    } else {
        $sql = " update {$g5['g5_contents_order_table']}
                    set od_cash = '1',
                        od_cash_no = '$cash_no',
                        od_cash_info = '$cash_info'
                  where od_id = '$od_id' ";
    }

    $result = sql_query($sql, false);

    if(!$result)
        include G5_CONTENTS_PATH.'/inicis/inipay_cancel.php';
}

$g5['title'] = '현금영수증 발급';
include_once(G5_PATH.'/head.sub.php');
?>

<script>
function showreceipt() // 현금 영수증 출력
{
    var showreceiptUrl = "https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid=<?php echo($inipay->GetResult('TID')); ?>" + "&clpaymethod=22";
    window.open(showreceiptUrl,"showreceipt","width=380,height=540, scrollbars=no,resizable=no");
}
</script>

<div id="lg_req_tx" class="new_win">
    <h1 id="win_title">현금영수증 - KG이니시스</h1>

    <div class="tbl_head01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">결과코드</th>
            <td><?php echo $inipay->GetResult('ResultCode'); ?></td>
        </tr>
        <tr>
            <th scope="row">결과 메세지</th>
            <td><?php echo iconv_utf8($inipay->GetResult('ResultMsg')); ?></td>
        </tr>
        <tr>
            <th scope="row">현금영수증 거래번호</th>
            <td><?php echo $inipay->GetResult('TID'); ?></td>
        </tr>
        <tr>
            <th scope="row">현금영수증 승인번호</th>
            <td><?php echo $inipay->GetResult('ApplNum'); ?></td>
        </tr>
        <tr>
            <th scope="row">승인시간</th>
            <td><?php echo preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6",$inipay->GetResult('ApplDate').$inipay->GetResult('ApplTime')); ?></td>
        </tr>
        <tr>
            <th scope="row">현금영수증 URL</th>
            <td>
                <button type="button" name="receiptView" class="btn_frmline" onClick="javascript:showreceipt();">영수증 확인</button>
                <p>영수증 확인은 실 등록의 경우에만 가능합니다.</p>
            </td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        </tbody>
        </table>
    </div>

</div>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>