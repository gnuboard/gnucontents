<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/event.php');
    return;
}

$sql = " select * from {$g5['g5_contents_event_table']}
          where ev_id = '$ev_id'
            and ev_use = 1 ";
$ev = sql_fetch($sql);
if (!$ev['ev_id'])
    alert('등록된 이벤트가 없습니다.');

$g5['title'] = $ev['ev_subject'];
include_once('./_head.php');

if ($is_admin)
    echo '<div class="sev_admin"><a href="'.G5_ADMIN_URL.'/contents_admin/itemeventform.php?w=u&amp;ev_id='.$ev['ev_id'].'" class="btn_admin">이벤트 관리</a></div>';
?>

<!-- 이벤트 시작 { -->
<?php
$himg = G5_DATA_PATH.'/cmevent/'.$ev_id.'_h';
if (file_exists($himg))
    echo '<div id="sev_himg" class="sev_img"><img src="'.G5_DATA_URL.'/cmevent/'.$ev_id.'_h" alt=""></div>';

// 상단 HTML
echo '<div id="sev_hhtml">'.stripslashes($ev['ev_head_html']).'</div>';

// 상품 출력순서가 있다면
if ($sort != "")
    $order_by = $sort.' '.$sortodr.' , b.it_order, b.it_id desc';
else
    $order_by = 'b.it_order, b.it_id desc';

if ($skin)
    $ev['ev_skin'] = $skin;

define('G5_CONTENTS_CSS_URL', G5_CONTENTS_SKIN_URL);

// 리스트 유형별로 출력
$list_file = G5_CONTENTS_SKIN_PATH."/{$ev['ev_skin']}";
if (file_exists($list_file))
{
    include G5_CONTENTS_SKIN_PATH.'/list.sort.skin.php';

    // 총몇개 = 한줄에 몇개 * 몇줄
    $items = $ev['ev_list_mod'] * $ev['ev_list_row'];
    // 페이지가 없으면 첫 페이지 (1 페이지)
    if ($page < 1) $page = 1;
    // 시작 레코드 구함
    $from_record = ($page - 1) * $items;

    $list = new cm_item_list(G5_CONTENTS_SKIN_PATH.'/'.$ev['ev_skin'], $ev['ev_list_mod'], $ev['ev_list_row'], $ev['ev_img_width'], $ev['ev_img_height']);
    $list->set_event($ev['ev_id']);
    $list->set_is_page(true);
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
    echo '<div align="center">'.$ev['ev_skin'].' 파일을 찾을 수 없습니다.<br>관리자에게 알려주시면 감사하겠습니다.</div>';
}
?>

<?php
$qstr .= 'skin='.$skin.'&amp;ev_id='.$ev_id.'&amp;sort='.$sort.'&amp;sortodr='.$sortodr;
echo get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['PHP_SELF']}?$qstr&amp;page=");
?>

<?php
// 하단 HTML
echo '<div id="sev_thtml">'.stripslashes($ev['ev_tail_html']).'</div>';

$timg = G5_DATA_PATH.'/cmevent/'.$ev_id.'_t';
if (file_exists($timg))
    echo '<div id="sev_timg" class="sev_img"><img src="'.G5_DATA_URL.'/cmevent/'.$ev_id.'_t" alt=""></div>';
?>
<!-- } 이벤트 끝 -->

<?php
include_once('./_tail.php');
?>