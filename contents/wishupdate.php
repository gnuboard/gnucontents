<?php
include_once('./_common.php');

if (!$is_member)
    alert('회원 전용 서비스 입니다.', G5_BBS_URL.'/login.php?url='.urlencode($url));

if ($w == "d")
{
    $wi_id = trim($_GET['wi_id']);

    $sql = " select mb_id, it_id from {$g5['g5_contents_wish_table']} where wi_id = '$wi_id' ";
    $row = sql_fetch($sql);

    if($row['mb_id'] != $member['mb_id'])
        alert('위시리시트 상품을 삭제할 권한이 없습니다.');

    $sql = " delete from {$g5['g5_contents_wish_table']}
              where wi_id = '$wi_id'
                and mb_id = '{$member['mb_id']}' ";
    sql_query($sql);

    // wish 수량 상품 테이블에 저장
    $sql = " select it_wish_qty from {$g5['g5_contents_item_table']} where it_id = '{$row['it_id']}' ";
    $it = sql_fetch($sql);

    if($it['it_wish_qty'] < 1)
        $it_wish_qty = 0;
    else
        $it_wish_qty = $it['it_wish_qty'] - 1;

    sql_query(" update {$g5['g5_contents_item_table']} set it_wish_qty = '$it_wish_qty' where it_id = '{$row['it_id']}' ");
}
else
{
    if(is_array($it_id))
        $it_id = $_POST['it_id'][0];

    if(!$it_id)
        alert('상품코드가 올바르지 않습니다.', G5_CONTENTS_URL);

    // 상품정보 체크
    $sql = " select it_id from {$g5['g5_contents_item_table']} where it_id = '$it_id' ";
    $row = sql_fetch($sql);

    if(!$row['it_id'])
        alert('상품정보가 존재하지 않습니다.', G5_CONTENTS_URL);

    $sql = " select wi_id from {$g5['g5_contents_wish_table']}
              where mb_id = '{$member['mb_id']}' and it_id = '$it_id' ";
    $row = sql_fetch($sql);

    if (!$row['wi_id']) { // 없다면 등록
        $sql = " insert {$g5['g5_contents_wish_table']}
                    set mb_id = '{$member['mb_id']}',
                        it_id = '$it_id',
                        wi_time = '".G5_TIME_YMDHIS."',
                        wi_ip = '$REMOTE_ADDR' ";
        sql_query($sql);

        // wish 수량 상품 테이블에 저장
        sql_query(" update {$g5['g5_contents_item_table']} set it_wish_qty = it_wish_qty + 1 where it_id = '$it_id' ");
    }
}

goto_url('./wishlist.php');
?>