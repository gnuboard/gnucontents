<?php
include_once('./_common.php');

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/item.php');
    return;
}

$it_id = trim($_GET['it_id']);

// 분류사용, 상품사용하는 상품의 정보를 얻음
$sql = " select a.*, b.ca_name, b.ca_use from {$g5['g5_contents_item_table']} a, {$g5['g5_contents_category_table']} b where a.it_id = '$it_id' and a.ca_id = b.ca_id ";
$it = sql_fetch($sql);
if (!$it['it_id'])
    alert('자료가 없습니다.');
if (!($it['ca_use'] && $it['it_use'])) {
    if (!$is_admin)
        alert('현재 판매가능한 상품이 아닙니다.');
}

// 분류 테이블에서 분류 상단, 하단 코드를 얻음
$sql = " select ca_skin_dir, ca_include_head, ca_include_tail, ca_cert_use, ca_adult_use from {$g5['g5_contents_category_table']} where ca_id = '{$it['ca_id']}' ";
$ca = sql_fetch($sql);

// 본인인증, 성인인증체크
if(!$is_admin) {
    $msg = cm_member_cert_check($it_id, 'item');
    if($msg)
        alert($msg, G5_CONTENTS_URL);
}

// 오늘 본 상품 저장 시작
// tv 는 today view 약자
$saved = false;
$tv_idx = (int)get_session("ss_cm_tv_idx");
if ($tv_idx > 0) {
    for ($i=1; $i<=$tv_idx; $i++) {
        if (get_session("ss_cm_tv[$i]") == $it_id) {
            $saved = true;
            break;
        }
    }
}

if (!$saved) {
    $tv_idx++;
    set_session("ss_cm_tv_idx", $tv_idx);
    set_session("ss_cm_tv[$tv_idx]", $it_id);
}
// 오늘 본 상품 저장 끝

// 조회수 증가
if (get_cookie('ck_cm_it_id') != $it_id) {
    sql_query(" update {$g5['g5_contents_item_table']} set it_hit = it_hit + 1 where it_id = '$it_id' "); // 1증가
    set_cookie("ck_cm_it_id", $it_id, time() + 3600); // 1시간동안 저장
}

// 스킨경로
$skin_dir = G5_CONTENTS_SKIN_PATH;
$ca_dir_check = true;

if($it['it_skin']) {
    if(preg_match('#^theme/(.+)$#', $it['it_skin'], $match))
        $skin_dir = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1];
    else
        $skin_dir = G5_PATH.'/'.G5_SKIN_DIR.'/contents/'.$it['it_skin'];

    if(is_dir($skin_dir)) {
        $form_skin_file = $skin_dir.'/item.form.skin.php';

        if(is_file($form_skin_file))
            $ca_dir_check = false;
    }
}

if($ca_dir_check) {
    if($ca['ca_skin_dir']) {
        if(preg_match('#^theme/(.+)$#', $ca['ca_skin_dir'], $match))
            $skin_dir = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/contents/'.$match[1];
        else
            $skin_dir = G5_PATH.'/'.G5_SKIN_DIR.'/contents/'.$ca['ca_skin_dir'];

        if(is_dir($skin_dir)) {
            $form_skin_file = $skin_dir.'/item.form.skin.php';

            if(!is_file($form_skin_file))
                $skin_dir = G5_CONTENTS_SKIN_PATH;
        } else {
            $skin_dir = G5_CONTENTS_SKIN_PATH;
        }
    }
}

define('G5_CONTENTS_CSS_URL', str_replace(G5_PATH, G5_URL, $skin_dir));

$g5['title'] = $it['ca_name'].' 컨텐츠 리스트';

// 분류 상단 코드가 있으면 출력하고 없으면 기본 상단 코드 출력
if ($ca['ca_include_head'])
    @include_once($ca['ca_include_head']);
else
    include_once(G5_CONTENTS_PATH.'/_head.php');
?>
<div id="cct_hd">
        <?php
        $nav_skin = $skin_dir.'/navigation.skin.php';
        if(!is_file($nav_skin))
            $nav_skin = G5_CONTENTS_SKIN_PATH.'/navigation.skin.php';
        include $nav_skin;

        if ($is_admin)
        echo '<div class="cct_admin"><a href="'.G5_ADMIN_URL.'/contents_admin/itemform.php?w=u&amp;it_id='.$it_id.'" class="btn_admin">상품 관리</a></div>';
        ?>
        <?php
            $cate_skin = $skin_dir.'/listcategory.skin.php';
            if(!is_file($cate_skin))
                $cate_skin = G5_CONTENTS_SKIN_PATH.'/listcategory.skin.php';
            include $cate_skin;
        ?>
    </div>


<!-- 상품 상세보기 시작 { -->
<?php
// 상단 HTML
echo '<div id="sit_hhtml">'.conv_content($it['it_head_html'], 1).'</div>';

// 보안서버경로
if (G5_HTTPS_DOMAIN)
    $action_url = G5_HTTPS_DOMAIN.'/'.G5_CONTENTS_DIR.'/cartupdate.php';
else
    $action_url = './cartupdate.php';

// 이전 상품보기
$sql = " select it_id, it_name from {$g5['g5_contents_item_table']} where it_id > '$it_id' and SUBSTRING(ca_id,1,4) = '".substr($it['ca_id'],0,4)."' and it_use = '1' order by it_id asc limit 1 ";
$row = sql_fetch($sql);
if ($row['it_id']) {
    $prev_title = '이전상품<span class="sound_only"> '.$row['it_name'].'</span>';
    $prev_href = '<a href="./item.php?it_id='.$row['it_id'].'" id="siblings_prev">';
    $prev_href2 = '</a>'.PHP_EOL;
} else {
    $prev_title = '';
    $prev_href = '';
    $prev_href2 = '';
}

// 다음 상품보기
$sql = " select it_id, it_name from {$g5['g5_contents_item_table']} where it_id < '$it_id' and SUBSTRING(ca_id,1,4) = '".substr($it['ca_id'],0,4)."' and it_use = '1' order by it_id desc limit 1 ";
$row = sql_fetch($sql);
if ($row['it_id']) {
    $next_title = '다음 상품<span class="sound_only"> '.$row['it_name'].'</span>';
    $next_href = '<a href="./item.php?it_id='.$row['it_id'].'" id="siblings_next">';
    $next_href2 = '</a>'.PHP_EOL;
} else {
    $next_title = '';
    $next_href = '';
    $next_href2 = '';
}

// 고객선호도 별점수
$star_score = cm_get_star_image($it['it_id']);

// 관리자가 확인한 사용후기의 개수를 얻음
$sql = " select count(*) as cnt from `{$g5['g5_contents_item_use_table']}` where it_id = '{$it_id}' and is_confirm = '1' ";
$row = sql_fetch($sql);
$item_use_count = $row['cnt'];

// 상품문의의 개수를 얻음
$sql = " select count(*) as cnt from `{$g5['g5_contents_item_qa_table']}` where it_id = '{$it_id}' ";
$row = sql_fetch($sql);
$item_qa_count = $row['cnt'];

// 관련상품의 개수를 얻음
$sql = " select count(*) as cnt from {$g5['g5_contents_item_relation_table']} a left join {$g5['g5_contents_item_table']} b on (a.it_id2=b.it_id and b.it_use='1') where a.it_id = '{$it['it_id']}' ";
$row = sql_fetch($sql);
$item_relation_count = $row['cnt'];

// 소셜 관련
$sns_title = get_text($it['it_name']).' | '.get_text($config['cf_title']);
$sns_url  = G5_CONTENTS_URL.'/item.php?it_id='.$it['it_id'];
$sns_share_links .= cm_get_sns_share_link('facebook', $sns_url, $sns_title, G5_CONTENTS_SKIN_URL.'/img/sns_fb_s.png').' ';
$sns_share_links .= cm_get_sns_share_link('twitter', $sns_url, $sns_title, G5_CONTENTS_SKIN_URL.'/img/sns_twt_s.png').' ';
$sns_share_links .= cm_get_sns_share_link('googleplus', $sns_url, $sns_title, G5_CONTENTS_SKIN_URL.'/img/sns_goo_s.png');

// 주문가능체크
$is_orderable = true;
if(!$it['it_use'] || $it['it_tel_inq'])
    $is_orderable = false;

// 선택옵션
$option_item = '';
if($is_orderable) {
    $sql = " select count(*) as cnt from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' and io_use = '1' ";
    $row = sql_fetch($sql);
    $option_count = $row['cnt'];
    if($option_count)
        $option_item = cm_get_item_options($it);
}

// 옵션정보가 없으면 구매불가
if(!$option_item)
    $is_orderable = false;

function pg_anchor($anc_id) {
    global $setting;
    global $item_use_count, $item_qa_count, $item_relation_count;
?>
    <ul class="c_anchor">
        <li><a href="#cit_inf" <?php if ($anc_id == 'inf') echo 'class="c_anchor_on"'; ?>>상품정보</a></li>
        <li><a href="#cit_use" <?php if ($anc_id == 'use') echo 'class="c_anchor_on"'; ?>>사용후기 <span class="item_use_count"><?php echo $item_use_count; ?></span></a></li>
        <li><a href="#cit_qa" <?php if ($anc_id == 'qa') echo 'class="c_anchor_on"'; ?>>상품문의 <span class="item_qa_count"><?php echo $item_qa_count; ?></span></a></li>
        <li><a href="#cit_rel" <?php if ($anc_id == 'rel') echo 'class="c_anchor_on"'; ?>>관련상품 <span class="item_relation_count"><?php echo $item_relation_count; ?></span></a></li>
    </ul>
<?php
}
?>

<div id="cit">

    <?php
    // 상품 구입폼
    include_once($skin_dir.'/item.form.skin.php');
    ?>

    <?php
    // 상품 상세정보
    $info_skin = $skin_dir.'/item.info.skin.php';
    if(!is_file($info_skin))
        $info_skin = G5_CONTENTS_SKIN_PATH.'/item.info.skin.php';
    include $info_skin;
    ?>

</div>

<?php
// 하단 HTML
echo conv_content($it['it_tail_html'], 1);
?>

<?php
if ($ca['ca_include_tail'])
    @include_once($ca['ca_include_tail']);
else
    include_once(G5_CONTENTS_PATH.'/_tail.php');
?>
