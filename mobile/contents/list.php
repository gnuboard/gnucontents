<?php
include_once('./_common.php');

$sql = " select * from {$g5['g5_contents_category_table']} where ca_id = '$ca_id' and ca_use = '1'  ";
$ca = sql_fetch($sql);
if (!$ca['ca_id'])
    alert('등록된 분류가 없습니다.');

// 본인인증, 성인인증체크
if(!$is_admin) {
    $msg = cm_member_cert_check($ca_id, 'list');
    if($msg)
        alert($msg, G5_CONTENTS_URL);
}

$g5['title'] = $ca['ca_name'].' 컨텐츠 리스트';
include_once(G5_MCONTENTS_PATH.'/_head.php');

// 스킨경로
$skin_dir = G5_MCONTENTS_SKIN_PATH;

if($ca['ca_mobile_skin_dir']) {
    $skin_dir = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/contents/'.$ca['ca_mobile_skin_dir'];

    if(is_dir($skin_dir)) {
        $skin_file = $skin_dir.'/'.$ca['ca_mobile_skin'];

        if(!is_file($skin_file))
            $skin_dir = G5_MCONTENTS_SKIN_PATH;
    } else {
        $skin_dir = G5_MCONTENTS_SKIN_PATH;
    }
}

define('G5_MCONTENTS_CSS_URL', str_replace(G5_PATH, G5_URL, $skin_dir));


?>


<!-- 상품 목록 시작 { -->

<div id="cct">
    <div id="cct_hd">
        <?php
        $nav_skin = $skin_dir.'/navigation.skin.php';
        if(!is_file($nav_skin))
            $nav_skin = G5_MCONTENTS_SKIN_PATH.'/navigation.skin.php';
        include $nav_skin;
        ?>
        <?php
            // 상단 HTML
            echo '<div id="sct_hhtml">'.conv_content($ca['ca_mobile_head_html'], 1).'</div>';

            $cate_skin = $skin_dir.'/listcategory.skin.php';
            if(!is_file($cate_skin))
                $cate_skin = G5_MCONTENTS_SKIN_PATH.'/listcategory.skin.php';
            include $cate_skin;
        ?>
    </div>
    <?php
    // 상품 출력순서가 있다면
    if ($sort != "")
        $order_by = $sort.' '.$sortodr.' , it_order, it_id desc';
    else
        $order_by = 'it_order, it_id desc';

    $error = '<p class="sct_noitem">등록된 상품이 없습니다.</p>';

    // 리스트 스킨
    $skin_file = $skin_dir.'/'.$ca['ca_mobile_skin'];

    if (file_exists($skin_file)) {

		echo '<div id="sct_sortlst">';
        $sort_skin = $skin_dir.'/list.sort.skin.php';
        if(!is_file($sort_skin))
            $sort_skin = G5_MCONTENTS_SKIN_PATH.'/list.sort.skin.php';
        include $sort_skin;

        echo '</div>';

        // 총몇개 = 한줄에 몇개 * 몇줄
        $items = $ca['ca_mobile_list_mod'];
        // 페이지가 없으면 첫 페이지 (1 페이지)
        if ($page < 1) $page = 1;
        // 시작 레코드 구함
        $from_record = ($page - 1) * $items;

        $list = new cm_item_list($skin_file, $ca['ca_mobile_list_mod'], 1, $ca['ca_mobile_img_width'], $ca['ca_mobile_img_height']);
        $list->set_category($ca['ca_id'], 1);
        $list->set_category($ca['ca_id'], 2);
        $list->set_category($ca['ca_id'], 3);
        $list->set_is_page(true);
        $list->set_mobile(true);
        $list->set_order_by($order_by);
        $list->set_from_record($from_record);
        $list->set_view('it_img', true);
        $list->set_view('it_id', false);
        $list->set_view('it_name', true);
        $list->set_view('it_basic', true);
        $list->set_view('it_price', true);
        //$list->set_view('it_icon', true);
        $list->set_view('it_sum_qty', true);
        $list->set_view('it_wish_qty', true);
        //$list->set_view('sns', true);
        echo $list->run();

        // where 된 전체 상품수
        $total_count = $list->total_count;
        // 전체 페이지 계산
        $total_page  = ceil($total_count / $items);
    }
    else
    {
        echo '<div class="sct_nofile">'.str_replace(G5_PATH.'/', '', $skin_file).' 파일을 찾을 수 없습니다.<br>관리자에게 알려주시면 감사하겠습니다.</div>';
    }
    ?>

    <?php
    $qstr1 .= 'ca_id='.$ca_id;
    $qstr1 .='&amp;sort='.$sort.'&amp;sortodr='.$sortodr;
    echo get_paging($config['cf_mobile_pages'], $page, $total_page, $_SERVER['PHP_SELF'].'?'.$qstr1.'&amp;page=');
    ?>

    <?php
    // 하단 HTML
    echo '<div id="sct_thtml">'.conv_content($ca['ca_mobile_tail_html'], 1).'</div>';

?>
</div>
<!-- } 상품 목록 끝 -->

<?php
include_once(G5_MCONTENTS_PATH.'/_tail.php');

echo "\n<!-- {$ca['ca_mobile_skin']} -->\n";
?>
