<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
?>

<form name="sm_form" method="POST" action="" accept-charset="euc-kr">
<input type="hidden" name="P_OID"        value="<?php echo $od_id; ?>">
<input type="hidden" name="P_GOODS"      value="">
<input type="hidden" name="P_AMT"        value="">
<input type="hidden" name="P_UNAME"      value="">
<input type="hidden" name="P_MOBILE"     value="">
<input type="hidden" name="P_EMAIL"      value="">
<input type="hidden" name="P_MID"        value="<?php echo $setting['de_inicis_mid']; ?>">
<input type="hidden" name="P_NEXT_URL"   value="<?php echo $next_url; ?>">
<input type="hidden" name="P_NOTI_URL"   value="<?php echo $noti_url; ?>">
<input type="hidden" name="P_RETURN_URL" value="">
<input type="hidden" name="P_HPP_METHOD" value="2">
<input type="hidden" name="P_RESERVED"   value="bank_receipt=N&twotrs_isp=Y&block_isp=Y<?php echo $useescrow; ?>">
<input type="hidden" name="P_NOTI"       value="<?php echo $od_id; ?>">
<input type="hidden" name="P_QUOTABASE"  value="01:02:03:04:05:06:07:08:09:10:11:12"> <!-- 할부기간 설정 01은 일시불 -->
</form>