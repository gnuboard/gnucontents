<?php
$sub_menu = '600400';
include_once('./_common.php');

check_demo();

if (!count($_POST['chk'])) {
    alert($_POST['act_button']." 하실 항목을 하나 이상 체크하세요.");
}

// 주문서가 있는 상품 수
$not_deleted = 0;

if ($_POST['act_button'] == "선택수정") {

    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<count($_POST['chk']); $i++) {

        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        $sql = "update {$g5['g5_contents_item_table']}
                   set ca_id          = '{$_POST['ca_id'][$k]}',
                       ca_id2         = '{$_POST['ca_id2'][$k]}',
                       ca_id3         = '{$_POST['ca_id3'][$k]}',
                       it_name        = '{$_POST['it_name'][$k]}',
                       it_price       = '{$_POST['it_price'][$k]}',
                       it_skin        = '{$_POST['it_skin'][$k]}',
                       it_mobile_skin = '{$_POST['it_mobile_skin'][$k]}',
                       it_use         = '{$_POST['it_use'][$k]}',
                       it_order       = '{$_POST['it_order'][$k]}',
                       it_update_time = '".G5_TIME_YMDHIS."'
                 where it_id   = '{$_POST['it_id'][$k]}' ";
        sql_query($sql);
    }
} else if ($_POST['act_button'] == "선택삭제") {

    if ($is_admin != 'super')
        alert('상품 삭제는 최고관리자만 가능합니다.');

    auth_check($auth[$sub_menu], 'd');

    // _ITEM_DELETE_ 상수를 선언해야 itemdelete.inc.php 가 정상 작동함
    define('_ITEM_DELETE_', true);

    for ($i=0; $i<count($_POST['chk']); $i++) {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];

        // include 전에 $it_id 값을 반드시 넘겨야 함
        $it_id = $_POST['it_id'][$k];

        // 주문이 있으면 삭제 불가
        $sql = " select count(*) as cnt from {$g5['g5_contents_cart_table']} where it_id = '$it_id' and ct_status != '쇼핑' ";
        $row = sql_fetch($sql);
        if((int)$row['cnt'] > 0) {
            $not_deleted++;
            continue;
        }

        include ('./itemdelete.inc.php');
    }
}

$url = "./itemlist.php?sca=$sca&amp;sst=$sst&amp;sod=$sod&amp;sfl=$sfl&amp;stx=$stx&amp;page=$page";

if($not_deleted > 0)
    alert('주문서가 있는 상품 '.number_format($not_deleted).'건을 제외한 선택 상품을 삭제했습니다.', $url);
else
    goto_url($url);
?>
