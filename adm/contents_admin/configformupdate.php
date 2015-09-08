<?php
$sub_menu = '600100';
include_once('./_common.php');

check_demo();

auth_check($auth[$sub_menu], "w");

// 로그인을 바로 이 주소로 하는 경우 쇼핑몰설정값이 사라지는 현상을 방지
if (!$de_admin_company_owner) goto_url("./configform.php");

if ($logo_img_del)  @unlink(G5_DATA_PATH."/common/cm_logo_img");
if ($logo_img_del2)  @unlink(G5_DATA_PATH."/common/cm_logo_img2");
if ($mobile_logo_img_del)  @unlink(G5_DATA_PATH."/common/cm_mobile_logo_img");
if ($mobile_logo_img_del2)  @unlink(G5_DATA_PATH."/common/cm_mobile_logo_img2");

if ($_FILES['logo_img']['name']) cm_upload_file($_FILES['logo_img']['tmp_name'], "cm_logo_img", G5_DATA_PATH."/common");
if ($_FILES['logo_img2']['name']) cm_upload_file($_FILES['logo_img2']['tmp_name'], "cm_logo_img2", G5_DATA_PATH."/common");
if ($_FILES['mobile_logo_img']['name']) cm_upload_file($_FILES['mobile_logo_img']['tmp_name'], "cm_mobile_logo_img", G5_DATA_PATH."/common");
if ($_FILES['mobile_logo_img2']['name']) cm_upload_file($_FILES['mobile_logo_img2']['tmp_name'], "cm_mobile_logo_img2", G5_DATA_PATH."/common");

$de_kcp_mid = substr($_POST['de_kcp_mid'],0,3);

// kcp 전자결제를 사용할 때 site key 입력체크
if($de_pg_service == 'kcp' && ($de_iche_use || $de_vbank_use || $de_hp_use || $de_card_use)) {
    if(trim($de_kcp_site_key) == '')
        alert('KCP SITE KEY를 입력해 주십시오.');
}

//
// 영카트 default
//
$sql = " update {$g5['g5_contents_default_table']}
            set de_admin_company_owner        = '{$_POST['de_admin_company_owner']}',
                de_admin_company_name         = '{$_POST['de_admin_company_name']}',
                de_admin_company_saupja_no    = '{$_POST['de_admin_company_saupja_no']}',
                de_admin_company_tel          = '{$_POST['de_admin_company_tel']}',
                de_admin_company_fax          = '{$_POST['de_admin_company_fax']}',
                de_admin_tongsin_no           = '{$_POST['de_admin_tongsin_no']}',
                de_admin_company_zip          = '{$_POST['de_admin_company_zip']}',
                de_admin_company_addr         = '{$_POST['de_admin_company_addr']}',
                de_admin_info_name            = '{$_POST['de_admin_info_name']}',
                de_admin_info_email           = '{$_POST['de_admin_info_email']}',
                de_chub_mid                   = '{$_POST['de_chub_mid']}',
                de_contents_skin              = '{$_POST['de_contents_skin']}',
                de_contents_mobile_skin       = '{$_POST['de_contents_mobile_skin']}',
                de_type1_list_use             = '{$_POST['de_type1_list_use']}',
                de_type1_list_skin            = '{$_POST['de_type1_list_skin']}',
                de_type1_list_mod             = '{$_POST['de_type1_list_mod']}',
                de_type1_list_row             = '{$_POST['de_type1_list_row']}',
                de_type1_img_width            = '{$_POST['de_type1_img_width']}',
                de_type1_img_height           = '{$_POST['de_type1_img_height']}',
                de_type2_list_use             = '{$_POST['de_type2_list_use']}',
                de_type2_list_skin            = '{$_POST['de_type2_list_skin']}',
                de_type2_list_mod             = '{$_POST['de_type2_list_mod']}',
                de_type2_list_row             = '{$_POST['de_type2_list_row']}',
                de_type2_img_width            = '{$_POST['de_type2_img_width']}',
                de_type2_img_height           = '{$_POST['de_type2_img_height']}',
                de_type3_list_use             = '{$_POST['de_type3_list_use']}',
                de_type3_list_skin            = '{$_POST['de_type3_list_skin']}',
                de_type3_list_mod             = '{$_POST['de_type3_list_mod']}',
                de_type3_list_row             = '{$_POST['de_type3_list_row']}',
                de_type3_img_width            = '{$_POST['de_type3_img_width']}',
                de_type3_img_height           = '{$_POST['de_type3_img_height']}',
                de_type4_list_use             = '{$_POST['de_type4_list_use']}',
                de_type4_list_skin            = '{$_POST['de_type4_list_skin']}',
                de_type4_list_mod             = '{$_POST['de_type4_list_mod']}',
                de_type4_list_row             = '{$_POST['de_type4_list_row']}',
                de_type4_img_width            = '{$_POST['de_type4_img_width']}',
                de_type4_img_height           = '{$_POST['de_type4_img_height']}',
                de_mobile_type1_list_use      = '{$_POST['de_mobile_type1_list_use']}',
                de_mobile_type1_list_skin     = '{$_POST['de_mobile_type1_list_skin']}',
                de_mobile_type1_list_mod      = '{$_POST['de_mobile_type1_list_mod']}',
                de_mobile_type1_list_row      = '{$_POST['de_mobile_type1_list_row']}',
                de_mobile_type1_img_width     = '{$_POST['de_mobile_type1_img_width']}',
                de_mobile_type1_img_height    = '{$_POST['de_mobile_type1_img_height']}',
                de_mobile_type2_list_use      = '{$_POST['de_mobile_type2_list_use']}',
                de_mobile_type2_list_skin     = '{$_POST['de_mobile_type2_list_skin']}',
                de_mobile_type2_list_mod      = '{$_POST['de_mobile_type2_list_mod']}',
                de_mobile_type2_list_row      = '{$_POST['de_mobile_type2_list_row']}',
                de_mobile_type2_img_width     = '{$_POST['de_mobile_type2_img_width']}',
                de_mobile_type2_img_height    = '{$_POST['de_mobile_type2_img_height']}',
                de_mobile_type3_list_use      = '{$_POST['de_mobile_type3_list_use']}',
                de_mobile_type3_list_skin     = '{$_POST['de_mobile_type3_list_skin']}',
                de_mobile_type3_list_mod      = '{$_POST['de_mobile_type3_list_mod']}',
                de_mobile_type3_list_row      = '{$_POST['de_mobile_type3_list_row']}',
                de_mobile_type3_img_width     = '{$_POST['de_mobile_type3_img_width']}',
                de_mobile_type3_img_height    = '{$_POST['de_mobile_type3_img_height']}',
                de_mobile_type4_list_use      = '{$_POST['de_mobile_type4_list_use']}',
                de_mobile_type4_list_skin     = '{$_POST['de_mobile_type4_list_skin']}',
                de_mobile_type4_list_mod      = '{$_POST['de_mobile_type4_list_mod']}',
                de_mobile_type4_list_row      = '{$_POST['de_mobile_type4_list_row']}',
                de_mobile_type4_img_width     = '{$_POST['de_mobile_type4_img_width']}',
                de_mobile_type4_img_height    = '{$_POST['de_mobile_type4_img_height']}',
                de_movie_skin                 = '{$_POST['de_movie_skin']}',
                de_rel_list_use               = '{$_POST['de_rel_list_use']}',
                de_rel_list_skin              = '{$_POST['de_rel_list_skin']}',
                de_rel_list_mod               = '{$_POST['de_rel_list_mod']}',
                de_rel_img_width              = '{$_POST['de_rel_img_width']}',
                de_rel_img_height             = '{$_POST['de_rel_img_height']}',
                de_mobile_rel_list_use        = '{$_POST['de_mobile_rel_list_use']}',
                de_mobile_rel_list_skin       = '{$_POST['de_mobile_rel_list_skin']}',
                de_mobile_rel_list_mod        = '{$_POST['de_mobile_rel_list_mod']}',
                de_mobile_rel_img_width       = '{$_POST['de_mobile_rel_img_width']}',
                de_mobile_rel_img_height      = '{$_POST['de_mobile_rel_img_height']}',
                de_search_list_skin           = '{$_POST['de_search_list_skin']}',
                de_search_list_mod            = '{$_POST['de_search_list_mod']}',
                de_search_list_row            = '{$_POST['de_search_list_row']}',
                de_search_img_width           = '{$_POST['de_search_img_width']}',
                de_search_img_height          = '{$_POST['de_search_img_height']}',
                de_mobile_search_list_skin    = '{$_POST['de_mobile_search_list_skin']}',
                de_mobile_search_list_mod     = '{$_POST['de_mobile_search_list_mod']}',
                de_mobile_search_list_row     = '{$_POST['de_mobile_search_list_row']}',
                de_mobile_search_img_width    = '{$_POST['de_mobile_search_img_width']}',
                de_mobile_search_img_height   = '{$_POST['de_mobile_search_img_height']}',
                de_bank_use                   = '{$_POST['de_bank_use']}',
                de_bank_account               = '{$_POST['de_bank_account']}',
                de_card_test                  = '{$_POST['de_card_test']}',
                de_card_use                   = '{$_POST['de_card_use']}',
                de_card_noint_use             = '{$_POST['de_card_noint_use']}',
                de_card_point                 = '{$_POST['de_card_point']}',
                de_settle_min_point           = '{$_POST['de_settle_min_point']}',
                de_settle_max_point           = '{$_POST['de_settle_max_point']}',
                de_settle_point_unit          = '{$_POST['de_settle_point_unit']}',
                de_point_days                 = '{$_POST['de_point_days']}',
                de_simg_width                 = '{$_POST['de_simg_width']}',
                de_simg_height                = '{$_POST['de_simg_height']}',
                de_mimg_width                 = '{$_POST['de_mimg_width']}',
                de_mimg_height                = '{$_POST['de_mimg_height']}',
                de_pg_service                 = '{$_POST['de_pg_service']}',
                de_kcp_mid                    = '{$_POST['de_kcp_mid']}',
                de_kcp_site_key               = '{$_POST['de_kcp_site_key']}',
                de_inicis_mid                 = '{$_POST['de_inicis_mid']}',
                de_inicis_admin_key           = '{$_POST['de_inicis_admin_key']}',
                de_iche_use                   = '{$_POST['de_iche_use']}',
                de_sms_cont1                  = '{$_POST['de_sms_cont1']}',
                de_sms_cont2                  = '{$_POST['de_sms_cont2']}',
                de_sms_cont3                  = '{$_POST['de_sms_cont3']}',
                de_sms_cont4                  = '{$_POST['de_sms_cont4']}',
                de_sms_use1                   = '{$_POST['de_sms_use1']}',
                de_sms_use2                   = '{$_POST['de_sms_use2']}',
                de_sms_use3                   = '{$_POST['de_sms_use3']}',
                de_sms_use4                   = '{$_POST['de_sms_use4']}',
                de_sms_hp                     = '{$_POST['de_sms_hp']}',
                de_item_use_use               = '{$_POST['de_item_use_use']}',
                de_item_use_write             = '{$_POST['de_item_use_write']}',
                de_code_dup_use               = '{$_POST['de_code_dup_use']}',
                de_cart_keep_term             = '{$_POST['de_cart_keep_term']}',
                de_admin_buga_no              = '{$_POST['de_admin_buga_no']}',
                de_vbank_use                  = '{$_POST['de_vbank_use']}',
                de_cash_use                   = '{$_POST['de_cash_use']}',
                de_cash_charge_use            = '{$_POST['de_cash_charge_use']}',
                de_cash_charge_price          = '{$_POST['de_cash_charge_price']}',
                de_taxsave_use                = '{$_POST['de_taxsave_use']}',
                de_hp_use                     = '{$_POST['de_hp_use']}',
                de_member_reg_coupon_use      = '{$_POST['de_member_reg_coupon_use']}',
                de_member_reg_coupon_term     = '{$_POST['de_member_reg_coupon_term']}',
                de_member_reg_coupon_price    = '{$_POST['de_member_reg_coupon_price']}',
                de_member_reg_coupon_minimum  = '{$_POST['de_member_reg_coupon_minimum']}'
                ";
sql_query($sql);

// 환경설정 > 포인트 사용
sql_query(" update {$g5['config_table']} set cf_use_point = '{$_POST['cf_use_point']}' ");

// LG, 아이코드 설정
$sql = " update {$g5['config_table']}
            set cf_sms_use              = '{$_POST['cf_sms_use']}',
                cf_icode_id             = '{$_POST['cf_icode_id']}',
                cf_icode_pw             = '{$_POST['cf_icode_pw']}',
                cf_icode_server_ip      = '{$_POST['cf_icode_server_ip']}',
                cf_icode_server_port    = '{$_POST['cf_icode_server_port']}',
                cf_lg_mid               = '{$_POST['cf_lg_mid']}',
                cf_lg_mert_key          = '{$_POST['cf_lg_mert_key']}' ";
sql_query($sql);

goto_url("./configform.php");
?>
