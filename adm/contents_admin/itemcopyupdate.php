<?php
$sub_menu = '600400';
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

if ($is_admin != "super")
    alert("최고관리자만 접근 가능합니다.");

if (!trim($it_id))
	alert("복사할 상품코드가 없습니다.");

$t_it_id = preg_replace("/[A-Za-z0-9\-_]/", "", $new_it_id);
if($t_it_id)
    alert("상품코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.");

$row = sql_fetch(" select count(*) as cnt from {$g5['g5_contents_item_table']} where it_id = '$new_it_id' ");
if ($row['cnt'])
    alert('이미 존재하는 상품코드 입니다.');

$sql = " select * from {$g5['g5_contents_item_table']} where it_id = '$it_id' limit 1 ";
$cp = sql_fetch($sql);


// 상품테이블의 필드가 추가되어도 수정하지 않도록 필드명을 추출하여 insert 퀴리를 생성한다. (상품코드만 새로운것으로 대체)
$sql_common = "";
$fields = mysql_list_fields(G5_MYSQL_DB, $g5['g5_contents_item_table']);
$columns = mysql_num_fields($fields);
for ($i = 0; $i < $columns; $i++) {
    $fld = mysql_field_name($fields, $i);
    if ($fld == 'it_id' || $fld == 'it_sum_qty' || $fld == 'it_use_cnt' || $fld == 'it_use_avg')
        continue;

    $sql_common .= " , $fld = '".addslashes($cp[$fld])."' ";
}

$sql = " insert {$g5['g5_contents_item_table']}
			set it_id = '$new_it_id'
                $sql_common ";
sql_query($sql);

// 선택옵션 copy
$sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' order by io_no asc ";
$result = sql_query($sql);

for($i=0; $row=sql_fetch_array($result); $i++) {
    $io_id = md5($new_it_id.'-'.$i);

    // 파일복사
    $io_file = trim($row['io_file']);

    if($io_file) {
        $file = G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$it_id.'/'.$io_file;

        if(is_file($file)) {
            $data_dir = G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$new_it_id;
            @mkdir($data_dir, G5_DIR_PERMISSION);
            @chmod($data_dir, G5_DIR_PERMISSION);

            $destfile = $data_dir.'/'.$io_file;
            copy($file, $destfile);
            @chmod($destfile, G5_FILE_PERMISSION);
        }
    }

    $sql2 = " insert into {$g5['g5_contents_item_option_table']}
                set io_name     = '{$row['io_name']}',
                    it_id       = '$new_it_id',
                    io_id       = '$io_id',
                    io_file     = '$io_file',
                    io_source   = '{$row['io_source']}',
                    io_filesize = '{$row['io_filesize']}',
                    io_price    = '{$row['io_price']}',
                    io_download = '{$row['io_download']}',
                    io_support  = '{$row['io_support']}',
                    io_use      = '{$row['io_use']}' ";
    sql_query($sql2);
}

// html 에디터로 첨부된 이미지 파일 복사
if($cp['it_explan']) {
    $matchs = get_editor_image($cp['it_explan'], false);

    // 파일의 경로를 얻어 복사
    for($i=0;$i<count($matchs[1]);$i++) {
        $p = parse_url($matchs[1][$i]);
        if(strpos($p['path'], "/data/") != 0)
            $src_path = preg_replace("/^\/.*\/data/", "/data", $p['path']);
        else
            $src_path = $p['path'];

        $srcfile = G5_PATH.$src_path;
        $dstfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $srcfile);

        if(is_file($srcfile)) {
            copy($srcfile, $dstfile);

            $newfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $matchs[1][$i]);
            $cp['it_explan'] = str_replace($matchs[1][$i], $newfile, $cp['it_explan']);
        }
    }

    $sql = " update {$g5['g5_contents_item_table']} set it_explan = '".addslashes($cp['it_explan'])."' where it_id = '$new_it_id' ";
    sql_query($sql);
}

if($cp['it_mobile_explan']) {
    $matchs = get_editor_image($cp['it_mobile_explan'], false);

    // 파일의 경로를 얻어 복사
    for($i=0;$i<count($matchs[1]);$i++) {
        $p = parse_url($matchs[1][$i]);
        if(strpos($p['path'], "/data/") != 0)
            $src_path = preg_replace("/^\/.*\/data/", "/data", $p['path']);
        else
            $src_path = $p['path'];

        $srcfile = G5_PATH.$src_path;
        $dstfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $srcfile);

        if(is_file($srcfile)) {
            copy($srcfile, $dstfile);

            $newfile = preg_replace("/\.([^\.]+)$/", "_".$new_it_id.".\\1", $matchs[1][$i]);
            $cp['it_mobile_explan'] = str_replace($matchs[1][$i], $newfile, $cp['it_mobile_explan']);
        }
    }

    $sql = " update {$g5['g5_contents_item_table']} set it_mobile_explan = '".addslashes($cp['it_mobile_explan'])."' where it_id = '$new_it_id' ";
    sql_query($sql);
}

// 상품이미지 복사
function copy_directory($src_dir, $dest_dir)
{
    if($src_dir == $dest_dir)
        return false;

    if(!is_dir($src_dir))
        return false;

    if(!is_dir($dest_dir)) {
        @mkdir($dest_dir, G5_DIR_PERMISSION);
        @chmod($dest_dir, G5_DIR_PERMISSION);
    }

    $dir = opendir($src_dir);
    while (false !== ($filename = readdir($dir))) {
        if($filename == "." || $filename == "..")
            continue;

        $files[] = $filename;
    }

    for($i=0; $i<count($files); $i++) {
        $src_file = $src_dir.'/'.$files[$i];
        $dest_file = $dest_dir.'/'.$files[$i];
        if(is_file($src_file)) {
            copy($src_file, $dest_file);
            @chmod($dest_file, G5_FILE_PERMISSION);
        }
    }
}

// 파일복사
$dest_path = G5_DATA_PATH.'/cmitem/'.$new_it_id;
@mkdir($dest_path, G5_DIR_PERMISSION);
@chmod($dest_path, G5_DIR_PERMISSION);
$comma = '';
$sql_img = '';

for($i=1; $i<=10; $i++) {
    $file = G5_DATA_PATH.'/cmitem/'.$cp['it_img'.$i];
    $new_img = '';

    if(is_file($file)) {
        $dstfile = $dest_path.'/'.basename($file);
        copy($file, $dstfile);
        @chmod($dstfile, G5_FILE_PERMISSION);
        $new_img = $new_it_id.'/'.basename($file);
    }

    $sql_img .= $comma." it_img{$i} = '$new_img' ";
    $comma = ',';
}

$sql = " update {$g5['g5_contents_item_table']}
            set $sql_img
            where it_id = '$new_it_id' ";
sql_query($sql);

$qstr = "ca_id=$ca_id&amp;sfl=$sfl&amp;sca=$sca&amp;page=$page&amp;stx=".urlencode($stx)."&amp;save_stx=".urlencode($save_stx);

goto_url("itemlist.php?$qstr");
?>