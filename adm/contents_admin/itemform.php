<?php
$sub_menu = '600400';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "상품 ";

if ($w == "")
{
    $html_title .= "입력";

    // 옵션은 쿠키에 저장된 값을 보여줌. 다음 입력을 위한것임
    //$it[ca_id] = _COOKIE[ck_ca_id];
    $it['ca_id'] = get_cookie("ck_ca_id");
    $it['ca_id2'] = get_cookie("ck_ca_id2");
    $it['ca_id3'] = get_cookie("ck_ca_id3");
    if (!$it['ca_id'])
    {
        $sql = " select ca_id from {$g5['g5_contents_category_table']} order by ca_order, ca_id limit 1 ";
        $row = sql_fetch($sql);
        if (!$row['ca_id'])
            alert("등록된 분류가 없습니다. 우선 분류를 등록하여 주십시오.", './categorylist.php');
        $it['ca_id'] = $row['ca_id'];
    }
    $it['it_contents_type'] = 0;
}
else if ($w == "u")
{
    $html_title .= "수정";

    if ($is_admin != 'super')
    {
        $sql = " select it_id from {$g5['g5_contents_item_table']} a, {$g5['g5_contents_category_table']} b
                  where a.it_id = '$it_id'
                    and a.ca_id = b.ca_id
                    and b.ca_mb_id = '{$member['mb_id']}' ";
        $row = sql_fetch($sql);
        if (!$row['it_id'])
            alert("\'{$member['mb_id']}\' 님께서 수정 할 권한이 없는 상품입니다.");
    }

    $sql = " select * from {$g5['g5_contents_item_table']} where it_id = '$it_id' ";
    $it = sql_fetch($sql);

    if (!$ca_id)
        $ca_id = $it['ca_id'];

    $sql = " select * from {$g5['g5_contents_category_table']} where ca_id = '$ca_id' ";
    $ca = sql_fetch($sql);
}
else
{
    alert();
}

$qstr  = $qstr.'&amp;sca='.$sca.'&amp;page='.$page;

$g5['title'] = $html_title;
include_once (G5_ADMIN_PATH.'/admin.head.php');

// 분류리스트
$category_select = '';
$script = '';
$sql = " select * from {$g5['g5_contents_category_table']} ";
if ($is_admin != 'super')
    $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
$sql .= " order by ca_order, ca_id ";
$result = sql_query($sql);
for ($i=0; $row=sql_fetch_array($result); $i++)
{
    $len = strlen($row['ca_id']) / 2 - 1;

    $nbsp = "";
    for ($i=0; $i<$len; $i++)
        $nbsp .= "&nbsp;&nbsp;&nbsp;";

    $category_select .= "<option value=\"{$row['ca_id']}\">$nbsp{$row['ca_name']}</option>\n";

    $script .= "ca_use['{$row['ca_id']}'] = {$row['ca_use']};\n";
    //$script .= "ca_explan_html['$row[ca_id]'] = $row[ca_explan_html];\n";
    $script .= "ca_sell_email['{$row['ca_id']}'] = '{$row['ca_sell_email']}';\n";
}

$pg_anchor ='<ul class="anchor">
<li><a href="#anc_sitfrm_cate">상품분류</a></li>
<li><a href="#anc_sitfrm_skin">스킨설정</a></li>
<li><a href="#anc_sitfrm_ini">기본정보</a></li>
<li><a href="#anc_sitfrm_cost">가격 및 재고</a></li>
<li><a href="#anc_sitfrm_img">상품이미지</a></li>
<li><a href="#anc_sitfrm_relation">관련상품</a></li>
<li><a href="#anc_sitfrm_event">관련이벤트</a></li>
<li><a href="#anc_sitfrm_optional">상세설명설정</a></li>
<li><a href="#anc_sitfrm_extra">여분필드</a></li>
</ul>
';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="./itemlist.php?'.$qstr.'">목록</a>';
if($it_id)
    $frm_submit .= PHP_EOL.'<a href="'.G5_CONTENTS_URL.'/item.php?it_id='.$it_id.'" class="btn_frmline">상품보기</a>';
$frm_submit .= '</div>';
?>

<form name="fitemform" action="./itemformupdate.php" method="post" enctype="MULTIPART/FORM-DATA" autocomplete="off" onsubmit="return fitemformcheck(this)">

<input type="hidden" name="codedup" value="<?php echo $setting['de_code_dup_use']; ?>">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="sca" value="<?php echo $sca; ?>">
<input type="hidden" name="sst" value="<?php echo $sst; ?>">
<input type="hidden" name="sod"  value="<?php echo $sod; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx"  value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">

<section id="anc_sitfrm_cate">
    <h2 class="h2_frm">상품분류</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>기본분류는 반드시 선택하셔야 합니다. 하나의 상품에 최대 3개의 다른 분류를 지정할 수 있습니다.</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>상품분류 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="ca_id">기본분류</label></th>
            <td>
                <?php if ($w == "") echo help("기본분류를 선택하면, 판매/재고/HTML사용/판매자 E-mail 등을, 선택한 분류의 기본값으로 설정합니다."); ?>
                <select name="ca_id" id="ca_id" onchange="categorychange(this.form)">
                    <option value="">선택하세요</option>
                    <?php echo cm_conv_selected_option($category_select, $it['ca_id']); ?>
                </select>
                <script>
                    var ca_use = new Array();
                    //var ca_explan_html = new Array();
                    var ca_sell_email = new Array();
                    <?php echo "\n$script"; ?>
                </script>
            </td>
        </tr>
        <?php for ($i=2; $i<=3; $i++) { ?>
        <tr>
            <th scope="row"><label for="ca_id<?php echo $i; ?>"><?php echo $i; ?>차 분류</label></th>
            <td>
                <?php echo help($i.'차 분류는 기본 분류의 하위 분류 개념이 아니므로 기본 분류 선택시 해당 상품이 포함될 최하위 분류만 선택하시면 됩니다.'); ?>
                <select name="ca_id<?php echo $i; ?>" id="ca_id<?php echo $i; ?>">
                    <option value="">선택하세요</option>
                    <?php echo cm_conv_selected_option($category_select, $it['ca_id'.$i]); ?>
                </select>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_skin">
    <h2 class="h2_frm">스킨설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>상품상세보기에서 사용할 스킨을 설정합니다.</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>스킨설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="it_skin">PC용 스킨</label></th>
            <td colspan="3">
                <?php echo get_skin_select('contents', 'it_skin', 'it_skin', $it['it_skin']); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_skin" value="1" id="chk_ca_it_skin">
                <label for="chk_ca_it_skin">분류적용</label>
                <input type="checkbox" name="chk_all_it_skin" value="1" id="chk_all_it_skin">
                <label for="chk_all_it_skin">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_mobile_skin">모바일용 스킨</label></th>
            <td colspan="3">
                <?php echo get_skin_select('contents', 'it_mobile_skin', 'it_mobile_skin', $it['it_mobile_skin']); ?>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_mobile_skin" value="1" id="chk_ca_it_mobile_skin">
                <label for="chk_ca_it_mobile_skin">분류적용</label>
                <input type="checkbox" name="chk_all_it_mobile_skin" value="1" id="chk_all_it_mobile_skin">
                <label for="chk_all_it_mobile_skin">전체적용</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_ini">
    <h2 class="h2_frm">기본정보</h2>
    <?php echo $pg_anchor; ?>
    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>기본정보 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">상품코드</th>
            <td colspan="2">
                <?php if ($w == '') { // 추가 ?>
                    <!-- 최근에 입력한 코드(자동 생성시)가 목록의 상단에 출력되게 하려면 아래의 코드로 대체하십시오. -->
                    <!-- <input type=text class=required name=it_id value="<?php echo 10000000000-time()?>" size=12 maxlength=10 required> <a href='javascript:;' onclick="codedupcheck(document.all.it_id.value)"><img src='./img/btn_code.gif' border=0 align=absmiddle></a> -->
                    <?php echo help("상품의 코드는 10자리 숫자로 자동생성합니다. <b>직접 상품코드를 입력할 수도 있습니다.</b>\n상품코드는 영문자, 숫자, - 만 입력 가능합니다."); ?>
                    <input type="text" name="it_id" value="<?php echo time(); ?>" id="it_id" required class="frm_input required" size="20" maxlength="20">
                <?php } else { ?>
                    <input type="hidden" name="it_id" value="<?php echo $it['it_id']; ?>">
                    <span class="frm_ca_id"><?php echo $it['it_id']; ?></span>
                    <a href="<?php echo G5_CONTENTS_URL; ?>/item.php?it_id=<?php echo $it_id; ?>" class="btn_frmline">상품확인</a>
                    <a href="<?php echo G5_ADMIN_URL; ?>/contents_admin/itemuselist.php?sfl=a.it_id&amp;stx=<?php echo $it_id; ?>" class="btn_frmline">사용후기</a>
                    <a href="<?php echo G5_ADMIN_URL; ?>/contents_admin/itemqalist.php?sfl=a.it_id&amp;stx=<?php echo $it_id; ?>" class="btn_frmline">상품문의</a>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_name">상품명</label></th>
            <td colspan="2">
                <?php echo help("HTML 입력이 불가합니다."); ?>
                <input type="text" name="it_name" value="<?php echo get_text(cut_str($it['it_name'], 250, "")); ?>" id="it_name" required class="frm_input required" size="95">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_basic">기본설명</label></th>
            <td>
                <?php echo help("상품명 하단에 상품에 대한 추가적인 설명이 필요한 경우에 입력합니다. HTML 입력도 가능합니다."); ?>
                <input type="text" name="it_basic" value="<?php echo get_text($it['it_basic']); ?>" id="it_basic" class="frm_input" size="95">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_basic" value="1" id="chk_ca_it_basic">
                <label for="chk_ca_it_basic">분류적용</label>
                <input type="checkbox" name="chk_all_it_basic" value="1" id="chk_all_it_basic">
                <label for="chk_all_it_basic">전체적용</label>
            </td>
        </tr>
        <?php
        for($i=1; $i<=5; $i++) {
        ?>
        <tr>
            <th scope="row">기본정보<?php echo $i ?></th>
            <td class="td_extra">
                <label for="it_info<?php echo $i ?>_subj">기본정보 <?php echo $i ?> 제목</label>
                <input type="text" name="it_info<?php echo $i ?>_subj" id="it_info<?php echo $i ?>_subj" value="<?php echo get_text($it['it_info'.$i.'_subj']) ?>" class="frm_input">
                <label for="it_info<?php echo $i ?>">기본정보 <?php echo $i ?> 값</label>
                <input type="text" name="it_info<?php echo $i ?>" value="<?php echo get_text($it['it_info'.$i]) ?>" id="it_info<?php echo $i ?>" class="frm_input">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_info<?php echo $i ?>" value="1" id="chk_ca_info<?php echo $i ?>">
                <label for="chk_ca_info<?php echo $i ?>">분류적용</label>
                <input type="checkbox" name="chk_all_info<?php echo $i ?>" value="1" id="chk_all_info<?php echo $i ?>">
                <label for="chk_all_info<?php echo $i ?>">전체적용</label>
            </td>
        </tr>
        <?php
        }
        ?>
        <tr>
            <th scope="row"><label for="it_user_demo">사용자데모 링크</label></th>
            <td>
                <input type="text" name="it_user_demo" value="<?php echo get_text($it['it_user_demo']); ?>" id="it_user_demo" class="frm_input" size="95">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_user_demo" value="1" id="chk_ca_it_user_demo">
                <label for="chk_ca_it_user_demo">분류적용</label>
                <input type="checkbox" name="chk_all_it_user_demo" value="1" id="chk_all_it_user_demo">
                <label for="chk_all_it_user_demo">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_admin_demo">관리자데모 링크</label></th>
            <td>
                <input type="text" name="it_admin_demo" value="<?php echo get_text($it['it_admin_demo']); ?>" id="it_admin_demo" class="frm_input" size="95">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_admin_demo" value="1" id="chk_ca_it_admin_demo">
                <label for="chk_ca_it_admin_demo">분류적용</label>
                <input type="checkbox" name="chk_all_it_admin_demo" value="1" id="chk_all_it_admin_demo">
                <label for="chk_all_it_admin_demo">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_order">출력순서</label></th>
            <td>
                <?php echo help("숫자가 작을 수록 상위에 출력됩니다. 음수 입력도 가능하며 입력 가능 범위는 -2147483648 부터 2147483647 까지입니다.\n<b>입력하지 않으면 자동으로 출력됩니다.</b>"); ?>
                <input type="text" name="it_order" value="<?php echo $it['it_order']; ?>" id="it_order" class="frm_input" size="12">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_order" value="1" id="chk_ca_it_order">
                <label for="chk_ca_it_order">분류적용</label>
                <input type="checkbox" name="chk_all_it_order" value="1" id="chk_all_it_order">
                <label for="chk_all_it_order">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row">상품유형</th>
            <td>
                <?php echo help("메인화면에 유형별로 출력할때 사용합니다.\n이곳에 체크하게되면 상품리스트에서 유형별로 정렬할때 체크된 상품이 가장 먼저 출력됩니다."); ?>
                <input type="checkbox" name="it_type1" value="1" <?php echo ($it['it_type1'] ? "checked" : ""); ?> id="it_type1">
                <label for="it_type1">추천 <img src="<?php echo G5_CONTENTS_URL; ?>/img/icon_rec.gif" alt=""></label>
                <input type="checkbox" name="it_type2" value="1" <?php echo ($it['it_type2'] ? "checked" : ""); ?> id="it_type2">
                <label for="it_type2">인기 <img src="<?php echo G5_CONTENTS_URL; ?>/img/icon_hit.gif" alt=""></label>
                <input type="checkbox" name="it_type3" value="1" <?php echo ($it['it_type3'] ? "checked" : ""); ?> id="it_type3">
                <label for="it_type3">신상품 <img src="<?php echo G5_CONTENTS_URL; ?>/img/icon_new.gif" alt=""></label>
                <input type="checkbox" name="it_type4" value="1" <?php echo ($it['it_type4'] ? "checked" : ""); ?> id="it_type4">
                <label for="it_type4">할인 <img src="<?php echo G5_CONTENTS_URL; ?>/img/icon_discount.gif" alt=""></label>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_type" value="1" id="chk_ca_it_type">
                <label for="chk_ca_it_type">분류적용</label>
                <input type="checkbox" name="chk_all_it_type" value="1" id="chk_all_it_type">
                <label for="chk_all_it_type">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_tel_inq">전화문의</label></th>
            <td>
                <?php echo help("상품 금액 대신 전화문의로 표시됩니다."); ?>
                <input type="checkbox" name="it_tel_inq" value="1" id="it_tel_inq" <?php echo ($it['it_tel_inq']) ? "checked" : ""; ?>> 예
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_tel_inq" value="1" id="chk_ca_it_tel_inq">
                <label for="chk_ca_it_tel_inq">분류적용</label>
                <input type="checkbox" name="chk_all_it_tel_inq" value="1" id="chk_all_it_tel_inq">
                <label for="chk_all_it_tel_inq">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_use">판매가능</label></th>
            <td>
                <?php echo help("잠시 판매를 중단하거나 재고가 없을 경우에 체크를 해제해 놓으면 출력되지 않으며, 주문도 받지 않습니다."); ?>
                <input type="checkbox" name="it_use" value="1" id="it_use" <?php echo ($it['it_use']) ? "checked" : ""; ?>> 예
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_use" value="1" id="chk_ca_it_use">
                <label for="chk_ca_it_use">분류적용</label>
                <input type="checkbox" name="chk_all_it_use" value="1" id="chk_all_it_use">
                <label for="chk_all_it_use">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_nocoupon">쿠폰적용안함</label></th>
            <td>
                <?php echo help("설정에 체크하시면 쿠폰 생성 때 상품 검색 결과에 노출되지 않습니다."); ?>
                <input type="checkbox" name="it_nocoupon" value="1" id="it_nocoupon" <?php echo ($it['it_nocoupon']) ? "checked" : ""; ?>> 예
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_nocoupon" value="1" id="chk_ca_it_nocoupon">
                <label for="chk_ca_it_nocoupon">분류적용</label>
                <input type="checkbox" name="chk_all_it_nocoupon" value="1" id="chk_all_it_nocoupon">
                <label for="chk_all_it_nocoupon">전체적용</label>
            </td>
        </tr>
        <?php if($setting['de_chub_mid'] && defined('G5_CONTENTS_HUB_URL') && G5_CONTENTS_HUB_URL) { ?>
        <tr>
            <th scope="row"><label for="it_chub_ca_id">컨텐츠허브 분류</label></th>
            <td>
                <?php echo help('SIR 컨텐츠허브에 등록하는 경우 컨텐츠허브 분류를 선택해 주십시오.'); ?>
                <select name="it_chub_ca_id" id="it_chub_ca_id">
                    <option value="">분류선택</option>
                    <?php
                    foreach($sir_chub_category as $key=>$val) {
                        echo '<option value="'.$key.'"'.get_selected($it['it_chub_ca_id'], $key).'>'.$val.'</option>'.PHP_EOL;
                    }
                    ?>
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_chub_ca_id" value="1" id="chk_ca_it_chub_ca_id">
                <label for="chk_ca_it_chub_ca_id">분류적용</label>
                <input type="checkbox" name="chk_all_it_chub_ca_id" value="1" id="chk_all_it_chub_ca_id">
                <label for="chk_all_it_chub_ca_id">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_chub_tag">컨텐츠허브 태그</label></th>
            <td>
                <?php echo help("컨텐츠허브에 등록될 태그를 , 로 구분하여 최대 5개까지 입력할 수 있습니다."); ?>
                <input type="text" name="it_chub_tag" id="it_chub_tag" value="<?php echo $it['it_chub_tag']; ?>" class="frm_input" size="50">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_chub_tag" value="1" id="chk_ca_it_chub_tag">
                <label for="chk_ca_it_chub_tag">분류적용</label>
                <input type="checkbox" name="chk_all_it_chub_tag" value="1" id="chk_all_it_chub_tag">
                <label for="chk_all_it_chub_tag">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_chub_explan">컨텐츠허브 상품설명</label></th>
            <td>
                <?php echo help("컨텐츠허브에 표시될 상품에 대한 설명을 입력합니다."); ?>
                <input type="text" name="it_chub_explan" value="<?php echo get_text($it['it_chub_explan']); ?>" id="it_chub_explan" class="frm_input" size="95">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_chub_explan" value="1" id="chk_ca_it_chub_explan">
                <label for="chk_ca_it_chub_explan">분류적용</label>
                <input type="checkbox" name="chk_all_it_chub_explan" value="1" id="chk_all_it_chub_explan">
                <label for="chk_all_it_chub_explan">전체적용</label>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <th scope="row">상품설명</th>
            <td colspan="2"> <?php echo editor_html('it_explan', $it['it_explan']); ?></td>
        </tr>
        <tr>
            <th scope="row">모바일 상품설명</th>
            <td colspan="2"> <?php echo editor_html('it_mobile_explan', $it['it_mobile_explan']); ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="it_sell_email">판매자 e-mail</label></th>
            <td>
                <?php echo help("운영자와 실제 판매자가 다른 경우 실제 판매자의 e-mail을 입력하면, 상품 주문 시점을 기준으로 실제 판매자에게도 주문서를 발송합니다."); ?>
                <input type="text" name="it_sell_email" value="<?php echo $it['it_sell_email']; ?>" id="it_sell_email" class="frm_input" size="40">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_sell_email" value="1" id="chk_ca_it_sell_email">
                <label for="chk_ca_it_sell_email">분류적용</label>
                <input type="checkbox" name="chk_all_it_sell_email" value="1" id="chk_all_it_sell_email">
                <label for="chk_all_it_sell_email">전체적용</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_cost">
    <h2 class="h2_frm">가격 및 옵션항목</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>가격 및 옵션항목 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="it_price">판매가격</label></th>
            <td>
                <input type="text" name="it_price" value="<?php echo $it['it_price']; ?>" id="it_price" class="frm_input" size="8"> 원
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_price" value="1" id="chk_ca_it_price">
                <label for="chk_ca_it_price">분류적용</label>
                <input type="checkbox" name="chk_all_it_price" value="1" id="chk_all_it_price">
                <label for="chk_all_it_price">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_point_type">포인트 유형</label></th>
            <td>
                <?php echo help("포인트 유형을 설정할 수 있습니다. 비율로 설정했을 경우 설정 기준금액의 %비율로 포인트가 지급됩니다."); ?>
                <select name="it_point_type" id="it_point_type">
                    <option value="0"<?php echo get_selected('0', $it['it_point_type']); ?>>설정금액</option>
                    <option value="1"<?php echo get_selected('1', $it['it_point_type']); ?>>판매가기준 설정비율</option>
                    <option value="2"<?php echo get_selected('2', $it['it_point_type']); ?>>구매가기준 설정비율</option>
                </select>
                <script>
                $(function() {
                    $("#it_point_type").change(function() {
                        if(parseInt($(this).val()) > 0)
                            $("#it_point_unit").text("%");
                        else
                            $("#it_point_unit").text("점");
                    });
                });
                </script>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_point_type" value="1" id="chk_ca_it_point_type">
                <label for="chk_ca_it_point_type">분류적용</label>
                <input type="checkbox" name="chk_all_it_point_type" value="1" id="chk_all_it_point_type">
                <label for="chk_all_it_point_type">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_point">포인트</label></th>
            <td>
                <?php echo help("주문완료후 환경설정에서 설정한 주문완료 설정일 후 회원에게 부여하는 포인트입니다.\n또, 포인트부여를 '아니오'로 설정한 경우 신용카드, 계좌이체로 주문하는 회원께는 부여하지 않습니다."); ?>
                <input type="text" name="it_point" value="<?php echo $it['it_point']; ?>" id="it_point" class="frm_input" size="8"> <span id="it_point_unit"><?php if($it['it_point_type']) echo '%'; else echo '점'; ?></span>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_point" value="1" id="chk_ca_it_point">
                <label for="chk_ca_it_point">분류적용</label>
                <input type="checkbox" name="chk_all_it_point" value="1" id="chk_all_it_point">
                <label for="chk_all_it_point">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="it_contents_type">컨텐츠 유형</label></th>
            <td>
                <?php echo help("컨텐츠 파일 등록 방식을 설정합니다. 직접 파일 업로드와 외부링크를 이용한 등록이 가능합니다."); ?>
                <select name="it_contents_type" id="it_contents_type">
                    <option value="0"<?php echo get_selected('0', $it['it_contents_type']); ?>>디자인소스 업로드</option>
                    <option value="1"<?php echo get_selected('1', $it['it_contents_type']); ?>>문서/서식 업로드</option>
                    <option value="2"<?php echo get_selected('2', $it['it_contents_type']); ?>>동영상 업로드</option>
                    <option value="3"<?php echo get_selected('3', $it['it_contents_type']); ?>>동영상 외부링크</option>
                    <option value="4"<?php echo get_selected('4', $it['it_contents_type']); ?>>디자인소스 외부링크</option>
                    <option value="5"<?php echo get_selected('5', $it['it_contents_type']); ?>>문서/서식 외부링크</option>
                </select>
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_contents_type" value="1" id="chk_ca_it_contents_type">
                <label for="chk_ca_it_contents_type">분류적용</label>
                <input type="checkbox" name="chk_all_it_contents_type" value="1" id="chk_all_it_contents_type">
                <label for="chk_all_it_contents_type">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row">상품옵션</th>
            <td colspan="2">
                <?php echo help("등록된 옵션항목은 삭제할 수 없습니다. 사용하지 않는 옵션항목은 사용여부를 <strong>사용안함</strong>으로 설정해 주십시오.<br>다운로드 불가능으로 설정하시면 신규 구매가 불가능하며 기존 구매자의 컨텐츠 다운로드도 금지됩니다.<br><strong>파일타입은 동영상의 경우 반드시 설정해 주셔야 합니다.</strong>"); ?>
                <div class="sit_option_frm_wrapper">
                    <table id="sit_option_fld">
                    <caption>상품옵션 입력</caption>
                    <thead>
                    <tr>
                        <th scope="col">
                            <label for="opt_chk_all" class="sound_only">전체 옵션</label>
                            <input type="checkbox" name="opt_chk_all" value="1" id="opt_chk_all">
                        </th>
                        <th scope="col">옵션명</th>
                        <th scope="col">파일타입</th>
                        <th scope="col">추가금액</th>
                        <th scope="col">다운로드</th>
                        <th scope="col">고객지원</th>
                        <th scope="col">사용여부</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    // 초기값
                    $io_price = 0;
                    $io_noti_qty = 10;
                    $option_count = 0;

                    if($w == 'u') {
                        $sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' order by io_no ";
                        $result = sql_query($sql);
                        $option_count = mysql_num_rows($result);
                    }

                    if($option_count > 0) {
                        $chk_disabled = ' disabled="disabled"';

                        for($i=0; $row=sql_fetch_array($result); $i++) {
                            if($it['it_contents_type'] == 2 || $it['it_contents_type'] == 3) {
                                $io_type = $row['io_type'];
                                $io_type_disabled = '';
                            } else {
                                $io_type = '';
                                $io_type_disabled = ' disabled="disabled"';
                            }
                    ?>
                    <tr>
                        <td class="td_chk">
                            <label for="io_chk_<?php echo $i; ?>" class="sound_only"></label>
                            <input type="checkbox" name="io_chk[<?php echo $i; ?>]" id="io_chk_<?php echo $i; ?>" value="1"<?php echo $chk_disabled; ?>>
                        </td>
                        <td class="opt-cell">
                            <label for="io_name_<?php echo $i; ?>" class="sound_only">옵션</label>
                            <input type="text" name="io_name[<?php echo $i; ?>]" value="<?php echo $row['io_name']; ?>" id="io_name_<?php echo $i; ?>" class="frm_input" size="50"><br><br>
                            <span class="it_type_file"><input type="file" name="io_file[<?php echo $i; ?>]"><span class="ct_file_name"><?php if($row['io_source']) echo '&nbsp;&nbsp;등록된 파일 : '.$row['io_source']; ?></span></span>
                            <span class="it_type_url">
                                <input type="text"  name="io_url[<?php echo $i; ?>]" id="io_url_<?php echo $i; ?>" value="<?php echo $row['io_file']; ?>" class="frm_input" size="50"><br>
                                <label for="io_url_<?php echo $i; ?>">컨텐츠 파일 URL</label>
                            </span>
                        </td>
                        <td class="td_mng">
                            <label for="io_type_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_type[<?php echo $i; ?>]" id="io_type_<?php echo $i; ?>"<?php echo $io_type_disabled; ?>>
                                <option value=""<?php echo get_selected('', $io_type); ?>>선택</option>
                                <option value="flv"<?php echo get_selected('flv', $io_type); ?>>FLV</option>
                                <option value="youtube"<?php echo get_selected('youtube', $io_type); ?>>Youtube</option>
                                <option value="vimeo"<?php echo get_selected('vimeo', $io_type); ?>>Vimeo</option>
                                <option value="mp4"<?php echo get_selected('mp4', $io_type); ?>>MP4</option>
                                <option value="ogg"<?php echo get_selected('ogg', $io_type); ?>>OGV</option>
                                <option value="webm"<?php echo get_selected('webm', $io_type); ?>>WebM</option>
                            </select>
                        </td>
                        <td class="td_numsmall">
                            <label for="io_price_<?php echo $i; ?>" class="sound_only"></label>
                            <input type="text" name="io_price[<?php echo $i; ?>]" value="<?php echo $row['io_price']; ?>" id="io_price_<?php echo $i; ?>" class="frm_input" size="9">
                        </td>
                        <td class="td_mng">
                            <label for="io_download_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_download[<?php echo $i; ?>]" id="io_download_<?php echo $i; ?>">
                                <option value="1"<?php echo get_selected('1', $row['io_download']); ?>>가능</option>
                                <option value="0"<?php echo get_selected('0', $row['io_download']); ?>>불가능</option>
                            </select>
                        </td>
                        <td class="td_mng">
                            <label for="io_support_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_support[<?php echo $i; ?>]" id="io_support_<?php echo $i; ?>">
                                <option value="1"<?php echo get_selected('1', $row['io_support']); ?>>가능</option>
                                <option value="0"<?php echo get_selected('0', $row['io_support']); ?>>불가능</option>
                            </select>
                        </td>
                        <td class="td_mng">
                            <label for="io_use_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_use[<?php echo $i; ?>]" id="io_use_<?php echo $i; ?>">
                                <option value="1"<?php echo get_selected('1', $row['io_use']); ?>>사용함</option>
                                <option value="0"<?php echo get_selected('0', $row['io_use']); ?>>사용안함</option>
                            </select>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        for($i=0;$i<G5_CONTENTS_OPTION_COUNT;$i++) {
                            $io_type_disabled = ' disabled="disabled"';
                    ?>
                    <tr>
                        <td class="td_chk">
                            <label for="io_chk_<?php echo $i; ?>" class="sound_only"></label>
                            <input type="checkbox" name="io_chk[]" id="io_chk_<?php echo $i; ?>" value="1">
                        </td>
                        <td class="opt-cell">
                            <label for="io_name_<?php echo $i; ?>" class="sound_only">옵션명</label>
                            <input type="text" name="io_name[]" value="" id="io_name_<?php echo $i; ?>" class="frm_input" size="50"><br><br>
                            <span class="it_type_file"><input type="file" name="io_file[]"></span>
                            <span class="it_type_url">
                                <input type="text"  name="io_url[]" id="io_url_<?php echo $i; ?>" value="" class="frm_input" size="50"><br>
                                <label for="io_url_<?php echo $i; ?>">컨텐츠 파일 URL</label>
                            </span>
                        </td>
                        <td class="td_mng">
                            <label for="io_type_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_type[]" id="io_type_<?php echo $i; ?>"<?php echo $io_type_disabled; ?>>
                                <option value="">선택</option>
                                <option value="youtube">Youtube</option>
                                <option value="vimeo">Vimeo</option>
                                <option value="mp4">MP4</option>
                                <option value="ogg">OGV</option>
                                <option value="webm">WebM</option>
                            </select>
                        </td>
                        <td class="td_numsmall">
                            <label for="io_price_<?php echo $i; ?>" class="sound_only"></label>
                            <input type="text" name="io_price[]" value="<?php echo $io_price; ?>" id="io_price_<?php echo $i; ?>" class="frm_input" size="9">
                        </td>
                        <td class="td_mng">
                            <label for="io_download_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_download[]" id="io_download_<?php echo $i; ?>">
                                <option value="1">가능</option>
                                <option value="0">불가능</option>
                            </select>
                        </td>
                        <td class="td_mng">
                            <label for="io_support_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_support[]" id="io_support_<?php echo $i; ?>">
                                <option value="1">가능</option>
                                <option value="0">불가능</option>
                            </select>
                        </td>
                        <td class="td_mng">
                            <label for="io_use_<?php echo $i; ?>" class="sound_only"></label>
                            <select name="io_use[]" id="io_use_<?php echo $i; ?>">
                                <option value="1">사용함</option>
                                <option value="0">사용안함</option>
                            </select>
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                    </tbody>
                    </table>
                    <div id="sit_option_addfrm_btn">
                        <button type="button" id="add_option_row" class="btn_frmline">옵션추가</button>
                    </div>
                    <div class="btn_list01 btn_list">
                        <input type="button" value="선택삭제" id="sel_option_delete">
                    </div>
                </div>

                <script>
                $(function() {
                    change_file_field(parseInt(<?php echo $it['it_contents_type']; ?>));

                    // 타입별 input 변경
                    $("#it_contents_type").on("change", function() {
                        var val = parseInt($(this).val());

                        change_file_field(val);
                    });

                    // 입력필드추가
                    $("#add_option_row").click(function() {
                        var $el = $("#sit_option_fld tbody tr:last");

                        var inp_chk = "<input type=\"checkbox\" name=\"io_chk[]\" value=\"1\" id=\"io_chk_0\">";
                        var inp_name = "<input type=\"text\" name=\"io_name[]\" value=\"\" id=\"io_name_0\" class=\"frm_input\" size=\"50\">";
                        var inp_file = "<input type=\"file\" name=\"io_file[]\">";
                        var inp_url = "<input type=\"text\"  name=\"io_url[]\" id=\"io_url_0\" value=\"\" class=\"frm_input\" size=\"50\">";
                        var sel_type = "<select name=\"io_type[]\" id=\"io_type_0\">";
                        sel_type += "<option value=\"\">선택</option>";
                        sel_type += "<option value=\"flv\">FLV</option>";
                        sel_type += "<option value=\"youtube\">Youtube</option>";
                        sel_type += "<option value=\"vimeo\">Vimeo</option>";
                        sel_type += "<option value=\"mp4\">MP4</option>";
                        sel_type += "<option value=\"ogg\">OGV</option>";
                        sel_type += "<option value=\"webm\">WebM</option>";
                        sel_type += "</select>";
                        var inp_price = "<input type=\"text\" name=\"io_price[]\" value=\"<?php echo $io_price; ?>\" id=\"io_price_0\" class=\"frm_input\" size=\"9\">";
                        var sel_download = "<select name=\"io_download[]\" id=\"io_download_0\">";
                        sel_download += "<option value=\"1\">가능</option>";
                        sel_download += "<option value=\"0\">불가능</option>";
                        sel_download += "</select>";
                        var sel_support = "<select name=\"io_support[]\" id=\"io_support_0\">";
                        sel_support += "<option value=\"1\">가능</option>";
                        sel_support += "<option value=\"0\">불가능</option>";
                        sel_support += "</select>";
                        var sel_use = "<select name=\"io_use[]\" id=\"io_use_0\">";
                        sel_use += "<option value=\"1\">사용함</option>";
                        sel_use += "<option value=\"0\">사용안함</option>";
                        sel_use += "</select>";

                        $el.after(
                                $el.clone()
                                    .find("input[name^=io_chk]").after(inp_chk).remove().end()
                                    .find("input[name^=io_name]").after(inp_name).remove().end()
                                    .find("input:file").after(inp_file).remove().end()
                                    .find("input[name^=io_url]").after(inp_url).remove().end()
                                    .find("select[name^=io_type]").after(sel_type).remove().end()
                                    .find("input[name^=io_price]").after(inp_price).remove().end()
                                    .find("select[name^=io_download]").after(sel_download).remove().end()
                                    .find("select[name^=io_support]").after(sel_support).remove().end()
                                    .find("select[name^=io_use]").after(sel_use).remove().end()
                                    .find(".ct_file_name").remove().end()
                            );

                        var $label, $input, $select, tname;

                        $("#sit_option_fld tbody tr").each(function(index) {
                            $label = $(this).find("label");
                            $input = $(this).find("input").not(":file");
                            $select = $(this).find("select");

                            $label.each(function() {
                                $(this).attr("for", $(this).attr("for").replace(/_[0-9]+$/g, "_"+index));
                            });

                            $input.each(function() {
                                $(this).attr("id", $(this).attr("id").replace(/_[0-9]+$/g, "_"+index));
                            });

                            $select.each(function() {
                                $(this).attr("id", $(this).attr("id").replace(/_[0-9]+$/g, "_"+index));
                            });
                        });
                    });

                    // 모두선택
                    $("input[name=opt_chk_all]").click(function() {
                        if($(this).is(":checked")) {
                            $("input[name^=io_chk]").not(":disabled").attr("checked", true);
                        } else {
                            $("input[name^=io_chk]").not(":disabled").attr("checked", false);
                        }
                    });

                    // 선택삭제
                    $("#sel_option_delete").click(function() {
                        var $el = $("input[name^=io_chk]:checked");
                        if($el.size() < 1) {
                            alert("삭제하려는 옵션을 하나 이상 선택해 주십시오.");
                            return false;
                        }

                        $el.closest("tr").remove();
                    });

                    function change_file_field(val)
                    {
                        if(val < 3) { // 파일업로드
                            $(".it_type_file").show();
                            $(".it_type_url").hide();
                        } else {
                            $(".it_type_url").show();
                            $(".it_type_file").hide();
                        }

                        if(val == 2 || val == 3) {
                            $("select[name^=io_type]").attr("disabled", false);
                        } else {
                            $("select[name^=io_type]").attr("disabled", true);
                        }
                    }
                });
                </script>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_img">
    <h2 class="h2_frm">이미지</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>이미지 업로드</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <?php for($i=1; $i<=10; $i++) { ?>
        <tr>
            <th scope="row"><label for="it_img1">이미지 <?php echo $i; ?></label></th>
            <td>
                <input type="file" name="it_img<?php echo $i; ?>" id="it_img<?php echo $i; ?>">
                <?php
                $it_img = G5_DATA_PATH.'/cmitem/'.$it['it_img'.$i];
                if(is_file($it_img) && $it['it_img'.$i]) {
                    $size = @getimagesize($it_img);
                    $thumb = cm_get_it_thumbnail($it['it_img'.$i], 25, 25);
                ?>
                <label for="it_img<?php echo $i; ?>_del"><span class="sound_only">이미지 <?php echo $i; ?> </span>파일삭제</label>
                <input type="checkbox" name="it_img<?php echo $i; ?>_del" id="it_img<?php echo $i; ?>_del" value="1">
                <span class="sit_wimg_limg<?php echo $i; ?>"><?php echo $thumb; ?></span>
                <div id="limg<?php echo $i; ?>" class="banner_or_img">
                    <img src="<?php echo G5_DATA_URL; ?>/cmitem/<?php echo $it['it_img'.$i]; ?>" alt="" width="<?php echo $size[0]; ?>" height="<?php echo $size[1]; ?>">
                    <button type="button" class="sit_wimg_close">닫기</button>
                </div>
                <script>
                $('<button type="button" id="it_limg<?php echo $i; ?>_view" class="btn_frmline sit_wimg_view">이미지<?php echo $i; ?> 확인</button>').appendTo('.sit_wimg_limg<?php echo $i; ?>');
                </script>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_relation" class="srel">
    <h2 class="h2_frm">관련상품</h2>
    <?php echo $pg_anchor; ?>

    <div class="local_desc02 local_desc">
        <p>
            등록된 전체상품 목록에서 상품분류를 선택하면 해당 상품 리스트가 연이어 나타납니다.<br>
            상품리스트에서 관련 상품으로 추가하시면 선택된 관련상품 목록에 <strong>함께</strong> 추가됩니다.<br>
            예를 들어, A 상품에 B 상품을 관련상품으로 등록하면 B 상품에도 A 상품이 관련상품으로 자동 추가되며, <strong>확인 버튼을 누르셔야 정상 반영됩니다.</strong>
        </p>
    </div>

    <div class="compare_wrap">
        <section class="compare_left">
            <h3>등록된 전체상품 목록</h3>
            <label for="sch_relation" class="sound_only">상품분류</label>
            <span class="srel_pad">
                <select id="sch_relation">
                    <option value=''>분류별 상품</option>
                    <?php
                        $sql = " select * from {$g5['g5_contents_category_table']} ";
                        if ($is_admin != 'super')
                            $sql .= " where ca_mb_id = '{$member['mb_id']}' ";
                        $sql .= " order by ca_order, ca_id ";
                        $result = sql_query($sql);
                        for ($i=0; $row=sql_fetch_array($result); $i++)
                        {
                            $len = strlen($row['ca_id']) / 2 - 1;

                            $nbsp = "";
                            for ($i=0; $i<$len; $i++)
                                $nbsp .= "&nbsp;&nbsp;&nbsp;";

                            echo "<option value=\"{$row['ca_id']}\">$nbsp{$row['ca_name']}</option>\n";
                        }
                    ?>
                </select>
                <label for="sch_name" class="sound_only">상품명</label>
                <input type="text" name="sch_name" id="sch_name" class="frm_input" size="15">
                <button type="button" id="btn_search_item" class="btn_frmline">검색</button>
            </span>
            <div id="relation" class="srel_list">
                <p>상품의 분류를 선택하시거나 상품명을 입력하신 후 검색하여 주십시오.</p>
            </div>
            <script>
            $(function() {
                $("#btn_search_item").click(function() {
                    var ca_id = $("#sch_relation").val();
                    var it_name = $.trim($("#sch_name").val());
                    var $relation = $("#relation");

                    if(ca_id == "" && it_name == "") {
                        $relation.html("<p>상품의 분류를 선택하시거나 상품명을 입력하신 후 검색하여 주십시오.</p>");
                        return false;
                    }

                    $("#relation").load(
                        "./itemformrelation.php",
                        { it_id: "<?php echo $it_id; ?>", ca_id: ca_id, it_name: it_name }
                    );
                });

                $(document).on("click", "#relation .add_item", function() {
                    // 이미 등록된 상품인지 체크
                    var $li = $(this).closest("li");
                    var it_id = $li.find("input:hidden").val();
                    var it_id2;
                    var dup = false;
                    $("#reg_relation input[name='re_it_id[]']").each(function() {
                        it_id2 = $(this).val();
                        if(it_id == it_id2) {
                            dup = true;
                            return false;
                        }
                    });

                    if(dup) {
                        alert("이미 선택된 상품입니다.");
                        return false;
                    }

                    var cont = "<li>"+$li.html().replace("add_item", "del_item").replace("추가", "삭제")+"</li>";
                    var count = $("#reg_relation li").size();

                    if(count > 0) {
                        $("#reg_relation li:last").after(cont);
                    } else {
                        $("#reg_relation").html("<ul>"+cont+"</ul>");
                    }

                    $li.remove();
                });

                $(document).on("click", "#reg_relation .del_item", function() {
                    if(!confirm("상품을 삭제하시겠습니까?"))
                        return false;

                    $(this).closest("li").remove();

                    var count = $("#reg_relation li").size();
                    if(count < 1)
                        $("#reg_relation").html("<p>선택된 상품이 없습니다.</p>");
                });
            });
            </script>
        </section>

        <section class="compare_right">
            <h3>선택된 관련상품 목록</h3>
            <span class="srel_pad"></span>
            <div id="reg_relation" class="srel_sel">
                <?php
                $str = array();
                $sql = " select b.ca_id, b.it_id, b.it_name, b.it_price
                           from {$g5['g5_contents_item_relation_table']} a
                           left join {$g5['g5_contents_item_table']} b on (a.it_id2=b.it_id)
                          where a.it_id = '$it_id'
                          order by ir_no asc ";
                $result = sql_query($sql);
                for($g=0; $row=sql_fetch_array($result); $g++)
                {
                    $it_name = cm_get_it_image($row['it_id'], 50, 50).' '.$row['it_name'];

                    if($g==0)
                        echo '<ul>';
                ?>
                    <li>
                        <input type="hidden" name="re_it_id[]" value="<?php echo $row['it_id']; ?>">
                        <div class="list_item"><?php echo $it_name; ?></div>
                        <div class="list_item_btn"><button type="button" class="del_item btn_frmline">삭제</button></div>
                    </li>
                <?php
                    $str[] = $row['it_id'];
                }
                $str = implode(",", $str);

                if($g > 0)
                    echo '</ul>';
                else
                    echo '<p>선택된 상품이 없습니다.</p>';
                ?>
            </div>
            <input type="hidden" name="it_list" value="<?php echo $str; ?>">
        </section>

    </div>

</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_event" class="srel">
    <h2 class="h2_frm">관련이벤트</h2>
    <?php echo $pg_anchor; ?>

    <div class="compare_wrap">
        <section class="compare_left">
            <h3>등록된 전체이벤트 목록</h3>
            <div id="event_list" class="srel_list srel_noneimg">
                <?php
                $sql = " select ev_id, ev_subject from {$g5['g5_contents_event_table']} order by ev_id desc ";
                $result = sql_query($sql);
                for ($g=0; $row=sql_fetch_array($result); $g++) {
                    if($g == 0)
                        echo '<ul>';
                ?>
                    <li>
                        <input type="hidden" name="ev_id[]" value="<?php echo $row['ev_id']; ?>">
                        <div class="list_item"><?php echo get_text($row['ev_subject']); ?></div>
                        <div class="list_item_btn"><button type="button" class="add_event btn_frmline">추가</button></div>
                    </li>
                <?php
                }

                if($g > 0)
                    echo '</ul>';
                else
                    echo '<p>등록된 이벤트가 없습니다.</p>';
                ?>
            </div>
            <script>
            $(function() {
                $(document).on("click", "#event_list .add_event", function() {
                    // 이미 등록된 이벤트인지 체크
                    var $li = $(this).closest("li");
                    var ev_id = $li.find("input:hidden").val();
                    var ev_id2;
                    var dup = false;
                    $("#reg_event_list input[name='ev_id[]']").each(function() {
                        ev_id2 = $(this).val();
                        if(ev_id == ev_id2) {
                            dup = true;
                            return false;
                        }
                    });

                    if(dup) {
                        alert("이미 선택된 이벤트입니다.");
                        return false;
                    }

                    var cont = "<li>"+$li.html().replace("add_event", "del_event").replace("추가", "삭제")+"</li>";
                    var count = $("#reg_event_list li").size();

                    if(count > 0) {
                        $("#reg_event_list li:last").after(cont);
                    } else {
                        $("#reg_event_list").html("<ul>"+cont+"</ul>");
                    }
                });

                $(document).on("click", "#reg_event_list .del_event", function() {
                    if(!confirm("상품을 삭제하시겠습니까?"))
                        return false;

                    $(this).closest("li").remove();

                    var count = $("#reg_event_list li").size();
                    if(count < 1)
                        $("#reg_event_list").html("<p>선택된 이벤트가 없습니다.</p>");
                });
            });
            </script>
        </section>

        <section class="compare_right">
            <h3>선택된 관련이벤트 목록</h3>
            <div id="reg_event_list" class="srel_sel srel_noneimg">
                <?php
                $str = "";
                $comma = "";
                $sql = " select b.ev_id, b.ev_subject
                           from {$g5['g5_contents_event_item_table']} a
                           left join {$g5['g5_contents_event_table']} b on (a.ev_id=b.ev_id)
                          where a.it_id = '$it_id'
                          order by b.ev_id desc ";
                $result = sql_query($sql);
                for ($g=0; $row=sql_fetch_array($result); $g++) {
                    $str .= $comma . $row['ev_id'];
                    $comma = ",";

                    if($g == 0)
                        echo '<ul>';
                ?>
                    <li>
                        <input type="hidden" name="ev_id[]" value="<?php echo $row['ev_id']; ?>">
                        <div class="list_item"><?php echo get_text($row['ev_subject']); ?></div>
                        <div class="list_item_btn"><button type="button" class="del_event btn_frmline">삭제</button></div>
                    </li>
                <?php
                }

                if($g > 0)
                    echo '</ul>';
                else
                    echo '<p>선택된 이벤트가 없습니다.</p>';
                ?>
            </div>
            <input type="hidden" name="ev_list" value="<?php echo $str; ?>">
        </section>
    </div>

</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_optional">
    <h2 class="h2_frm">상세설명설정</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>상세설명설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">상품상단내용</th>
            <td><?php echo help("상품상세설명 페이지 상단에 출력하는 HTML 내용입니다."); ?><?php echo editor_html('it_head_html', $it['it_head_html']); ?></td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_head_html" value="1" id="chk_ca_it_head_html">
                <label for="chk_ca_it_head_html">분류적용</label>
                <input type="checkbox" name="chk_all_it_head_html" value="1" id="chk_all_it_head_html">
                <label for="chk_all_it_head_html">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row">상품하단내용</th>
            <td><?php echo help("상품상세설명 페이지 하단에 출력하는 HTML 내용입니다."); ?><?php echo editor_html('it_tail_html', $it['it_tail_html']); ?></td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_tail_html" value="1" id="chk_ca_it_tail_html">
                <label for="chk_ca_it_tail_html">분류적용</label>
                <input type="checkbox" name="chk_all_it_tail_html" value="1" id="chk_all_it_tail_html">
                <label for="chk_all_it_tail_html">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 상품상단내용</th>
            <td><?php echo help("모바일 상품상세설명 페이지 상단에 출력하는 HTML 내용입니다."); ?><?php echo editor_html('it_mobile_head_html', $it['it_mobile_head_html']); ?></td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_mobile_head_html" value="1" id="chk_ca_it_mobile_head_html">
                <label for="chk_ca_it_mobile_head_html">분류적용</label>
                <input type="checkbox" name="chk_all_it_mobile_head_html" value="1" id="chk_all_it_mobile_head_html">
                <label for="chk_all_it_mobile_head_html">전체적용</label>
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 상품하단내용</th>
            <td><?php echo help("모바일 상품상세설명 페이지 하단에 출력하는 HTML 내용입니다."); ?><?php echo editor_html('it_mobile_tail_html', $it['it_mobile_tail_html']); ?></td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_it_mobile_tail_html" value="1" id="chk_ca_it_mobile_tail_html">
                <label for="chk_ca_it_mobile_tail_html">분류적용</label>
                <input type="checkbox" name="chk_all_it_mobile_tail_html" value="1" id="chk_all_it_mobile_tail_html">
                <label for="chk_all_it_mobile_tail_html">전체적용</label>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_sitfrm_extra">
    <h2>여분필드 설정</h2>
    <?php echo $pg_anchor ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_3">
        </colgroup>
        <tbody>
        <?php for ($i=1; $i<=10; $i++) { ?>
        <tr>
            <th scope="row">여분필드<?php echo $i ?></th>
            <td class="td_extra">
                <label for="it_<?php echo $i ?>_subj">여분필드 <?php echo $i ?> 제목</label>
                <input type="text" name="it_<?php echo $i ?>_subj" id="it_<?php echo $i ?>_subj" value="<?php echo get_text($it['it_'.$i.'_subj']) ?>" class="frm_input">
                <label for="it_<?php echo $i ?>">여분필드 <?php echo $i ?> 값</label>
                <input type="text" name="it_<?php echo $i ?>" value="<?php echo get_text($it['it_'.$i]) ?>" id="it_<?php echo $i ?>" class="frm_input">
            </td>
            <td class="td_grpset">
                <input type="checkbox" name="chk_ca_<?php echo $i ?>" value="1" id="chk_ca_<?php echo $i ?>">
                <label for="chk_ca_<?php echo $i ?>">분류적용</label>
                <input type="checkbox" name="chk_all_<?php echo $i ?>" value="1" id="chk_all_<?php echo $i ?>">
                <label for="chk_all_<?php echo $i ?>">전체적용</label>
            </td>
        </tr>
        <?php } ?>
        <?php if ($w == "u") { ?>
        <tr>
            <th scope="row">입력일시</th>
            <td colspan="2">
                <?php echo help("상품을 처음 입력(등록)한 시간입니다."); ?>
                <?php echo $it['it_time']; ?>
            </td>
        </tr>
        <tr>
            <th scope="row">수정일시</th>
            <td colspan="2">
                <?php echo help("상품을 최종 수정한 시간입니다."); ?>
                <?php echo $it['it_update_time']; ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>
</form>


<script>
var f = document.fitemform;

<?php if ($w == 'u') { ?>
$(".banner_or_img").addClass("sit_wimg");
$(function() {
    $(".sit_wimg_view").bind("click", function() {
        var sit_wimg_id = $(this).attr("id").split("_");
        var $img_display = $("#"+sit_wimg_id[1]);

        $img_display.toggle();

        if($img_display.is(":visible")) {
            $(this).text($(this).text().replace("확인", "닫기"));
        } else {
            $(this).text($(this).text().replace("닫기", "확인"));
        }

        var $img = $("#"+sit_wimg_id[1]).children("img");
        var width = $img.width();
        var height = $img.height();
        if(width > 700) {
            var img_width = 700;
            var img_height = Math.round((img_width * height) / width);

            $img.width(img_width).height(img_height);
        }
    });
    $(".sit_wimg_close").bind("click", function() {
        var $img_display = $(this).parents(".banner_or_img");
        var id = $img_display.attr("id");
        $img_display.toggle();
        var $button = $("#it_"+id+"_view");
        $button.text($button.text().replace("닫기", "확인"));
    });
});
<?php } ?>

function codedupcheck(id)
{
    if (!id) {
        alert('상품코드를 입력하십시오.');
        f.it_id.focus();
        return;
    }

    var it_id = id.replace(/[A-Za-z0-9\-_]/g, "");
    if(it_id.length > 0) {
        alert("상품코드는 영문자, 숫자, -, _ 만 사용할 수 있습니다.");
        return false;
    }

    $.post(
        "./codedupcheck.php",
        { it_id: id },
        function(data) {
            if(data.name) {
                alert("코드 '"+data.code+"' 는 '".data.name+"' (으)로 이미 등록되어 있으므로\n\n사용하실 수 없습니다.");
                return false;
            } else {
                alert("'"+data.code+"' 은(는) 등록된 코드가 없으므로 사용하실 수 있습니다.");
                document.fitemform.codedup.value = '';
            }
        }, "json"
    );
}

function fitemformcheck(f)
{
    if (!f.ca_id.value) {
        alert("기본분류를 선택하십시오.");
        f.ca_id.focus();
        return false;
    }

    if (f.w.value == "") {
        var error = "";
        $.ajax({
            url: "./ajax.it_id.php",
            type: "POST",
            data: {
                "it_id": f.it_id.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                error = data.error;
            }
        });

        if (error) {
            alert(error);
            return false;
        }
    }

    if(f.it_point_type.value == "1") {
        var point = parseInt(f.it_point.value);
        if(point > 99) {
            alert("포인트 비율을 0과 99 사이의 값으로 입력해 주십시오.");
            return false;
        }
    }

    // 관련상품처리
    var item = new Array();
    var re_item = it_id = "";

    $("#reg_relation input[name='re_it_id[]']").each(function() {
        it_id = $(this).val();
        if(it_id == "")
            return true;

        item.push(it_id);
    });

    if(item.length > 0)
        re_item = item.join();

    $("input[name=it_list]").val(re_item);

    // 이벤트처리
    var evnt = new Array();
    var ev = ev_id = "";

    $("#reg_event_list input[name='ev_id[]']").each(function() {
        ev_id = $(this).val();
        if(ev_id == "")
            return true;

        evnt.push(ev_id);
    });

    if(evnt.length > 0)
        ev = evnt.join();

    $("input[name=ev_list]").val(ev);

    // 옵션 입력 체크
    var option_count = 0;
    $("input[name^=io_name]").each(function() {
        if($.trim($(this).val()).length > 0)
            option_count++;
    });

    if(option_count == 0) {
        alert("상품옵션을 하나이상 입력해 주십시오.");
        return false;
    }

    <?php echo get_editor_js('it_explan'); ?>
    <?php echo get_editor_js('it_mobile_explan'); ?>
    <?php echo get_editor_js('it_head_html'); ?>
    <?php echo get_editor_js('it_tail_html'); ?>
    <?php echo get_editor_js('it_mobile_head_html'); ?>
    <?php echo get_editor_js('it_mobile_tail_html'); ?>

    return true;
}

function categorychange(f)
{
    var idx = f.ca_id.value;

    if (f.w.value == "" && idx)
    {
        f.it_use.checked = ca_use[idx] ? true : false;
        f.it_sell_email.value = ca_sell_email[idx];
    }
}

categorychange(document.fitemform);
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
