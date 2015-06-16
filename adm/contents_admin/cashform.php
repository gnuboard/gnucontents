<?php
$sub_menu = '600250';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = "캐시충전 내역 수정";
include_once(G5_ADMIN_PATH.'/admin.head.php');

//------------------------------------------------------------------------------
// 주문서 정보
//------------------------------------------------------------------------------
$sql = " select * from {$g5['g5_contents_cash_table']} where cs_id = '$cs_id' ";
$cs = sql_fetch($sql);
if (!$cs['cs_id']) {
    alert("해당 주문번호로 주문서가 존재하지 않습니다.");
}
//------------------------------------------------------------------------------


$pg_anchor = '<ul class="anchor">
<li><a href="#anc_scs_pay">주문결제 내역</a></li>
<li><a href="#anc_scs_chk">결제상세정보 확인</a></li>
<li><a href="#anc_scs_paymo">결제상세정보 수정</a></li>
<li><a href="#anc_scs_memo">상점메모</a></li>
<li><a href="#anc_scs_orderer">구매하신 분</a></li>
</ul>';

$html_receipt_chk = '<input type="checkbox" id="cs_receipt_chk" value="'.$cs['cs_misu'].'" onclick="chk_receipt_price()">
<label for="od_receipt_chk">결제금액 입력</label><br>';

$qstr1 = "cs_status=".urlencode($cs_status)."&amp;cs_settle_case=".urlencode($cs_settle_case)."&amp;cs_misu=$cs_misu&amp;cs_refund_price=$cs_refund_price&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sfl=$sfl&amp;stx=$stx&amp;save_stx=$stx";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

// LG 현금영수증 JS
if($cs['cs_pg'] == 'lg') {
    if($setting['de_card_test']) {
    echo '<script language="JavaScript" src="http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js"></script>'.PHP_EOL;
    } else {
        echo '<script language="JavaScript" src="http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js"></script>'.PHP_EOL;
    }
}
?>

<section id="anc_scs_pay">
    <h2 class="h2_frm">주문결제 내역</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_head01 tbl_wrap">
        <strong class="scs_nonpay">미수금 <?php echo cm_display_price($cs['cs_misu']); ?></strong>

        <table>
        <caption>주문결제 내역</caption>
        <thead>
        <tr>
            <th scope="col">주문번호</th>
            <th scope="col">회원아이디</th>
            <th scope="col">결제방법</th>
            <th scope="col">충전금액</th>
            <th scope="col">결제금액</th>
            <th scope="col">환불금액</th>
            <th scope="col">상태</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $cs['cs_id']; ?></td>
            <td class="td_numbig"><?php echo get_text($cs['mb_id']); ?></td>
            <td class="td_paybybig "><?php echo $cs['cs_settle_case']; ?></td>
            <td class="td_numbig td_numsum td_r"><?php echo cm_display_price($cs['cs_cash_price']); ?></td>
            <td class="td_numbig td_numincome td_r"><?php echo cm_display_price($cs['cs_receipt_price']); ?></td>
            <td class="td_numbig td_numcancel td_r"><?php echo cm_display_price($cs['cs_refund_price']); ?></td>
            <td class="td_numbig"><?php echo $cs['cs_status']; ?></td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<section class="">
    <h2 class="h2_frm">결제상세정보</h2>
    <?php echo $pg_anchor; ?>

    <form name="fcashform" action="./cashformupdate.php" method="post" onsubmit="return fcashform_submit(this);" autocomplete="off">
    <input type="hidden" name="cs_id" value="<?php echo $cs_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
    <input type="hidden" name="stx" value="<?php echo $stx; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="csh_status" value="<?php echo $cs_status; ?>">
    <input type="hidden" name="csh_settle_case" value="<?php echo $cs_settle_case; ?>">
    <input type="hidden" name="csh_misu" value="<?php echo $cs_misu; ?>">
    <input type="hidden" name="csh_refund_price" value="<?php echo $cs_refund_price; ?>">
    <input type="hidden" name="csh_fr_date" value="<?php echo $fr_date; ?>">
    <input type="hidden" name="csh_to_date" value="<?php echo $to_date; ?>">

    <div class="compare_wrap">

        <section id="anc_scs_chk" class="compare_left">
            <h3>결제상세정보 확인</h3>

            <div class="tbl_frm01">
                <table>
                <caption>결제상세정보</caption>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <?php if ($cs['cs_settle_case'] == '무통장' || $cs['cs_settle_case'] == '가상계좌' || $cs['cs_settle_case'] == '계좌이체') { ?>
                <?php if ($cs['cs_settle_case'] == '무통장' || $cs['cs_settle_case'] == '가상계좌') { ?>
                <tr>
                    <th scope="row">계좌번호</th>
                    <td><?php echo $cs['cs_bank_account']; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><?php echo $cs['cs_settle_case']; ?> 입금액</th>
                    <td><?php echo cm_display_price($cs['cs_receipt_price']); ?></td>
                </tr>
                <tr>
                    <th scope="row">입금자</th>
                    <td><?php echo $cs['cs_deposit_name']; ?></td>
                </tr>
                <tr>
                    <th scope="row">입금확인일시</th>
                    <td>
                        <?php if ($cs['cs_receipt_time'] == 0) { ?>입금 확인일시를 체크해 주세요.
                        <?php } else { ?><?php echo $cs['cs_receipt_time']; ?> (<?php echo get_yoil($cs['cs_receipt_time']); ?>)
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($cs['cs_settle_case'] == '휴대폰') { ?>
                <tr>
                    <th scope="row">휴대폰번호</th>
                    <td><?php echo $cs['cs_bank_account']; ?></td>
                    </tr>
                <tr>
                    <th scope="row"><?php echo $cs['cs_settle_case']; ?> 결제액</th>
                    <td><?php echo cm_display_price($cs['cs_receipt_price']); ?></td>
                </tr>
                <tr>
                    <th scope="row">결제 확인일시</th>
                    <td>
                        <?php if ($cs['cs_receipt_time'] == 0) { ?>결제 확인일시를 체크해 주세요.
                        <?php } else { ?><?php echo $cs['cs_receipt_time']; ?> (<?php echo get_yoil($cs['cs_receipt_time']); ?>)
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($cs['cs_settle_case'] == '신용카드') { ?>
                <tr>
                    <th scope="row" class="scs_sppay">신용카드 결제금액</th>
                    <td>
                        <?php if ($cs['cs_receipt_time'] == "0000-00-00 00:00:00") {?>0원
                        <?php } else { ?><?php echo cm_display_price($cs['cs_receipt_price']); ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="scs_sppay">카드 승인일시</th>
                    <td>
                        <?php if ($cs['cs_receipt_time'] == "0000-00-00 00:00:00") {?>신용카드 결제 일시 정보가 없습니다.
                        <?php } else { ?><?php echo substr($cs['cs_receipt_time'], 0, 20); ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($cs['cs_settle_case'] != '무통장') { ?>
                <tr>
                    <th scope="row">결제대행사 링크</th>
                    <td>
                        <?php
                        if ($cs['cs_settle_case'] != '무통장') {
                            switch($cs['cs_pg']) {
                                case 'lg':
                                    $pg_url  = 'http://pgweb.uplus.co.kr';
                                    $pg_test = 'LG유플러스';
                                    if ($setting['de_card_test']) {
                                        $pg_url = 'http://pgweb.uplus.co.kr/tmert';
                                        $pg_test .= ' 테스트 ';
                                    }
                                    break;
                                case 'inicis':
                                    $pg_url  = 'https://iniweb.inicis.com/';
                                    $pg_test = 'KG이니시스';
                                    break;
                                default:
                                    $pg_url  = 'http://admin8.kcp.co.kr';
                                    $pg_test = 'KCP';
                                    if ($setting['de_card_test']) {
                                        // 로그인 아이디 / 비번
                                        // 일반 : test1234 / test12345
                                        // 에스크로 : escrow / escrow913
                                        $pg_url = 'http://testadmin8.kcp.co.kr';
                                        $pg_test .= ' 테스트 ';
                                    }

                                }
                            echo "<a href=\"{$pg_url}\" target=\"_blank\">{$pg_test}바로가기</a><br>";
                        }
                        //------------------------------------------------------------------------------
                        ?>
                    </td>
                </tr>
                <?php } ?>

                <tr>
                    <th scope="row">결제취소/환불액</th>
                    <td><?php echo cm_display_price($cs['cs_refund_price']); ?></td>
                </tr>
                <?php
                if ($cs['cs_misu'] == 0 && $cs['cs_status'] == '입금') {
                    if ($cs['cs_receipt_price'] && ($cs['cs_settle_case'] == '무통장' || $cs['cs_settle_case'] == '가상계좌' || $cs['cs_settle_case'] == '계좌이체')) {
                ?>
                <tr>
                    <th scope="row">현금영수증</th>
                    <td>
                    <?php
                    if ($cs['cs_taxsave']) {
                        if($cs['cs_pg'] == 'lg') {
                            require G5_CONTENTS_PATH.'/settle_lg.inc.php';

                            switch($cs['cs_settle_case']) {
                                case '계좌이체':
                                    $trade_type = 'BANK';
                                    break;
                                case '가상계좌':
                                    $trade_type = 'CAS';
                                    break;
                                default:
                                    $trade_type = 'CR';
                                    break;
                            }
                            $cash_receipt_script = 'javascript:showCashReceipts(\''.$LGD_MID.'\',\''.$cs['cs_id'].'\',\''.$cs['cs_casseqno'].'\',\''.$trade_type.'\',\''.$CST_PLATFORM.'\');';
                        } else if($cs['cs_pg'] == 'inicis') {
                            $cash = unserialize($cs['tx_taxsave_info']);
                            $cash_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid='.$cash['TID'].'&clpaymethod=22\',\'showreceipt\',\'width=380,height=540,scrollbars=no,resizable=no\');';
                        } else {
                            require G5_CONTENTS_PATH.'/settle_kcp.inc.php';

                            $cash = unserialize($cs['cs_taxsave_info']);
                            $cash_receipt_script = 'window.open(\''.G5_CM_CASH_RECEIPT_URL.$contetns['de_kcp_mid'].'&orderid='.$cs['cs_id'].'&bill_yn=Y&authno='.$cash['receipt_no'].'\', \'taxsave_receipt\', \'width=360,height=647,scrollbars=0,menus=0\');';
                        }
                    ?>
                        <a href="javascript:;" onclick="<?php echo $cash_receipt_script; ?>">현금영수증 확인</a>
                    <?php } else { ?>
                        <a href="javascript:;" onclick="window.open('<?php echo G5_CONTENTS_URL; ?>/taxsave.php?tx=cash&od_id=<?php echo $cs['cs_id']; ?>', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');">현금영수증 발급</a>
                    <?php } ?>
                    </td>
                </tr>
                <?php
                    }
                }
                ?>
                </tbody>
                </table>
            </div>
        </section>

        <section id="anc_scs_paymo" class="compare_right">
            <h3>결제상세정보 수정</h3>

            <div class="tbl_frm01">
                <table>
                <caption>결제상세정보 수정</caption>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <?php if ($cs['cs_settle_case'] == '무통장' || $cs['cs_settle_case'] == '가상계좌' || $cs['cs_settle_case'] == '계좌이체') { ########## 시작?>
                <?php
                if ($cs['cs_settle_case'] == '무통장')
                {
                    // 은행계좌를 배열로 만든후
                    $str = explode("\n", $setting['de_bank_account']);
                    $bank_account .= '<select name="cs_bank_account" id="cs_bank_account">'.PHP_EOL;
                    $bank_account .= '<option value="">선택하십시오</option>'.PHP_EOL;
                    for ($i=0; $i<count($str); $i++) {
                        $str[$i] = str_replace("\r", "", $str[$i]);
                        $bank_account .= '<option value="'.$str[$i].'" '.get_selected($cs['cs_bank_account'], $str[$i]).'>'.$str[$i].'</option>'.PHP_EOL;
                    }
                    $bank_account .= '</select> ';
                }
                else if ($cs['cs_settle_case'] == '가상계좌')
                    $bank_account = $cs['cs_bank_account'].'<input type="hidden" name="cs_bank_account" value="'.$cs['cs_bank_account'].'">';
                else if ($cs['cs_settle_case'] == '계좌이체')
                    $bank_account = $cs['cs_settle_case'];
                ?>

                <?php if ($cs['cs_settle_case'] == '무통장' || $cs['cs_settle_case'] == '가상계좌') { ?>
                <tr>
                    <th scope="row"><label for="cs_bank_account">계좌번호</label></th>
                    <td><?php echo $bank_account; ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <th scope="row"><label for="cs_receipt_price"><?php echo $cs['cs_settle_case']; ?> 입금액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="cs_receipt_price" value="<?php echo $cs['cs_receipt_price']; ?>" id="cs_receipt_price" class="frm_input"> 원
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cs_deposit_name">입금자명</label></th>
                    <td>
                        <?php if ($config['cf_sms_use'] && $setting['de_sms_use4']) { ?>
                        <input type="checkbox" name="cs_sms_ipgum_check" id="cs_sms_ipgum_check">
                        <label for="cs_sms_ipgum_check">SMS 입금 문자전송</label>
                        <br>
                        <?php } ?>
                        <input type="text" name="cs_deposit_name" value="<?php echo $cs['cs_deposit_name']; ?>" id="cs_deposit_name" class="frm_input">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cs_receipt_time">입금 확인일시</label></th>
                    <td>
                        <input type="checkbox" name="cs_bank_chk" id="cs_bank_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.cs_receipt_time.value=this.form.cs_bank_chk.value; else this.form.cs_receipt_time.value = this.form.cs_receipt_time.defaultValue;">
                        <label for="cs_bank_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="cs_receipt_time" value="<?php echo cm_is_null_time($cs['cs_receipt_time']) ? "" : $cs['cs_receipt_time']; ?>" id="cs_receipt_time" class="frm_input" maxlength="19">
                    </td>
                </tr>
                <?php } ?>

                <?php if ($cs['cs_settle_case'] == '휴대폰') { ?>
                <tr>
                    <th scope="row">휴대폰번호</th>
                    <td><?php echo $cs['cs_bank_account']; ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="cs_receipt_price"><?php echo $cs['cs_settle_case']; ?> 결제액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="cs_receipt_price" value="<?php echo $cs['cs_receipt_price']; ?>" id="cs_receipt_price" class="frm_input"> 원
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cs_receipt_time">휴대폰 결제일시</label></th>
                    <td>
                        <input type="checkbox" name="cs_hp_chk" id="cs_hp_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.cs_receipt_time.value=this.form.cs_hp_chk.value; else this.form.cs_receipt_time.value = this.form.cs_receipt_time.defaultValue;">
                        <label for="cs_hp_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="cs_receipt_time" value="<?php echo cm_is_null_time($cs['cs_receipt_time']) ? "" : $cs['cs_receipt_time']; ?>" id="cs_receipt_time" class="frm_input" size="19" maxlength="19">
                    </td>
                </tr>
                <?php } ?>

                <?php if ($cs['cs_settle_case'] == '신용카드') { ?>
                <tr>
                    <th scope="row" class="scs_sppay"><label for="cs_receipt_price">신용카드 결제금액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="cs_receipt_price" id="cs_receipt_price" value="<?php echo $cs['cs_receipt_price']; ?>" class="frm_input" size="10"> 원
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="scs_sppay"><label for="od_receipt_time">카드 승인일시</label></th>
                    <td>
                        <input type="checkbox" name="cs_card_chk" id="cs_card_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.cs_receipt_time.value=this.form.cs_card_chk.value; else this.form.cs_receipt_time.value = this.form.cs_receipt_time.defaultValue;">
                        <label for="cs_card_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="cs_receipt_time" value="<?php echo cm_is_null_time($cs['cs_receipt_time']) ? "" : $cs['cs_receipt_time']; ?>" id="cs_receipt_time" class="frm_input" size="19" maxlength="19">
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="cs_status">상태</label></th>
                    <td>
                        <select name="cs_status" id="cs_status">
                            <option value="접수"<?php echo get_selected('접수', $cs['cs_status']); ?>>접수</option>
                            <option value="입금"<?php echo get_selected('입금', $cs['cs_status']); ?>>입금</option>
                            <option value="취소"<?php echo get_selected('취소', $cs['cs_status']); ?>>취소</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="cs_refund_price">결제취소/환불 금액</label></th>
                    <td>
                        <input type="text" name="cs_refund_price" value="<?php echo $cs['cs_refund_price']; ?>" class="frm_input" size="10"> 원
                    </td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>

    </div>

    <section id="anc_scs_memo">
        <h2 class="h2_frm">상점메모</h2>
        <?php echo $pg_anchor; ?>
        <div class="local_desc02 local_desc">
            <p>
                현재 열람 중인 캐시충전에 대한 내용을 메모하는곳입니다.
            </p>
        </div>

        <div class="tbl_wrap">
            <label for="cs_shop_memo" class="sound_only">상점메모</label>
            <textarea name="cs_shop_memo" id="cs_shop_memo" rows="8"><?php echo stripslashes($cs['cs_shop_memo']); ?></textarea>
        </div>
    </section>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="내역 수정" class="btn_submit">
        <a href="./cashlist.php?<?php echo $qstr; ?>">목록</a>
    </div>
    </form>
</section>

<section>
    <h2 class="h2_frm">구매자 정보</h2>
    <?php echo $pg_anchor; ?>

    <div class="compare_wrap">

        <section id="anc_scs_orderer" class="compare_left">
            <h3>구매하신 분</h3>

            <div class="tbl_frm01">
                <table>
                <caption>구매자 정보</caption>
                <colgroup>
                    <col class="grid_4">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><span class="sound_only">구매하신 분 </span>이름</th>
                    <td><?php echo get_text($cs['cs_name']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">구매하신 분 </span>핸드폰</th>
                    <td><?php echo get_text($cs['cs_hp']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">구매하신 분 </span>E-mail</th>
                    <td><?php echo get_text($cs['cs_email']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">구매하신 분 </span>IP Address</th>
                    <td><?php echo $cs['cs_ip']; ?></td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>
    </div>
</section>

<script>
function fcashform_submit(f)
{
    <?php if($cs['cs_status'] != '취소') { ?>
    var sel = f.cs_status;
    var status = sel.options[sel.selectedIndex].value;

    if(status == "취소") {
        if(!confirm("캐시충전 내역을 취소하시겠습니까?"))
            return false;
    }
    <?php } ?>

    return true;
}
// 결제금액 수동 설정
function chk_receipt_price()
{
    var chk = document.getElementById("cs_receipt_chk");
    var price = document.getElementById("cs_receipt_price");
    price.value = chk.checked ? (parseInt(chk.value) + parseInt(price.defaultValue)) : price.defaultValue;
}
</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>