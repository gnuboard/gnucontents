<?php
include_once('./_common.php');

$type = $_REQUEST['type'];
if ($type == 1)      $g5['title'] = '추천상품';
else if ($type == 2) $g5['title'] = '인기상품';
else if ($type == 3) $g5['title'] = '최신상품';
else if ($type == 4) $g5['title'] = '할인상품';
else
    alert('상품유형이 아닙니다.');

include_once(G5_MCONTENTS_PATH.'/_head.php');

// 한페이지에 출력하는 이미지수 = $list_mod * $list_row
// 모바일에서는 계산된 이미지수가 중요함
$list_mod   = 1;    // 한줄에 이미지 몇개씩 출력? 단, 모바일환경에서는 사용되지 않음.
$list_row   = 10;    // 한 페이지에 몇라인씩 출력?

$img_width  = 230;  // 출력이미지 폭
$img_height = 230;  // 출력이미지 높이

// 상품 출력순서가 있다면
$order_by = ' it_order, it_id desc ';
if ($sort != '')
    $order_by = $sort.' '.$sortodr.' , it_order, it_id desc';
else
    $order_by = 'it_order, it_id desc';

if (!$skin)
    $skin = 'list.10.skin.php';

define('G5_MCONTENTS_CSS_URL', G5_MCONTENTS_SKIN_URL);

// 리스트 유형별로 출력
$list_file = G5_MCONTENTS_SKIN_PATH.'/'.$skin;
if (file_exists($list_file)) {
    // 총몇개 = 한줄에 몇개 * 몇줄
    $items = $list_mod * $list_row;
    // 페이지가 없으면 첫 페이지 (1 페이지)
    if ($page < 1) $page = 1;
    // 시작 레코드 구함
    $from_record = ($page - 1) * $items;

    $list = new cm_item_list();
    $list->set_type($type);
    $list->set_list_skin($list_file);
    $list->set_list_mod($list_mod);
    $list->set_list_row($list_row);
    $list->set_img_size($img_width, $img_height);
    $list->set_is_page(true);
    $list->set_mobile(true);
    $list->set_order_by($order_by);
    $list->set_from_record($from_record);
    $list->set_view('it_img', true);
    $list->set_view('it_id', false);
    $list->set_view('it_name', true);
    $list->set_view('it_price', true);
    //$list->set_view('it_icon', true);
    //$list->set_view('sns', true);
    $list->set_view('it_sum_qty', true);
    $list->set_view('it_wish_qty', true);
    echo $list->run();

    // where 된 전체 상품수
    $total_count = $list->total_count;
    // 전체 페이지 계산
    $total_page  = ceil($total_count / $items);
}
else
{
    echo '<div align="center">'.$skin.' 파일을 찾을 수 없습니다.<br>관리자에게 알려주시면 감사하겠습니다.</div>';
}
?>

<?php
$qstr .= '&amp;type='.$type.'&amp;sort='.$sort;
echo get_paging($config['cf_mobile_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page=");
?>

<?php
include_once(G5_MCONTENTS_PATH.'/_tail.php');
?>
