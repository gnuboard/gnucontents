<?php
include_once('./_common.php');

define("_INDEX_", TRUE);

include_once(G5_MCONTENTS_PATH.'/contents.head.php');
?>

<script src="<?php echo G5_JS_URL; ?>/swipe.js"></script>
<script src="<?php echo G5_JS_URL; ?>/contents.mobile.main.js"></script>

<div id="sidx" class="swipe">
    <div id="sidx_slide" class="swipe-wrap">

        <?php if($setting['de_mobile_type1_list_use']) { ?>
        <div class="cct_wrap">
            <!-- 추천상품 시작 { -->
            <header>
                <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=1">추천상품</a></h2>

            </header>
            <?php
            $list = new cm_item_list();
            $list->set_mobile(true);
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
            <div class="more_btn"><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=1">더보기 +</a></div>
            <!-- } 추천상품 끝 -->
        </div>
        <?php } ?>

        <?php if($setting['de_mobile_type2_list_use']) { ?>
        <div class="cct_wrap">
            <!-- 인기상품 시작 { -->
            <header>
                <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=2">인기상품</a></h2>

            </header>
            <?php
            $list = new cm_item_list();
            $list->set_mobile(true);
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
            <div class="more_btn"><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=2">더보기 +</a></div>
            <!-- } 인기상품 끝 -->
        </div>
        <?php } ?>

        <?php if($setting['de_mobile_type3_list_use']) { ?>
        <div class="cct_wrap">
            <!-- 최신상품 시작 { -->
            <header>
                <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=3">최신상품</a></h2>

            </header>
            <?php
            $list = new cm_item_list();
            $list->set_mobile(true);
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
            <div class="more_btn"><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=3">더보기 +</a></div>
            <!-- } 최신상품 끝 -->
        </div>
        <?php } ?>

        <?php if($setting['de_mobile_type4_list_use']) { ?>
        <div class="cct_wrap">
            <!-- 할인상품 시작 { -->
            <header>
                <h2><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=4">할인상품</a></h2>

            </header>
            <?php
            $list = new cm_item_list();
            $list->set_mobile(true);
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
            <div class="more_btn"><a href="<?php echo G5_CONTENTS_URL; ?>/listtype.php?type=4">더보기 +</a></div>
            <!-- } 할인상품 끝 -->
        </div>
        <?php } ?>

        <?php
        $hsql = " select ev_id, ev_subject, ev_subject_strong from {$g5['g5_contents_event_table']} where ev_use = '1' order by ev_id desc ";
        $hresult = sql_query($hsql);

        if(mysql_num_rows($hresult)) {
        ?>
        <div class="cct_wrap">
            <header>
                <h2>이벤트</h2>
                <p class="sct_wrap_hdesc"><?php echo $config['cf_title']; ?> 이벤트 모음</p>
            </header>
            <?php include_once(G5_MCONTENTS_SKIN_PATH.'/main.event.skin.php'); ?>
        </div>
        <?php
        }
        ?>

    </div>
</div>

<script>
$(function() {
    $("#sidx").swipeSlide({
        slides: ".swipe-wrap > div",
        header: "header h2",
        tabWrap: "slide_tab",
        tabActive: "tab_active",
        tabOffset: 10,
        startSlide: 0,
        auto: 0
    });
});
</script>

<?php
include_once(G5_MCONTENTS_PATH.'/contents.tail.php');
?>