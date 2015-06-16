<?php
$sub_menu = '600100';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "r");

if (!$config['cf_icode_server_ip'])   $config['cf_icode_server_ip'] = '211.172.232.124';
if (!$config['cf_icode_server_port']) $config['cf_icode_server_port'] = '7295';

if ($config['cf_icode_id'] && $config['cf_icode_pw']) {
    $userinfo = get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);
}

$g5['title'] = '컨텐츠몰설정';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$pg_anchor = '<ul class="anchor">
<li><a href="#anc_scf_info">사업자정보</a></li>
<li><a href="#anc_scf_lay">레이아웃 설정</a></li>
<li><a href="#anc_scf_skin">스킨설정</a></li>
<li><a href="#anc_scf_index">컨텐츠몰 초기화면</a></li>
<li><a href="#anc_mscf_index">모바일 초기화면</a></li>
<li><a href="#anc_scf_payment">결제설정</a></li>
<li><a href="#anc_scf_chub">컨텐츠허브 이용설정</a></li>
<li><a href="#anc_scf_etc">기타설정</a></li>
<li><a href="#anc_scf_sms">SMS설정</a></li>
</ul>';

$frm_submit = '<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="'.G5_CONTENTS_URL.'">컨텐츠몰</a>
</div>';
?>

<form name="fconfig" action="./configformupdate.php" onsubmit="return fconfig_check(this)" method="post" enctype="MULTIPART/FORM-DATA">
<section id="anc_scf_info">
    <h2 class="h2_frm">사업자정보</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>사업자정보는 tail.php 와 content.php 에서 표시합니다.</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>사업자정보 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="de_admin_company_name">회사명</label></th>
            <td>
                <input type="text" name="de_admin_company_name" value="<?php echo $setting['de_admin_company_name']; ?>" id="de_admin_company_name" class="frm_input" size="30">
            </td>
            <th scope="row"><label for="de_admin_company_saupja_no">사업자등록번호</label></th>
            <td>
                <input type="text" name="de_admin_company_saupja_no"  value="<?php echo $setting['de_admin_company_saupja_no']; ?>" id="de_admin_company_saupja_no" class="frm_input" size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_admin_company_owner">대표자명</label></th>
            <td colspan="3">
                <input type="text" name="de_admin_company_owner" value="<?php echo $setting['de_admin_company_owner']; ?>" id="de_admin_company_owner" class="frm_input" size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_admin_company_tel">대표전화번호</label></th>
            <td>
                <input type="text" name="de_admin_company_tel" value="<?php echo $setting['de_admin_company_tel']; ?>" id="de_admin_company_tel" class="frm_input" size="30">
            </td>
            <th scope="row"><label for="de_admin_company_fax">팩스번호</label></th>
            <td>
                <input type="text" name="de_admin_company_fax" value="<?php echo $setting['de_admin_company_fax']; ?>" id="de_admin_company_fax" class="frm_input" size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_admin_tongsin_no">통신판매업 신고번호</label></th>
            <td>
                <input type="text" name="de_admin_tongsin_no" value="<?php echo $setting['de_admin_tongsin_no']; ?>" id="de_admin_tongsin_no" class="frm_input" size="30">
            </td>
            <th scope="row"><label for="de_admin_buga_no">부가통신 사업자번호</label></th>
            <td>
                <input type="text" name="de_admin_buga_no" value="<?php echo $setting['de_admin_buga_no']; ?>" id="de_admin_buga_no" class="frm_input" size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_admin_company_zip">사업장우편번호</label></th>
            <td>
                <input type="text" name="de_admin_company_zip" value="<?php echo $setting['de_admin_company_zip']; ?>" id="de_admin_company_zip" class="frm_input" size="10">
            </td>
            <th scope="row"><label for="de_admin_company_addr">사업장주소</label></th>
            <td>
                <input type="text" name="de_admin_company_addr" value="<?php echo $setting['de_admin_company_addr']; ?>" id="de_admin_company_addr" class="frm_input" size="30">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_admin_info_name">정보관리책임자명</label></th>
            <td>
                <input type="text" name="de_admin_info_name" value="<?php echo $setting['de_admin_info_name']; ?>" id="de_admin_info_name" class="frm_input" size="30">
            </td>
            <th scope="row"><label for="de_admin_info_email">정보책임자 e-mail</label></th>
            <td>
                <input type="text" name="de_admin_info_email" value="<?php echo $setting['de_admin_info_email']; ?>" id="de_admin_info_email" class="frm_input" size="30">
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_scf_lay">
    <h2 class="h2_frm">레이아웃 설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>기본 설정된 파일 등을 변경할 수 있습니다.</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>레이아웃 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="de_include_index">초기화면 파일</label></th>
            <td>
                <?php echo help('입력이 없으면 '.G5_CONTENTS_DIR.'/index.php가 초기화면 파일로 설정됩니다.<br>초기화면 파일은 '.G5_CONTENTS_DIR.'/index.php 파일과 동일한 위치에 존재해야 합니다.') ?>
                <input type="text" name="de_include_index" value="<?php echo $setting['de_include_index'] ?>" id="de_include_index" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_include_head">상단 파일</label></th>
            <td>
                <?php echo help('입력이 없으면 '.G5_CONTENTS_DIR.'/shop.head.php가 상단 파일로 설정됩니다.<br>상단 파일은 '.G5_CONTENTS_DIR.'/shop.head.php 파일과 동일한 위치에 존재해야 합니다.') ?>
                <input type="text" name="de_include_head" value="<?php echo $setting['de_include_head'] ?>" id="de_include_head" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_include_tail">하단 파일</label></th>
            <td>
                <?php echo help('입력이 없으면 '.G5_CONTENTS_DIR.'/shop.tail.php가 하단 파일로 설정됩니다.<br>하단 파일은 '.G5_CONTENTS_DIR.'/shop.tail.php 파일과 동일한 위치에 존재해야 합니다.') ?>
                <input type="text" name="de_include_tail" value="<?php echo $setting['de_include_tail'] ?>" id="de_include_tail" class="frm_input" size="50">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_root_index_use">루트 index 사용</label></th>
            <td>
                <?php echo help('쇼핑몰의 접속경로를 '.G5_CONTENTS_URL.' 에서 '.G5_URL.' 으로 변경하시려면 사용으로 설정해 주십시오.'); ?>
                <select name="de_root_index_use" id="de_root_index_use">
                    <option value="0" <?php echo get_selected($setting['de_root_index_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_root_index_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_contents_layout_use">컨텐츠몰 레이아웃 사용</label></th>
            <td>
                <?php echo help('커뮤니티의 레이아웃을 컨텐츠몰과 동일하게 적용하시려면 사용으로 설정해 주십시오.'); ?>
                <select name="de_contents_layout_use" id="de_contents_layout_use">
                    <option value="0" <?php echo get_selected($setting['de_contents_layout_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_contents_layout_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>

        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_scf_skin">
    <h2 class="h2_frm">스킨설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>상품 분류리스트, 상품상세보기 등 에서 사용할 스킨을 설정합니다.</p>
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
            <th scope="row"><label for="de_contents_skin">PC용 스킨</label></th>
            <td colspan="3">
                <select name="de_contents_skin" id="de_contents_skin" required class="required">
                <?php
                $arr = get_skin_dir('contents');
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">선택</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($setting['de_contents_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_contents_mobile_skin">모바일용 스킨</label></th>
            <td colspan="3">
                <select name="de_contents_mobile_skin" id="de_contents_mobile_skin" required class="required">
                <?php
                $arr = get_skin_dir('contents', G5_MOBILE_PATH.'/'.G5_SKIN_DIR);
                for ($i=0; $i<count($arr); $i++) {
                    if ($i == 0) echo "<option value=\"\">선택</option>";
                    echo "<option value=\"".$arr[$i]."\"".get_selected($setting['de_contents_mobile_skin'], $arr[$i]).">".$arr[$i]."</option>\n";
                }
                ?>
                </select>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_scf_index">
    <h2 class="h2_frm">쇼핑몰 초기화면</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>
            상품관리에서 선택한 상품의 타입대로 쇼핑몰 초기화면에 출력합니다. (상품 타입 추천/인기/최신/할인)<br>
            각 타입별로 선택된 상품이 없으면 쇼핑몰 초기화면에 출력하지 않습니다.
        </p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>쇼핑몰 초기화면 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">추천상품출력</th>
            <td>
                <label for="de_type1_list_use">출력</label>
                <input type="checkbox" name="de_type1_list_use" value="1" id="de_type1_list_use" <?php echo $setting['de_type1_list_use']?"checked":""; ?>>
                <label for="de_type1_list_skin">스킨</label>
                <select name="de_type1_list_skin" id="de_type1_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_type1_list_skin']); ?>
                </select>
                <label for="de_type1_list_mod">1줄당 이미지 수</label>
                <input type="text" name="de_type1_list_mod" value="<?php echo $setting['de_type1_list_mod']; ?>" id="de_type1_list_mod" class="frm_input" size="3">
                <label for="de_type1_list_row">출력할 줄 수</label>
                <input type="text" name="de_type1_list_row" value="<?php echo $setting['de_type1_list_row']; ?>" id="de_type1_list_row" class="frm_input" size="3">
                <label for="de_type1_img_width">이미지 폭</label>
                <input type="text" name="de_type1_img_width" value="<?php echo $setting['de_type1_img_width']; ?>" id="de_type1_img_width" class="frm_input" size="3">
                <label for="de_type1_img_height">이미지 높이</label>
                <input type="text" name="de_type1_img_height" value="<?php echo $setting['de_type1_img_height']; ?>" id="de_type1_img_height" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">인기상품출력</th>
            <td>
                <label for="de_type2_list_use">출력</label>
                <input type="checkbox" name="de_type2_list_use" value="1" id="de_type2_list_use" <?php echo $setting['de_type2_list_use']?"checked":""; ?>>
                <label for="de_type2_list_skin">스킨</label>
                <select name="de_type2_list_skin" id="de_type2_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_type2_list_skin']); ?>
                </select>
                <label for="de_type2_list_mod">1줄당 이미지 수</label>
                <input type="text" name="de_type2_list_mod" value="<?php echo $setting['de_type2_list_mod']; ?>" id="de_type2_list_mod" class="frm_input" size="3">
                <label for="de_type2_list_row">출력할 줄 수</label>
                <input type="text" name="de_type2_list_row" value="<?php echo $setting['de_type2_list_row']; ?>" id="de_type2_list_row" class="frm_input" size="3">
                <label for="de_type2_img_width">이미지 폭</label>
                <input type="text" name="de_type2_img_width" value="<?php echo $setting['de_type2_img_width']; ?>" id="de_type2_img_width" class="frm_input" size="3">
                <label for="de_type2_img_height">이미지 높이</label>
                <input type="text" name="de_type2_img_height" value="<?php echo $setting['de_type2_img_height']; ?>" id="de_type2_img_height" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">최신상품출력</th>
            <td>
                <label for="de_type3_list_use">출력</label>
                <input type="checkbox" name="de_type3_list_use" value="1" id="de_type3_list_use" <?php echo $setting['de_type3_list_use']?"checked":""; ?>>
                <label for="de_type3_list_skin">스킨</label>
                <select name="de_type3_list_skin" id="de_type3_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_type3_list_skin']); ?>
                </select>
                <label for="de_type3_list_mod">1줄당 이미지 수</label>
                <input type="text" name="de_type3_list_mod" value="<?php echo $setting['de_type3_list_mod']; ?>" id="de_type3_list_mod" class="frm_input" size="3">
                <label for="de_type3_list_row">출력할 줄 수</label>
                <input type="text" name="de_type3_list_row" value="<?php echo $setting['de_type3_list_row']; ?>" id="de_type3_list_row" class="frm_input" size="3">
                <label for="de_type3_img_width">이미지 폭</label>
                <input type="text" name="de_type3_img_width" value="<?php echo $setting['de_type3_img_width']; ?>" id="de_type3_img_width" class="frm_input" size="3">
                <label for="de_type3_img_height">이미지 높이</label>
                <input type="text" name="de_type3_img_height" value="<?php echo $setting['de_type3_img_height']; ?>" id="de_type3_img_height" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">할인상품출력</th>
            <td>
                <label for="de_type4_list_use">출력</label>
                <input type="checkbox" name="de_type4_list_use" value="1" id="de_type4_list_use" <?php echo $setting['de_type4_list_use']?"checked":""; ?>>
                <label for="de_type4_list_skin">스킨</label>
                <select name="de_type4_list_skin" id="de_type4_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_type4_list_skin']); ?>
                </select>
                <label for="de_type4_list_mod">1줄당 이미지 수</label>
                <input type="text" name="de_type4_list_mod" value="<?php echo $setting['de_type4_list_mod']; ?>" id="de_type4_list_mod" class="frm_input" size="3">
                <label for="de_type4_list_row">출력할 줄 수</label>
                <input type="text" name="de_type4_list_row" value="<?php echo $setting['de_type4_list_row']; ?>" id="de_type4_list_row" class="frm_input" size="3">
                <label for="de_type4_img_width">이미지 폭</label>
                <input type="text" name="de_type4_img_width" value="<?php echo $setting['de_type4_img_width']; ?>" id="de_type4_img_width" class="frm_input" size="3">
                <label for="de_type4_img_height">이미지 높이</label>
                <input type="text" name="de_type4_img_height" value="<?php echo $setting['de_type4_img_height']; ?>" id="de_type4_img_height" class="frm_input" size="3">
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_mscf_index">
    <h2 class="h2_frm">모바일 쇼핑몰 초기화면 설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>
            상품관리에서 선택한 상품의 타입대로 쇼핑몰 초기화면에 출력합니다. (상품 타입 추천/인기/최신/할인)<br>
            각 타입별로 선택된 상품이 없으면 쇼핑몰 초기화면에 출력하지 않습니다.
        </p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>모바일 쇼핑몰 초기화면 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">추천상품출력</th>
            <td>
                <label for="de_mobile_type1_list_use">출력</label>
                <input type="checkbox" name="de_mobile_type1_list_use" value="1" id="de_mobile_type1_list_use" <?php echo $setting['de_mobile_type1_list_use']?"checked":""; ?>>
                <label for="de_mobile_type1_list_skin">스킨</label>
                <select name="de_mobile_type1_list_skin" id="de_mobile_type1_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_MCONTENTS_SKIN_PATH, $setting['de_mobile_type1_list_skin']); ?>
                </select>
                <label for="de_mobile_type1_list_mod">출력할 이미지 수</label>
                <input type="text" name="de_mobile_type1_list_mod" value="<?php echo $setting['de_mobile_type1_list_mod']; ?>" id="de_mobile_type1_list_mod" class="frm_input" size="3">
                <label for="de_mobile_type1_img_width">이미지 폭</label>
                <input type="text" name="de_mobile_type1_img_width" value="<?php echo $setting['de_mobile_type1_img_width']; ?>" id="de_mobile_type1_img_width" class="frm_input" size="3">
                <label for="de_mobile_type1_img_height">이미지 높이</label>
                <input type="text" name="de_mobile_type1_img_height" value="<?php echo $setting['de_mobile_type1_img_height']; ?>" id="de_mobile_type1_img_height" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">인기상품출력</th>
            <td>
                <label for="de_mobile_type2_list_use">출력</label> <input type="checkbox" name="de_mobile_type2_list_use" value="1" id="de_mobile_type2_list_use" <?php echo $setting['de_mobile_type2_list_use']?"checked":""; ?>>
                <label for="de_mobile_type2_list_skin">스킨 </label>
                <select name="de_mobile_type2_list_skin" id="de_mobile_type2_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_MCONTENTS_SKIN_PATH, $setting['de_mobile_type2_list_skin']); ?>
                </select>
                <label for="de_mobile_type2_list_mod">출력할 이미지 수</label>
                <input type="text" name="de_mobile_type2_list_mod" value="<?php echo $setting['de_mobile_type2_list_mod']; ?>" id="de_mobile_type2_list_mod" class="frm_input" size="3">
                <label for="de_mobile_type2_img_width">이미지 폭</label>
                <input type="text" name="de_mobile_type2_img_width" value="<?php echo $setting['de_mobile_type2_img_width']; ?>" id="de_mobile_type2_img_width" class="frm_input" size="3">
                <label for="de_mobile_type2_img_height">이미지 높이</label>
                <input type="text" name="de_mobile_type2_img_height" value="<?php echo $setting['de_mobile_type2_img_height']; ?>" id="de_mobile_type2_img_height" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">최신상품출력</th>
            <td>
                <label for="de_mobile_type3_list_use">출력</label>
                <input type="checkbox" name="de_mobile_type3_list_use" value="1" id="de_mobile_type3_list_use" <?php echo $setting['de_mobile_type3_list_use']?"checked":""; ?>>
                <label for="de_mobile_type3_list_skin">스킨</label>
                <select name="de_mobile_type3_list_skin" id="de_mobile_type3_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_MCONTENTS_SKIN_PATH, $setting['de_mobile_type3_list_skin']); ?>
                </select>
                <label for="de_mobile_type3_list_mod">출력할 이미지 수</label>
                <input type="text" name="de_mobile_type3_list_mod" value="<?php echo $setting['de_mobile_type3_list_mod']; ?>" id="de_mobile_type3_list_mod" class="frm_input" size="3">
                <label for="de_mobile_type3_img_width">이미지 폭</label>
                <input type="text" name="de_mobile_type3_img_width" value="<?php echo $setting['de_mobile_type3_img_width']; ?>" id="de_mobile_type3_img_width" class="frm_input" size="3">
                <label for="de_mobile_type3_img_height">이미지 높이</label>
                <input type="text" name="de_mobile_type3_img_height" value="<?php echo $setting['de_mobile_type3_img_height']; ?>" id="de_mobile_type3_img_height" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">할인상품출력</th>
            <td>
                <label for="de_mobile_type4_list_use">출력</label>
                <input type="checkbox" name="de_mobile_type4_list_use" value="1" id="de_mobile_type4_list_use" <?php echo $setting['de_mobile_type4_list_use']?"checked":""; ?>>
                <label for="de_mobile_type4_list_skin">스킨</label>
                <select name="de_mobile_type4_list_skin" id="de_mobile_type4_list_skin">
                    <?php echo cm_get_list_skin_options("^main.[0-9]+\.skin\.php", G5_MCONTENTS_SKIN_PATH, $setting['de_mobile_type4_list_skin']); ?>
                </select>
                <label for="de_mobile_type4_list_mod">출력할 이미지 수</label>
                <input type="text" name="de_mobile_type4_list_mod" value="<?php echo $setting['de_mobile_type4_list_mod']; ?>" id="de_mobile_type4_list_mod" class="frm_input" size="3">
                <label for="de_mobile_type4_img_width">이미지 폭</label>
                <input type="text" name="de_mobile_type4_img_width" value="<?php echo $setting['de_mobile_type4_img_width']; ?>" id="de_mobile_type4_img_width" class="frm_input" size="3">
                <label for="de_mobile_type4_img_height">이미지 높이</label>
                <input type="text" name="de_mobile_type4_img_height" value="<?php echo $setting['de_mobile_type4_img_height']; ?>" id="de_mobile_type4_img_height" class="frm_input" size="3">
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id ="anc_scf_payment">
    <h2 class="h2_frm">결제설정</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>결제설정 입력</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="de_bank_use">무통장입금사용</label></th>
            <td>
                <?php echo help("주문시 무통장으로 입금을 가능하게 할것인지를 설정합니다.\n사용할 경우 은행계좌번호를 반드시 입력하여 주십시오."); ?>
                <select id="de_bank_use" name="de_bank_use">
                    <option value="0" <?php echo get_selected($setting['de_bank_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_bank_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_bank_account">은행계좌번호</label></th>
            <td>
                <textarea name="de_bank_account" id="de_bank_account"><?php echo $setting['de_bank_account']; ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_iche_use">계좌이체 결제사용</label></th>
            <td>
            <?php echo help("주문시 실시간 계좌이체를 가능하게 할것인지를 설정합니다."); ?>
                <select id="de_iche_use" name="de_iche_use">
                    <option value="0" <?php echo get_selected($setting['de_iche_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_iche_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_vbank_use">가상계좌 결제사용</label></th>
            <td>
                <?php echo help("주문자가 현금거래를 원할 경우, 해당 거래건에 대해 주문자에게 고유로 발행되는 일회용 계좌번호입니다."); ?>
                <select name="de_vbank_use" id="de_vbank_use">
                    <option value="0" <?php echo get_selected($setting['de_vbank_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_vbank_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr id="kcp_vbank_url" class="pg_vbank_url">
            <th scope="row">KCP 가상계좌 입금통보 URL</th>
            <td>
                <?php echo help("KCP 가상계좌 사용시 다음 주소를 <strong><a href=\"http://admin.kcp.co.kr\" target=\"_blank\">KCP 관리자</a> &gt; 상점정보관리 &gt; 정보변경 &gt; 공통URL 정보 &gt; 공통URL 변경후</strong>에 넣으셔야 상점에 자동으로 입금 통보됩니다."); ?>
                <?php echo G5_CONTENTS_URL; ?>/settle_kcp_common.php</td>
        </tr>
        <tr id="inicis_vbank_url" class="pg_vbank_url">
            <th scope="row">KG이니시스 가상계좌 입금통보 URL</th>
            <td>
                <?php echo help("KG이니시스 가상계좌 사용시 다음 주소를 <strong><a href=\"https://iniweb.inicis.com/\" target=\"_blank\">KG이니시스 관리자</a> &gt; 거래조회 &gt; 가상계좌 &gt; 입금통보방식선택 &gt; URL 수신 설정</strong>에 넣으셔야 상점에 자동으로 입금 통보됩니다."); ?>
                <?php echo G5_CONTENTS_URL; ?>/settle_inicis_common.php</td>
        </tr>
        <tr>
            <th scope="row"><label for="de_hp_use">휴대폰결제사용</label></th>
            <td>
                <?php echo help("주문시 휴대폰 결제를 가능하게 할것인지를 설정합니다."); ?>
                <select id="de_hp_use" name="de_hp_use">
                    <option value="0" <?php echo get_selected($setting['de_hp_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_hp_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_card_use">신용카드결제사용</label></th>
            <td>
                <?php echo help("주문시 신용카드 결제를 가능하게 할것인지를 설정합니다."); ?>
                <select id="de_card_use" name="de_card_use">
                    <option value="0" <?php echo get_selected($setting['de_card_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_card_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_card_noint_use">신용카드 무이자할부사용</label></th>
            <td>
                <?php echo help("주문시 신용카드 무이자할부를 가능하게 할것인지를 설정합니다.<br>사용으로 설정하시면 PG사 가맹점 관리자 페이지에서 설정하신 무이자할부 설정이 적용됩니다.<br>사용안함으로 설정하시면 PG사 무이자 이벤트 카드를 제외한 모든 카드의 무이자 설정이 적용되지 않습니다."); ?>
                <select id="de_card_noint_use" name="de_card_noint_use">
                    <option value="0" <?php echo get_selected($setting['de_card_noint_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_card_noint_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_taxsave_use">현금영수증<br>발급사용</label></th>
            <td>
                <?php echo help("관리자는 설정에 관계없이 <a href=\"".G5_ADMIN_URL."/contents_admin/orderlist.php\">주문내역</a> &gt; 보기에서 발급이 가능합니다.\n현금영수증 발급 취소는 PG사에서 지원하는 현금영수증 취소 기능을 사용하시기 바랍니다."); ?>
                <select id="de_taxsave_use" name="de_taxsave_use">
                    <option value="0" <?php echo get_selected($setting['de_taxsave_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_taxsave_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_use_point">포인트 사용</label></th>
            <td>
                <?php echo help("<a href=\"".G5_ADMIN_URL."/config_form.php#frm_board\" target=\"_blank\">환경설정 &gt; 기본환경설정</a>과 동일한 설정입니다."); ?>
                <input type="checkbox" name="cf_use_point" value="1" id="cf_use_point"<?php echo $config['cf_use_point']?' checked':''; ?>> 사용
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_settle_min_point">결제 최소포인트</label></th>
            <td>
                <?php echo help("회원의 포인트가 설정값 이상일 경우만 주문시 결제에 사용할 수 있습니다.\n포인트 사용을 하지 않는 경우에는 의미가 없습니다."); ?>
                <input type="text" name="de_settle_min_point" value="<?php echo $setting['de_settle_min_point']; ?>" id="de_settle_min_point" class="frm_input" size="10"> 점
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_settle_max_point">최대 결제포인트</label></th>
            <td>
                <?php echo help("주문 결제시 최대로 사용할 수 있는 포인트를 설정합니다.\n포인트 사용을 하지 않는 경우에는 의미가 없습니다."); ?>
                <input type="text" name="de_settle_max_point" value="<?php echo $setting['de_settle_max_point']; ?>" id="de_settle_max_point" class="frm_input" size="10"> 점
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_settle_point_unit">결제 포인트단위</label></th>
            <td>
                <?php echo help("주문 결제시 사용되는 포인트의 절사 단위를 설정합니다."); ?>
                <select id="de_settle_point_unit" name="de_settle_point_unit">
                    <option value="100" <?php echo get_selected($setting['de_settle_point_unit'], 100); ?>>100</option>
                    <option value="10"  <?php echo get_selected($setting['de_settle_point_unit'],  10); ?>>10</option>
                    <option value="1"   <?php echo get_selected($setting['de_settle_point_unit'],   1); ?>>1</option>
                </select> 점
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_card_point">포인트부여</label></th>
            <td>
                <?php echo help("신용카드, 계좌이체 결제시 포인트를 부여할지를 설정합니다. (기본값은 '아니오')"); ?>
                <select id="de_card_point" name="de_card_point">
                    <option value="0" <?php echo get_selected($setting['de_card_point'], 0); ?>>아니오</option>
                    <option value="1" <?php echo get_selected($setting['de_card_point'], 1); ?>>예</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_point_days">주문완료 포인트</label></th>
            <td>
                <?php echo help("주문자가 회원일 경우에만 주문완료 포인트를 지급합니다. 주문취소 등을 고려하여 적당한 기간을 입력하십시오. (기본값은 7)\n0 으로 설정하는 경우 주문완료와 동시에 포인트를 부여합니다."); ?>
                주문 완료 <input type="text" name="de_point_days" value="<?php echo $setting['de_point_days']; ?>" id="de_point_days" class="frm_input" size="2"> 일 이후에 포인트를 부여
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_cash_use">캐시결제사용</label></th>
            <td colspan="3">
                <select name="de_cash_use" id="de_cash_use">
                    <option value="0" <?php echo get_selected($setting['de_cash_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_cash_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_cash_charge_use">캐시충전사용</label></th>
            <td colspan="3">
                <select name="de_cash_charge_use" id="de_cash_charge_use">
                    <option value="0" <?php echo get_selected($setting['de_cash_charge_use'], 0); ?>>사용안함</option>
                    <option value="1" <?php echo get_selected($setting['de_cash_charge_use'], 1); ?>>사용</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_cash_charge_price">캐시충전금액</label></th>
            <td colspan="3">
                <?php echo help('캐시 충전금액을 결제금액:충전금액 형식으로 설정합니다. 충전금액이 여러 개 일 경우 <strong>|</strong> 로 구분해 입력합니다. 예)10000:11000|30000:33000'); ?>
                <input type="text" name="de_cash_charge_price" value="<?php echo $setting['de_cash_charge_price']; ?>" id="de_cash_charge_price" class="frm_input" size="110">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_pg_service">결제대행사</label></th>
            <td>
                <?php echo help('쇼핑몰에서 사용할 결제대행사를 선택합니다.'); ?>
                <select id="de_pg_service" name="de_pg_service">
                    <option value="kcp" <?php echo get_selected($setting['de_pg_service'], 'kcp'); ?>>KCP</option>
                    <option value="lg" <?php echo get_selected($setting['de_pg_service'], 'lg'); ?>>LG유플러스</option>
                    <option value="inicis" <?php echo get_selected($setting['de_pg_service'], 'inicis'); ?>>KG이니시스</option>
                </select>
            </td>
        </tr>
        <tr class="pg_info_fld kcp_info_fld">
            <th scope="row">
                <label for="de_kcp_mid">KCP SITE CODE</label><br>
                <a href="http://sir.co.kr/main/service/p_pg.php" target="_blank" id="scf_kcpreg" class="scf_reg">KCP서비스신청하기</a>
            </th>
            <td>
                <?php echo help("KCP 에서 받은 SR 로 시작하는 영대문자, 숫자 혼용 총 5자리 SITE CODE 를 입력하세요.\n만약, 사이트코드가 SR로 시작하지 않는다면 KCP에 사이트코드 변경 요청을 하십시오. 예) SRZ89"); ?>
                <span class="sitecode">SR</span> <input type="text" name="de_kcp_mid" value="<?php echo $setting['de_kcp_mid']; ?>" id="de_kcp_mid" class="frm_input" size="2" maxlength="3" style="font:bold 15px Verdana;"> 영대문자, 숫자 혼용 3자리
            </td>
        </tr>
        <tr class="pg_info_fld kcp_info_fld">
            <th scope="row"><label for="de_kcp_site_key">KCP SITE KEY</label></th>
            <td>
                <?php echo help("25자리 영대소문자와 숫자 - 그리고 _ 로 이루어 집니다. SITE KEY 발급 KCP 전화: 1544-8660\n예) 1Q9YRV83gz6TukH8PjH0xFf__"); ?>
                <input type="text" name="de_kcp_site_key" value="<?php echo $setting['de_kcp_site_key']; ?>" id="de_kcp_site_key" class="frm_input" size="32" maxlength="25">
            </td>
        </tr>
        <tr class="pg_info_fld lg_info_fld">
            <th scope="row">
                <label for="cf_lg_mid">LG유플러스 상점아이디</label><br>
                <a href="http://sir.co.kr/main/service/lg_pg.php" target="_blank" id="scf_lgreg" class="scf_reg">LG유플러스 서비스신청하기</a>
            </th>
            <td>
                <?php echo help("LG유플러스 에서 받은 si_ 로 시작하는 상점 ID를 입력하세요.\n만약, 상점 ID가 si_로 시작하지 않는다면 LG유플러스에 사이트코드 변경 요청을 하십시오. 예) si_lguplus\n<a href=\"".G5_ADMIN_URL."/config_form.php#anc_cf_cert\">기본환경설정 &gt; 본인확인</a> 설정의 LG유플러스 상점아이디와 동일합니다."); ?>
                <span class="sitecode">si_</span> <input type="text" name="cf_lg_mid" value="<?php echo $config['cf_lg_mid']; ?>" id="cf_lg_mid" class="frm_input" size="10" maxlength="20" style="font:bold 15px Verdana;"> 영문자, 숫자 혼용
            </td>
        </tr>
        <tr class="pg_info_fld lg_info_fld">
            <th scope="row"><label for="cf_lg_mert_key">LG유플러스 MERT KEY</label></th>
            <td>
                <?php echo help("LG유플러스 상점MertKey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실 수 있습니다.\n예) 95160cce09854ef44d2edb2bfb05f9f3\n<a href=\"".G5_ADMIN_URL."/config_form.php#anc_cf_cert\">기본환경설정 &gt; 본인확인</a> 설정의 LG유플러스 MERT KEY와 동일합니다."); ?>
                <input type="text" name="cf_lg_mert_key" value="<?php echo $config['cf_lg_mert_key']; ?>" id="cf_lg_mert_key" class="frm_input" size="32" maxlength="50">
            </td>
        </tr>
        <tr class="pg_info_fld inicis_info_fld">
            <th scope="row">
                <label for="de_inicis_mid">KG이니시스 상점아이디</label><br>
                <a href="http://sir.co.kr/main/service/inicis_pg.php" target="_blank" id="scf_icsreg" class="scf_reg">KG이니시스 서비스신청하기</a>
            </th>
            <td>
                <?php echo help("KG이니시스로 부터 발급 받으신 상점아이디(MID) 10자리 중 SIR 을 제외한 나머지 7자리를 입력 합니다.\n만약, 상점아이디가 SIR로 시작하지 않는다면 계약담당자에게 변경 요청을 해주시기 바랍니다. (Tel. 02-3430-5858) 예) SIRpaytest"); ?>
                <span class="sitecode">SIR</span> <input type="text" name="de_inicis_mid" value="<?php echo $setting['de_inicis_mid']; ?>" id="de_inicis_mid" class="frm_input" size="10" maxlength="10" style="font:bold 15px Verdana;"> 영문소문자(숫자포함 가능)
            </td>
        </tr>
        <tr class="pg_info_fld inicis_info_fld">
            <th scope="row"><label for="de_inicis_admin_key">KG이니시스 키패스워드</label></th>
            <td>
                <?php echo help("KG이니시스에서 발급받은 4자리 상점 키패스워드를 입력합니다.\nKG이니시스 상점관리자 패스워드와 관련이 없습니다.\n키패스워드 값을 확인하시려면 상점측에 발급된 키파일 안의 readme.txt 파일을 참조해 주십시오"); ?>
                <input type="text" name="de_inicis_admin_key" value="<?php echo $setting['de_inicis_admin_key']; ?>" id="de_inicis_admin_key" class="frm_input" size="5" maxlength="4">
            </td>
        </tr>
        <tr>
            <th scope="row">신용카드 결제테스트</th>

            <td>
                <?php echo help("신용카드를 테스트 하실 경우에 체크하세요. 결제단위 최소 1,000원"); ?>
                <input type="radio" name="de_card_test" value="0" <?php echo $setting['de_card_test']==0?"checked":""; ?> id="de_card_test1">
                <label for="de_card_test1">실결제 </label>
                <input type="radio" name="de_card_test" value="1" <?php echo $setting['de_card_test']==1?"checked":""; ?> id="de_card_test2">
                <label for="de_card_test2">테스트결제</label>
                <div class="scf_cardtest kcp_cardtest">

                    <a href="http://admin.kcp.co.kr/" target="_blank" class="btn_frmline">실결제 관리자</a>
                    <a href="http://testadmin8.kcp.co.kr/" target="_blank" class="btn_frmline">테스트 관리자</a>
                </div>
                <div class="scf_cardtest lg_cardtest">

                    <a href="https://pgweb.uplus.co.kr/" target="_blank" class="btn_frmline">실결제 관리자</a>
                    <a href="https://pgweb.uplus.co.kr/tmert" target="_blank" class="btn_frmline">테스트 관리자</a>
                </div>
                <div id="scf_cardtest_tip">
                    <strong>일반결제 사용시 테스트 결제</strong>
                    <dl>
                        <dt>신용카드</dt><dd>1000원 이상, 모든 카드가 테스트 되는 것은 아니므로 여러가지 카드로 결제해 보셔야 합니다.<br>(BC, 현대, 롯데, 삼성카드)</dd>
                        <dt>계좌이체</dt><dd>150원 이상, 계좌번호, 비밀번호는 가짜로 입력해도 되며, 주민등록번호는 공인인증서의 것과 일치해야 합니다.</dd>
                        <dt>가상계좌</dt><dd>1원 이상, 모든 은행이 테스트 되는 것은 아니며 "해당 은행 계좌 없음" 자주 발생함.<br>(광주은행, 하나은행)</dd>
                        <dt>휴대폰</dt><dd>1004원, 실결제가 되며 다음날 새벽에 일괄 취소됨</dd>
                    </dl>
                    <strong>에스크로 사용시 테스트 결제</strong><br>
                    <dl>
                        <dt>신용카드</dt><dd>1000원 이상, 모든 카드가 테스트 되는 것은 아니므로 여러가지 카드로 결제해 보셔야 합니다.<br>(BC, 현대, 롯데, 삼성카드)</dd>
                        <dt>계좌이체</dt><dd>150원 이상, 계좌번호, 비밀번호는 가짜로 입력해도 되며, 주민등록번호는 공인인증서의 것과 일치해야 합니다.</dd>
                        <dt>가상계좌</dt><dd>1원 이상, 입금통보는 제대로 되지 않음.</dd>
                        <dt>휴대폰</dt><dd>테스트 지원되지 않음.</dd>
                    </dl>
                    <ul id="kcp_cardtest_tip" class="scf_cardtest_tip_adm scf_cardtest_tip_adm_hide">
                        <li>테스트결제의 <a href="http://testadmin8.kcp.co.kr/assist/login.LoginAction.do" target="_blank">상점관리자</a> 로그인 정보는 KCP로 문의하시기 바랍니다. (기술지원 1544-8661)</li>
                        <li><b>일반결제</b>의 테스트 사이트코드는 <b>T0000</b> 이며, <b>에스크로 결제</b>의 테스트 사이트코드는 <b>T0007</b> 입니다.</li>
                    </ul>
                    <ul id="lg_cardtest_tip" class="scf_cardtest_tip_adm scf_cardtest_tip_adm_hide">
                        <li>테스트결제의 <a href="http://pgweb.dacom.net:7085/" target="_blank">상점관리자</a> 로그인 정보는 LG유플러스 상점아이디 첫 글자에 t를 추가해서 로그인하시기 바랍니다. 예) tsi_lguplus</li>
                    </ul>
                </div>
            </td>
        </tr>
        </tbody>
        </table>
        <script>
        $('#scf_cardtest_tip').addClass('scf_cardtest_tip');
        $('<button type="button" class="scf_cardtest_btn btn_frmline">테스트결제 팁 더보기</button>').appendTo('.scf_cardtest');

        $(".scf_cardtest").addClass("scf_cardtest_hide");
        $(".<?php echo $setting['de_pg_service']; ?>_cardtest").removeClass("scf_cardtest_hide");
        $("#<?php echo $setting['de_pg_service']; ?>_cardtest_tip").removeClass("scf_cardtest_tip_adm_hide");
        </script>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_scf_chub">
    <h2 class="h2_frm">컨텐츠허브 이용설정</h2>
    <?php echo $pg_anchor; ?>
    <div class="local_desc02 local_desc">
        <p>SIR 컨텐츠허브 이용을 위한 정보를 입력합니다.</p>
    </div>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>컨텐츠허브 이용설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row">
                <label for="de_chub_mid">컨텐츠허브 MID</label><br>
                <a href="http://sir.co.kr/chub/cpregister.php" target="_blank" id="scf_chubreg" class="scf_reg">컨텐츠허브 CP가입</a>
            </th>
            <td>
                <?php echo help('SIR에서 CP가입 후 발급된 MID 값을 입력합니다.<br>MID를 입력하지 않으시면 컨텐츠허브 등록기능을 사용할 수 없습니다.'); ?>
                <input type="text" name="de_chub_mid" id="de_chub_mid" value="<?php echo $setting['de_chub_mid']; ?>" class="frm_input" size="80">
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<section id="anc_scf_etc">
    <h2 class="h2_frm">기타 설정</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>기타 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="de_movie_skin">동영상보기 스킨</label></th>
            <td>
                <?php echo help("동영상보기 스킨을 설정합니다.<br><strong>동영상보기 기능은 PC에서만 작동합니다.</strong>"); ?>
                <select name="de_movie_skin" id="de_movie_skin">
                    <?php echo cm_get_list_skin_options("^movie.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_movie_skin']); ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">관련상품출력</th>
            <td>
                <?php echo help("관련상품의 경우 등록된 상품은 모두 출력하므로 '출력할 줄 수'는 설정하지 않습니다. 이미지높이를 0으로 설정하면 상품이미지를 이미지폭에 비례하여 생성합니다."); ?>
                <label for="de_rel_list_skin">스킨</label>
                <select name="de_rel_list_skin" id="de_rel_list_skin">
                    <?php echo cm_get_list_skin_options("^relation.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_rel_list_skin']); ?>
                </select>
                <label for="de_rel_img_width">이미지폭</label>
                <input type="text" name="de_rel_img_width" value="<?php echo $setting['de_rel_img_width']; ?>" id="de_rel_img_width" class="frm_input" size="3">
                <label for="de_rel_img_height">이미지높이</label>
                <input type="text" name="de_rel_img_height" value="<?php echo $setting['de_rel_img_height']; ?>" id="de_rel_img_height" class="frm_input" size="3">
                <label for="de_rel_list_mod">1줄당 이미지 수</label>
                <input type="text" name="de_rel_list_mod" value="<?php echo $setting['de_rel_list_mod']; ?>" id="de_rel_list_mod" class="frm_input" size="3">
                <label for="de_rel_list_use">출력</label>
                <input type="checkbox" name="de_rel_list_use" value="1" id="de_rel_list_use" <?php echo $setting['de_rel_list_use']?"checked":""; ?>>
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 관련상품출력</th>
            <td>
                <?php echo help("관련상품의 경우 등록된 상품은 모두 출력하므로 '출력할 줄 수'는 설정하지 않습니다. 이미지높이를 0으로 설정하면 상품이미지를 이미지폭에 비례하여 생성합니다."); ?>
                <label for="de_mobile_rel_list_skin">스킨</label>
                <select name="de_mobile_rel_list_skin" id="de_mobile_rel_list_skin">
                    <?php echo cm_get_list_skin_options("^relation.[0-9]+\.skin\.php", G5_MCONTENTS_SKIN_PATH, $setting['de_mobile_rel_list_skin']); ?>
                </select>
                <label for="de_mobile_rel_img_width">이미지폭</label>
                <input type="text" name="de_mobile_rel_img_width" value="<?php echo $setting['de_mobile_rel_img_width']; ?>" id="de_mobile_rel_img_width" class="frm_input" size="3">
                <label for="de_mobile_rel_img_height">이미지높이</label>
                <input type="text" name="de_mobile_rel_img_height" value="<?php echo $setting['de_mobile_rel_img_height']; ?>" id="de_mobile_rel_img_height" class="frm_input" size="3">
                <label for="de_mobile_rel_list_use">출력</label>
                <input type="checkbox" name="de_mobile_rel_list_use" value="1" id="de_mobile_rel_list_use" <?php echo $setting['de_mobile_rel_list_use']?"checked":""; ?>>
            </td>
        </tr>
        <tr>
            <th scope="row">검색상품출력</th>
            <td>
                <label for="de_search_list_skin">스킨</label>
                <select name="de_search_list_skin" id="de_search_list_skin">
                    <?php echo cm_get_list_skin_options("^list.[0-9]+\.skin\.php", G5_CONTENTS_SKIN_PATH, $setting['de_search_list_skin']); ?>
                </select>
                <label for="de_search_img_width">이미지폭</label>
                <input type="text" name="de_search_img_width" value="<?php echo $setting['de_search_img_width']; ?>" id="de_search_img_width" class="frm_input" size="3">
                <label for="de_search_img_height">이미지높이</label>
                <input type="text" name="de_search_img_height" value="<?php echo $setting['de_search_img_height']; ?>" id="de_search_img_height" class="frm_input" size="3">
                <label for="de_search_list_mod">1줄당 이미지 수</label>
                <input type="text" name="de_search_list_mod" value="<?php echo $setting['de_search_list_mod']; ?>" id="de_search_list_mod" class="frm_input" size="3">
                <label for="de_search_list_row">출력할 줄 수</label>
                <input type="text" name="de_search_list_row" value="<?php echo $setting['de_search_list_row']; ?>" id="de_search_list_row" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 검색상품출력</th>
            <td>
                <label for="de_mobile_search_list_skin">스킨</label>
                <select name="de_mobile_search_list_skin" id="de_mobile_search_list_skin">
                    <?php echo cm_get_list_skin_options("^list.[0-9]+\.skin\.php", G5_MCONTENTS_SKIN_PATH, $setting['de_mobile_search_list_skin']); ?>
                </select>
                <label for="de_mobile_search_img_width">이미지폭</label>
                <input type="text" name="de_mobile_search_img_width" value="<?php echo $setting['de_mobile_search_img_width']; ?>" id="de_mobile_search_img_width" class="frm_input" size="3">
                <label for="de_mobile_search_img_height">이미지높이</label>
                <input type="text" name="de_mobile_search_img_height" value="<?php echo $setting['de_mobile_search_img_height']; ?>" id="de_mobile_search_img_height" class="frm_input" size="3">
                <label for="de_mobile_search_list_mod">이미지 수</label>
                <input type="text" name="de_mobile_search_list_mod" value="<?php echo $setting['de_mobile_search_list_mod']; ?>" id="de_mobile_search_list_mod" class="frm_input" size="3">
            </td>
        </tr>
        <tr>
            <th scope="row">이미지(소)</th>
            <td>
                <?php echo help("분류리스트에서 보여지는 사이즈를 설정하시면 됩니다. 분류관리의 출력 이미지폭, 높이의 기본값으로 사용됩니다. 높이를 0 으로 설정하시면 폭에 비례하여 높이를 썸네일로 생성합니다."); ?>
                <label for="de_simg_width"><span class="sound_only">이미지(소) </span>폭</label>
                <input type="text" name="de_simg_width" value="<?php echo $setting['de_simg_width']; ?>" id="de_simg_width" class="frm_input" size="5"> 픽셀
                /
                <label for="de_simg_height"><span class="sound_only">이미지(소) </span>높이</label>
                <input type="text" name="de_simg_height" value="<?php echo $setting['de_simg_height']; ?>" id="de_simg_height" class="frm_input" size="5"> 픽셀
            </td>
        </tr>
        <tr>
            <th scope="row">이미지(중)</th>
            <td>
                <?php echo help("상품상세보기에서 보여지는 상품이미지의 사이즈를 픽셀로 설정합니다. 높이를 0 으로 설정하시면 폭에 비례하여 높이를 썸네일로 생성합니다."); ?>
                <label for="de_mimg_width"><span class="sound_only">이미지(중) </span>폭</label>
                <input type="text" name="de_mimg_width" value="<?php echo $setting['de_mimg_width']; ?>" id="de_mimg_width" class="frm_input" size="5"> 픽셀
                /
                <label for="de_mimg_height"><span class="sound_only">이미지(중) </span>높이</label>
                <input type="text" name="de_mimg_height" value="<?php echo $setting['de_mimg_height']; ?>" id="de_mimg_height" class="frm_input" size="5"> 픽셀
            </td>
        </tr>
        <tr>
            <th scope="row">상단로고이미지</th>
            <td>
                <?php echo help("쇼핑몰 상단로고를 직접 올릴 수 있습니다. 이미지 파일만 가능합니다."); ?>
                <input type="file" name="logo_img" id="logo_img">
                <?php
                $logo_img = G5_DATA_PATH."/common/cm_logo_img";
                if (file_exists($logo_img))
                {
                    $size = getimagesize($logo_img);
                ?>
                <input type="checkbox" name="logo_img_del" value="1" id="logo_img_del">
                <label for="logo_img_del"><span class="sound_only">상단로고이미지</span> 삭제</label>
                <span class="scf_img_logoimg"></span>
                <div id="logoimg" class="banner_or_img">
                    <img src="<?php echo G5_DATA_URL; ?>/common/cm_logo_img" alt="">
                    <button type="button" class="sit_wimg_close">닫기</button>
                </div>
                <script>
                $('<button type="button" id="cf_logoimg_view" class="btn_frmline scf_img_view">상단로고이미지 확인</button>').appendTo('.scf_img_logoimg');
                </script>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row">하단로고이미지</th>
            <td>
                <?php echo help("쇼핑몰 하단로고를 직접 올릴 수 있습니다. 이미지 파일만 가능합니다."); ?>
                <input type="file" name="logo_img2" id="logo_img2">
                <?php
                $logo_img2 = G5_DATA_PATH."/common/cm_logo_img2";
                if (file_exists($logo_img2))
                {
                    $size = getimagesize($logo_img2);
                ?>
                <input type="checkbox" name="logo_img_del2" value="1" id="logo_img_del2">
                <label for="logo_img_del2"><span class="sound_only">하단로고이미지</span> 삭제</label>
                <span class="scf_img_logoimg2"></span>
                <div id="logoimg2" class="banner_or_img">
                    <img src="<?php echo G5_DATA_URL; ?>/common/cm_logo_img2" alt="">
                    <button type="button" class="sit_wimg_close">닫기</button>
                </div>
                <script>
                $('<button type="button" id="cf_logoimg2_view" class="btn_frmline scf_img_view">하단로고이미지 확인</button>').appendTo('.scf_img_logoimg2');
                </script>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 상단로고이미지</th>
            <td>
                <?php echo help("모바일 쇼핑몰 상단로고를 직접 올릴 수 있습니다. 이미지 파일만 가능합니다."); ?>
                <input type="file" name="mobile_logo_img" id="mobile_logo_img">
                <?php
                $mobile_logo_img = G5_DATA_PATH."/common/cm_mobile_logo_img";
                if (file_exists($mobile_logo_img))
                {
                    $size = getimagesize($mobile_logo_img);
                ?>
                <input type="checkbox" name="mobile_logo_img_del" value="1" id="mobile_logo_img_del">
                <label for="mobile_logo_img_del"><span class="sound_only">모바일 상단로고이미지</span> 삭제</label>
                <span class="scf_img_mobilelogoimg"></span>
                <div id="mobilelogoimg" class="banner_or_img">
                    <img src="<?php echo G5_DATA_URL; ?>/common/cm_mobile_logo_img" alt="">
                    <button type="button" class="sit_wimg_close">닫기</button>
                </div>
                <script>
                $('<button type="button" id="cf_mobilelogoimg_view" class="btn_frmline scf_img_view">모바일 상단로고이미지 확인</button>').appendTo('.scf_img_mobilelogoimg');
                </script>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row">모바일 하단로고이미지</th>
            <td>
                <?php echo help("모바일 쇼핑몰 하단로고를 직접 올릴 수 있습니다. 이미지 파일만 가능합니다."); ?>
                <input type="file" name="mobile_logo_img2" id="mobile_logo_img2">
                <?php
                $mobile_logo_img2 = G5_DATA_PATH."/common/cm_mobile_logo_img2";
                if (file_exists($mobile_logo_img2))
                {
                    $size = getimagesize($mobile_logo_img2);
                ?>
                <input type="checkbox" name="mobile_logo_img_del2" value="1" id="mobile_logo_img_del2">
                <label for="mobile_logo_img_del2"><span class="sound_only">모바일 하단로고이미지</span> 삭제</label>
                <span class="scf_img_mobilelogoimg2"></span>
                <div id="mobilelogoimg2" class="banner_or_img">
                    <img src="<?php echo G5_DATA_URL; ?>/common/cm_mobile_logo_img2" alt="">
                    <button type="button" class="sit_wimg_close">닫기</button>
                </div>
                <script>
                $('<button type="button" id="cf_mobilelogoimg2_view" class="btn_frmline scf_img_view">모바일 하단로고이미지 확인</button>').appendTo('.scf_img_mobilelogoimg2');
                </script>
                <?php } ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_item_use_write">사용후기 작성</label></th>
            <td>
                 <?php echo help("주문상태에 따른 사용후기 작성여부를 설정합니다.", 50); ?>
                <select name="de_item_use_write" id="de_item_use_write">
                    <option value="0" <?php echo get_selected($setting['de_item_use_write'], 0); ?>>주문상태와 무관하게 작성가능</option>
                    <option value="1" <?php echo get_selected($setting['de_item_use_write'], 1); ?>>주문상태가 완료인 경우에만 작성가능</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_item_use_use">사용후기</label></th>
            <td>
                 <?php echo help("사용후기가 올라오면, 즉시 출력 혹은 관리자 승인 후 출력 여부를 설정합니다.", 50); ?>
                <select name="de_item_use_use" id="de_item_use_use">
                    <option value="0" <?php echo get_selected($setting['de_item_use_use'], 0); ?>>즉시 출력</option>
                    <option value="1" <?php echo get_selected($setting['de_item_use_use'], 1); ?>>관리자 승인 후 출력</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_code_dup_use">코드 중복검사</label></th>
            <td>
                 <?php echo help("분류, 상품 등을 추가할 때 자동으로 코드 중복검사를 하려면 체크하십시오."); ?>
                <input type="checkbox" name="de_code_dup_use" value="1" id="de_code_dup_use"<?php echo $setting['de_code_dup_use']?' checked':''; ?>> 사용
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_cart_keep_term">장바구니 보관기간</label></th>
            <td>
                 <?php echo help("장바구니 상품의 보관 기간을 설정하십시오."); ?>
                <input type="text" name="de_cart_keep_term" value="<?php echo $setting['de_cart_keep_term']; ?>" id="de_cart_keep_term" class="frm_input" size="5"> 일
            </td>
        </tr>
        <tr>
            <th scope="row">신규회원 쿠폰발행</th>
            <td>
                 <?php echo help("신규회원에게 주문금액 할인 쿠폰을 발행하시려면 아래를 설정하십시오."); ?>
                <label for="de_member_reg_coupon_use">쿠폰발행</label>
                <input type="checkbox" name="de_member_reg_coupon_use" value="1" id="de_member_reg_coupon_use"<?php echo $setting['de_member_reg_coupon_use']?' checked':''; ?>>
                <label for="de_member_reg_coupon_price">쿠폰할인금액</label>
                <input type="text" name="de_member_reg_coupon_price" value="<?php echo $setting['de_member_reg_coupon_price']; ?>" id="de_member_reg_coupon_price" class="frm_input" size="10"> 원
                <label for="de_member_reg_coupon_minimum">주문최소금액</label>
                <input type="text" name="de_member_reg_coupon_minimum" value="<?php echo $setting['de_member_reg_coupon_minimum']; ?>" id="de_member_reg_coupon_minimum" class="frm_input" size="10"> 원이상
                <label for="de_member_reg_coupon_term">쿠폰유효기간</label>
                <input type="text" name="de_member_reg_coupon_term" value="<?php echo $setting['de_member_reg_coupon_term']; ?>" id="de_member_reg_coupon_term" class="frm_input" size="5"> 일
            </td>
        </tr>
        <tr>
            <th scope="row">MYSQL USER</th>
            <td><?php echo G5_MYSQL_USER; ?></td>
        </tr>
        <tr>
            <th scope="row">MYSQL DB</th>
            <td><?php echo G5_MYSQL_DB; ?></td>
        </tr>
        <tr>
            <th scope="row">서버 IP</th>
            <td><?php echo ($_SERVER['SERVER_ADDR']?$_SERVER['SERVER_ADDR']:$_SERVER['LOCAL_ADDR']); ?></td>
        </tr>
        </tbody>
        </table>
    </div>
</section>

<?php echo $frm_submit; ?>

<?php if (file_exists($logo_img) || file_exists($logo_img2) || file_exists($mobile_logo_img) || file_exists($mobile_logo_img2)) { ?>
<script>
$(".banner_or_img").addClass("scf_img");
$(function() {
    $(".scf_img_view").bind("click", function() {
        var sit_wimg_id = $(this).attr("id").split("_");
        var $img_display = $("#"+sit_wimg_id[1]);

        $img_display.toggle();

        if($img_display.is(":visible")) {
            $(this).text($(this).text().replace("확인", "닫기"));
        } else {
            $(this).text($(this).text().replace("닫기", "확인"));
        }

        if(sit_wimg_id[1].search("mainimg") > -1) {
            var $img = $("#"+sit_wimg_id[1]).children("img");
            var width = $img.width();
            var height = $img.height();
            if(width > 700) {
                var img_width = 700;
                var img_height = Math.round((img_width * height) / width);

                $img.width(img_width).height(img_height);
            }
        }
    });
    $(".sit_wimg_close").bind("click", function() {
        var $img_display = $(this).parents(".banner_or_img");
        var id = $img_display.attr("id");
        $img_display.toggle();
        var $button = $("#cf_"+id+"_view");
        $button.text($button.text().replace("닫기", "확인"));
    });
});
</script>
<?php } ?>

<script>
function byte_check(el_cont, el_byte)
{
    var cont = document.getElementById(el_cont);
    var bytes = document.getElementById(el_byte);
    var i = 0;
    var cnt = 0;
    var exceed = 0;
    var ch = '';

    for (i=0; i<cont.value.length; i++) {
        ch = cont.value.charAt(i);
        if (escape(ch).length > 4) {
            cnt += 2;
        } else {
            cnt += 1;
        }
    }

    //byte.value = cnt + ' / 80 bytes';
    bytes.innerHTML = cnt + ' / 80 bytes';

    if (cnt > 80) {
        exceed = cnt - 80;
        alert('메시지 내용은 80바이트를 넘을수 없습니다.\r\n작성하신 메세지 내용은 '+ exceed +'byte가 초과되었습니다.\r\n초과된 부분은 자동으로 삭제됩니다.');
        var tcnt = 0;
        var xcnt = 0;
        var tmp = cont.value;
        for (i=0; i<tmp.length; i++) {
            ch = tmp.charAt(i);
            if (escape(ch).length > 4) {
                tcnt += 2;
            } else {
                tcnt += 1;
            }

            if (tcnt > 80) {
                tmp = tmp.substring(0,i);
                break;
            } else {
                xcnt = tcnt;
            }
        }
        cont.value = tmp;
        //byte.value = xcnt + ' / 80 bytes';
        bytes.innerHTML = xcnt + ' / 80 bytes';
        return;
    }
}
</script>

<section id="anc_scf_sms" >
    <h2 class="h2_frm">SMS 설정</h2>
    <?php echo $pg_anchor; ?>

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <caption>SMS 설정</caption>
        <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><label for="cf_sms_use">SMS 사용</label></th>
            <td>
                <?php echo help("SMS  서비스 회사를 선택하십시오. 서비스 회사를 선택하지 않으면, SMS 발송 기능이 동작하지 않습니다.<br>아이코드는 무료 문자메세지 발송 테스트 환경을 지원합니다.<br><a href=\"".G5_ADMIN_URL."/config_form.php#anc_cf_sms\">기본환경설정 &gt; SMS</a> 설정과 동일합니다."); ?>
                <select id="cf_sms_use" name="cf_sms_use">
                    <option value="" <?php echo get_selected($config['cf_sms_use'], ''); ?>>사용안함</option>
                    <option value="icode" <?php echo get_selected($config['cf_sms_use'], 'icode'); ?>>아이코드</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="de_sms_hp">관리자 휴대폰번호</label></th>
            <td>
                <?php echo help("주문서작성시 쇼핑몰관리자가 문자메세지를 받아볼 번호를 숫자만으로 입력하세요. 예) 0101234567"); ?>
                <input type="text" name="de_sms_hp" value="<?php echo $setting['de_sms_hp']; ?>" id="de_sms_hp" class="frm_input" size="20">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_icode_id">아이코드 회원아이디</label></th>
            <td>
                <?php echo help("아이코드에서 사용하시는 회원아이디를 입력합니다."); ?>
                <input type="text" name="cf_icode_id" value="<?php echo $config['cf_icode_id']; ?>" id="cf_icode_id" class="frm_input" size="20">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="cf_icode_pw">아이코드 비밀번호</label></th>
            <td>
                <?php echo help("아이코드에서 사용하시는 비밀번호를 입력합니다."); ?>
                <input type="password" name="cf_icode_pw" value="<?php echo $config['cf_icode_pw']; ?>" class="frm_input" id="cf_icode_pw">
            </td>
        </tr>
        <tr>
            <th scope="row">요금제</th>
            <td>
                <input type="hidden" name="cf_icode_server_ip" value="<?php echo $config['cf_icode_server_ip']; ?>">
                <?php
                    if ($userinfo['payment'] == 'A') {
                       echo '충전제';
                        echo '<input type="hidden" name="cf_icode_server_port" value="7295">';
                    } else if ($userinfo['payment'] == 'C') {
                        echo '정액제';
                        echo '<input type="hidden" name="cf_icode_server_port" value="7296">';
                    } else {
                        echo '가입해주세요.';
                        echo '<input type="hidden" name="cf_icode_server_port" value="7295">';
                    }
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row">아이코드 SMS 신청<br>회원가입</th>
            <td>
                <?php echo help("아래 링크에서 회원가입 하시면 문자 건당 16원에 제공 받을 수 있습니다."); ?>
                <a href="http://icodekorea.com/res/join_company_fix_a.php?sellid=sir2" target="_blank" class="btn_frmline">아이코드 회원가입</a>
            </td>
        </tr>
         <?php if ($userinfo['payment'] == 'A') { ?>
        <tr>
            <th scope="row">충전 잔액</th>
            <td colspan="3">
                <?php echo number_format($userinfo['coin']); ?> 원.
                <a href="http://www.icodekorea.com/smsbiz/credit_card_amt.php?icode_id=<?php echo $config['cf_icode_id']; ?>&amp;icode_passwd=<?php echo $config['cf_icode_pw']; ?>" target="_blank" class="btn_frmline" onclick="window.open(this.href,'icode_payment', 'scrollbars=1,resizable=1'); return false;">충전하기</a>
            </td>
        </tr>
        <tr>
            <th scope="row">건수별 금액</th>
            <td colspan="3">
                <?php echo number_format($userinfo['gpay']); ?> 원.
            </td>
        </tr>
        <?php } ?>
         </tbody>
        </table>
    </div>

    <section id="scf_sms_pre">
        <h3>사전에 정의된 SMS프리셋</h3>
        <div class="local_desc01 local_desc">
            <dl>
                <dt>회원가입시</dt>
                <dd>{이름} {회원아이디} {회사명}</dd>
                <dt>주문서작성</dt>
                <dd>{이름} {보낸분} {받는분} {주문번호} {주문금액} {회사명}</dd>
                <dt>입금확인시</dt>
                <dd>{이름} {입금액} {주문번호} {회사명}</dd>
            </dl>
           <p><?php echo help('주의! 80 bytes 까지만 전송됩니다. (영문 한글자 : 1byte , 한글 한글자 : 2bytes , 특수문자의 경우 1 또는 2 bytes 임)'); ?></p>
        </div>

        <div id="scf_sms">
            <?php
            $scf_sms_title = array (1=>"회원가입시 고객님께 발송", "주문시 고객님께 발송", "주문시 관리자에게 발송", "입금확인시 고객님께 발송");
            for ($i=1; $i<=4; $i++) {
            ?>
            <section class="scf_sms_box">
                <h4><?php echo $scf_sms_title[$i]?></h4>
                <input type="checkbox" name="de_sms_use<?php echo $i; ?>" value="1" id="de_sms_use<?php echo $i; ?>" <?php echo ($setting["de_sms_use".$i] ? " checked" : ""); ?>>
                <label for="de_sms_use<?php echo $i; ?>"><span class="sound_only"><?php echo $scf_sms_title; ?></span>사용</label>
                <div class="scf_sms_img">
                    <textarea id="de_sms_cont<?php echo $i; ?>" name="de_sms_cont<?php echo $i; ?>" ONKEYUP="byte_check('de_sms_cont<?php echo $i; ?>', 'byte<?php echo $i; ?>');"><?php echo $setting['de_sms_cont'.$i]; ?></textarea>
                </div>
                <span id="byte<?php echo $i; ?>" class="scf_sms_cnt">0 / 80 바이트</span>
            </section>

            <script>
            byte_check('de_sms_cont<?php echo $i; ?>', 'byte<?php echo $i; ?>');
            </script>
            <?php } ?>
        </div>
    </section>

</section>

<?php echo $frm_submit; ?>

</form>

<script>
function fconfig_check(f)
{
    return true;
}

$(function() {
    $(".pg_info_fld").hide();
    $(".pg_vbank_url").hide();
    <?php if($setting['de_pg_service']) { ?>
    $(".<?php echo $setting['de_pg_service']; ?>_info_fld").show();
    $("#<?php echo $setting['de_pg_service']; ?>_vbank_url").show();
    <?php } else { ?>
    $(".kcp_info_fld").show();
    $("#kcp_vbank_url").show();
    <?php } ?>
    $("#de_pg_service").on("change", function() {
        var pg = $(this).val();
        $(".pg_info_fld:visible").hide();
        $(".pg_vbank_url:visible").hide();
        $("."+pg+"_info_fld").show();
        $("#"+pg+"_vbank_url").show();
        $(".scf_cardtest").addClass("scf_cardtest_hide");
        $("."+pg+"_cardtest").removeClass("scf_cardtest_hide");
        $(".scf_cardtest_tip_adm").addClass("scf_cardtest_tip_adm_hide");
        $("#"+pg+"_cardtest_tip").removeClass("scf_cardtest_tip_adm_hide");
    });

    $(".scf_cardtest_btn").bind("click", function() {
        var $cf_cardtest_tip = $("#scf_cardtest_tip");
        var $cf_cardtest_btn = $(".scf_cardtest_btn");

        $cf_cardtest_tip.toggle();

        if($cf_cardtest_tip.is(":visible")) {
            $cf_cardtest_btn.text("테스트결제 팁 닫기");
        } else {
            $cf_cardtest_btn.text("테스트결제 팁 더보기");
        }
    })
});
</script>

<?php
// 결제모듈 실행권한 체크
if($setting['de_iche_use'] || $setting['de_vbank_use'] || $setting['de_hp_use'] || $setting['de_card_use']) {
    // kcp의 경우 pp_cli 체크
    if($setting['de_pg_service'] == 'kcp') {
        $is_linux = true;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            $is_linux = false;

        $exe = '/kcp/bin/';
        if($is_linux) {
            if(PHP_INT_MAX == 2147483647) // 32-bit
                $exe .= 'pp_cli';
            else
                $exe .= 'pp_cli_x64';
        } else {
            $exe .= 'pp_cli_exe.exe';
        }

        echo module_exec_check(G5_CONTENTS_PATH.$exe, 'pp_cli');
    }

    // LG의 경우 log 디렉토리 체크
    if($setting['de_pg_service'] == 'lg') {
        $log_path = G5_LGXPAY_PATH.'/lgdacom/log';

        if(!is_dir($log_path)) {
            echo '<script>'.PHP_EOL;
            echo 'alert("'.str_replace(G5_PATH.'/', '', G5_LGXPAY_PATH).'/lgdacom 폴더 안에 log 폴더를 생성하신 후 쓰기권한을 부여해 주십시오.\n> mkdir log\n> chmod 707 log");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        } else {
            if(!is_writable($log_path)) {
                echo '<script>'.PHP_EOL;
                echo 'alert("'.str_replace(G5_PATH.'/', '',$log_path).' 폴더에 쓰기권한을 부여해 주십시오.\n> chmod 707 log");'.PHP_EOL;
                echo '</script>'.PHP_EOL;
            }
        }
    }

    // 이니시스의 경우 log 디렉토리 체크
    if($setting['de_pg_service'] == 'inicis') {
        if (!function_exists('xml_set_element_handler')) {
            echo '<script>'.PHP_EOL;
            echo 'alert("XML 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        }

        if (!function_exists('openssl_get_publickey')) {
            echo '<script>'.PHP_EOL;
            echo 'alert("OPENSSL 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        }

        if (!function_exists('socket_create')) {
            echo '<script>'.PHP_EOL;
            echo 'alert("SOCKET 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        }

        if (!function_exists('mcrypt_cbc')) {
            echo '<script>'.PHP_EOL;
            echo 'alert("MCRYPT 관련 함수를 사용할 수 없습니다.\n서버 관리자에게 문의해 주십시오.");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        }

        $log_path = G5_CONTENTS_PATH.'/inicis/log';

        if(!is_dir($log_path)) {
            echo '<script>'.PHP_EOL;
            echo 'alert("'.str_replace(G5_PATH.'/', '', G5_CONTENTS_PATH).'/inicis 폴더 안에 log 폴더를 생성하신 후 쓰기권한을 부여해 주십시오.\n> mkdir log\n> chmod 707 log");'.PHP_EOL;
            echo '</script>'.PHP_EOL;
        } else {
            if(!is_writable($log_path)) {
                echo '<script>'.PHP_EOL;
                echo 'alert("'.str_replace(G5_PATH.'/', '',$log_path).' 폴더에 쓰기권한을 부여해 주십시오.\n> chmod 707 log");'.PHP_EOL;
                echo '</script>'.PHP_EOL;
            }
        }
    }
}

include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
