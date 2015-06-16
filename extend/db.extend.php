<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// inicis 필드 추가
if(!isset($setting['de_inicis_mid'])) {
    sql_query(" ALTER TABLE `{$g5['g5_contents_default_table']}`
                    ADD `de_inicis_mid` varchar(255) NOT NULL DEFAULT '' AFTER `de_kcp_site_key`,
                    ADD `de_inicis_admin_key` varchar(255) NOT NULL DEFAULT '' AFTER `de_inicis_mid` ", true);
}

// chub mid 필드 추가
if(!isset($setting['de_chub_mid'])) {
    sql_query(" ALTER TABLE `{$g5['g5_contents_default_table']}`
                    ADD `de_chub_mid` varchar(255) NOT NULL DEFAULT '' AFTER `de_admin_info_email` ", true);
}

// item 테이블에 컨텐츠허브 관련 필드 추가
if(!sql_query(" select it_chub_ca_id from {$g5['g5_contents_item_table']} limit 1 ", false)) {
    sql_query(" ALTER TABLE `{$g5['g5_contents_item_table']}`
                    ADD `it_chub_ca_id` varchar(255) NOT NULL DEFAULT '' AFTER `it_nocoupon`,
                    ADD `it_chub_tag` varchar(255) NOT NULL DEFAULT '' AFTER `it_chub_ca_id`,
                    ADD `it_chub_explan` varchar(255) NOT NULL DEFAULT '' AFTER `it_chub_tag`", true);
}


// 레이아웃 파일 필드 추가
if(!isset($setting['de_include_index'])) {
    sql_query(" ALTER TABLE `{$g5['g5_contents_default_table']}`
                    ADD `de_include_index` varchar(255) NOT NULL DEFAULT '' AFTER `de_admin_info_email`,
                    ADD `de_include_head` varchar(255) NOT NULL DEFAULT '' AFTER `de_include_index`,
                    ADD `de_include_tail` varchar(255) NOT NULL DEFAULT '' AFTER `de_include_head` ", true);

}
?>