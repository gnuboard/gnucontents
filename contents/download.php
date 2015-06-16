<?php
set_time_limit(0);
include_once('./_common.php');

// clean the output buffer
ob_end_clean();

// Referer 체크
check_referer();

if($is_guest)
    alert('정상적인 방법으로 이용해 주십시오.', G5_CONTENTS_URL);

// 주문정보
$od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' and mb_id = '{$member['mb_id']}' ");
if(!$od)
    alert('주문 정보가 존재하지 않습니다.', G5_CONTENTS_URL);

// 주문상태가 입금이 아니면 다운로드 불가
if($od['od_status'] != '입금')
    alert('입금 완료된 주문에 한해 다운로드 가능합니다.', G5_CONTENTS_URL);

$sql = " select a.ct_id, a.od_id, a.mb_id, a.ct_status, a.it_id, a.io_id, a.ct_time, a.ct_ip, b.io_file, b.io_source, b.io_download, b.io_support
            from {$g5['g5_contents_cart_table']} a left join {$g5['g5_contents_item_option_table']} b
              on ( a.it_id = b.it_id and a.io_id = b.io_id )
            where a.od_id = '$od_id'
              and a.ct_id = '$ct_id'
              and a.mb_id = '{$member['mb_id']}' ";
$row = sql_fetch($sql);

if(!$row)
    alert('주문 상세정보가 존재하지 않습니다.', G5_CONTENTS_URL);

// 세션의 uid 체크
$uid = md5($row['ct_id'].$row['ct_time'].$row['ct_ip']);
if(get_session('ss_contents_'.$row['ct_id'].'_uid') != $uid)
    alert('잘못된 접근입니다.', G5_CONTENTS_URL);

// 상태가 입금이 아니면 다운로드 불가
if($row['ct_status'] != '입금')
    alert('입금 완료된 주문에 한해 다운로드 가능합니다.', G5_CONTENTS_URL);

// 다운로드 가능한지 체크
if(!$row['io_download'])
    alert('이 항목은 다운로드 불가 상태입니다.\\n사이트 운영자에게 문의해 주십시오.', G5_CONTENTS_URL);

// 컨텐츠 유형에 따른 처리
$sql = " select it_contents_type from {$g5['g5_contents_item_table']} where it_id = '{$row['it_id']}' ";
$it = sql_fetch($sql);

if($it['it_contents_type'] == 0 || $it['it_contents_type'] == 1) // 로컬파일
{
    // 파일이 있는지 체크
    $file = G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$row['it_id'].'/'.$row['io_file'];
    if(!is_file($file))
        alert('파일이 존재하지 않습니다.\\n사이트 운영자에게 문의해 주십시오.', G5_CONTENTS_URL);

    // 다운로드 수 반영
    update_download_count($row['od_id'], $row['ct_id']);

    // 파일 다운로드
    $filename = urlencode($row['io_source']);
    if(preg_match("/msie/i", $_SERVER['HTTP_USER_AGENT']) && preg_match("/5\.5/", $_SERVER['HTTP_USER_AGENT'])) {
        header("content-type: doesn/matter");
        header("content-length: ".filesize("$file"));
        header("content-disposition: attachment; filename=\"$filename\"");
        header("content-transfer-encoding: binary");
    } else {
        header("content-type: file/unknown");
        header("content-length: ".filesize("$file"));
        header("content-disposition: attachment; filename=\"$filename\"");
        header("content-description: php generated data");
    }
    header("pragma: no-cache");
    header("expires: 0");
    flush();

    $fp = fopen($file, 'rb');

    $download_rate = 10;

    while(!feof($fp)) {
        //echo fread($fp, 100*1024);
        /*
        echo fread($fp, 100*1024);
        flush();
        */

        print fread($fp, round($download_rate * 1024));
        flush();
        usleep(1000);
    }
    fclose ($fp);
    flush();
}
else if($it['it_contents_type'] == 4 || $it['it_contents_type'] == 5) // 외부링크
{
    $io_file = trim($row['io_file']);
    if(!$io_file)
        alert('파일정보가 존재하지 않습니다.\\n사이트 운영자에게 문의해 주십시오.', G5_CONTENTS_URL);

    $file_url = set_http($io_file);

    // 원격지에 파일이 존재하는지 체크
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $file_url);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    curl_exec ($ch);

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($http_code == 200) {
        // 다운로드 수 반영
        update_download_count($row['od_id'], $row['ct_id']);

        // 파일 다운로드
        $filename = urlencode($row['io_source']);
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-type: application/octet-stream");
        header("Content-Transfer-Encoding: binary");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $file_url);

        $file = curl_exec ($ch);
        curl_close($ch);
    } else {
        alert('파일을 다운로드할 수 없습니다.\\n사이트 운영자에게 문의해 주십시오.', G5_CONTENTS_URL);
    }
}
?>
