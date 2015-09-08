<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_CONTENTS_PATH.'/contents.head.php');
    return;
}

include_once(G5_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
?>

<!-- 상단 시작 { -->
<div id="hd">
    <h1 id="hd_h1"><?php echo $g5['title'] ?></h1>

    <div id="skip_to_container"><a href="#container">본문 바로가기</a></div>

    <?php
    if(defined('_INDEX_')) // index에서만 실행
        include G5_BBS_PATH.'/newwin.inc.php'; // 팝업레이어
    ?>

    <div id="hd_wrapper">
        <div id="logo"><a href="<?php echo (defined('G5_COMMUNITY_USE') && G5_COMMUNITY_USE) ? G5_URL : G5_CONTENTS_URL; ?>/"><img src="<?php echo G5_DATA_URL; ?>/common/cm_logo_img" alt="<?php echo $config['cf_title']; ?>"></a></div>

        <div id="hd_sch">
            <h3>컨텐츠몰 검색</h3>
            <form name="frmsearch1" action="<?php echo G5_CONTENTS_URL; ?>/search.php" onsubmit="return search_submit(this);">

            <label for="sch_stc" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
            <input type="text" name="q" value="<?php echo stripslashes(get_text(get_search_string($q))); ?>" id="sch_stc" required>
            <input type="submit" value="검색" id="sch_submit">

            </form>
            <script>
                function search_submit(f) {
                if (f.q.value.length < 2) {
                    alert("검색어는 두글자 이상 입력하십시오.");
                    f.q.select();
                    f.q.focus();
                    return false;
                }

                return true;
            }
            </script>
        </div>

        <div id="tnb">
            <h3>회원메뉴</h3>
            <ul>

                <?php if ($is_admin) {  ?>
                <li><a href="<?php echo G5_ADMIN_URL; ?>/contents_admin/"><b>관리자</b></a></li>
                <?php }  ?>
                <li><a href="<?php echo G5_CONTENTS_URL; ?>/cart.php">장바구니</a></li>
                <li><a href="<?php echo G5_CONTENTS_URL; ?>/mypage.php">마이페이지</a></li>
                <?php if ($is_member) { ?>
                <li><a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=register_form.php">정보수정</a></li>
                <li><a href="<?php echo G5_BBS_URL; ?>/logout.php?url=contents">로그아웃</a></li>
                <?php } else { ?>
                <li><a href="<?php echo G5_BBS_URL; ?>/login.php?url=<?php echo $urlencode; ?>"><b>로그인</b></a></li>
                <?php } ?>
                <li><a href="<?php echo G5_BBS_URL; ?>/qalist.php">1:1문의</a></li>
                <?php if($setting['de_cash_charge_use']) { ?>
                <li><a href="<?php echo G5_CONTENTS_URL; ?>/cashform.php">캐시충전</a></li>
                <?php } ?>
                <li><a href="<?php echo G5_BBS_URL; ?>/content.php?co_id=guide">이용안내</a></li>
                <?php if(G5_COMMUNITY_USE) { ?>
                <li><a href="<?php echo G5_URL; ?>/">커뮤니티</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

</div>

<div id="wrapper">
    <?php include(G5_CONTENTS_SKIN_PATH.'/boxtodayview.skin.php'); // 오늘 본 상품 ?>

    <div id="aside">
        <?php echo outlogin('basic'); // 아웃로그인 ?>

        <?php include_once(G5_CONTENTS_SKIN_PATH.'/boxcategory.skin.php'); // 상품분류 ?>

        <?php include_once(G5_CONTENTS_SKIN_PATH.'/boxcart.skin.php'); // 장바구니 ?>

        <?php include_once(G5_CONTENTS_SKIN_PATH.'/boxwish.skin.php'); // 위시리스트 ?>

        <?php include_once(G5_CONTENTS_SKIN_PATH.'/boxevent.skin.php'); // 이벤트 ?>

        <?php include_once(G5_CONTENTS_SKIN_PATH.'/boxcommunity.skin.php'); // 커뮤니티  */?>


        <!-- 컨텐츠몰 배너 시작 { -->
        <?php echo cm_display_banner('왼쪽'); ?>
        <!-- } 컨텐츠몰 배너 끝 -->

        <?php include_once(G5_CONTENTS_PATH.'/smsinquiry.php'); // SMS문의  ?>

        <div id="cscenter">
            <h2 id="cs_tit">고객센터</h2>
            <p id="cs_num"><?php echo $setting['de_admin_company_tel']; ?></p>
        </div>

    </div>
<!-- } 상단 끝 -->

    <!-- 콘텐츠 시작 { -->
    <div id="container">
        <?php if ((!$bo_table || $w == 's' ) && !defined('_INDEX_')) { ?><div id="wrapper_title"><?php echo $g5['title'] ?></div><?php } ?>
        <!-- 글자크기 조정 display:none 되어 있음 시작 { -->
        <div id="text_size">
            <button class="no_text_resize" onclick="font_resize('container', 'decrease');">작게</button>
            <button class="no_text_resize" onclick="font_default('container');">기본</button>
            <button class="no_text_resize" onclick="font_resize('container', 'increase');">크게</button>
        </div>
        <!-- } 글자크기 조정 display:none 되어 있음 끝 -->