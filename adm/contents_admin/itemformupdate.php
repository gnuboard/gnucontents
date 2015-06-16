<?php
$sub_menu = '600400';
include_once('./_common.php');

if ($w == "u" || $w == "d")
    check_demo();

if ($w == '' || $w == 'u')
    auth_check($auth[$sub_menu], "w");
else if ($w == 'd')
    auth_check($auth[$sub_menu], "d");

$upload_max_filesize = ini_get('upload_max_filesize');

if (empty($_POST)) {
    alert("파일 또는 글내용의 크기가 서버에서 설정한 값을 넘어 오류가 발생하였습니다.\\npost_max_size=".ini_get('post_max_size')." , upload_max_filesize=".$upload_max_filesize."\\n게시판관리자 또는 서버관리자에게 문의 바랍니다.");
}

@mkdir(G5_DATA_PATH."/cmitem", G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH."/cmitem", G5_DIR_PERMISSION);
@mkdir(G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR, G5_DIR_PERMISSION);
@chmod(G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR, G5_DIR_PERMISSION);

// input vars 체크
check_input_vars();

// 등록일 때 옵션 입력 체크
$option_count = count($_POST['io_name']);
$io_name_count = 0;
for($i=0; $i<$option_count; $i++) {
    if(trim($_POST['io_name'][$i]))
        $io_name_count++;
}

if(!$io_name_count)
    alert("상품옵션을 하나이상 입력해 주십시오.");

// 파일정보
if($w == "u") {
    $sql = " select it_img1, it_img2, it_img3, it_img4, it_img5, it_img6, it_img7, it_img8, it_img9, it_img10
                from {$g5['g5_contents_item_table']}
                where it_id = '$it_id' ";
    $file = sql_fetch($sql);

    $it_img1    = $file['it_img1'];
    $it_img2    = $file['it_img2'];
    $it_img3    = $file['it_img3'];
    $it_img4    = $file['it_img4'];
    $it_img5    = $file['it_img5'];
    $it_img6    = $file['it_img6'];
    $it_img7    = $file['it_img7'];
    $it_img8    = $file['it_img8'];
    $it_img9    = $file['it_img9'];
    $it_img10   = $file['it_img10'];
}

$it_img_dir = G5_DATA_PATH.'/cmitem';

// 파일삭제
if ($it_img1_del) {
    $file_img1 = $it_img_dir.'/'.$it_img1;
    @unlink($file_img1);
    cm_delete_item_thumbnail(dirname($file_img1), basename($file_img1));
    $it_img1 = '';
}
if ($it_img2_del) {
    $file_img2 = $it_img_dir.'/'.$it_img2;
    @unlink($file_img2);
    cm_delete_item_thumbnail(dirname($file_img2), basename($file_img2));
    $it_img2 = '';
}
if ($it_img3_del) {
    $file_img3 = $it_img_dir.'/'.$it_img3;
    @unlink($file_img3);
    cm_delete_item_thumbnail(dirname($file_img3), basename($file_img3));
    $it_img3 = '';
}
if ($it_img4_del) {
    $file_img4 = $it_img_dir.'/'.$it_img4;
    @unlink($file_img4);
    cm_delete_item_thumbnail(dirname($file_img4), basename($file_img4));
    $it_img4 = '';
}
if ($it_img5_del) {
    $file_img5 = $it_img_dir.'/'.$it_img5;
    @unlink($file_img5);
    cm_delete_item_thumbnail(dirname($file_img5), basename($file_img5));
    $it_img5 = '';
}
if ($it_img6_del) {
    $file_img6 = $it_img_dir.'/'.$it_img6;
    @unlink($file_img6);
    cm_delete_item_thumbnail(dirname($file_img6), basename($file_img6));
    $it_img6 = '';
}
if ($it_img7_del) {
    $file_img7 = $it_img_dir.'/'.$it_img7;
    @unlink($file_img7);
    cm_delete_item_thumbnail(dirname($file_img7), basename($file_img7));
    $it_img7 = '';
}
if ($it_img8_del) {
    $file_img8 = $it_img_dir.'/'.$it_img8;
    @unlink($file_img8);
    cm_delete_item_thumbnail(dirname($file_img8), basename($file_img8));
    $it_img8 = '';
}
if ($it_img9_del) {
    $file_img9 = $it_img_dir.'/'.$it_img9;
    @unlink($file_img9);
    cm_delete_item_thumbnail(dirname($file_img9), basename($file_img9));
    $it_img9 = '';
}
if ($it_img10_del) {
    $file_img10 = $it_img_dir.'/'.$it_img10;
    @unlink($file_img10);
    cm_delete_item_thumbnail(dirname($file_img10), basename($file_img10));
    $it_img10 = '';
}

// 이미지업로드
if ($_FILES['it_img1']['name']) {
    if($w == 'u' && $it_img1) {
        $file_img1 = $it_img_dir.'/'.$it_img1;
        @unlink($file_img1);
        cm_delete_item_thumbnail(dirname($file_img1), basename($file_img1));
    }
    $it_img1 = cm_it_img_upload($_FILES['it_img1']['tmp_name'], $_FILES['it_img1']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img2']['name']) {
    if($w == 'u' && $it_img2) {
        $file_img2 = $it_img_dir.'/'.$it_img2;
        @unlink($file_img2);
        cm_delete_item_thumbnail(dirname($file_img2), basename($file_img2));
    }
    $it_img2 = cm_it_img_upload($_FILES['it_img2']['tmp_name'], $_FILES['it_img2']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img3']['name']) {
    if($w == 'u' && $it_img3) {
        $file_img3 = $it_img_dir.'/'.$it_img3;
        @unlink($file_img3);
        cm_delete_item_thumbnail(dirname($file_img3), basename($file_img3));
    }
    $it_img3 = cm_it_img_upload($_FILES['it_img3']['tmp_name'], $_FILES['it_img3']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img4']['name']) {
    if($w == 'u' && $it_img4) {
        $file_img4 = $it_img_dir.'/'.$it_img4;
        @unlink($file_img4);
        cm_delete_item_thumbnail(dirname($file_img4), basename($file_img4));
    }
    $it_img4 = cm_it_img_upload($_FILES['it_img4']['tmp_name'], $_FILES['it_img4']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img5']['name']) {
    if($w == 'u' && $it_img5) {
        $file_img5 = $it_img_dir.'/'.$it_img5;
        @unlink($file_img5);
        cm_delete_item_thumbnail(dirname($file_img5), basename($file_img5));
    }
    $it_img5 = cm_it_img_upload($_FILES['it_img5']['tmp_name'], $_FILES['it_img5']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img6']['name']) {
    if($w == 'u' && $it_img6) {
        $file_img6 = $it_img_dir.'/'.$it_img6;
        @unlink($file_img6);
        cm_delete_item_thumbnail(dirname($file_img6), basename($file_img6));
    }
    $it_img6 = cm_it_img_upload($_FILES['it_img6']['tmp_name'], $_FILES['it_img6']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img7']['name']) {
    if($w == 'u' && $it_img7) {
        $file_img7 = $it_img_dir.'/'.$it_img7;
        @unlink($file_img7);
        cm_delete_item_thumbnail(dirname($file_img7), basename($file_img7));
    }
    $it_img7 = cm_it_img_upload($_FILES['it_img7']['tmp_name'], $_FILES['it_img7']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img8']['name']) {
    if($w == 'u' && $it_img8) {
        $file_img8 = $it_img_dir.'/'.$it_img8;
        @unlink($file_img8);
        cm_delete_item_thumbnail(dirname($file_img8), basename($file_img8));
    }
    $it_img8 = cm_it_img_upload($_FILES['it_img8']['tmp_name'], $_FILES['it_img8']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img9']['name']) {
    if($w == 'u' && $it_img9) {
        $file_img9 = $it_img_dir.'/'.$it_img9;
        @unlink($file_img9);
        cm_delete_item_thumbnail(dirname($file_img9), basename($file_img9));
    }
    $it_img9 = cm_it_img_upload($_FILES['it_img9']['tmp_name'], $_FILES['it_img9']['name'], $it_img_dir.'/'.$it_id);
}
if ($_FILES['it_img10']['name']) {
    if($w == 'u' && $it_img10) {
        $file_img10 = $it_img_dir.'/'.$it_img10;
        @unlink($file_img10);
        cm_delete_item_thumbnail(dirname($file_img10), basename($file_img10));
    }
    $it_img10 = cm_it_img_upload($_FILES['it_img10']['tmp_name'], $_FILES['it_img10']['name'], $it_img_dir.'/'.$it_id);
}

if ($w == "" || $w == "u")
{
    // 다음 입력을 위해서 옵션값을 쿠키로 한달동안 저장함
    @set_cookie("ck_ca_id", $ca_id, time() + 86400*31);
    @set_cookie("ck_ca_id2", $ca_id2, time() + 86400*31);
    @set_cookie("ck_ca_id3", $ca_id3, time() + 86400*31);
}

// 관련상품을 우선 삭제함
sql_query(" delete from {$g5['g5_contents_item_relation_table']} where it_id = '$it_id' ");

// 관련상품의 반대도 삭제
sql_query(" delete from {$g5['g5_contents_item_relation_table']} where it_id2 = '$it_id' ");

// 이벤트상품을 우선 삭제함
sql_query(" delete from {$g5['g5_contents_event_item_table']} where it_id = '$it_id' ");

// 포인트 비율 값 체크
if($it_point_type == 1 && $it_point > 99)
    alert("포인트 비율을 0과 99 사이의 값으로 입력해 주십시오.");

$it_name = strip_tags(trim($_POST['it_name']));
if ($it_name == "")
    alert("상품명을 입력해 주십시오.");

$sql_common = " ca_id               = '$ca_id',
                ca_id2              = '$ca_id2',
                ca_id3              = '$ca_id3',
                it_skin             = '$it_skin',
                it_mobile_skin      = '$it_mobile_skin',
                it_name             = '$it_name',
                it_contents_type    = '$it_contents_type',
                it_type1            = '$it_type1',
                it_type2            = '$it_type2',
                it_type3            = '$it_type3',
                it_type4            = '$it_type4',
                it_basic            = '$it_basic',
                it_user_demo        = '$it_user_demo',
                it_admin_demo       = '$it_admin_demo',
                it_info1_subj       = '$it_info1_subj',
                it_info2_subj       = '$it_info2_subj',
                it_info3_subj       = '$it_info3_subj',
                it_info4_subj       = '$it_info4_subj',
                it_info5_subj       = '$it_info5_subj',
                it_info1            = '$it_info1',
                it_info2            = '$it_info2',
                it_info3            = '$it_info3',
                it_info4            = '$it_info4',
                it_info5            = '$it_info5',
                it_explan           = '$it_explan',
                it_explan2          = '".strip_tags(trim($_POST['it_explan']))."',
                it_mobile_explan    = '$it_mobile_explan',
                it_price            = '$it_price',
                it_point            = '$it_point',
                it_point_type       = '$it_point_type',
                it_sell_email       = '$it_sell_email',
                it_use              = '$it_use',
                it_nocoupon         = '$it_nocoupon',
                it_chub_ca_id       = '$it_chub_ca_id',
                it_chub_tag         = '$it_chub_tag',
                it_chub_explan      = '$it_chub_explan',
                it_head_html        = '$it_head_html',
                it_tail_html        = '$it_tail_html',
                it_mobile_head_html = '$it_mobile_head_html',
                it_mobile_tail_html = '$it_mobile_tail_html',
                it_ip               = '{$_SERVER['REMOTE_ADDR']}',
                it_order            = '$it_order',
                it_tel_inq          = '$it_tel_inq',
                it_img1             = '$it_img1',
                it_img2             = '$it_img2',
                it_img3             = '$it_img3',
                it_img4             = '$it_img4',
                it_img5             = '$it_img5',
                it_img6             = '$it_img6',
                it_img7             = '$it_img7',
                it_img8             = '$it_img8',
                it_img9             = '$it_img9',
                it_img10            = '$it_img10',
                it_1_subj           = '$it_1_subj',
                it_2_subj           = '$it_2_subj',
                it_3_subj           = '$it_3_subj',
                it_4_subj           = '$it_4_subj',
                it_5_subj           = '$it_5_subj',
                it_6_subj           = '$it_6_subj',
                it_7_subj           = '$it_7_subj',
                it_8_subj           = '$it_8_subj',
                it_9_subj           = '$it_9_subj',
                it_10_subj          = '$it_10_subj',
                it_1                = '$it_1',
                it_2                = '$it_2',
                it_3                = '$it_3',
                it_4                = '$it_4',
                it_5                = '$it_5',
                it_6                = '$it_6',
                it_7                = '$it_7',
                it_8                = '$it_8',
                it_9                = '$it_9',
                it_10               = '$it_10'
                ";

if ($w == "")
{
    $it_id = $_POST['it_id'];

    if (!trim($it_id)) {
        alert('상품 코드가 없으므로 상품을 추가하실 수 없습니다.');
    }

    $t_it_id = preg_replace("/[A-Za-z0-9\-_]/", "", $it_id);
    if($t_it_id)
        alert('상품 코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.');

    $sql_common .= " , it_time = '".G5_TIME_YMDHIS."' ";
    $sql_common .= " , it_update_time = '".G5_TIME_YMDHIS."' ";
    $sql = " insert {$g5['g5_contents_item_table']}
                set it_id = '$it_id',
					$sql_common	";
    sql_query($sql);
}
else if ($w == "u")
{
    $sql_common .= " , it_update_time = '".G5_TIME_YMDHIS."' ";
    $sql = " update {$g5['g5_contents_item_table']}
                set $sql_common
              where it_id = '$it_id' ";
    sql_query($sql);
}

if ($w == "" || $w == "u")
{
    // 관련상품 등록
    $it_id2 = explode(",", $it_list);
    for ($i=0; $i<count($it_id2); $i++)
    {
        if (trim($it_id2[$i]))
        {
            $sql = " insert into {$g5['g5_contents_item_relation_table']}
                        set it_id  = '$it_id',
                            it_id2 = '$it_id2[$i]',
                            ir_no = '$i' ";
            sql_query($sql, false);

            // 관련상품의 반대로도 등록
            $sql = " insert into {$g5['g5_contents_item_relation_table']}
                        set it_id  = '$it_id2[$i]',
                            it_id2 = '$it_id',
                            ir_no = '$i' ";
            sql_query($sql, false);
        }
    }

    // 이벤트상품 등록
    $ev_id = explode(",", $ev_list);
    for ($i=0; $i<count($ev_id); $i++)
    {
        if (trim($ev_id[$i]))
        {
            $sql = " insert into {$g5['g5_contents_event_item_table']}
                        set ev_id = '$ev_id[$i]',
                            it_id = '$it_id' ";
            sql_query($sql, false);
        }
    }
}

// 선택옵션등록
$option_count = count($_POST['io_name']);
$comma = '';
$opt_no = 0;

if($option_count) {
    for($i=0; $i<$option_count; $i++) {
        $io_name = trim(strip_tags($_POST['io_name'][$i]));
        if(!$io_name)
            continue;

        $io_file = '';
        if($it_contents_type < 3) {
            $file_upload_msg = '';
            $tmp_file  = $_FILES['io_file']['tmp_name'][$i];
            $filesize  = $_FILES['io_file']['size'][$i];
            $filename  = $_FILES['io_file']['name'][$i];
            $filename  = preg_replace('/(<|>|=)/', '', $filename);

            // 서버에 설정된 값보다 큰파일을 업로드 한다면
            if ($filename) {
                if ($_FILES['io_file']['error'][$i] == 1) {
                    $file_upload_msg .= '\"'.$filename.'\" 파일의 용량이 서버에 설정('.$upload_max_filesize.')된 값보다 크므로 업로드 할 수 없습니다.\\n';
                    continue;
                }
                else if ($_FILES['io_file']['error'][$i] != 0) {
                    $file_upload_msg .= '\"'.$filename.'\" 파일이 정상적으로 업로드 되지 않았습니다.\\n';
                    continue;
                }

                if($w == 'u') { // 기존 파일 삭제
                    $io_id = md5($it_id.'-'.$i);
                    $sql = " select io_file from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' and io_id = '$io_id' ";
                    $row = sql_fetch($sql);

                    if($row['io_file'])
                        @unlink(G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$it_id.'/'.$row['io_file']);
                }

                // 파일 등록
                $io_file = contents_file_upload($it_id, $tmp_file, $filename, $i);
            }
        } else { // 외부링크
            $io_file  = trim(strip_tags($_POST['io_url'][$i]));
            $filename = basename($io_file);
            $filesize = 0;

            // 수정일 때 업로된 파일이 있다면 삭제
            if($w == 'u') {
                $io_id = md5($it_id.'-'.$i);
                $sql = " select io_file from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' and io_id = '$io_id' ";
                $row = sql_fetch($sql);

                if($row['io_file'])
                    @unlink(G5_DATA_PATH.'/'.G5_CONTENTS_SAVE_DIR.'/'.$it_id.'/'.$row['io_file']);
            }
        }

        $sql_common = " io_name         = '$io_name',
                        io_price        = '{$_POST['io_price'][$i]}',
                        io_download     = '{$_POST['io_download'][$i]}',
                        io_support      = '{$_POST['io_support'][$i]}',
                        io_use          = '{$_POST['io_use'][$i]}' ";

        // 파일정보 있으면 반영
        if($io_file) {
            $io_source = $filename;

            $sql_common .= " , io_file     = '$io_file',
                               io_source   = '$io_source',
                               io_filesize = '$filesize' ";
        }

        if($it_contents_type == 2 || $it_contents_type == 3)
            $io_type = $_POST['io_type'][$i];
        else
            $io_type = '';

        $sql_common .= ", io_type = '$io_type' ";

        if($w == '') {
            $io_id = md5($it_id.'-'.$opt_no);
            $sql = " insert into {$g5['g5_contents_item_option_table']}
                        set it_id = '$it_id',
                            io_id = '$io_id',
                        $sql_common ";

            sql_query($sql);
        } else { // 상품수정
            $io_id = md5($it_id.'-'.$i);
            $sql = " select count(*) as cnt from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' and io_id = '$io_id' ";
            $row = sql_fetch($sql);

            if($row['cnt']) {
                $sql = " update {$g5['g5_contents_item_option_table']}
                            set $sql_common
                            where it_id = '$it_id'
                              and io_id = '$io_id' ";
            } else {
                $sql = " insert into {$g5['g5_contents_item_option_table']}
                            set it_id = '$it_id',
                                io_id = '".md5($it_id.'-'.$opt_no)."',
                                $sql_common ";
            }

            sql_query($sql);
        }

        $opt_no++;
    }
}

// 동일 분류내 상품 동일 옵션 적용
$ca_fields = '';
if(is_checked('chk_ca_it_skin'))                $ca_fields .= " , it_skin = '$it_skin' ";
if(is_checked('chk_ca_it_contents_type'))       $ca_fields .= " , it_contents_type = '$it_contents_type' ";
if(is_checked('chk_ca_it_mobile_skin'))         $ca_fields .= " , it_mobile_skin = '$it_mobile_skin' ";
if(is_checked('chk_ca_it_basic'))               $ca_fields .= " , it_basic = '$it_basic' ";
if(is_checked('chk_ca_info1'))                  $ca_fields .= " , it_info1_subj = '$it_info1_subj', it_info1 = '$it_info1' ";
if(is_checked('chk_ca_info2'))                  $ca_fields .= " , it_info2_subj = '$it_info2_subj', it_info2 = '$it_info2' ";
if(is_checked('chk_ca_info3'))                  $ca_fields .= " , it_info3_subj = '$it_info3_subj', it_info3 = '$it_info3' ";
if(is_checked('chk_ca_info4'))                  $ca_fields .= " , it_info4_subj = '$it_info4_subj', it_info4 = '$it_info4' ";
if(is_checked('chk_ca_info5'))                  $ca_fields .= " , it_info5_subj = '$it_info5_subj', it_info5 = '$it_info5' ";
if(is_checked('chk_ca_it_user_demo'))           $ca_fields .= " , it_user_demo = '$it_user_demo' ";
if(is_checked('chk_ca_it_admin_demo'))          $ca_fields .= " , it_admin_demo = '$it_admin_demo' ";
if(is_checked('chk_ca_it_order'))               $ca_fields .= " , it_order = '$it_order' ";
if(is_checked('chk_ca_it_type'))                $ca_fields .= " , it_type1 = '$it_type1', it_type2 = '$it_type2', it_type3 = '$it_type3', it_type4 = '$it_type4' ";
if(is_checked('chk_ca_it_sell_email'))          $ca_fields .= " , it_sell_email = '$it_sell_email' ";
if(is_checked('chk_ca_it_tel_inq'))             $ca_fields .= " , it_tel_inq = '$it_tel_inq' ";
if(is_checked('chk_ca_it_use'))                 $ca_fields .= " , it_use = '$it_use' ";
if(is_checked('chk_ca_it_nocoupon'))            $ca_fields .= " , it_nocoupon = '$it_nocoupon' ";
if(is_checked('chk_ca_it_chub_ca_id'))          $ca_fields .= " , it_chub_ca_id = '$it_chub_ca_id' ";
if(is_checked('chk_ca_it_chub_tag'))            $ca_fields .= " , it_chub_tag = '$it_chub_tag' ";
if(is_checked('chk_ca_it_price'))               $ca_fields .= " , it_price = '$it_price' ";
if(is_checked('chk_ca_it_point'))               $ca_fields .= " , it_point = '$it_point' ";
if(is_checked('chk_ca_it_point_type'))          $ca_fields .= " , it_point_type = '$it_point_type' ";
if(is_checked('chk_ca_it_head_html'))           $ca_fields .= " , it_head_html = '$it_head_html' ";
if(is_checked('chk_ca_it_tail_html'))           $ca_fields .= " , it_tail_html = '$it_tail_html' ";
if(is_checked('chk_ca_it_mobile_head_html'))    $ca_fields .= " , it_mobile_head_html = '$it_mobile_head_html' ";
if(is_checked('chk_ca_it_mobile_tail_html'))    $ca_fields .= " , it_mobile_tail_html = '$it_mobile_tail_html' ";
if(is_checked('chk_ca_1'))                      $ca_fields .= " , it_1_subj = '$it_1_subj', it_1 = '$it_1' ";
if(is_checked('chk_ca_2'))                      $ca_fields .= " , it_2_subj = '$it_2_subj', it_2 = '$it_2' ";
if(is_checked('chk_ca_3'))                      $ca_fields .= " , it_3_subj = '$it_3_subj', it_3 = '$it_3' ";
if(is_checked('chk_ca_4'))                      $ca_fields .= " , it_4_subj = '$it_4_subj', it_4 = '$it_4' ";
if(is_checked('chk_ca_5'))                      $ca_fields .= " , it_5_subj = '$it_5_subj', it_5 = '$it_5' ";
if(is_checked('chk_ca_6'))                      $ca_fields .= " , it_6_subj = '$it_6_subj', it_6 = '$it_6' ";
if(is_checked('chk_ca_7'))                      $ca_fields .= " , it_7_subj = '$it_7_subj', it_7 = '$it_7' ";
if(is_checked('chk_ca_8'))                      $ca_fields .= " , it_8_subj = '$it_8_subj', it_8 = '$it_8' ";
if(is_checked('chk_ca_9'))                      $ca_fields .= " , it_9_subj = '$it_9_subj', it_9 = '$it_9' ";
if(is_checked('chk_ca_10'))                     $ca_fields .= " , it_10_subj = '$it_10_subj', it_10 = '$it_10' ";

if($ca_fields) {
    sql_query(" update {$g5['g5_contents_item_table']} set it_name = it_name {$ca_fields} where ca_id = '$ca_id' ");
    if($ca_id2)
        sql_query(" update {$g5['g5_contents_item_table']} set it_name = it_name {$ca_fields} where ca_id2 = '$ca_id2' ");
    if($ca_id3)
        sql_query(" update {$g5['g5_contents_item_table']} set it_name = it_name {$ca_fields} where ca_id3 = '$ca_id3' ");
}

// 모든 상품 동일 옵션 적용
$all_fields = '';
if(is_checked('chk_all_it_skin'))                $all_fields .= " , it_skin = '$it_skin' ";
if(is_checked('chk_all_it_contents_type'))       $all_fields .= " , it_contents_type = '$it_contents_type' ";
if(is_checked('chk_all_it_mobile_skin'))         $all_fields .= " , it_mobile_skin = '$it_mobile_skin' ";
if(is_checked('chk_all_it_basic'))               $all_fields .= " , it_basic = '$it_basic' ";
if(is_checked('chk_all_info1'))                  $all_fields .= " , it_info1_subj = '$it_info1_subj', it_info1 = '$it_info1' ";
if(is_checked('chk_all_info2'))                  $all_fields .= " , it_info2_subj = '$it_info2_subj', it_info2 = '$it_info2' ";
if(is_checked('chk_all_info3'))                  $all_fields .= " , it_info3_subj = '$it_info3_subj', it_info3 = '$it_info3' ";
if(is_checked('chk_all_info4'))                  $all_fields .= " , it_info4_subj = '$it_info4_subj', it_info4 = '$it_info4' ";
if(is_checked('chk_all_info5'))                  $all_fields .= " , it_info5_subj = '$it_info5_subj', it_info5 = '$it_info5' ";
if(is_checked('chk_all_it_user_demo'))           $all_fields .= " , it_user_demo = '$it_user_demo' ";
if(is_checked('chk_all_it_admin_demo'))          $all_fields .= " , it_admin_demo = '$it_admin_demo' ";
if(is_checked('chk_all_it_order'))               $all_fields .= " , it_order = '$it_order' ";
if(is_checked('chk_all_it_type'))                $all_fields .= " , it_type1 = '$it_type1', it_type2 = '$it_type2', it_type3 = '$it_type3', it_type4 = '$it_type4' ";
if(is_checked('chk_all_it_sell_email'))          $all_fields .= " , it_sell_email = '$it_sell_email' ";
if(is_checked('chk_all_it_tel_inq'))             $all_fields .= " , it_tel_inq = '$it_tel_inq' ";
if(is_checked('chk_all_it_use'))                 $all_fields .= " , it_use = '$it_use' ";
if(is_checked('chk_all_it_nocoupon'))            $all_fields .= " , it_nocoupon = '$it_nocoupon' ";
if(is_checked('chk_all_it_chub_ca_id'))          $all_fields .= " , it_chub_ca_id = '$it_chub_ca_id' ";
if(is_checked('chk_all_it_chub_tag'))            $all_fields .= " , it_chub_tag = '$it_chub_tag' ";
if(is_checked('chk_all_it_price'))               $all_fields .= " , it_price = '$it_price' ";
if(is_checked('chk_all_it_point'))               $all_fields .= " , it_point = '$it_point' ";
if(is_checked('chk_all_it_point_type'))          $all_fields .= " , it_point_type = '$it_point_type' ";
if(is_checked('chk_all_it_head_html'))           $all_fields .= " , it_head_html = '$it_head_html' ";
if(is_checked('chk_all_it_tail_html'))           $all_fields .= " , it_tail_html = '$it_tail_html' ";
if(is_checked('chk_all_it_mobile_head_html'))    $all_fields .= " , it_mobile_head_html = '$it_mobile_head_html' ";
if(is_checked('chk_all_it_mobile_tail_html'))    $all_fields .= " , it_mobile_tail_html = '$it_mobile_tail_html' ";
if(is_checked('chk_all_1'))                      $all_fields .= " , it_1_subj = '$it_1_subj', it_1 = '$it_1' ";
if(is_checked('chk_all_2'))                      $all_fields .= " , it_2_subj = '$it_2_subj', it_2 = '$it_2' ";
if(is_checked('chk_all_3'))                      $all_fields .= " , it_3_subj = '$it_3_subj', it_3 = '$it_3' ";
if(is_checked('chk_all_4'))                      $all_fields .= " , it_4_subj = '$it_4_subj', it_4 = '$it_4' ";
if(is_checked('chk_all_5'))                      $all_fields .= " , it_5_subj = '$it_5_subj', it_5 = '$it_5' ";
if(is_checked('chk_all_6'))                      $all_fields .= " , it_6_subj = '$it_6_subj', it_6 = '$it_6' ";
if(is_checked('chk_all_7'))                      $all_fields .= " , it_7_subj = '$it_7_subj', it_7 = '$it_7' ";
if(is_checked('chk_all_8'))                      $all_fields .= " , it_8_subj = '$it_8_subj', it_8 = '$it_8' ";
if(is_checked('chk_all_9'))                      $all_fields .= " , it_9_subj = '$it_9_subj', it_9 = '$it_9' ";
if(is_checked('chk_all_10'))                     $all_fields .= " , it_10_subj = '$it_10_subj', it_10 = '$it_10' ";

if($all_fields) {
    sql_query(" update {$g5['g5_contents_item_table']} set it_name = it_name {$all_fields} ");
}

// 컨텐츠허브 등록
insert_contentshub($it_id, $it_name, $it_chub_explan, $price, $it_chub_ca_id, $it_chub_tag, $w);

$qstr = "$qstr&amp;sca=$sca&amp;page=$page";

if ($w == "u") {
    goto_url("./itemform.php?w=u&amp;it_id=$it_id&amp;$qstr");
} else if ($w == "d")  {
    $qstr = "ca_id=$ca_id&amp;sfl=$sfl&amp;sca=$sca&amp;page=$page&amp;stx=".urlencode($stx)."&amp;save_stx=".urlencode($save_stx);
    goto_url("./itemlist.php?$qstr");
}

echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">";
?>
<script>
    if (confirm("계속 입력하시겠습니까?"))
        //location.href = "<?php echo "./itemform.php?it_id=$it_id&amp;sort1=$sort1&amp;sort2=$sort2&amp;sel_ca_id=$sel_ca_id&amp;sel_field=$sel_field&amp;search=$search&amp;page=$page"?>";
        location.href = "<?php echo "./itemform.php?".str_replace('&amp;', '&', $qstr); ?>";
    else
        location.href = "<?php echo "./itemlist.php?".str_replace('&amp;', '&', $qstr); ?>";
</script>
