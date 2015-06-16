<?php
set_time_limit(0);
include_once('./_common.php');

// Clears the cache and prevent unwanted output
ob_clean();
@ini_set('error_reporting', E_ALL & ~ E_NOTICE);
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 'Off');

// Referer 체크
check_referer();

if($is_guest)
    die('정상적인 방법으로 이용해 주십시오.');

// 주문정보
$od = sql_fetch(" select * from {$g5['g5_contents_order_table']} where od_id = '$od_id' and mb_id = '{$member['mb_id']}' ");
if(!$od)
    die('주문 정보가 존재하지 않습니다.');

// 주문상태가 입금이 아니면 다운로드 불가
if($od['od_status'] != '입금')
    die('입금 완료된 주문에 한해 동영상보기가 가능합니다.');

$sql = " select a.ct_id, a.od_id, a.mb_id, a.ct_status, a.it_id, a.io_id, a.ct_time, a.ct_ip, b.io_file, b.io_source, b.io_download, b.io_support
            from {$g5['g5_contents_cart_table']} a left join {$g5['g5_contents_item_option_table']} b
              on ( a.it_id = b.it_id and a.io_id = b.io_id )
            where a.od_id = '$od_id'
              and a.ct_id = '$ct_id'
              and a.mb_id = '{$member['mb_id']}' ";
$row = sql_fetch($sql);

if(!$row)
    die('주문 상세정보가 존재하지 않습니다.');

// 세션의 uid 체크
$uid = md5($row['ct_id'].$row['ct_time'].$row['ct_ip']);
if(get_session('ss_contents_'.$row['ct_id'].'_uid') != $uid)
    die('잘못된 접근입니다.');

// 상태가 입금이 아니면 다운로드 불가
if($row['ct_status'] != '입금')
    die('입금 완료된 주문에 한해 동영상보기가 가능합니다.');

// 다운로드 가능한지 체크
if(!$row['io_download'])
    die('이 항목은 동영상보기 불가 상태입니다.\\n사이트 운영자에게 문의해 주십시오.');


$file = G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$row['it_id'].'/'.$row['io_file']; // The media file's location
if(!is_file($file))
    die('파일이 존재하지 않습니다. 사이트 운영자에게 문의해 주십시오.');

if(G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/moviefile.php');
    return;
}


// Stream videos to HTML5 video container using HTTP & PHP
// http://licson.net/post/stream-videos-php/

$mime = "application/octet-stream"; // The MIME type of the file, this should be replaced with your own.
$size = filesize($file); // The size of the file

// Send the content type header
header('Content-type: ' . $mime);

// Check if it's a HTTP range request
if(isset($_SERVER['HTTP_RANGE'])){
    // Parse the range header to get the byte offset
    $ranges = array_map(
        'intval', // Parse the parts into integer
        explode(
            '-', // The range separator
            substr($_SERVER['HTTP_RANGE'], 6) // Skip the `bytes=` part of the header
        )
    );

    // If the last range param is empty, it means the EOF (End of File)
    if(!$ranges[1]){
        $ranges[1] = $size - 1;
    }

    // Send the appropriate headers
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    // Content-Length 가 맞지 않는 오류가 발생하여 주석처리 후 아래 코드로 대체
    //header('Content-Length: ' . ($ranges[1] - $ranges[0])); // The size of the range
    header('Content-Length: ' . $size);

    // Send the ranges we offered
    header(
        sprintf(
            'Content-Range: bytes %d-%d/%d', // The header format
            $ranges[0], // The start range
            $ranges[1], // The end range
            $size // Total size of the file
        )
    );

    // It's time to output the file
    $f = fopen($file, 'rb'); // Open the file in binary mode
    $chunkSize = 8192; // The size of each chunk to output

    // Seek to the requested start range
    fseek($f, $ranges[0]);

    // Start outputting the data
    while(true){
        // Check if we have outputted all the data requested
        if(ftell($f) >= $ranges[1]){
            break;
        }

        // Output the data
        echo fread($f, $chunkSize);

        // Flush the buffer immediately
        @ob_flush();
        flush();
    }
} else {
    // It's not a range request, output the file anyway
    header('Content-Length: ' . $size);

    // Read the file
    @readfile($file);

    // and flush the buffer
    @ob_flush();
    flush();
}
?>