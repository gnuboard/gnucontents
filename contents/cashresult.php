<?php
include_once('./_common.php');

if (!$is_member)
    alert('회원 로그인 후 이용해 주십시오.', G5_BBS_URL.'/login.php?url='.urlencode(G5_CONTENTS_URL.'/cashlist.php'));

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/cashresult.php');
    return;
}

$sql = "select * from {$g5['g5_contents_cash_table']} where cs_id = '$cs_id' and mb_id = '{$member['mb_id']}'  ";
$cs = sql_fetch($sql);
if (!$cs['cs_id']) {
    alert("조회하실 캐시충전 결제내역이 없습니다.", G5_CONTENTS_URL);
}

// 결제방법
$settle_case = $cs['cs_settle_case'];

$g5['title'] = '캐시충전 결제상세내역';
include_once('./_head.php');

// LG 현금영수증 JS
if($cs['cs_pg'] == 'lg') {
    if($setting['de_card_test']) {
    echo '<script language="JavaScript" src="http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js"></script>'.PHP_EOL;
    } else {
        echo '<script language="JavaScript" src="http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js"></script>'.PHP_EOL;
    }
}
?>

<!-- 주문상세내역 시작 { -->
<div id="sod_fin">

    <p id="sod_fin_no">캐시충전 주문번호 <strong><?php echo $cs_id; ?></strong></p>

    <section id="sod_fin_view">
        <h2>결제 정보</h2>
        <?php
        $misu = true;

        if ($cs['cs_misu'] == 0) {
            $wanbul = " (완불)";
            $misu = false; // 미수금 없음
        }
        else
        {
            $wanbul = cm_display_price($cs['cs_receipt_price']);
        }

        $misu_price = $cs['cs_misu'];

        // 결제정보처리
        if($cs['cs_receipt_price'] > 0)
            $cs_receipt_price = cm_display_price($cs['cs_receipt_price']);
        else
            $cs_receipt_price = '아직 입금되지 않았거나 입금정보를 입력하지 못하였습니다.';

        $app_no_subj = '';
        $disp_bank = true;
        $disp_receipt = false;
        if($cs['cs_settle_case'] == '신용카드') {
            $app_no_subj = '승인번호';
            $app_no = $cs['cs_app_no'];
            $disp_bank = false;
            $disp_receipt = true;
        } else if($cs['cs_settle_case'] == '휴대폰') {
            $app_no_subj = '휴대폰번호';
            $app_no = $cs['cs_bank_account'];
            $disp_bank = false;
            $disp_receipt = true;
        } else if($cs['cs_settle_case'] == '가상계좌' || $cs['cs_settle_case'] == '계좌이체') {
            $app_no_subj = '거래번호';
            $app_no = $cs['cs_tno'];
        }
        ?>

        <section id="sod_fin_pay">
            <h3>결제정보</h3>

            <div class="tbl_head01 tbl_wrap">
                <table>
                <colgroup>
                    <col class="grid_3">
                    <col>
                </colgroup>
                <tbody>
                <?php if($cs['cs_id']) { ?>
                <tr>
                    <th scope="row">주문번호</th>
                    <td><?php echo $cs['cs_id']; ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <th scope="row">캐시충전</th>
                    <td><?php echo number_format($cs['cs_cash_price']); ?></td>
                </tr>
                <tr>
                    <th scope="row">결제방식</th>
                    <td><?php echo $cs['cs_settle_case']; ?></td>
                </tr>
                <tr>
                    <th scope="row">결제금액</th>
                    <td><?php echo $cs_receipt_price; ?></td>
                </tr>
                <?php if(!cm_is_null_time($cs['cs_receipt_time'])) { ?>
                <tr>
                    <th scope="row">결제일시</th>
                    <td><?php echo $cs['cs_receipt_time']; ?></td>
                </tr>
                <?php } ?>
                <?php
                // 승인번호, 휴대폰번호, 거래번호
                if($app_no_subj)
                {
                ?>
                <tr>
                    <th scope="row"><?php echo $app_no_subj; ?></th>
                    <td><?php echo $app_no; ?></td>
                </tr>
                <?php
                }

                // 계좌정보
                if($disp_bank)
                {
                ?>
                <tr>
                    <th scope="row">입금자명</th>
                    <td><?php echo get_text($cs['cs_deposit_name']); ?></td>
                </tr>
                <tr>
                    <th scope="row">입금계좌</th>
                    <td><?php echo get_text($cs['cs_bank_account']); ?></td>
                </tr>
                <?php
                }

                if($disp_receipt) {
                ?>
                <tr>
                    <th scope="row">영수증</th>
                    <td>
                        <?php
                        if($cs['cs_settle_case'] == '휴대폰')
                        {
                            if($cs['cs_pg'] == 'lg') {
                                require_once G5_CONTENTS_PATH.'/settle_lg.inc.php';
                                $LGD_TID      = $cs['cs_tno'];
                                $LGD_MERTKEY  = $config['cf_lg_mert_key'];
                                $LGD_HASHDATA = md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);

                                $hp_receipt_script = 'showReceiptByTID(\''.$LGD_MID.'\', \''.$LGD_TID.'\', \''.$LGD_HASHDATA.'\');';
                            } else if($cs['cs_pg'] == 'inicis') {
                                $hp_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid='.$cs['cs_tno'].'&noMethod=1\',\'receipt\',\'width=430,height=700\');';

                            } else {
                                $hp_receipt_script = 'window.open(\''.G5_CM_BILL_RECEIPT_URL.'mcash_bill&tno='.$cs['cs_tno'].'&order_no='.$cs['cs_id'].'&trade_mony='.$cs['cs_receipt_price'].'\', \'winreceipt\', \'width=500,height=690,scrollbars=yes,resizable=yes\');';
                            }
                        ?>
                        <a href="javascript:;" onclick="<?php echo $hp_receipt_script; ?>">영수증 출력</a>
                        <?php
                        }

                        if($cs['cs_settle_case'] == '신용카드')
                        {
                            if($cs['cs_pg'] == 'lg') {
                                require_once G5_CONTENTS_PATH.'/settle_lg.inc.php';
                                $LGD_TID      = $cs['cs_tno'];
                                $LGD_MERTKEY  = $config['cf_lg_mert_key'];
                                $LGD_HASHDATA = md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);

                                $card_receipt_script = 'showReceiptByTID(\''.$LGD_MID.'\', \''.$LGD_TID.'\', \''.$LGD_HASHDATA.'\');';
                            } else if($cs['cs_pg'] == 'inicis') {
                                $card_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid='.$cs['cs_tno'].'&noMethod=1\',\'receipt\',\'width=430,height=700\');';

                            } else {
                                $card_receipt_script = 'window.open(\''.G5_CM_BILL_RECEIPT_URL.'card_bill&tno='.$cs['cs_tno'].'&order_no='.$cs['cs_id'].'&trade_mony='.$cs['cs_receipt_price'].'\', \'winreceipt\', \'width=470,height=815,scrollbars=yes,resizable=yes\');';
                            }
                        ?>
                        <a href="javascript:;" onclick="<?php echo $card_receipt_script; ?>">영수증 출력</a>
                        <?php
                        }
                        ?>
                    <td>
                    </td>
                </tr>
                <?php
                }
                ?>
                <?php
                // 현금영수증 발급을 사용하는 경우에만
                if ($setting['de_taxsave_use']) {
                    // 미수금이 없고 현금일 경우에만 현금영수증을 발급 할 수 있습니다.
                    if ($cs['cs_misu'] == 0 && $cs['cs_receipt_price'] && ($cs['cs_settle_case'] == '무통장' || $cs['cs_settle_case'] == '계좌이체' || $cs['cs_settle_case'] == '가상계좌')) {
                ?>
                <tr>
                    <th scope="row">현금영수증</th>
                    <td>
                    <?php
                    if ($cs['cs_taxsave'])
                    {
                        if($cs['cs_pg'] == 'lg') {
                            require_once G5_CONTENTS_PATH.'/settle_lg.inc.php';

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
                            $cash = unserialize($cs['cs_taxsave_info']);
                            $cash_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid='.$cash['TID'].'&clpaymethod=22\',\'showreceipt\',\'width=380,height=540,scrollbars=no,resizable=no\');';

                        } else {
                            require_once G5_CONTENTS_PATH.'/settle_kcp.inc.php';

                            $cash = unserialize($cs['cs_taxsave_info']);
                            $cash_receipt_script = 'window.open(\''.G5_CM_CASH_RECEIPT_URL.$setting['de_kcp_mid'].'&orderid='.$cs['cs_id'].'&bill_yn=Y&authno='.$cash['receipt_no'].'\', \'taxsave_receipt\', \'width=360,height=647,scrollbars=0,menus=0\');';
                        }
                    ?>
                        <a href="javascript:;" onclick="<?php echo $cash_receipt_script; ?>" class="btn_frmline">현금영수증 확인하기</a>
                    <?php
                    }
                    else
                    {
                    ?>
                        <a href="javascript:;" onclick="window.open('<?php echo G5_CONTENTS_URL; ?>/taxsave.php?tx=cash&od_id=<?php echo $cs['cs_id']; ?>', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');" class="btn_frmline">현금영수증을 발급하시려면 클릭하십시오.</a>
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
    </section>

    <section id="sod_fin_tot">
        <h2>결제합계</h2>

        <ul>
            <li>
                총 주문액
                <strong><?php echo cm_display_price($cs['cs_price']); ?></strong>
            </li>
            <?php
            if ($misu_price > 0) {
            echo '<li>';
            echo '미결제액'.PHP_EOL;
            echo '<strong>'.cm_display_price($misu_price).'</strong>';
            echo '</li>';
            }
            ?>
            <li id="alrdy">
                결제액
                <strong><?php echo $wanbul; ?></strong>
            </li>
        </ul>
    </section>

    <?php if ($cs['cs_settle_case'] == '가상계좌'  && $cs['cs_receipt_price'] == 0 && $setting['de_card_test'] && $is_admin && $cs['cs_pg'] == 'kcp') {
    preg_match("/\s{1}([^\s]+)\s?/", $cs['cs_bank_account'], $matchs);
    $deposit_no = trim($matchs[1]);
    ?>
    <div class="tbl_frm01 tbl_wrap od_acc">
        <form method="post" action="http://devadmin.kcp.co.kr/Modules/Noti/TEST_Vcnt_Noti_Proc.jsp" target="_blank">
        <p>관리자가 가상계좌 테스트를 한 경우에만 보입니다.</p>
        <table>
        <caption>모의입금처리</caption>
        <colgroup>
            <col class="grid_3">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="col"><label for="e_trade_no">KCP 거래번호</label></th>
            <td><input type="text" name="e_trade_no" value="<?php echo $cs['cs_tno']; ?>" class="frm_input"></td>
        </tr>
        <tr>
            <th scope="col"><label for="deposit_no">입금계좌</label></th>
            <td><input type="text" name="deposit_no" value="<?php echo $deposit_no; ?>"class="frm_input"></td>
        </tr>
        <tr>
            <th scope="col"><label for="req_name">입금자명</label></th>
            <td><input type="text" name="req_name" value="<?php echo $cs['cs_deposit_name']; ?>"  class="frm_input"></td>
        </tr>
        <tr>
            <th scope="col"><label for="noti_url">입금통보 URL</label></th>
            <td><input type="text" name="noti_url" value="<?php echo G5_CONTENTS_URL; ?>/settle_kcp_common.php" size="80" class="frm_input"></td>
        </tr>
        </tbody>
        </table>
        <div id="sod_fin_test" class="btn_confirm">
            <input type="submit" value="입금통보 테스트" class="btn_submit">
        </div>
        </form>
    </div>
    <?php } ?>

</div>
<!-- } 개인결제상세내역 끝 -->

<?php
include_once('./_tail.php');
?>