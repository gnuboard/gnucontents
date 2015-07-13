<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/index.php');
    return;
}

define("_INDEX_", TRUE);

include_once(G5_CONTENTS_PATH.'/contents.head.php');
?>

<!-- 메인이미지 시작 { -->
<?php echo cm_display_banner('메인', 'mainbanner.10.skin.php'); ?>

<!-- } 메인이미지 끝 -->
<div id="cct_container">

<?php if($setting['de_type1_list_use']) { ?>
<!-- 추천상품 시작 { -->
<section class="cct_wrap">
    <header>
        <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=1">추천상품</a></h2>

    </header>
    <?php
    $list = new cm_item_list();
    $list->set_type(1);
    $list->set_view('it_img', true);
    $list->set_view('it_id', false);
    $list->set_view('it_name', true);
    $list->set_view('it_basic', true);
    $list->set_view('it_price', true);
    $list->set_view('it_sum_qty', true);
    $list->set_view('it_wish_qty', true);
    $list->set_view('it_icon', false);
    echo $list->run();
    ?>
</section>
<!-- } 추천상품 끝 -->
<?php } ?>

<?php if($setting['de_type2_list_use']) { ?>
<!-- 인기상품 시작 { -->
<section class="cct_wrap">
    <header>
        <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=2">인기상품</a></h2>

    </header>
    <?php
    $list = new cm_item_list();
    $list->set_type(2);
    $list->set_view('it_img', true);
    $list->set_view('it_id', false);
    $list->set_view('it_name', true);
    $list->set_view('it_basic', true);
    $list->set_view('it_price', true);
    $list->set_view('it_sum_qty', true);
    $list->set_view('it_wish_qty', true);
    $list->set_view('it_icon', false);
    echo $list->run();
    ?>
</section>
<!-- } 인기상품 끝 -->
<?php } ?>

<?php if($setting['de_type3_list_use']) { ?>
<!-- 최신상품 시작 { -->
<section class="cct_wrap">
    <header>
        <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=3">최신상품</a></h2>

    </header>
    <?php
    $list = new cm_item_list();
    $list->set_type(3);
    $list->set_view('it_img', true);
    $list->set_view('it_id', false);
    $list->set_view('it_name', true);
    $list->set_view('it_basic', true);
    $list->set_view('it_price', true);
    $list->set_view('it_sum_qty', true);
    $list->set_view('it_wish_qty', true);
    $list->set_view('it_icon', false);
    echo $list->run();
    ?>
</section>
<!-- } 최신상품 끝 -->
<?php } ?>

<?php if($setting['de_type4_list_use']) { ?>
<!-- 할인상품 시작 { -->
<section class="cct_wrap">
    <header>
        <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=4">할인상품</a></h2>
    </header>
    <?php
    $list = new cm_item_list();
    $list->set_type(4);
    $list->set_view('it_img', true);
    $list->set_view('it_id', false);
    $list->set_view('it_name', true);
    $list->set_view('it_basic', true);
    $list->set_view('it_price', true);
    $list->set_view('it_sum_qty', true);
    $list->set_view('it_wish_qty', true);
    $list->set_view('it_icon', false);
    echo $list->run();
    ?>
</section>
<!-- } 할인상품 끝 -->
<?php } ?>

</div> <!--cct_container end-->

<?php
include_once(G5_CONTENTS_PATH.'/contents.tail.php');
?>