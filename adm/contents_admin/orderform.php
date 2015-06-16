<?php
$sub_menu = '600200';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$g5['title'] = "주문 내역 수정";
include_once(G5_ADMIN_PATH.'/admin.head.php');

//------------------------------------------------------------------------------
// 설정 시간이 지난 주문서 없는 장바구니 자료 삭제
//------------------------------------------------------------------------------
$keep_term = $setting['de_cart_keep_term'];
if (!$keep_term) $keep_term = 15; // 기본값 15일
$beforetime = date('Y-m-d', ( G5_SERVER_TIME - (86400 * $keep_term) ) );
$sql = " delete from {$g5['g5_contents_cart_table']} where ct_status = '쇼핑' and substring(ct_time, 1, 10) < '$beforetime' ";
sql_query($sql);
//------------------------------------------------------------------------------


// 완료된 주문에 포인트를 적립한다.
cm_save_order_point("입금");


//------------------------------------------------------------------------------
// 주문서 정보
//------------------------------------------------------------------------------
$sql = " select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ";
$od = sql_fetch($sql);
if (!$od['od_id']) {
    alert("해당 주문번호로 주문서가 존재하지 않습니다.");
}

$od['mb_id'] = $od['mb_id'] ? $od['mb_id'] : "비회원";
//------------------------------------------------------------------------------


$pg_anchor = '<ul class="anchor">
<li><a href="#anc_sodr_list">주문상품 목록</a></li>
<li><a href="#anc_sodr_pay">주문결제 내역</a></li>
<li><a href="#anc_sodr_chk">결제상세정보 확인</a></li>
<li><a href="#anc_sodr_paymo">결제상세정보 수정</a></li>
<li><a href="#anc_sodr_memo">상점메모</a></li>
<li><a href="#anc_sodr_orderer">구매하신 분</a></li>
</ul>';

$html_receipt_chk = '<input type="checkbox" id="od_receipt_chk" value="'.$od['od_misu'].'" onclick="chk_receipt_price()">
<label for="od_receipt_chk">결제금액 입력</label><br>';

$qstr1 = "od_status=".urlencode($od_status)."&amp;od_settle_case=".urlencode($od_settle_case)."&amp;od_misu=$od_misu&amp;od_refund_price=$od_refund_price&amp;od_receipt_cash=$od_receipt_cash&amp;od_receipt_point=$od_receipt_point&amp;od_coupon=$od_coupon&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

// 상품목록
$sql = " select it_id,
                it_name,
                cp_price
           from {$g5['g5_contents_cart_table']}
          where od_id = '{$od['od_id']}'
          group by it_id
          order by ct_id ";
$result = sql_query($sql);

// LG 현금영수증 JS
if($od['od_pg'] == 'lg') {
    if($setting['de_card_test']) {
    echo '<script language="JavaScript" src="http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js"></script>'.PHP_EOL;
    } else {
        echo '<script language="JavaScript" src="http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js"></script>'.PHP_EOL;
    }
}
?>

<section id="anc_sodr_list">
    <h2 class="h2_frm">주문상품 목록</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>
            현재 주문상태 <strong><?php echo $od['od_status'] ?></strong>
            |
            주문일시 <strong><?php echo substr($od['od_time'],0,16); ?> (<?php echo get_yoil($od['od_time']); ?>)</strong>
            |
            주문총액 <strong><?php echo number_format($od['od_cart_price']); ?></strong>원
        </p>
        <?php if($od['od_mobile']) { ?>
        <p>모바일 컨텐츠몰의 주문입니다.</p>
        <?php } ?>
    </div>

    <form name="frmorderform" method="post" action="./orderformcartupdate.php" onsubmit="return form_submit(this);">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="mb_id" value="<?php echo $od['mb_id']; ?>">
    <input type="hidden" name="od_email" value="<?php echo $od['od_email']; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page;?>">
    <input type="hidden" name="odr_status" value="<?php echo $od_status; ?>">
    <input type="hidden" name="odr_settle_case" value="<?php echo $od_settle_case; ?>">
    <input type="hidden" name="odr_misu" value="<?php echo $od_misu; ?>">
    <input type="hidden" name="odr_refund_price" value="<?php echo $od_refund_price; ?>">
    <input type="hidden" name="odr_receipt_cash" value="<?php echo $od_receipt_cash; ?>">
    <input type="hidden" name="odr_receipt_point" value="<?php echo $od_receipt_point; ?>">
    <input type="hidden" name="odr_coupon" value="<?php echo $od_coupon; ?>">
    <input type="hidden" name="odr_fr_date" value="<?php echo $fr_date; ?>">
    <input type="hidden" name="odr_to_date" value="<?php echo $to_date; ?>">
    <input type="hidden" name="pg_cancel" value="0">

    <div class="tbl_head01 tbl_wrap">

        <table>
        <caption>구매 상품 목록</caption>
        <thead>
        <tr>
            <th scope="col">상품명</th>
            <th scope="col">
                <label for="sit_select_all" class="sound_only">구매 상품 전체</label>
                <input type="checkbox" id="sit_select_all">
            </th>
            <th scope="col">옵션항목</th>
            <th scope="col">상태</th>
            <th scope="col">수량</th>
            <th scope="col">판매가</th>
            <th scope="col">소계</th>
            <th scope="col">쿠폰</th>
            <th scope="col">다운로드</th>
            <th scope="col">포인트</th>
            <th scope="col">포인트반영</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $chk_cnt = 0;
        for($i=0; $row=sql_fetch_array($result); $i++) {
            // 상품이미지
            $image = cm_get_it_image($row['it_id'], 50, 50);

            // 상품의 옵션정보
            $sql = " select ct_id, it_id, ct_price, ct_point, ct_qty, ct_option, ct_status, cp_price, ct_point_use, io_price, ct_download
                        from {$g5['g5_contents_cart_table']}
                        where od_id = '{$od['od_id']}'
                          and it_id = '{$row['it_id']}'
                        order by ct_id asc ";
            $res = sql_query($sql);
            $rowspan = mysql_num_rows($res);

            for($k=0; $opt=sql_fetch_array($res); $k++) {
                $opt_price = $opt['ct_price'] + $opt['io_price'];

                // 소계
                $ct_price['stotal'] = $opt_price * $opt['ct_qty'];
                $ct_point['stotal'] = $opt['ct_point'] * $opt['ct_qty'];
            ?>
            <tr>
                <?php if($k == 0) { ?>
                <td rowspan="<?php echo $rowspan; ?>">
                    <a href="./itemform.php?w=u&amp;it_id=<?php echo $row['it_id']; ?>"><?php echo $image; ?> <?php echo stripslashes($row['it_name']); ?></a>
                </td>
                <td rowspan="<?php echo $rowspan; ?>" class="td_chk">
                    <label for="sit_sel_<?php echo $i; ?>" class="sound_only"><?php echo $row['it_name']; ?> 옵션 전체선택</label>
                    <input type="checkbox" id="sit_sel_<?php echo $i; ?>" name="it_sel[]">
                </td>
                <?php } ?>
                <td>
                    <label for="ct_opt_chk_<?php echo $chk_cnt; ?>" class="sound_only"><?php echo $opt['ct_option']; ?></label>
                    <input type="checkbox" name="ct_chk[<?php echo $chk_cnt; ?>]" id="ct_chk_<?php echo $chk_cnt; ?>" value="<?php echo $chk_cnt; ?>" class="sct_sel_<?php echo $i; ?>">
                    <input type="hidden" name="ct_id[<?php echo $chk_cnt; ?>]" value="<?php echo $opt['ct_id']; ?>">
                    <?php echo $opt['ct_option']; ?>
                </td>
                <td class="td_mngsmall"><?php echo $opt['ct_status']; ?></td>
                <td class="td_num td_c">
                    <label for="ct_qty_<?php echo $chk_cnt; ?>" class="sound_only"><?php echo $opt['ct_option']; ?> 수량</label>
                    <input type="text" name="ct_qty[<?php echo $chk_cnt; ?>]" id="ct_qty_<?php echo $chk_cnt; ?>" value="<?php echo $opt['ct_qty']; ?>" required class="frm_input required" size="5">
                </td>
                <td class="td_num td_r"><?php echo number_format($opt_price); ?></td>
                <td class="td_num td_r"><?php echo number_format($ct_price['stotal']); ?></td>
                <td class="td_num td_r"><?php echo number_format($opt['cp_price']); ?></td>
                <td class="td_num td_c"><?php echo number_format($opt['ct_download']); ?></td>
                <td class="td_num td_r"><?php echo number_format($ct_point['stotal']); ?></td>
                <td class="td_mngsmall"><?php echo cm_get_yn($opt['ct_point_use']); ?></td>
            </tr>
            <?php
                $chk_cnt++;
            }
            ?>
        <?php
        }
        ?>
        </tbody>
        </table>

    </div>

    <div class="btn_list02 btn_list">
        <p>
            <input type="hidden" name="chk_cnt" value="<?php echo $chk_cnt; ?>">
            <strong>주문 및 장바구니 상태 변경</strong>
            <input type="submit" name="ct_status" value="주문" onclick="document.pressed=this.value">
            <input type="submit" name="ct_status" value="입금" onclick="document.pressed=this.value">
            <input type="submit" name="ct_status" value="취소" onclick="document.pressed=this.value">
        </p>
    </div>

    <div class="local_desc01 local_desc">
        <p>개별적인(이곳에서의) 상태 변경은 모든 작업을 수동으로 처리합니다. 예를 들어 주문에서 입금으로 상태 변경시 입금액(결제금액)을 포함한 모든 정보는 수동 입력으로 처리하셔야 합니다.</p>
    </div>

    </form>

    <?php if ($od['od_mod_history']) { ?>
    <section id="sodr_qty_log">
        <h3>구매 수량변경 및 취소처리 내역</h3>
        <div>
            <?php echo conv_content($od['od_mod_history'], 0); ?>
        </div>
    </section>
    <?php } ?>

</section>

<section id="anc_sodr_pay">
    <h2 class="h2_frm">주문결제 내역</h2>
    <?php echo $pg_anchor; ?>

    <?php
    // 주문금액 = 상품구입금액
    $amount['order'] = $od['od_cart_price'];

    // 입금액 = 결제금액 + 캐시 + 포인트
    $amount['receipt'] = $od['od_receipt_price'] + $od['od_receipt_cash'] + $od['od_receipt_point'];

    // 쿠폰금액
    $amount['coupon'] = $od['od_cart_coupon'] + $od['od_coupon'];

    // 취소금액
    $amount['cancel'] = $od['od_cancel_price'];

    // 결제방법
    $s_receipt_way = $od['od_settle_case'];

    $s_plus = '';
    if ($od['od_receipt_cash'] > 0) {
        if($s_receipt_way)
            $s_plus = ' + ';

        $s_receipt_way .= $s_plus.'캐시';
    }

    if ($od['od_receipt_point'] > 0) {
        if($s_receipt_way)
            $s_plus = ' + ';

        $s_receipt_way .= $s_plus.'포인트';
    }
    ?>

    <div class="tbl_head01 tbl_wrap">
        <strong class="sodr_nonpay">미수금 <?php echo cm_display_price($od['od_misu']); ?></strong>

        <table>
        <caption>주문결제 내역</caption>
        <thead>
        <tr>
            <th scope="col">주문번호</th>
            <th scope="col">결제방법</th>
            <th scope="col">주문총액</th>
            <th scope="col">캐시결제</th>
            <th scope="col">포인트결제</th>
            <th scope="col">총결제액</th>
            <th scope="col">쿠폰</th>
            <th scope="col">주문취소</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo $od['od_id']; ?></td>
            <td class="td_paybybig"><?php echo $s_receipt_way; ?></td>
            <td class="td_numbig td_numsum"><?php echo cm_display_price($amount['order']); ?></td>
            <td class="td_numbig td_r"><?php echo cm_display_price($od['od_receipt_cash']); ?></td>
            <td class="td_numbig td_r"><?php echo cm_display_point($od['od_receipt_point']); ?></td>
            <td class="td_numbig td_numincome"><?php echo cm_display_price($amount['receipt']); ?></td>
            <td class="td_numbig td_numcoupon"><?php echo cm_display_price($amount['coupon']); ?></td>
            <td class="td_numbig td_numcancel td_r"><?php echo cm_display_price($amount['cancel']); ?></td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<section class="">
    <h2 class="h2_frm">결제상세정보</h2>
    <?php echo $pg_anchor; ?>

    <form name="frmorderreceiptform" action="./orderformreceiptupdate.php" method="post" autocomplete="off">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="od_name" value="<?php echo $od['od_name']; ?>">
    <input type="hidden" name="od_hp" value="<?php echo $od['od_hp']; ?>">
    <input type="hidden" name="od_tno" value="<?php echo $od['od_tno']; ?>">
    <input type="hidden" name="od_pg" value="<?php echo $od['od_pg']; ?>">
    <input type="hidden" name="odr_status" value="<?php echo $od_status; ?>">
    <input type="hidden" name="odr_settle_case" value="<?php echo $od_settle_case; ?>">
    <input type="hidden" name="odr_misu" value="<?php echo $od_misu; ?>">
    <input type="hidden" name="odr_refund_price" value="<?php echo $od_refund_price; ?>">
    <input type="hidden" name="odr_receipt_cash" value="<?php echo $od_receipt_cash; ?>">
    <input type="hidden" name="odr_receipt_point" value="<?php echo $od_receipt_point; ?>">
    <input type="hidden" name="odr_coupon" value="<?php echo $od_coupon; ?>">
    <input type="hidden" name="odr_fr_date" value="<?php echo $fr_date; ?>">
    <input type="hidden" name="odr_to_date" value="<?php echo $to_date; ?>">

    <div class="compare_wrap">

        <section id="anc_sodr_chk" class="compare_left">
            <h3>결제상세정보 확인</h3>

            <div class="tbl_frm01">
                <table>
                <caption>결제상세정보</caption>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <?php if ($od['od_settle_case'] == '무통장' || $od['od_settle_case'] == '가상계좌' || $od['od_settle_case'] == '계좌이체') { ?>
                <?php if ($od['od_settle_case'] == '무통장' || $od['od_settle_case'] == '가상계좌') { ?>
                <tr>
                    <th scope="row">계좌번호</th>
                    <td><?php echo $od['od_bank_account']; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><?php echo $od['od_settle_case']; ?> 입금액</th>
                    <td><?php echo cm_display_price($od['od_receipt_price']); ?></td>
                </tr>
                <tr>
                    <th scope="row">입금자</th>
                    <td><?php echo $od['od_deposit_name']; ?></td>
                </tr>
                <tr>
                    <th scope="row">입금확인일시</th>
                    <td>
                        <?php if ($od['od_receipt_time'] == 0) { ?>입금 확인일시를 체크해 주세요.
                        <?php } else { ?><?php echo $od['od_receipt_time']; ?> (<?php echo get_yoil($od['od_receipt_time']); ?>)
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($od['od_settle_case'] == '휴대폰') { ?>
                <tr>
                    <th scope="row">휴대폰번호</th>
                    <td><?php echo $od['od_bank_account']; ?></td>
                    </tr>
                <tr>
                    <th scope="row"><?php echo $od['od_settle_case']; ?> 결제액</th>
                    <td><?php echo cm_display_price($od['od_receipt_price']); ?></td>
                </tr>
                <tr>
                    <th scope="row">결제 확인일시</th>
                    <td>
                        <?php if ($od['od_receipt_time'] == 0) { ?>결제 확인일시를 체크해 주세요.
                        <?php } else { ?><?php echo $od['od_receipt_time']; ?> (<?php echo get_yoil($od['od_receipt_time']); ?>)
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($od['od_settle_case'] == '신용카드') { ?>
                <tr>
                    <th scope="row" class="sodr_sppay">신용카드 결제금액</th>
                    <td>
                        <?php if ($od['od_receipt_time'] == "0000-00-00 00:00:00") {?>0원
                        <?php } else { ?><?php echo cm_display_price($od['od_receipt_price']); ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="sodr_sppay">카드 승인일시</th>
                    <td>
                        <?php if ($od['od_receipt_time'] == "0000-00-00 00:00:00") {?>신용카드 결제 일시 정보가 없습니다.
                        <?php } else { ?><?php echo substr($od['od_receipt_time'], 0, 20); ?>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <?php if ($od['od_settle_case'] != '무통장') { ?>
                <tr>
                    <th scope="row">결제대행사 링크</th>
                    <td>
                        <?php
                        if ($od['od_settle_case'] != '무통장') {
                            switch($od['od_pg']) {
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
                    <th scope="row">구매금액할인</th>
                    <td><?php echo cm_display_price($od['od_coupon']); ?></td>
                </tr>
                <tr>
                    <th scope="row">캐시</th>
                    <td><?php echo cm_display_price($od['od_receipt_cash']); ?></td>
                </tr>
                <tr>
                    <th scope="row">포인트</th>
                    <td><?php echo cm_display_point($od['od_receipt_point']); ?></td>
                </tr>
                <tr>
                    <th scope="row">결제취소/환불액</th>
                    <td><?php echo cm_display_price($od['od_refund_price']); ?></td>
                </tr>
                <?php
                if ($od['od_misu'] == 0 && $od['od_receipt_price'] && ($od['od_settle_case'] == '무통장' || $od['od_settle_case'] == '가상계좌' || $od['od_settle_case'] == '계좌이체')) {
                ?>
                <tr>
                    <th scope="row">현금영수증</th>
                    <td>
                    <?php
                    if ($od['od_cash']) {
                        if($od['od_pg'] == 'lg') {
                            require G5_CONTENTS_PATH.'/settle_lg.inc.php';

                            switch($od['od_settle_case']) {
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
                            $cash_receipt_script = 'javascript:showCashReceipts(\''.$LGD_MID.'\',\''.$od['od_id'].'\',\''.$od['od_casseqno'].'\',\''.$trade_type.'\',\''.$CST_PLATFORM.'\');';
                        } else if($od['od_pg'] == 'inicis') {
                            $cash = unserialize($od['od_cash_info']);
                            $cash_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid='.$cash['TID'].'&clpaymethod=22\',\'showreceipt\',\'width=380,height=540,scrollbars=no,resizable=no\');';
                        } else {
                            require G5_CONTENTS_PATH.'/settle_kcp.inc.php';

                            $cash = unserialize($od['od_cash_info']);
                            $cash_receipt_script = 'window.open(\''.G5_CM_CASH_RECEIPT_URL.$contetns['de_kcp_mid'].'&orderid='.$od_id.'&bill_yn=Y&authno='.$cash['receipt_no'].'\', \'taxsave_receipt\', \'width=360,height=647,scrollbars=0,menus=0\');';
                        }
                    ?>
                        <a href="javascript:;" onclick="<?php echo $cash_receipt_script; ?>">현금영수증 확인</a>
                    <?php } else { ?>
                        <a href="javascript:;" onclick="window.open('<?php echo G5_CONTENTS_URL; ?>/taxsave.php?od_id=<?php echo $od_id; ?>', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');">현금영수증 발급</a>
                    <?php } ?>
                    </td>
                </tr>
                <?php
                }
                ?>
                </tbody>
                </table>
            </div>
        </section>

        <section id="anc_sodr_paymo" class="compare_right">
            <h3>결제상세정보 수정</h3>

            <div class="tbl_frm01">
                <table>
                <caption>결제상세정보 수정</caption>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <?php if ($od['od_settle_case'] == '무통장' || $od['od_settle_case'] == '가상계좌' || $od['od_settle_case'] == '계좌이체') { ########## 시작?>
                <?php
                if ($od['od_settle_case'] == '무통장')
                {
                    // 은행계좌를 배열로 만든후
                    $str = explode("\n", $setting['de_bank_account']);
                    $bank_account .= '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
                    $bank_account .= '<option value="">선택하십시오</option>'.PHP_EOL;
                    for ($i=0; $i<count($str); $i++) {
                        $str[$i] = str_replace("\r", "", $str[$i]);
                        $bank_account .= '<option value="'.$str[$i].'" '.get_selected($od['od_bank_account'], $str[$i]).'>'.$str[$i].'</option>'.PHP_EOL;
                    }
                    $bank_account .= '</select> ';
                }
                else if ($od['od_settle_case'] == '가상계좌')
                    $bank_account = $od['od_bank_account'].'<input type="hidden" name="od_bank_account" value="'.$od['od_bank_account'].'">';
                else if ($od['od_settle_case'] == '계좌이체')
                    $bank_account = $od['od_settle_case'];
                ?>

                <?php if ($od['od_settle_case'] == '무통장' || $od['od_settle_case'] == '가상계좌') { ?>
                <tr>
                    <th scope="row"><label for="od_bank_account">계좌번호</label></th>
                    <td><?php echo $bank_account; ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <th scope="row"><label for="od_receipt_price"><?php echo $od['od_settle_case']; ?> 입금액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="od_receipt_price" value="<?php echo $od['od_receipt_price']; ?>" id="od_receipt_price" class="frm_input"> 원
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_deposit_name">입금자명</label></th>
                    <td>
                        <?php if ($config['cf_sms_use'] && $setting['de_sms_use4']) { ?>
                        <input type="checkbox" name="od_sms_ipgum_check" id="od_sms_ipgum_check">
                        <label for="od_sms_ipgum_check">SMS 입금 문자전송</label>
                        <br>
                        <?php } ?>
                        <input type="text" name="od_deposit_name" value="<?php echo $od['od_deposit_name']; ?>" id="od_deposit_name" class="frm_input">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_receipt_time">입금 확인일시</label></th>
                    <td>
                        <input type="checkbox" name="od_bank_chk" id="od_bank_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.od_receipt_time.value=this.form.od_bank_chk.value; else this.form.od_receipt_time.value = this.form.od_receipt_time.defaultValue;">
                        <label for="od_bank_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="od_receipt_time" value="<?php echo cm_is_null_time($od['od_receipt_time']) ? "" : $od['od_receipt_time']; ?>" id="od_receipt_time" class="frm_input" maxlength="19">
                    </td>
                </tr>
                <?php } ?>

                <?php if ($od['od_settle_case'] == '휴대폰') { ?>
                <tr>
                    <th scope="row">휴대폰번호</th>
                    <td><?php echo $od['od_bank_account']; ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_receipt_price"><?php echo $od['od_settle_case']; ?> 결제액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="od_receipt_price" value="<?php echo $od['od_receipt_price']; ?>" id="od_receipt_price" class="frm_input"> 원
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="op_receipt_time">휴대폰 결제일시</label></th>
                    <td>
                        <input type="checkbox" name="od_hp_chk" id="od_hp_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.od_receipt_time.value=this.form.od_hp_chk.value; else this.form.od_receipt_time.value = this.form.od_receipt_time.defaultValue;">
                        <label for="od_hp_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="od_receipt_time" value="<?php echo cm_is_null_time($od['od_receipt_time']) ? "" : $od['od_receipt_time']; ?>" id="op_receipt_time" class="frm_input" size="19" maxlength="19">
                    </td>
                </tr>
                <?php } ?>

                <?php if ($od['od_settle_case'] == '신용카드') { ?>
                <tr>
                    <th scope="row" class="sodr_sppay"><label for="od_receipt_price">신용카드 결제금액</label></th>
                    <td>
                        <?php echo $html_receipt_chk; ?>
                        <input type="text" name="od_receipt_price" id="od_receipt_price" value="<?php echo $od['od_receipt_price']; ?>" class="frm_input" size="10"> 원
                    </td>
                </tr>
                <tr>
                    <th scope="row" class="sodr_sppay"><label for="od_receipt_time">카드 승인일시</label></th>
                    <td>
                        <input type="checkbox" name="od_card_chk" id="od_card_chk" value="<?php echo date("Y-m-d H:i:s", G5_SERVER_TIME); ?>" onclick="if (this.checked == true) this.form.od_receipt_time.value=this.form.od_card_chk.value; else this.form.od_receipt_time.value = this.form.od_receipt_time.defaultValue;">
                        <label for="od_card_chk">현재 시간으로 설정</label><br>
                        <input type="text" name="od_receipt_time" value="<?php echo cm_is_null_time($od['od_receipt_time']) ? "" : $od['od_receipt_time']; ?>" id="od_receipt_time" class="frm_input" size="19" maxlength="19">
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row"><label for="od_receipt_cash">캐시 결제액</label></th>
                    <td><input type="text" name="od_receipt_cash" value="<?php echo $od['od_receipt_cash']; ?>" id="od_receipt_cash" class="frm_input" size="10"> 원</td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_receipt_point">포인트 결제액</label></th>
                    <td><input type="text" name="od_receipt_point" value="<?php echo $od['od_receipt_point']; ?>" id="od_receipt_point" class="frm_input" size="10"> 점</td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_refund_price">결제취소/환불 금액</label></th>
                    <td>
                        <input type="text" name="od_refund_price" value="<?php echo $od['od_refund_price']; ?>" class="frm_input" size="10"> 원
                    </td>
                </tr>
                <?php if ($config['cf_email_use']) { ?>
                <tr>
                    <th scope="row"><label for="od_send_mail">메일발송</label></th>
                    <td>
                        <?php echo help("주문자님께 입금내역을 메일로 발송합니다.\n메일발송시 상점메모에 기록됩니다."); ?>
                        <input type="checkbox" name="od_send_mail" value="1" id="od_send_mail"> 메일발송
                    </td>
                </tr>
                <?php } ?>

                </tbody>
                </table>
            </div>
        </section>

    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="결제내역 수정" class="btn_submit">
        <?php if($od['od_misu'] < 0 && ($od['od_receipt_price'] - $od['od_refund_price']) > 0 && ($od['od_settle_case'] == '신용카드' || $od['od_settle_case'] == '계좌이체')) { ?>
        <a href="./orderpartcancel.php?od_id=<?php echo $od_id; ?>" id="orderpartcancel"><?php echo $od['od_settle_case']; ?> 부분취소</a>
        <?php } ?>
        <a href="./orderlist.php?<?php echo $qstr; ?>">목록</a>
    </div>
    </form>
</section>

<section id="anc_sodr_memo">
    <h2 class="h2_frm">상점메모</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>
            현재 열람 중인 주문에 대한 내용을 메모하는곳입니다.<br>
            입금 내역을 메일로 발송할 경우 함께 기록됩니다.
        </p>
    </div>

    <form name="frmorderform2" action="./orderformupdate.php" method="post">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="mod_type" value="memo">
    <input type="hidden" name="odr_status" value="<?php echo $od_status; ?>">
    <input type="hidden" name="odr_settle_case" value="<?php echo $od_settle_case; ?>">
    <input type="hidden" name="odr_misu" value="<?php echo $od_misu; ?>">
    <input type="hidden" name="odr_refund_price" value="<?php echo $od_refund_price; ?>">
    <input type="hidden" name="odr_receipt_cash" value="<?php echo $od_receipt_cash; ?>">
    <input type="hidden" name="odr_receipt_point" value="<?php echo $od_receipt_point; ?>">
    <input type="hidden" name="odr_coupon" value="<?php echo $od_coupon; ?>">
    <input type="hidden" name="odr_fr_date" value="<?php echo $fr_date; ?>">
    <input type="hidden" name="odr_to_date" value="<?php echo $to_date; ?>">

    <div class="tbl_wrap">
        <label for="od_shop_memo" class="sound_only">상점메모</label>
        <textarea name="od_shop_memo" id="od_shop_memo" rows="8"><?php echo stripslashes($od['od_shop_memo']); ?></textarea>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="메모 수정" class="btn_submit">
    </div>

    </form>
</section>

<section>
    <h2 class="h2_frm">구매자 정보</h2>
    <?php echo $pg_anchor; ?>

    <form name="frmorderform3" action="./orderformupdate.php" method="post">
    <input type="hidden" name="od_id" value="<?php echo $od_id; ?>">
    <input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
    <input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
    <input type="hidden" name="sel_field" value="<?php echo $sel_field; ?>">
    <input type="hidden" name="search" value="<?php echo $search; ?>">
    <input type="hidden" name="page" value="<?php echo $page; ?>">
    <input type="hidden" name="mod_type" value="info">
    <input type="hidden" name="odr_status" value="<?php echo $od_status; ?>">
    <input type="hidden" name="odr_settle_case" value="<?php echo $od_settle_case; ?>">
    <input type="hidden" name="odr_misu" value="<?php echo $od_misu; ?>">
    <input type="hidden" name="odr_refund_price" value="<?php echo $od_refund_price; ?>">
    <input type="hidden" name="odr_receipt_cash" value="<?php echo $od_receipt_cash; ?>">
    <input type="hidden" name="odr_receipt_point" value="<?php echo $od_receipt_point; ?>">
    <input type="hidden" name="odr_coupon" value="<?php echo $od_coupon; ?>">
    <input type="hidden" name="odr_fr_date" value="<?php echo $fr_date; ?>">
    <input type="hidden" name="odr_to_date" value="<?php echo $to_date; ?>">

    <div class="compare_wrap">

        <section id="anc_sodr_orderer">
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
                    <th scope="row"><label for="od_name"><span class="sound_only">구매하신 분 </span>이름</label></th>
                    <td><input type="text" name="od_name" value="<?php echo $od['od_name']; ?>" id="od_name" required class="frm_input required"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_tel"><span class="sound_only">구매하신 분 </span>전화번호</label></th>
                    <td><input type="text" name="od_tel" value="<?php echo $od['od_tel']; ?>" id="od_tel" required class="frm_input required"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_hp"><span class="sound_only">구매하신 분 </span>핸드폰</label></th>
                    <td><input type="text" name="od_hp" value="<?php echo $od['od_hp']; ?>" id="od_hp" class="frm_input"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="od_email"><span class="sound_only">구매하신 분 </span>E-mail</label></th>
                    <td><input type="text" name="od_email" value="<?php echo $od['od_email']; ?>" id="od_email" required class="frm_input email required" size="30"></td>
                </tr>
                <tr>
                    <th scope="row"><span class="sound_only">구매하신 분 </span>IP Address</th>
                    <td><?php echo $od['od_ip']; ?></td>
                </tr>
                <tr>
                    <th scope="row">전달 메세지</th>
                    <td><?php if ($od['od_memo']) echo nl2br($od['od_memo']);else echo "없음";?></td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>
    </div>

    <div class="btn_confirm01 btn_confirm">
        <input type="submit" value="구매자정보 수정" class="btn_submit">
        <a href="./orderlist.php?<?php echo $qstr; ?>">목록</a>
    </div>

    </form>
</section>

<script>
$(function() {
    // 전체 옵션선택
    $("#sit_select_all").click(function() {
        if($(this).is(":checked")) {
            $("input[name='it_sel[]']").attr("checked", true);
            $("input[name^=ct_chk]").attr("checked", true);
        } else {
            $("input[name='it_sel[]']").attr("checked", false);
            $("input[name^=ct_chk]").attr("checked", false);
        }
    });

    // 상품의 옵션선택
    $("input[name='it_sel[]']").click(function() {
        var cls = $(this).attr("id").replace("sit_", "sct_");
        var $chk = $("input[name^=ct_chk]."+cls);
        if($(this).is(":checked"))
            $chk.attr("checked", true);
        else
            $chk.attr("checked", false);
    });

    // 부분취소창
    $("#orderpartcancel").on("click", function() {
        var href = this.href;
        window.open(href, "partcancelwin", "left=100, top=100, width=600, height=350, scrollbars=yes");
        return false;
    });
});

function form_submit(f)
{
    var check = false;
    var status = document.pressed;

    for (i=0; i<f.chk_cnt.value; i++) {
        if (document.getElementById('ct_chk_'+i).checked == true)
            check = true;
    }

    if (check == false) {
        alert("처리할 자료를 하나 이상 선택해 주십시오.");
        return false;
    }

    var msg = "";

    <?php if($od['od_settle_case'] == '신용카드') { ?>
    if(status == "취소") {
        var $ct_chk = $("input[name^=ct_chk]");
        var chk_cnt = $ct_chk.size();
        var chked_cnt = $ct_chk.filter(":checked").size();

        if(chk_cnt == chked_cnt) {
            if(confirm("PG사의 신용카드 결제를 함께 취소하시겠습니까?\n\n한번 취소한 결제는 다시 복구할 수 없습니다.")) {
                f.pg_cancel.value = 1;
                msg = "PG사의 신용카드 결제 취소와 함께 ";
            } else {
                f.pg_cancel.value = 0;
                msg = "";
            }
        }
    }
    <?php } ?>

    if (confirm(msg+"\'" + status + "\' 상태를 선택하셨습니다.\n\n선택하신대로 처리하시겠습니까?")) {
        return true;
    } else {
        return false;
    }
}

function del_confirm()
{
    if(confirm("주문서를 삭제하시겠습니까?")) {
        return true;
    } else {
        return false;
    }
}

// 결제금액 수동 설정
function chk_receipt_price()
{
    var chk = document.getElementById("od_receipt_chk");
    var price = document.getElementById("od_receipt_price");
    price.value = chk.checked ? (parseInt(chk.value) + parseInt(price.defaultValue)) : price.defaultValue;
}
</script>

<?php
include_once(G5_ADMIN_PATH.'/admin.tail.php');
?>