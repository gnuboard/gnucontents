<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$admin = get_admin("super");

// 사용자 화면 우측과 하단을 담당하는 페이지입니다.
// 우측, 하단 화면을 꾸미려면 이 파일을 수정합니다.
?>

    </div>
    <!-- } 콘텐츠 끝 -->

<!-- 하단 시작 { -->
</div>

<div id="ft">
    <div id="ft_cont">
        <a href="<?php echo $setting['de_root_index_use'] ? G5_URL : G5_CONTENTS_URL; ?>/" id="ft_logo"><img src="<?php echo G5_DATA_URL; ?>/common/cm_logo_img2" alt="처음으로"></a>
        <div id="ft_info">
            <p>
                <span><?php echo $setting['de_admin_company_addr']; ?></span><br>
                <span><b>전화</b> <?php echo $setting['de_admin_company_tel']; ?></span>
                <span><b>팩스</b> <?php echo $setting['de_admin_company_fax']; ?></span>
                <span><b>운영자</b> <?php echo $admin['mb_name']; ?></span><br>
                <span><b>사업자 등록번호</b> <?php echo $setting['de_admin_company_saupja_no']; ?></span>
                <span><b>대표</b> <?php echo $setting['de_admin_company_owner']; ?></span><br>
                <span><b>개인정보관리책임자</b> <?php echo $setting['de_admin_info_name']; ?></span><br>
                <span><b>통신판매업신고번호</b> <?php echo $setting['de_admin_tongsin_no']; ?></span><br>
            <?php if ($setting['de_admin_buga_no']) echo '<span><b>부가통신사업신고번호</b> '.$setting['de_admin_buga_no'].'</span>'; ?><br>
            <span class="ft_copy">Copyright &copy; 2001-2014 <?php echo $setting['de_admin_company_name']; ?>. All Rights Reserved.</span>
            </p>
         </div>
         <a href="#" id="ft_totop" title="상단으로">상단으로</a>
    </div>
</div>

<?php
$sec = get_microtime() - $begin_time;
$file = $_SERVER['PHP_SELF'];

if ($config['cf_analytics']) {
    echo $config['cf_analytics'];
}
?>

<script src="<?php echo G5_JS_URL; ?>/sns.js"></script>
<!-- } 하단 끝 -->

<?php
include_once(G5_PATH.'/tail.sub.php');
?>
