<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

include_once(G5_THEME_PATH.'/head.sub.php');
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

        <?php include_once(G5_MCONTENTS_PATH.'/category.php'); // 분류 ?>

        <button type="button" id="hd_sch_open">검색<span class="sound_only"> 열기</span></button>
        <div id="hd_sch">
            <h3>컨텐츠몰 검색</h3>
            <form name="frmsearch1" action="<?php echo G5_CONTENTS_URL; ?>/search.php" onsubmit="return search_submit(this);">
                <label for="sch_stc">상품명<strong class="sound_only"> 필수</strong></label>
                <input type="text" name="q" value="<?php echo stripslashes(get_text(get_search_string($q))); ?>" id="sch_stc" required>
                <input type="submit" value="검색" id="sch_submit">
            </form>
            <button type="button" class="close_btn pop_close">닫기</button>
            <script>
                $(function (){
                var $hd_sch = $("#hd_sch");
                $("#hd_sch_open").click(function(){
                    $hd_sch.css("display","block");
                });
                $("#hd_sch .pop_close").click(function(){
                    $hd_sch.css("display","none");
                });
            });
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