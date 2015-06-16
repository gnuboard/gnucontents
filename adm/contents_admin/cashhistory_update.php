<?php
$sub_menu = '600260';
include_once('./_common.php');

check_demo();
check_token();

$mb_id = $_POST['mb_id'];
$ch_price = $_POST['ch_price'];
$ch_memo = trim(strip_tags($_POST['ch_memo']));

$mb = get_member($mb_id);

if (!$mb['mb_id'])
    alert('존재하는 회원아이디가 아닙니다.', './cashhistory.php?'.$qstr);

if(!$ch_memo)
    alert('캐시 내용을 입력해 주십시오.', './cashhistory.php?'.$qstr);

if (($ch_price < 0) && ($ch_price * (-1) > get_member_cash($mb_id)))
    alert('캐시를 차감하는 경우 현재 캐시금액보다 작으면 안됩니다.', './cashhistory.php?'.$qstr);

$cs_id = 10000000000000000;
insert_cash($mb_id, $cs_id, $ch_price, strip_tags($ch_memo));

goto_url('./cashhistory.php?'.$qstr);
?>