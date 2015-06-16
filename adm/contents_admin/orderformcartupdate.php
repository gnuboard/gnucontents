<?php
$sub_menu = '600200';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$ct_chk_count = count($_POST['ct_chk']);
if(!$ct_chk_count)
    alert('처리할 자료를 하나 이상 선택해 주십시오.');

$status_normal = array('주문','입금');
$status_cancel = array('취소');

if (in_array($_POST['ct_status'], $status_normal) || in_array($_POST['ct_status'], $status_cancel)) {
    ; // 통과
} else {
    alert('변경할 상태가 올바르지 않습니다.');
}

$mod_history = '';
$cnt = count($_POST['ct_id']);
for ($i=0; $i<$cnt; $i++)
{
    $k = $_POST['ct_chk'][$i];
    $ct_id = $_POST['ct_id'][$k];

    if(!$ct_id)
        continue;

    $sql = " select * from {$g5['g5_contents_cart_table']} where od_id = '$od_id' and ct_id  = '$ct_id' ";
    $ct = sql_fetch($sql);
    if(!$ct['ct_id'])
        continue;

    // 수량이 변경됐다면
    $ct_qty = $_POST['ct_qty'][$k];
    if($ct['ct_qty'] != $ct_qty) {
        // 입금 상태에서 수량이 변경됐다면 판매수량에서 차감
        if($ct['ct_status'] == '입금') {
            substract_item_sale_qty($ct['od_id'], $ct['ct_id']);
        }

        $sql = " update {$g5['g5_contents_cart_table']}
                    set ct_qty = '$ct_qty'
                    where ct_id = '$ct_id'
                      and od_id = '$od_id' ";
        sql_query($sql);
        $mod_history .= G5_TIME_YMDHIS.' '.$ct['ct_option'].' 수량변경 '.$ct['ct_qty'].' -> '.$ct_qty."\n";

        // 입금 상태에서 수량이 변경됐다면 판매수량 증가
        if($ct['ct_status'] == '입금') {
            add_item_sale_qty($ct['od_id'], $ct['ct_id']);
        }
    }

    $point_use = $ct['ct_point_use'];
    // 회원이면서 포인트가 0보다 크면
    // 이미 포인트를 부여했다면 뺀다.
    if ($mb_id && $ct['ct_point'] && $ct['ct_point_use'])
    {
        $point_use = 0;
        //insert_point($mb_id, (-1) * ($ct[ct_point] * $ct[ct_qty]), "주문번호 $od_id ($ct_id) 취소");
        delete_point($mb_id, "@contents", $mb_id, "$od_id,$ct_id");
    }

    // 히스토리에 남김
    // 히스토리에 남길때는 작업|아이디|시간|IP|그리고 나머지 자료
    $now = G5_TIME_YMDHIS;
    $ct_history="\n$ct_status|{$member['mb_id']}|$now|$REMOTE_ADDR";

    $sql = " update {$g5['g5_contents_cart_table']}
                set ct_point_use  = '$point_use',
                    ct_status     = '$ct_status',
                    ct_history    = CONCAT(ct_history,'$ct_history')
                where od_id = '$od_id'
                and ct_id  = '$ct_id' ";
    sql_query($sql);

    // 주문, 취소에서 입금으로 변경시 판매수량 증가
    if(($ct['ct_status'] == '주문' || $ct['ct_status'] == '취소') && $ct_status == '입금')
        add_item_sale_qty($ct['od_id'], $ct['ct_id']);

    // 입금에서 취소, 주문 변경시 판매수량 차감
    if($ct['ct_status'] == '입금' && ($ct_status == '주문' || $ct_status == '취소'))
        substract_item_sale_qty($ct['od_id'], $ct['ct_id']);
}

// 장바구니 상품 모두 취소일 경우 주문상태 변경
$cancel_change = false;

if (in_array($_POST['ct_status'], $status_cancel)) {
    $sql = " select count(*) as od_count1,
                    SUM(IF(ct_status = '취소', 1, 0)) as od_count2
                from {$g5['g5_contents_cart_table']}
                where od_id = '$od_id' ";
    $row = sql_fetch($sql);

    if($row['od_count1'] == $row['od_count2']) {
        $cancel_change = true;

        $pg_res_cd = '';
        $pg_res_msg = '';
        $pg_cancel_log = '';

        $sql = " select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' ";
        $od = sql_fetch($sql);

        // PG 신용카드 결제 취소일 때
        if($pg_cancel == 1) {
            if($od['od_status'] == '입금' && $od['od_tno'] && $od['od_settle_case'] == '신용카드') {
                switch($od['od_pg']) {
                    case 'lg':
                        include_once(G5_CONTENTS_PATH.'/settle_lg.inc.php');

                        $LGD_TID = $od['od_tno'];

                        $xpay = new XPay($configPath, $CST_PLATFORM);

                        // Mert Key 설정
                        $xpay->set_config_value('t'.$LGD_MID, $config['cf_lg_mert_key']);
                        $xpay->set_config_value($LGD_MID, $config['cf_lg_mert_key']);

                        $xpay->Init_TX($LGD_MID);

                        $xpay->Set('LGD_TXNAME', 'Cancel');
                        $xpay->Set('LGD_TID', $LGD_TID);

                        if ($xpay->TX()) {
                            $res_cd = $xpay->Response_Code();
                            if($res_cd != '0000' && $res_cd != 'AV11') {
                                $pg_res_cd = $res_cd;
                                $pg_res_msg = $xpay->Response_Msg();
                            }
                        } else {
                            $pg_res_cd = $xpay->Response_Code();
                            $pg_res_msg = $xpay->Response_Msg();
                        }
                        break;
                    case 'inicis':
                        include_once(G5_CONTENTS_PATH.'/settle_inicis.inc.php');
                        $cancel_msg = iconv_euckr('컨텐츠몰 운영자 승인 취소');

                        /*********************
                         * 3. 취소 정보 설정 *
                         *********************/
                        $inipay->SetField("type",      "cancel");                        // 고정 (절대 수정 불가)
                        $inipay->SetField("mid",       $setting['de_inicis_mid']);       // 상점아이디
                        /**************************************************************************************************
                         * admin 은 키패스워드 변수명입니다. 수정하시면 안됩니다. 1111의 부분만 수정해서 사용하시기 바랍니다.
                         * 키패스워드는 상점관리자 페이지(https://iniweb.inicis.com)의 비밀번호가 아닙니다. 주의해 주시기 바랍니다.
                         * 키패스워드는 숫자 4자리로만 구성됩니다. 이 값은 키파일 발급시 결정됩니다.
                         * 키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오.
                         **************************************************************************************************/
                        $inipay->SetField("admin",     $setting['de_inicis_admin_key']); //비대칭 사용키 키패스워드
                        $inipay->SetField("tid",       $od['od_tno']);                   // 취소할 거래의 거래아이디
                        $inipay->SetField("cancelmsg", $cancel_msg);                     // 취소사유

                        /****************
                         * 4. 취소 요청 *
                         ****************/
                        $inipay->startAction();

                        /****************************************************************
                         * 5. 취소 결과                                           	*
                         *                                                        	*
                         * 결과코드 : $inipay->getResult('ResultCode') ("00"이면 취소 성공)  	*
                         * 결과내용 : $inipay->getResult('ResultMsg') (취소결과에 대한 설명) 	*
                         * 취소날짜 : $inipay->getResult('CancelDate') (YYYYMMDD)          	*
                         * 취소시각 : $inipay->getResult('CancelTime') (HHMMSS)            	*
                         * 현금영수증 취소 승인번호 : $inipay->getResult('CSHR_CancelNum')    *
                         * (현금영수증 발급 취소시에만 리턴됨)                          *
                         ****************************************************************/

                        $res_cd  = $inipay->getResult('ResultCode');
                        $res_msg = $inipay->getResult('ResultMsg');

                        if($res_cd != '00') {
                            $pg_res_cd = $res_cd;
                            $pg_res_msg = iconv_utf8($res_msg);
                        }
                        break;
                    default:
                        include_once(G5_CONTENTS_PATH.'/settle_kcp.inc.php');
                        require_once(G5_CONTENTS_PATH.'/kcp/pp_ax_hub_lib.php');

                        // locale ko_KR.euc-kr 로 설정
                        setlocale(LC_CTYPE, 'ko_KR.euc-kr');

                        $c_PayPlus = new C_PP_CLI;

                        $c_PayPlus->mf_clear();

                        $tno = $od['od_tno'];
                        $tran_cd = '00200000';
                        $g_conf_home_dir  = G5_CONTENTS_PATH.'/kcp';
                        $g_conf_key_dir   = '';
                        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                        {
                            $g_conf_log_dir   = G5_CONTENTS_PATH.'/kcp/log';
                            $g_conf_key_dir   = G5_CONTENTS_PATH.'/kcp/bin/pub.key';
                        }
                        $g_conf_site_cd  = $setting['de_kcp_mid'];

                        if (preg_match("/^T000/", $g_conf_site_cd) || $setting['de_card_test']) {
                            $g_conf_gw_url  = "testpaygw.kcp.co.kr";
                        } else {
                            $g_conf_gw_url  = "paygw.kcp.co.kr";
                        }
                        $cancel_msg = iconv_euckr('컨텐츠몰 운영자 승인 취소');
                        $cust_ip = $_SERVER['REMOTE_ADDR'];
                        $bSucc_mod_type = "STSC";

                        $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                        $c_PayPlus->mf_set_modx_data( "mod_type", $bSucc_mod_type              );  // 원거래 변경 요청 종류
                        $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP
                        $c_PayPlus->mf_set_modx_data( "mod_desc", $cancel_msg );  // 변경 사유

                        $c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd,
                                              $g_conf_site_key,  $tran_cd,    "",
                                              $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                              $ordr_idxx, $cust_ip, "3" ,
                                              0, 0, $g_conf_key_dir, $g_conf_log_dir);

                        $res_cd  = $c_PayPlus->m_res_cd;
                        $res_msg = $c_PayPlus->m_res_msg;

                        if($res_cd != '0000') {
                            $pg_res_cd = $res_cd;
                            $pg_res_msg = iconv_utf8($res_msg);
                        }

                        // locale 설정 초기화
                        setlocale(LC_CTYPE, '');
                        break;
                }

                // PG 취소요청 성공했으면
                if($pg_res_cd == '') {
                    $pg_cancel_log = ' PG 신용카드 승인취소 처리';
                    $sql = " update {$g5['g5_contents_order_table']}
                                set od_refund_price = '{$od['od_receipt_price']}'
                                where od_id = '$od_id' ";
                    sql_query($sql);
                }
            }
        }

        // 사용된 캐시가 있다면 반환
        if($od['od_status'] == '입금' && $od['od_receipt_cash'] > 0) {
            $ch_price = -1 * $od['od_receipt_cash'];

            // 캐시 사용내역 조회
            $sql = " select count(*) as cnt
                        from {$g5['g5_contents_cash_history_table']}
                        where mb_id = '{$od['mb_id']}'
                          and cs_id = '$od_id'
                          and ch_price = '$ch_price' ";
            $row = sql_fetch($sql);

            if($row['cnt'] > 0) {
                $ch_memo = '컨텐츠몰 주문번호 '.$od_id.' 주문취소 환불';
                insert_cash($od['mb_id'], $od_id, $od['od_receipt_cash'], $ch_memo);

                // 주문정보의 캐시 금액 초기화
                sql_query(" update {$g5['g5_contents_order_table']} set od_receipt_cash = '0' where od_id = '$od_id' ");
            }
        }

        // 관리자 주문취소 로그
        $mod_history .= G5_TIME_YMDHIS.' '.$member['mb_id'].' 주문'.$_POST['ct_status'].' 처리'.$pg_cancel_log."\n";
    }
}

// 미수금 등의 정보
$info = cm_get_order_info($od_id);

if(!$info)
    alert('주문자료가 존재하지 않습니다.');

$sql = " update {$g5['g5_contents_order_table']}
            set od_cart_price   = '{$info['od_cart_price']}',
                od_cart_coupon  = '{$info['od_cart_coupon']}',
                od_coupon       = '{$info['od_coupon']}',
                od_cancel_price = '{$info['od_cancel_price']}',
                od_misu         = '{$info['od_misu']}' ";
if ($mod_history) { // 주문변경 히스토리 기록
    $sql .= " , od_mod_history = CONCAT(od_mod_history,'$mod_history') ";
}

if($cancel_change) {
    $sql .= " , od_status = '취소' "; // 주문상품 모두 취소이면 주문 취소
} else {
    if (in_array($_POST['ct_status'], $status_normal)) { // 정상인 주문상태만 기록
        $sql .= " , od_status = '{$_POST['ct_status']}' ";
    }
}

$sql .= " where od_id = '$od_id' ";
sql_query($sql);

// 신용카드 취소 때 오류가 있으면 알림
if($pg_cancel == 1 && $pg_res_cd && $pg_res_msg) {
    echo '<script>';
    echo 'alert("오류코드 : '.$pg_res_cd.' 오류내용 : '.$pg_res_msg.'");';
    echo '</script>';
}

$qstr1 = "od_status=".urlencode($odr_status)."&amp;od_settle_case=".urlencode($odr_settle_case)."&amp;od_misu=$odr_misu&amp;od_refund_price=$odr_refund_price&amp;od_receipt_cash=$odr_receipt_cash&amp;od_receipt_point=$odr_receipt_point&amp;od_coupon=$odr_coupon&amp;fr_date=$odr_fr_date&amp;to_date=$odr_to_date&amp;sel_field=$sel_field&amp;search=$search&amp;save_search=$search";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$url = "./orderform.php?od_id=$od_id&amp;$qstr";

// 1.06.06
$od = sql_fetch(" select od_receipt_point from {$g5['g5_contents_order_table']} where od_id = '$od_id' ");
if ($od['od_receipt_point'])
    alert("포인트로 결제한 주문은,\\n\\n주문상태 변경으로 인해 포인트의 가감이 발생하는 경우\\n\\n회원관리 > 포인트관리에서 수작업으로 포인트를 맞추어 주셔야 합니다.", $url);
else
    goto_url($url);
?>
