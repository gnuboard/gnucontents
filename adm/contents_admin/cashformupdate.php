<?php
$sub_menu = '600250';
include_once('./_common.php');
include_once('./admin.contents.lib.php');
include_once(G5_LIB_PATH.'/icode.sms.lib.php');

auth_check($auth[$sub_menu], "w");

$sql = " select * from {$g5['g5_contents_cash_table']} where cs_id = '$cs_id' ";
$cs  = sql_fetch($sql);
if(!$cs['cs_id'])
    alert('캐시충전 주문서가 존재하지 않습니다.');

if ($cs_receipt_time) {
    if (cm_check_datetime($cs_receipt_time) == false)
        alert('결제일시 오류입니다.');
}

$cs_status = $_POST['cs_status'];
$cs_misu   = $cs['cs_price'] - $_POST['cs_receipt_price'];
if($cs['cs_status'] == '접수' && $cs_misu == 0)
    $cs_status = '입금';

// 결제정보 반영
$sql = " update {$g5['g5_contents_cash_table']}
            set cs_deposit_name    = '{$_POST['cs_deposit_name']}',
                cs_bank_account    = '{$_POST['cs_bank_account']}',
                cs_receipt_time    = '{$_POST['cs_receipt_time']}',
                cs_receipt_price   = '{$_POST['cs_receipt_price']}',
                cs_refund_price    = '{$_POST['cs_refund_price']}',
                cs_misu            = '$cs_misu',
                cs_status          = '$cs_status',
                cs_shop_memo       = '$cs_shop_memo'
            where cs_id = '$cs_id' ";
sql_query($sql);

// 캐시충전내역 기록
if($cs_misu == 0 && $cs_status == '입금') {
    $ch_memo = $cs['cs_settle_case'].'('.$cs['cs_id'].') 충전';

    // 기존 내역 존재하는지 체크
    $sql = " select count(*) as cnt
                from {$g5['g5_contents_cash_history_table']}
                where cs_id = '$cs_id'
                  and mb_id = '{$cs['mb_id']}'
                  and ch_price = '{$cs['cs_cash_price']}'
                  and ch_memo = '$ch_memo' ";
    $row = sql_fetch($sql);

    if(!$row['cnt'])
        insert_cash($cs['mb_id'], $cs['cs_id'], $cs['cs_cash_price'], $ch_memo);
}

// 상태가 취소이면 캐시 차감
if($cs['cs_status'] == '입금' && $cs_status == '취소') {
    $ch_memo = $cs['cs_settle_case'].'('.$cs['cs_id'].') 충전취소';

    // 기존 내역 존재하는지 체크
    $sql = " select count(*) as cnt
                from {$g5['g5_contents_cash_history_table']}
                where cs_id = '$cs_id'
                  and mb_id = '{$cs['mb_id']}'
                  and ch_price = '-{$cs['cs_cash_price']}'
                  and ch_memo = '$ch_memo' ";
    $row = sql_fetch($sql);

    if(!$row['cnt'])
        insert_cash($cs['mb_id'], $cs['cs_id'], (-1)*$cs['cs_cash_price'], $ch_memo);

    // 신용카드면 승인 취소
    if($cs['cs_tno'] && $cs['cs_settle_case'] == '신용카드') {
        $pg_res_cd = '';
        $pg_res_msg = '';

        switch($cs['cs_pg']) {
            case 'lg':
                include_once(G5_CONTENTS_PATH.'/settle_lg.inc.php');

                $LGD_TID = $cs['cs_tno'];

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
                $inipay->SetField("tid",       $cs['cs_tno']);                   // 취소할 거래의 거래아이디
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

                $tno = $cs['cs_tno'];
                $tran_cd = '00200000';
                $g_conf_home_dir  = G5_CONTENTS_PATH.'/kcp';
                $g_conf_key_dir   = '';
                $g_conf_log_dir   = G5_CONTENTS_PATH.'/kcp/log';
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
                {
                    $g_conf_key_dir   = G5_CONTENTS_PATH.'/kcp/bin/pub.key';
                }
                $g_conf_site_cd  = $setting['de_kcp_mid'];

                if (preg_match("/^T000/", $g_conf_site_cd) || $setting['de_card_test']) {
                    $g_conf_gw_url  = "testpaygw.kcp.co.kr";
                } else {
                    $g_conf_gw_url  = "paygw.kcp.co.kr";
                }
                $cancel_msg = iconv_euckr('쇼핑몰 운영자 승인 취소');
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
        if($pg_res_cd == '')
            $pg_cancel_log = ' PG 신용카드 승인취소 처리';
    }

    $cs_shop_memo = $cs['cs_shop_memo'];
    $cs_shop_memo .= G5_TIME_YMDHIS.' '.$member['mb_id'].' 캐시충전취소 처리'.$pg_cancel_log."\n";

    $sql = " update {$g5['g5_contents_cash_table']}
                set cs_refund_price = '{$cs['cs_receipt_price']}',
                    cs_shop_memo = '$cs_shop_memo'
                where cs_id = '$cs_id' ";
    sql_query($sql);
}

// SMS 문자전송
if ($config['cf_sms_use']) {
    if ($cs_sms_ipgum_check && $setting['de_sms_use4'])
    {
        $receive_number = preg_replace("/[^0-9]/", "", $cs['cs_hp']);	// 수신자번호 (받는사람 핸드폰번호 ... 여기서는 주문자님의 핸드폰번호임)
        $send_number = preg_replace("/[^0-9]/", "", $setting['de_admin_company_tel']); // 발신자번호

        if ($cs['cs_status'] == '접수' && $cs_misu == 0 && $cs_status == '입금')
        {
            $sms_contents = cm_conv_sms_contents($cs['cs_id'], $setting['de_sms_cont4'], 'cash');

            $SMS = new SMS;
            $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
            $SMS->Add($receive_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_contents)), "");
            $SMS->Send();
        }
    }
}

$qstr1 = "cs_status=".urlencode($csh_status)."&amp;cs_settle_case=".urlencode($csh_settle_case)."&amp;cs_misu=$csh_misu&amp;cs_refund_price=$csh_refund_price&amp;fr_date=$csh_fr_date&amp;to_date=$csh_to_date&amp;sfl=$sfl&amp;stx=$stx&amp;save_stx=$stx";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

goto_url("./cashform.php?cs_id=$cs_id&amp;$qstr");
?>
