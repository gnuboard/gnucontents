<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<input type="hidden" name="good_mny"          value="<?php echo $tot_price ?>" >
<input type="hidden" name="res_cd"            value="">                                     <!-- 결과 코드          -->

<input type="hidden" name="P_HASH"            value="">
<input type="hidden" name="P_TYPE"            value="">
<input type="hidden" name="P_UNAME"           value="">
<input type="hidden" name="P_GOODS"           value="<?php echo $goods; ?>">
<input type="hidden" name="P_AUTH_DT"         value="">
<input type="hidden" name="P_AUTH_NO"         value="">
<input type="hidden" name="P_HPP_CORP"        value="">
<input type="hidden" name="P_APPL_NUM"        value="">
<input type="hidden" name="P_VACT_NUM"        value="">
<input type="hidden" name="P_VACT_NAME"       value="">
<input type="hidden" name="P_VACT_BANK"       value="">
<input type="hidden" name="P_CARD_ISSUER"     value="">

<div id="display_pay_button" class="btn_confirm">
    <span id="show_req_btn"><input type="button" name="submitChecked" onClick="pay_approval();" value="결제등록" class="btn_submit"></span>
    <span id="show_pay_btn" style="display:none;"><input type="button" onClick="forderform_check();" value="주문하기" class="btn_submit"></span>
    <a href="javascript:history.go(-1);" class="btn_cancel">취소</a>
</div>