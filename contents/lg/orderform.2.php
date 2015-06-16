<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

$LGD_CUSTOM_PROCESSTYPE = 'TWOTR';
?>

<input type="hidden" name="CST_PLATFORM"                id="CST_PLATFORM"       value="<?php echo $CST_PLATFORM; ?>">      <!-- 테스트, 서비스 구분 -->
<input type="hidden" name="CST_MID"                     id="CST_MID"            value="<?php echo $CST_MID; ?>">           <!-- 상점아이디 -->
<input type="hidden" name="LGD_MID"                     id="LGD_MID"            value="<?php echo $LGD_MID; ?>">           <!-- 상점아이디 -->
<input type="hidden" name="LGD_OID"                     id="LGD_OID"            value="<?php echo $od_id; ?>">             <!-- 주문번호 -->
<input type="hidden" name="LGD_BUYER"                   id="LGD_BUYER"          value="">                                  <!-- 구매자 -->
<input type="hidden" name="LGD_PRODUCTINFO"             id="LGD_PRODUCTINFO"    value="<?php echo $goods; ?>">             <!-- 상품정보 -->
<input type="hidden" name="LGD_AMOUNT"                  id="LGD_AMOUNT"         value="">                                  <!-- 결제금액 -->
<input type="hidden" name="LGD_CUSTOM_FIRSTPAY"         id="LGD_CUSTOM_FIRSTPAY" value="">                                 <!-- 결제수단 -->
<input type="hidden" name="LGD_BUYEREMAIL"              id="LGD_BUYEREMAIL"     value="">                                  <!-- 구매자 이메일 -->
<input type="hidden" name="LGD_CUSTOM_SKIN"             id="LGD_CUSTOM_SKIN"    value="<?php echo $LGD_CUSTOM_SKIN; ?>">   <!-- 결제창 SKIN -->
<input type="hidden" name="LGD_WINDOW_VER"              id="LGD_WINDOW_VER"     value="<?php echo $LGD_WINDOW_VER; ?>">    <!-- 결제창버전정보 (삭제하지 마세요) -->
<input type="hidden" name="LGD_CUSTOM_PROCESSTYPE"      id="LGD_CUSTOM_PROCESSTYPE" value="<?php echo $LGD_CUSTOM_PROCESSTYPE; ?>">         <!-- 트랜잭션 처리방식 -->
<input type="hidden" name="LGD_TIMESTAMP"               id="LGD_TIMESTAMP"      value="<?php echo $LGD_TIMESTAMP; ?>">     <!-- 타임스탬프 -->
<input type="hidden" name="LGD_HASHDATA"                id="LGD_HASHDATA"       value="">                                  <!-- MD5 해쉬암호값 -->
<input type="hidden" name="LGD_PAYKEY"                  id="LGD_PAYKEY"         value="">                                  <!-- LG유플러스 PAYKEY(인증후 자동셋팅)-->
<input type="hidden" name="LGD_VERSION"                 id="LGD_VERSION"        value="PHP_XPay_2.5">                      <!-- 버전정보 (삭제하지 마세요) -->
<input type="hidden" name="LGD_ESCROW_USEYN"            id="LGD_ESCROW_USEYN"   value="N">                                 <!-- 에스크로 적용 여부 -->
<input type="hidden" name="LGD_TAXFREEAMOUNT"           id="LGD_TAXFREEAMOUNT"  value="0">                                 <!-- 결제금액 중 면세금액 -->
<input type="hidden" name="LGD_BUYERIP"                 id="LGD_BUYERIP"        value="<?php echo $LGD_BUYERIP; ?>">       <!-- 구매자IP -->
<input type="hidden" name="LGD_BUYERID"                 id="LGD_BUYERID"        value="<?php echo $LGD_BUYERID; ?>">       <!-- 구매자ID -->
<input type="hidden" name="LGD_CUSTOM_USABLEPAY"        id="LGD_CUSTOM_USABLEPAY"   value="<?php echo $LGD_CUSTOM_USABLEPAY; ?>">   <!-- 결제가능수단 -->
<input type="hidden" name="LGD_CASHRECEIPTYN"           id="LGD_CASHRECEIPTYN"  value="N">                                 <!-- 현금영수증 사용 설정 -->
<input type="hidden" name="LGD_BUYERADDRESS"            id="LGD_BUYERADDRESS"   value="">                                  <!-- 구매자 주소 -->
<input type="hidden" name="LGD_BUYERPHONE"              id="LGD_BUYERPHONE"     value="">                                  <!-- 구매자 휴대폰번호 -->
<input type="hidden" name="LGD_RECEIVER"                id="LGD_RECEIVER"       value="">                                  <!-- 수취인 -->
<input type="hidden" name="LGD_RECEIVERPHONE"           id="LGD_RECEIVERPHONE"  value="">                                  <!-- 수취인 휴대폰번호 -->

<!-- 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 . -->
<input type="hidden" name="LGD_CASNOTEURL"              id="LGD_CASNOTEURL"     value="<?php echo $LGD_CASNOTEURL ?>">     <!-- 가상계좌 NOTEURL -->

<?php /* 주문폼 자바스크립트 에러 방지를 위해 추가함 */ ?>
<input type="hidden" name="good_mny"    value="<?php echo $tot_price; ?>">