<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<input type="hidden" name="LGD_PAYKEY"        id="LGD_PAYKEY">                      <!-- LG유플러스 PAYKEY(인증후 자동셋팅)-->

<input type="hidden" name="good_mny"          value="<?php echo $tot_price ?>" >
<input type="hidden" name="LGD_PRODUCTINFO"   value="<?php echo $goods; ?>">        <!-- 상품정보 -->
<input type="hidden" name="res_cd"            value="">                             <!-- 결과 코드          -->

<div id="display_pay_button" class="btn_confirm">
    <span id="show_req_btn"><input type="button" name="submitChecked" onClick="pay_approval();" value="결제등록요청" class="btn_submit"></span>
    <span id="show_pay_btn" style="display:none;"><input type="button" onClick="forderform_check();" value="주문하기" class="btn_submit"></span>
    <a href="javascript:history.go(-1);" class="btn_cancel">취소</a>
</div>