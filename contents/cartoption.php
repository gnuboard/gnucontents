<?php
include_once('./_common.php');

if($is_guest)
    die('login');

$it_id = $_POST['it_id'];

$sql = " select * from {$g5['g5_contents_item_table']} where it_id = '$it_id' and it_use = '1' ";
$it = sql_fetch($sql);

if(!$it['it_id'])
 die('no-item');

// 장바구니 자료
$cart_id = get_session('ss_cm_cart_id');
$sql = " select count(*) as cnt from {$g5['g5_contents_cart_table']} where od_id = '$cart_id' and it_id = '$it_id' ";
$row = sql_fetch($sql);

if(!$row['cnt'])
    die('no-cart');

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/cartoption.php');
    return;
}

$option = cm_get_cart_options($it, $cart_id);

if(!$option)
    die('no-option');
?>

<!-- 장바구니 옵션 시작 { -->
<form name="foption" method="post" action="<?php echo G5_CONTENTS_URL; ?>/cartupdate.php" onsubmit="return fcart_submit(this);">
<input type="hidden" name="act" value="optionmod">
<input type="hidden" name="it_id" value="<?php echo $it['it_id']; ?>">
<section class="tbl_wrap tbl_head02">
    <h3>상품옵션</h3>
    <table class="cit_ov_tbl">
    <thead>
    <tr>
        <th>옵션</th>
        <th class="chk_all_td" id="cart_chk">
            <label for="chk_opt_all" class="sound_only">옵션 전체 선택</label>
            <input type="checkbox" name="chk_opt_all" id="chk_opt_all">
        </th>

        <th class="cart_op_qty">수량</th>
        <th class="cart_op_pr">가격</th>
    </tr>
    </thead>
    <tbody>
    <?php echo $option; ?>
    </tbody>
    </table>
</section>

<div id="cit_opt_prc">
    총 구매금액 <span>0원</span>
</div>

<div class="btn_confirm">
    <input type="submit" value="선택사항적용" class="btn_submit">
    <button type="button" id="mod_option_close" class="btn_cancel">닫기</button>
</div>
</form>

<script>
// 구매금액 계산
price_calculate();

function fcart_submit(f)
{
    var $el_chk = $("input[name^=io_chk]:checked");

    if($el_chk.size() < 1) {
        alert("상품의 옵션을 하나이상 선택해 주십시오.");
        return false;
    }

    // 수량체크
    var is_qty = true;
    var ct_qty = 0;
    $el_chk.each(function() {
        ct_qty = parseInt($(this).closest("tr").find("input[name^=ct_qty]").val().replace(/[^0-9]/g, ""));
        if(isNaN(ct_qty))
            ct_qty = 0;

        if(ct_qty < 1) {
            is_qty = false;
            return false;
        }
    });

    if(!is_qty) {
        alert("수량을 1이상 입력해 주십시오.");
        return false;
    }

    return true;
}
</script>
<!-- } 장바구니 옵션 끝 -->