<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.G5_MCONTENTS_SKIN_URL.'/style.css">', 0);
?>

<script src="<?php echo G5_JS_URL; ?>/viewimageresize.js"></script>

<!-- 전체 상품 문의 목록 시작 { -->

<form method="get" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>">
<div id="cqa_sch">
    <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>">전체보기</a>
    <label for="sfl" class="sound_only">검색항목<strong class="sound_only"> 필수</strong></label>
    <select name="sfl" id="sfl" required class="required">
        <option value="">선택</option>
        <option value="b.it_name"    <?php echo get_selected($sfl, "b.it_name", true); ?>>상품명</option>
        <option value="a.it_id"      <?php echo get_selected($sfl, "a.it_id"); ?>>상품코드</option>
        <option value="a.iq_subject" <?php echo get_selected($sfl, "a.is_subject"); ?>>문의제목</option>
        <option value="a.iq_question"<?php echo get_selected($sfl, "a.iq_question"); ?>>문의내용</option>
        <option value="a.iq_name"    <?php echo get_selected($sfl, "a.it_id"); ?>>작성자명</option>
        <option value="a.mb_id"      <?php echo get_selected($sfl, "a.mb_id"); ?>>작성자아이디</option>
    </select>

    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" required class="required frm_input" size="10">
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<div id="cqa">

    <!-- <p><?php echo $config['cf_title']; ?> 전체 상품문의 목록입니다.</p> -->

    <?php
    $thumbnail_width = 500;
    $num = $total_count - ($page - 1) * $rows;

    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $iq_subject = conv_subject($row['iq_subject'],50,"…");

        $is_secret = false;
        if($row['iq_secret']) {
            $iq_subject .= ' <img src="'.G5_MCONTENTS_SKIN_URL.'/img/icon_secret.gif" alt="비밀글">';

            if($is_admin || $member['mb_id' ] == $row['mb_id']) {
                $iq_question = get_view_thumbnail(conv_content($row['iq_question'], 1), $thumbnail_width);
            } else {
                $iq_question = '비밀글로 보호된 문의입니다.';
                $is_secret = true;
            }
        } else {
            $iq_question = get_view_thumbnail(conv_content($row['iq_question'], 1), $thumbnail_width);
        }

        $it_href = G5_CONTENTS_URL.'/item.php?it_id='.$row['it_id'];

        if ($row['iq_answer'])
        {
            $iq_answer = get_view_thumbnail(conv_content($row['iq_answer'], 1), $thumbnail_width);
            $iq_stats = '답변완료';
            $iq_style = 'cit_qaa_done';
            $is_answer = true;
        } else {
            $iq_stats = '답변전';
            $iq_style = 'cit_qaa_yet';
            $iq_answer = '답변이 등록되지 않았습니다.';
            $is_answer = false;
        }

        if ($i == 0) echo '<ol>';
    ?>
    <li>

        <div class="cqa_img">
            <a href="<?php echo $it_href; ?>">
                <?php echo cm_get_it_image($row['it_id'], 70, 70); ?>
                <span><?php echo $row['it_name']; ?></span>
            </a>
        </div>

        <section class="cqa_section">
            <h2><?php echo $iq_subject; ?></h2>

            <dl class="cqa_dl">
                <dt>작성자</dt>
                <dd><?php echo get_text($row['iq_name']); ?></dd>
                <dt>작성일</dt>
                <dd><?php echo substr($row['iq_time'],0,10); ?></dd>
                <dt>상태</dt>
                <dd class="<?php echo $iq_style; ?>"><?php echo $iq_stats; ?></dd>
            </dl>

            <div id="cqa_con_<?php echo $i; ?>" class="cqa_con" style="display:none;">
                <div class="cit_qa_qaq">
                    <strong>문의내용</strong><br>
                    <?php echo $iq_question; // 상품 문의 내용 ?>
                </div>
                <?php if(!$is_secret) { ?>
                <div class="cit_qa_qaa">
                    <strong>답변</strong><br>
                    <?php echo $iq_answer; ?>
                </div>
                <?php } ?>
            </div>

            <div class="cqa_con_btn"><button class="cqa_con_<?php echo $i; ?>">보기</button></div>
        </section>

    </li>
    <?php
        $num--;
    }

    if ($i > 0) echo '</ol>';
    if ($i == 0) echo '<p id="cqa_empty">자료가 없습니다.</p>';
    ?>
</div>

<?php echo get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
$(function(){
    // 상품문의 더보기
    $(".cqa_con_btn button").click(function(){
        var $con = $(this).parent().prev();
        if($con.is(":visible")) {
            $con.slideUp();
            $(this).text("보기");
        } else {
            $(".cqa_con_btn button").text("보기");
            $("div[id^=cqa_con]:visible").hide();
            $con.slideDown(
                function() {
                    // 이미지 리사이즈
                    $con.viewimageresize2();
                }
            );
            $(this).text("닫기");
        }
    });

    $("a.view_image").click(function() {
        window.open(this.href, "large_image", "location=yes,links=no,toolbar=no,top=10,left=10,width=10,height=10,resizable=yes,scrollbars=no,status=no");
        return false;
    });
});
</script>
<!-- } 전체 상품 사용후기 목록 끝 -->