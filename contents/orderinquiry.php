<?php
include_once('./_common.php');

if (!$is_member)
    alert('회원 로그인 후 이용해 주십시오.', G5_BBS_URL.'/login.php?url='.urlencode(G5_CONTENTS_URL.'/orderinquiry.php'));

define("_ORDERINQUIRY_", true);

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/orderinquiry.php');
    return;
}

$sql_common = " from {$g5['g5_contents_order_table']} where mb_id = '{$member['mb_id']}' ";

// 테이블의 전체 레코드수만 얻음
$sql = " select count(*) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

// 비회원 주문확인시 비회원의 모든 주문이 다 출력되는 오류 수정
// 조건에 맞는 주문서가 없다면
if ($total_count == 0)
{
    alert('주문이 존재하지 않습니다.', G5_CONTENTS_URL);
}

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$g5['title'] = '주문내역조회';
include_once('./_head.php');
?>

<!-- 주문 내역 시작 { -->
<div id="sod_v">
    <p id="sod_v_info">주문서번호 링크를 누르시면 주문상세내역을 조회하실 수 있습니다.</p>

    <?php
    $limit = " limit $from_record, $rows ";
    include "./orderinquiry.sub.php";
    ?>

    <?php echo get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>
</div>
<!-- } 주문 내역 끝 -->

<?php
include_once('./_tail.php');
?>
