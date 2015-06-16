<?php
$sub_menu = '600420';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$sql = " select *
           from {$g5['g5_contents_item_use_table']} a
           left join {$g5['member_table']} b on (a.mb_id = b.mb_id)
           left join {$g5['g5_contents_item_table']} c on (a.it_id = c.it_id)
          where is_id = '$is_id' ";
$is = sql_fetch($sql);
if (!$is['is_id'])
    alert('등록된 자료가 없습니다.');

$name = get_sideview($is['mb_id'], get_text($is['is_name']), $is['mb_email'], $is['mb_homepage']);

// 확인
$is_confirm_yes  =  $is['is_confirm'] ? 'checked="checked"' : '';
$is_confirm_no   = !$is['is_confirm'] ? 'checked="checked"' : '';

$g5['title'] = '사용후기';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$qstr = 'page='.$page.'&amp;sort1='.$sort1.'&amp;sort2='.$sort2;
?>

<form name="fitemuseform" method="post" action="./itemuseformupdate.php" onsubmit="return fitemuseform_submit(this);">
<input type="hidden" name="w" value="<?php echo $w; ?>">
<input type="hidden" name="is_id" value="<?php echo $is_id; ?>">
<input type="hidden" name="it_id" value="<?php echo $is['is_id']; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">


<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?> 수정</caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <tr>
        <th scope="row">상품명</th>
        <td><a href="<?php echo G5_CONTENTS_URL; ?>/item.php?it_id=<?php echo $is['it_id']; ?>"><?php echo $is['it_name']; ?></a></td>
    </tr>
    <tr>
        <th scope="row">이름</th>
        <td><?php echo $name; ?></td>
    </tr>
    <tr>
        <th scope="row">평점</th>
        <td><img src="<?php echo G5_CONTENTS_URL; ?>/img/s_star<?php echo $is['is_score']; ?>.png"> (<?php echo $is['is_score']; ?>점)</td>
    </tr>
    <tr>
        <th scope="row"><label for="is_subject">제목</label></th>
        <td><input type="text" name="is_subject" required class="required frm_input" id="is_subject" size="100"
        value='<?php echo conv_subject($is['is_subject'], 120); ?>'></td>
    </tr>
    <tr>
        <th scope="row">내용</th>
        <td><?php echo editor_html('is_content', $is['is_content']); ?></td>
    </tr>
    <tr>
        <th scope="row">확인</th>
        <td>
            <input type="radio" name="is_confirm" value="1" id="is_confirm_yes" <?php echo $is_confirm_yes; ?>>
            <label for="is_confirm_yes">예</label>
            <input type="radio" name="is_confirm" value="0" id="is_confirm_no" <?php echo $is_confirm_no; ?>>
            <label for="is_confirm_no">아니오</label>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
    <a href="./itemuselist.php?<?php echo $qstr; ?>">목록</a>
</div>
</form>

<script>
function fitemuseform_submit(f)
{
    <?php echo get_editor_js('is_content'); ?>

    return true;
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
