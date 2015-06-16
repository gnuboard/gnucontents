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

$g5['title'] = '캐시충전';
include_once(G5_MCONTENTS_PATH.'/_head.php');

$action_url = G5_HTTPS_MCONTENTS_URL.'/cashformupdate.php';

// 주문폼과 공통 사용을 위해 추가
$od_id = get_uniqid();
set_session('ss_cm_cash_charge_id', $od_id);
set_session('ss_cm_cash_charge_price', $setting['de_cash_charge_price']);
$tot_price = 0;
$goods = '';

require_once(G5_MCONTENTS_PATH.'/settle_'.$setting['de_pg_service'].'.inc.php');

// 결제대행사별 코드 include (스크립트 등)
require_once(G5_MCONTENTS_PATH.'/'.$setting['de_pg_service'].'/orderform.1.php');
?>

<form name="forderform" id="forderform" method="post" action="<?php echo $action_url; ?>" onsubmit="return forderform_check();" autocomplete="off">
    <input type="hidden" name="cs_id" value="<?php echo $od_id; ?>">
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
                <td><input type="text" name="cs_email" value="<?php echo $member['mb_email']; ?>" id="cs_email" required class="required frm_input"></td>
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
    require_once(G5_MCONTENTS_PATH.'/'.$setting['de_pg_service'].'/orderform.2.php');
    ?>

    <div id="show_progress" style="display:none;">
        <span style="display:block; text-align:center;margin-top:120px"><img src="<?php echo G5_CONTENTS_URL; ?>/img/loading.gif" alt="" ></span>
        <span style="display:block; text-align:center;margin-top:10px; font-size:14px">주문완료 중입니다. 잠시만 기다려 주십시오.</span>
    </div>

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
        $("#show_req_btn").hide();
        $("#show_pay_btn").show();
    });

    $("#cs_settle_iche,#cs_settle_card,#cs_settle_vbank,#cs_settle_hp").bind("click", function() {
        $("#settle_bank").hide();
        $("#show_req_btn").show();
        $("#show_pay_btn").hide();
    });
});

/* 결제방법에 따른 처리 후 결제등록요청 실행 */
var settle_method = "";
var settle_check = false;
var price_check = false;
var price = 0;
var cs_temp_cash = 0;

function pay_approval()
{
    var f = document.sm_form;
    var pf = document.forderform;

    // 필드체크
    if(!orderfield_check(pf))
        return false;

    // 금액체크
    if(!payment_check(pf))
        return false;

    if(settle_method == "무통장") {
        pf.submit();
        return;
    }

    <?php if($setting['de_pg_service'] == 'kcp') { ?>
    f.buyr_name.value = pf.cs_name.value;
    f.buyr_mail.value = pf.cs_email.value;
    f.buyr_tel1.value = pf.cs_hp.value;
    f.buyr_tel2.value = pf.cs_hp.value;
    f.rcvr_name.value = pf.cs_name.value;
    f.rcvr_tel1.value = pf.cs_hp.value;
    f.rcvr_tel2.value = pf.cs_hp.value;
    f.rcvr_mail.value = pf.cs_email.value;
    f.good_mny.value  = pf.good_mny.value;
    f.good_name.value = pf.cs_name.value + "님 캐시충전";
    f.settle_method.value = settle_method;
    <?php } else if($setting['de_pg_service'] == 'lg') { ?>
    var pay_method = "";
    switch(settle_method) {
        case "계좌이체":
            pay_method = "SC0030";
            break;
        case "가상계좌":
            pay_method = "SC0040";
            break;
        case "휴대폰":
            pay_method = "SC0060";
            break;
        case "신용카드":
            pay_method = "SC0010";
            break;
    }
    f.LGD_CUSTOM_FIRSTPAY.value = pay_method;
    f.LGD_BUYER.value = pf.cs_name.value;
    f.LGD_BUYEREMAIL.value = pf.cs_email.value;
    f.LGD_BUYERPHONE.value = pf.cs_hp.value;
    f.LGD_AMOUNT.value = pf.good_mny.value;
    f.LGD_PRODUCTINFO.value = pf.cs_name.value + "님 캐시충전";
    f.LGD_RECEIVER.value = pf.cs_name.value;
    f.LGD_RECEIVERPHONE.value = pf.cs_hp.value;
    <?php } else if($setting['de_pg_service'] == 'inicis') { ?>
    var paymethod = "";
    var width = 330;
    var height = 480;
    var xpos = (screen.width - width) / 2;
    var ypos = (screen.width - height) / 2;
    var position = "top=" + ypos + ",left=" + xpos;
    var features = position + ", width=320, height=440";
    switch(settle_method) {
        case "계좌이체":
            paymethod = "bank";
            break;
        case "가상계좌":
            paymethod = "vbank";
            break;
        case "휴대폰":
            paymethod = "mobile";
            break;
        case "신용카드":
            paymethod = "wcard";
            break;
    }
    f.P_AMT.value = pf.good_mny.value;
    f.P_GOODS.value = pf.cs_name.value + "님 캐시충전";
    f.P_UNAME.value = pf.cs_name.value;
    f.P_MOBILE.value = pf.cs_hp.value;
    f.P_EMAIL.value = pf.cs_email.value;
    f.P_RETURN_URL.value = "<?php echo $return_url.$od_id; ?>";
    f.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
    <?php } ?>

    //var new_win = window.open("about:blank", "tar_opener", "scrollbars=yes,resizable=yes");
    //f.target = "tar_opener";

    // 주문 정보 임시저장
    var order_data = $(pf).serialize();
    var save_result = "";
    $.ajax({
        type: "POST",
        data: order_data,
        url: g5_url+"/contents/ajax.orderdatasave.php",
        cache: false,
        async: false,
        success: function(data) {
            save_result = data;
        }
    });

    if(save_result) {
        alert(save_result);
        return false;
    }

    f.submit();
}

function orderfield_check(f)
{
    var temp_price = document.getElementsByName("cs_temp_price");
    var temp_cash = document.getElementsByName("temp_charge_price[]");

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

    return true;
}

function payment_check(f)
{
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

    return true;
}

function forderform_check()
{
    var f = document.forderform;

    // 필드체크
    if(!orderfield_check(f))
        return false;

    // 금액체크
    if(!payment_check(f))
        return false;

    if(settle_method != "무통장" && f.res_cd.value != "0000") {
        alert("결제등록요청 후 주문해 주십시오.");
        return false;
    }

    document.getElementById("display_pay_button").style.display = "none";
    document.getElementById("show_progress").style.display = "block";

    setTimeout(function() {
        f.submit();
    }, 300);
}
</script>

<?php
include_once(G5_MCONTENTS_PATH.'/_tail.php');
?>