<?php
include_once('./_common.php');

// Referer 체크
check_referer();

$g5['title'] = '동영상보기';
include_once(G5_PATH.'/head.sub.php');

if(G5_IS_MOBILE)
    alert_close('모바일 기기에서는 동영상보기를 지원하지 않습니다.');

if($is_guest)
    alert_close('정상적인 방법으로 이용해 주십시오.');

// 주문정보
$od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' and mb_id = '{$member['mb_id']}' ");
if(!$od)
    alert_close('주문 정보가 존재하지 않습니다.');

// 주문상태가 입금이 아니면 다운로드 불가
if($od['od_status'] != '입금')
    alert_close('입금 완료된 주문에 한해 동영상보기가 가능합니다.');

$sql = " select a.ct_id, a.od_id, a.mb_id, a.ct_status, a.it_id, a.io_id, a.ct_time, a.ct_ip, b.io_type, b.io_file, b.io_source, b.io_download, b.io_support
            from {$g5['g5_contents_cart_table']} a left join {$g5['g5_contents_item_option_table']} b
              on ( a.it_id = b.it_id and a.io_id = b.io_id )
            where a.od_id = '$od_id'
              and a.ct_id = '$ct_id'
              and a.mb_id = '{$member['mb_id']}' ";
$row = sql_fetch($sql);

if(!$row)
    alert_close('주문 상세정보가 존재하지 않습니다.');

// 세션의 uid 체크
$uid = md5($row['ct_id'].$row['ct_time'].$row['ct_ip']);
if(get_session('ss_contents_'.$row['ct_id'].'_uid') != $uid)
    alert_close('잘못된 접근입니다.');

// 상태가 입금이 아니면 다운로드 불가
if($row['ct_status'] != '입금')
    alert_close('입금 완료된 주문에 한해 동영상보기가 가능합니다.');

// 다운로드 가능한지 체크
if(!$row['io_download'])
    alert_close('이 항목은 동영상보기 불가 상태입니다.\\n사이트 운영자에게 문의해 주십시오.');

// 스킨
$skin = G5_CONTENTS_SKIN_PATH.'/'.$setting['de_movie_skin'];

if(!is_file($skin))
    die(str_replace(G5_PATH, '', $skin).' 파일이 존재하지 않습니다.');

// 동영상소스
$sql = " select it_contents_type from {$g5['g5_contents_item_table']} where it_id = '{$row['it_id']}' ";
$it = sql_fetch($sql);

if($it['it_contents_type'] == 2) { // 업로드
    // 파일이 있는지 체크
    $file = G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$row['it_id'].'/'.$row['io_file'];
    if(!is_file($file))
        alert_close('파일이 존재하지 않습니다.\\n사이트 운영자에게 문의해 주십시오.');

    $url = G5_CONTENTS_URL.'/moviefile.php?od_id='.$row['od_id'].'&ct_id='.$row['ct_id'];
} else if($it['it_contents_type'] == 3) { // 외부링크
    $url = trim($row['io_file']);

    if(!$url)
        alert_close('동영상 정보가 존재하지 않습니다.\\n사이트 운영자에게 문의해 주십시오.');
} else {
    alert_close('올바른 방법으로 이용해 주십시오.');
}

// 다운로드 수 반영
update_download_count($row['od_id'], $row['ct_id']);

$type = ' type="video/'.$row['io_type'].'"';

$video_source = '<source';
if($type)
    $video_source .= $type;
$video_source .= ' src="'.$url.'" />'.PHP_EOL;

include_once($skin);

include_once(G5_PATH.'/tail.sub.php');
?>