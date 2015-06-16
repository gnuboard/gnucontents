<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

// kcp 전자결제를 사용할 때만 실행
if($setting['de_iche_use'] || $setting['de_vbank_use'] || $setting['de_hp_use'] || $setting['de_card_use']) {
?>
<script>
StartSmartUpdate();
</script>
<?php } ?>