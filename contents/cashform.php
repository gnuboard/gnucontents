<?php
include_once('./_common.php');

if($is_guest)
    alert('회원 로그인 후 이용해 주십시오.');

if(!$setting['de_cash_charge_use']|| !$setting['de_cash_charge_price'])
    alert('캐시 충전이 불가능합니다. 관리자에게 문의해 주십시오.', G5_CONTENTS_URL);

// 캐시충전항목
$cash = explode('|', $setting['de_cash_charge_price']);
$cash_count = count($cash);
if(!$cash_count)
    alert('캐시충전 목록이 없습니다. 관리자에게 문의해 주십시오.', G5_CONTENTS_URL);

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/cashform.php');
    return;
}

$g5['title'] = '캐시충전';

// 전자결제를 사용할 때만 실행
if($setting['de_iche_use'] || $setting['de_vbank_use'] || $setting['de_hp_use'] || $setting['de_card_use']) {
    switch($setting['de_pg_service']) {
        case 'lg':
            $g5['body_script'] = 'onload="isActiveXOK();"';
            break;
        case 'inicis':
            $g5['body_script'] = 'onload="javascript:enable_click()"';
            break;
        default:
            $g5['body_script'] = 'onload="CheckPayplusInstall();"';
            break;
    }
}

include_once('./_head.php');

$action_url = G5_HTTPS_CONTENTS_URL.'/cashformupdate.php';

// 주문폼과 공통 사용을 위해 추가
$od_id = get_uniqid();
set_session('ss_cm_cash_charge_id', $od_id);
set_session('ss_cm_cash_charge_price', $setting['de_cash_charge_price']);
$tot_price = 0;
$goods = '';

require_once('./settle_'.$setting['de_pg_service'].'.inc.php');

// 결제대행사별 코드 include (스크립트 등)
require_once('./'.$setting['de_pg_service'].'/orderform.1.php');
?>

<form name="forderform" id="forderform" method="post" action="<?php echo $action_url; ?>" onsubmit="return forderform_check(this);" autocomplete="off">
    <input type="hidden" name="cs_temp_cash" value="0">

    <section id="cash_list">
        <h2>캐시충전 목록</h2>
        <ul>
            <li class="cash_list_tit ">
                <div class="csli_tit">
                <span class="cash_sl">선택</span>
                <span class="cash_paypr">결제금액</span>
                <span class="cash_rppr">충전금액</span>
                </div>
                <div class="csli_tit">
                <span class="cash_sl">선택</span>
                <span class="cash_paypr">결제금액</span>
                <span class="cash_rppr">충전금액</span>
                </div>
            </li>
            <?php
                for($i=0; $i<$cash_count; $i++) {
                    $info = explode(':', $cash[$i]);
                    $price = $info[0];
                    $charge = $info[1];

                    if(!$price || !$charge)
                        continue;
            ?>
            <li>
                <span class="cash_sl">
                    <input type="hidden" name="temp_charge_price[]" value="<?php echo $charge; ?>">
                    <label for="cs_temp_price_<?php echo $i; ?>" class="sound_only"><?php echo $price; ?>원 결제선택</label>
                    <input type="radio" name="cs_temp_price" id="cs_temp_price_<?php echo $i; ?>" value="<?php echo $price; ?>">
                </span>

                <span class="cash_paypr"><?php echo cm_display_price($price); ?></span>
                <span class="cash_rppr"><?php echo cm_display_price($charge); ?></span>
                <?php
                }

                if($i ==0)
                    alert('캐시충전 목록이 없습니다. 관리자에게 문의해 주십시오.', G5_CONTENTS_URL);
                ?>
            </li>
        </ul>

    </section>

    <?php
    // 결제대행사별 코드 include (결제대행사 정보 필드)
    require_once('./'.$setting['de_pg_service'].'/orderform.2.php');
    ?>

    <section id="sod_frm_pay">
        <h2>결제정보</h2>

        <div class="tbl_frm01 tbl_wrap">
            <table>
            <tbody>
            <tr>
                <th>결제금액</th>
                <td id="dsp_temp_price">0원</td>
            </tr>
            <tr>
                <th scope="row"><label for="cs_name">이름<strong class="sound_only"> 필수</strong></label></th>
                <td><input type="text" name="cs_name" value="<?php echo $member['mb_name']; ?>" id="cs_name" required class="required frm_input"></td>
            </tr>
            <tr>
                <th scope="row"><label for="cs_email">이메일<strong class="sound_only"> 필수</strong></label></th>
                <td><input type="text" name="cs_email" value="<?php echo $member['mb_email']; ?>" id="cs_email" required class="required frm_input" size="30"></td>
            </tr>
            <tr>
                <th scope="row"><label for="cs_hp">휴대폰</label></th>
                <td><input type="text" name="cs_hp" value="<?php echo $member['mb_hp']; ?>" id="cs_hp" required class="required frm_input"></td>
            </tr>
            </tbody>
            </table>
        </div>

        <?php
        $multi_settle == 0;
        $checked = '';

        if ($setting['de_bank_use'] || $setting['de_vbank_use'] || $setting['de_iche_use'] || $setting['de_card_use'] || $setting['de_hp_use']) {
            echo '<fieldset id="sod_frm_paysel">';
            echo '<legend>결제방법 선택</legend>';
        }

        // 무통장입금 사용
        if ($setting['de_bank_use']) {
            $multi_settle++;
            echo '<input type="radio" id="cs_settle_bank" name="cs_settle_case" value="무통장" '.$checked.'> <label for="cs_settle_bank">무통장입금</label>'.PHP_EOL;
            $checked = '';
        }

        // 가상계좌 사용
        if ($setting['de_vbank_use']) {
            $multi_settle++;
            echo '<input type="radio" id="cs_settle_vbank" name="cs_settle_case" value="가상계좌" '.$checked.'> <label for="cs_settle_vbank">가상계좌</label>'.PHP_EOL;
            $checked = '';
        }

        // 계좌이체 사용
        if ($setting['de_iche_use']) {
            $multi_settle++;
            echo '<input type="radio" id="cs_settle_iche" name="cs_settle_case" value="계좌이체" '.$checked.'> <label for="cs_settle_iche">계좌이체</label>'.PHP_EOL;
            $checked = '';
        }

        // 휴대폰 사용
        if ($setting['de_hp_use']) {
            $multi_settle++;
            echo '<input type="radio" id="cs_settle_hp" name="cs_settle_case" value="휴대폰" '.$checked.'> <label for="cs_settle_hp">휴대폰</label>'.PHP_EOL;
            $checked = '';
        }

        // 신용카드 사용
        if ($setting['de_card_use']) {
            $multi_settle++;
            echo '<input type="radio" id="cs_settle_card" name="cs_settle_case" value="신용카드" '.$checked.'> <label for="cs_settle_card">신용카드</label>'.PHP_EOL;
            $checked = '';
        }

        if ($setting['de_bank_use']) {
            // 은행계좌를 배열로 만든후
            $str = explode("\n", trim($setting['de_bank_account']));
            if (count($str) <= 1)
            {
                $bank_account = '<input type="hidden" name="cs_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
            }
            else
            {
                $bank_account = '<select name="cs_bank_account" id="cs_bank_account">'.PHP_EOL;
                $bank_account .= '<option value="">선택하십시오.</option>';
                for ($i=0; $i<count($str); $i++)
                {
                    //$str[$i] = str_replace("\r", "", $str[$i]);
                    $str[$i] = trim($str[$i]);
                    $bank_account .= '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
                }
                $bank_account .= '</select>'.PHP_EOL;
            }
            echo '<div id="settle_bank" style="display:none">';
            echo '<label for="cs_bank_account" class="sound_only">입금할 계좌</label>';
            echo $bank_account;
            echo '<br><label for="cs_deposit_name">입금자명</label>';
            echo '<input type="text" name="cs_deposit_name" id="cs_deposit_name" class="frm_input" size="10" maxlength="20">';
            echo '</div>';
        }

        if ($setting['de_bank_use'] || $setting['de_vbank_use'] || $setting['de_iche_use'] || $setting['de_card_use'] || $setting['de_hp_use']) {
            echo '</fieldset>';
        }

        if ($multi_settle == 0)
            echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';
        ?>
    </section>

    <?php
    // 결제대행사별 코드 include (주문버튼)
    require_once('./'.$setting['de_pg_service'].'/orderform.3.php');
    ?>

</form>

<script>
$(function() {
    $("input[name=cs_temp_price]").on("click", function() {
        var prc = String($(this).val());
        $("#dsp_temp_price").text(number_format(prc)+"원");
    });

    $("#cs_settle_bank").on("click", function() {
        $("[name=cs_deposit_name]").val( $("[name=cs_name]").val() );
        $("#settle_bank").show();
    });

    $("#cs_settle_iche,#cs_settle_card,#cs_settle_vbank,#cs_settle_hp").bind("click", function() {
        $("#settle_bank").hide();
    });
});

function forderform_check(f)
{
    var temp_price = document.getElementsByName("cs_temp_price");
    var temp_cash = document.getElementsByName("temp_charge_price[]");
    var price_check = false;
    var price = 0;
    var cs_temp_cash = 0;
    for (i=0; i<temp_price.length; i++)
    {
        if (temp_price[i].checked)
        {
            price_check = true;
            price = parseInt(temp_price[i].value);
            cs_temp_cash = temp_cash[i].value;
            break;
        }
    }
    if (!price_check)
    {
        alert("결제금액을 선택하십시오.");
        return false;
    }

    var settle_case = document.getElementsByName("cs_settle_case");
    var settle_check = false;
    var settle_method = "";
    for (i=0; i<settle_case.length; i++)
    {
        if (settle_case[i].checked)
        {
            settle_check = true;
            settle_method = settle_case[i].value;
            break;
        }
    }
    if (!settle_check)
    {
        alert("결제방식을 선택하십시오.");
        return false;
    }

    if (document.getElementById("cs_settle_iche")) {
        if (document.getElementById("cs_settle_iche").checked) {
            if (price < 150) {
                alert("계좌이체는 150원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("cs_settle_card")) {
        if (document.getElementById("cs_settle_card").checked) {
            if (price < 1000) {
                alert("신용카드는 1000원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("cs_settle_hp")) {
        if (document.getElementById("cs_settle_hp").checked) {
            if (price < 350) {
                alert("휴대폰은 350원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    f.cs_temp_cash.value = cs_temp_cash;
    f.good_mny.value = price;

    // pay_method 설정
    <?php if($setting['de_pg_service'] == 'kcp') { ?>
    switch(settle_method)
    {
        case "계좌이체":
            f.pay_method.value = "010000000000";
            break;
        case "가상계좌":
            f.pay_method.value = "001000000000";
            break;
        case "휴대폰":
            f.pay_method.value = "000010000000";
            break;
        case "신용카드":
            f.pay_method.value = "100000000000";
            break;
        default:
            f.pay_method.value = "무통장";
            break;
    }
    <?php } else if($setting['de_pg_service'] == 'lg') { ?>
    switch(settle_method)
    {
        case "계좌이체":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0030";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0030";
            break;
        case "가상계좌":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0040";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0040";
            break;
        case "휴대폰":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0060";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0060";
            break;
        case "신용카드":
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0010";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0010";
            break;
        default:
            f.LGD_CUSTOM_FIRSTPAY.value = "무통장";
            break;
    }
    <?php }  else if($setting['de_pg_service'] == 'inicis') { ?>
    switch(settle_method)
    {
        case "계좌이체":
            f.gopaymethod.value = "onlydbank";
            break;
        case "가상계좌":
            f.gopaymethod.value = "onlyvbank";
            break;
        case "휴대폰":
            f.gopaymethod.value = "onlyhpp";
            break;
        case "신용카드":
            f.gopaymethod.value = "onlycard";
            break;
        default:
            f.gopaymethod.value = "무통장";
            break;
    }
    <?php } ?>

    // 결제정보설정
    <?php if($setting['de_pg_service'] == 'kcp') { ?>
    f.good_name.value = f.cs_name.value + "님 캐시충전";
    f.buyr_name.value = f.cs_name.value;
    f.buyr_mail.value = f.cs_email.value;
    f.buyr_tel1.value = f.cs_hp.value;
    f.buyr_tel2.value = f.cs_hp.value;
    f.rcvr_name.value = f.cs_name.value;
    f.rcvr_tel1.value = f.cs_hp.value;
    f.rcvr_tel2.value = f.cs_hp.value;
    f.rcvr_mail.value = f.cs_email.value;

    if(f.pay_method.value != "무통장") {
        if(jsf__pay( f )) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
    <?php } if($setting['de_pg_service'] == 'lg') { ?>
    f.LGD_PRODUCTINFO.value = f.cs_name.value + "님 캐시충전";
    f.LGD_BUYER.value = f.cs_name.value;
    f.LGD_BUYEREMAIL.value = f.cs_email.value;
    f.LGD_BUYERPHONE.value = f.cs_hp.value;
    f.LGD_AMOUNT.value = f.good_mny.value;
    f.LGD_RECEIVER.value = f.cs_name.value;
    f.LGD_RECEIVERPHONE.value = f.cs_hp.value;

    if(f.LGD_CUSTOM_FIRSTPAY.value != "무통장") {
          Pay_Request("<?php echo $od_id; ?>", f.LGD_AMOUNT.value, f.LGD_TIMESTAMP.value);
          return false;
    } else {
        return true;
    }
    <?php } if($setting['de_pg_service'] == 'inicis') { ?>
    f.goodname.value    = f.cs_name.value + "님 캐시충전";
    f.buyername.value   = f.cs_name.value;
    f.buyeremail.value  = f.cs_email.value;
    f.buyertel.value    = f.cs_hp.value;
    f.recvname.value    = f.cs_name.value;
    f.recvtel.value     = f.cs_hp.value;

    if(f.gopaymethod.value != "무통장") {
        if(!set_encrypt_data(f))
            return false;

        return pay(f);
    } else {
        return true;
    }
    <?php } ?>
}
</script>

<?php
include_once('./_tail.php');

// 결제대행사별 코드 include (스크립트 실행)
require_once('./'.$setting['de_pg_service'].'/orderform.4.php');
?>