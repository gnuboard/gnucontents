<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<div id="display_pay_button" class="btn_confirm" style="display:none">
    <input type="submit" value="주문하기" class="btn_submit">
    <a href="javascript:history.go(-1);" class="btn01">취소</a>
</div>
<div id="display_pay_process" style="display:none">
    <img src="<?php echo G5_CONTENTS_URL; ?>/img/loading.gif" alt="">
    <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<?php
// 무통장 입금만 사용할 때는 주문하기 버튼 보이게
if(!($setting['de_iche_use'] || $setting['de_vbank_use'] || $setting['de_hp_use'] || $setting['de_card_use'])) {
?>
<script>
document.getElementById("display_pay_button").style.display = "" ;
</script>
<?php } ?>