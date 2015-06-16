<?php
include_once('./_common.php');

if($is_guest)
    alert('회원 로그인 후 이용해 주십시오.');

set_session("ss_cm_direct", $sw_direct);
// 장바구니가 비어있는가?
if ($sw_direct) {
    $tmp_cart_id = get_session('ss_cm_cart_direct');
}
else {
    $tmp_cart_id = get_session('ss_cm_cart_id');
}

if (cm_get_cart_count($tmp_cart_id) == 0)
    alert('장바구니가 비어 있습니다.', G5_CONTENTS_URL.'/cart.php');

$g5['title'] = '주문서 작성';
include_once(G5_MCONTENTS_PATH.'/_head.php');

// 새로운 주문번호 생성
$od_id = get_uniqid();
set_session('ss_cm_order_id', $od_id);
$s_cart_id = $tmp_cart_id;
$order_action_url = G5_HTTPS_MCONTENTS_URL.'/orderformupdate.php';

require_once(G5_MCONTENTS_PATH.'/settle_'.$setting['de_pg_service'].'.inc.php');

// 결제등록 요청시 사용할 입금마감일
$ipgm_date = date("Ymd", (G5_SERVER_TIME + 86400 * 5));
$tablet_size = "1.0"; // 화면 사이즈 조정 - 기기화면에 맞게 수정(갤럭시탭,아이패드 - 1.85, 스마트폰 - 1.0)

// 결제대행사별 코드 include (결제대행사 정보 필드)
require_once(G5_MCONTENTS_PATH.'/'.$setting['de_pg_service'].'/orderform.1.php');

// 캐시충전번호제거
set_session('ss_cm_cash_charge_id', '');
?>

<form name="forderform" id="forderform" method="post" action="<?php echo $order_action_url; ?>" onsubmit="return forderform_check(this);" autocomplete="off">
<div id="cod_frm">
    <!-- 주문상품 확인 시작 { -->
    <p>주문하실 상품을 확인하세요.</p>

    <div class="tbl_head01 tbl_wrap">
        <ul class="sod_list">

            <?php
            $tot_point = 0;
            $tot_sell_price = 0;

            $goods = $goods_it_id = "";
            $goods_count = -1;

            // $s_cart_id 로 현재 장바구니 자료 쿼리
            $sql = " select a.ct_id,
                            a.it_id,
                            a.it_name,
                            a.ct_price,
                            a.ct_point,
                            a.ct_qty,
                            a.ct_status,
                            b.ca_id,
                            b.ca_id2,
                            b.ca_id3
                       from {$g5['g5_contents_cart_table']} a left join {$g5['g5_contents_item_table']} b on ( a.it_id = b.it_id )
                      where a.od_id = '$s_cart_id'
                        and a.ct_select = '1' ";
            if($setting['de_cart_keep_term']) {
                $ctime = date('Y-m-d', G5_SERVER_TIME - ($setting['de_cart_keep_term'] * 86400));
                $sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
            }
            $sql .= " group by a.it_id ";
            $sql .= " order by a.ct_id ";
            $result = sql_query($sql);

            $good_info = '';
            $it_cp_count = 0;

            for ($i=0; $row=mysql_fetch_array($result); $i++)
            {
                // 합계금액 계산
                $sql = " select SUM((ct_price + io_price) * ct_qty) as price,
                                SUM(ct_point * ct_qty) as point,
                                SUM(ct_qty) as qty
                            from {$g5['g5_contents_cart_table']}
                            where it_id = '{$row['it_id']}'
                              and od_id = '$s_cart_id' ";
                $sum = sql_fetch($sql);

                if (!$goods)
                {
                    $goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $row['it_name']);
                    $goods_it_id = $row['it_id'];
                }
                $goods_count++;

                $image_width = 70;
                $image_height = 70;
                $image = cm_get_it_image($row['it_id'], $image_width, $image_height);

                $it_name = '<b>' . get_text($row['it_name']) . '</b>';
                $it_options = cm_print_item_options($row['it_id'], $s_cart_id);
                if($it_options) {
                    $it_name .= '<div class="cod_opt">'.$it_options.'</div>';
                }

                $point      = $sum['point'];
                $sell_price = $sum['price'];

                // 쿠폰
                if($is_member) {
                    $cp_button = '';
                    $cp_count = 0;

                    $sql = " select cp_id
                                from {$g5['g5_contents_coupon_table']}
                                where mb_id IN ( '{$member['mb_id']}', '전체회원' )
                                  and cp_start <= '".G5_TIME_YMD."'
                                  and cp_end >= '".G5_TIME_YMD."'
                                  and cp_minimum <= '$sell_price'
                                  and (
                                        ( cp_method = '0' and cp_target = '{$row['it_id']}' )
                                        OR
                                        ( cp_method = '1' and ( cp_target IN ( '{$row['ca_id']}', '{$row['ca_id2']}', '{$row['ca_id3']}' ) ) )
                                      ) ";
                    $res = sql_query($sql);

                    for($k=0; $cp=sql_fetch_array($res); $k++) {
                        if(cm_is_used_coupon($member['mb_id'], $cp['cp_id']))
                            continue;

                        $cp_count++;
                    }

                    if($cp_count) {
                        $cp_button = '<div class="li_cp"><button type="button" class="cp_btn">쿠폰적용</button></div>';
                        $it_cp_count++;
                    }
                }
            ?>

            <li class="sod_li">
                <input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
                <input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
                <input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $sell_price; ?>">
                <input type="hidden" name="cp_id[<?php echo $i; ?>]" value="">
                <input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0">
                <div class="li_name">
                    <?php echo $it_name; ?>
                </div>
                <div class="li_total" style="padding-left:<?php echo $image_width + 10; ?>px;height:auto !important;height:<?php echo $image_height; ?>px;min-height:<?php echo $image_height; ?>px">
                    <span class="total_img"><?php echo $image; ?></span>
                    <span class="total_price total_span"><span>소계 </span><strong><?php echo number_format($sell_price); ?></strong></span>
                    <span class="total_point total_span"><span>적립포인트 </span><strong><?php echo number_format($point); ?></strong></span>
                </div>
                <?php echo $cp_button; ?>
            </li>

            <?php
                $tot_point      += $point;
                $tot_sell_price += $sell_price;
            } // for 끝

            if ($i == 0) {
                alert('장바구니가 비어 있습니다.', G5_CONTENTS_URL.'/cart.php');
            }
            ?>
        </ul>
    </div>

    <?php if ($goods_count) $goods .= ' 외 '.$goods_count.'건'; ?>
    <!-- } 주문상품 확인 끝 -->

    <!-- 주문상품 합계 시작 { -->
    <dl id="sod_bsk_tot">
        <dt class="sod_bsk_sell">주문</dt>
        <dd class="sod_bsk_sell"><strong><?php echo number_format($tot_sell_price); ?> 원</strong></dd>
        <?php if($it_cp_count > 0) { ?>
        <dt class="sod_bsk_coupon">쿠폰할인</dt>
        <dd class="sod_bsk_coupon"><strong id="ct_tot_coupon">0 원</strong></dd>
        <?php } ?>
        <dt class="sod_bsk_cnt">총계</dt>
        <dd class="sod_bsk_cnt">
            <?php $tot_price = $tot_sell_price; // 총계 = 주문상품금액합계 ?>
            <strong id="ct_tot_price"><?php echo number_format($tot_price); ?> 원</strong>
        </dd>
        <dt class="sod_bsk_point">포인트</dt>
        <dd class="sod_bsk_point"><strong><?php echo number_format($tot_point); ?> 점</strong></dd>
    </dl>
    <!-- } 주문상품 합계 끝 -->

    <input type="hidden" name="od_price"    value="<?php echo $tot_sell_price; ?>">
    <input type="hidden" name="org_od_price"    value="<?php echo $tot_sell_price; ?>">
    <input type="hidden" name="item_coupon" value="0">
    <input type="hidden" name="od_coupon" value="0">

    <!-- 구매하시는 분 입력 시작 { -->
    <section id="sod_frm_orderer">
        <h2>구매하시는 분</h2>

        <div class="tbl_frm01 tbl_wrap">
            <table>
            <tbody>
            <tr>
                <th scope="row"><label for="od_name">이름<strong class="sound_only"> 필수</strong></label></th>
                <td><input type="text" name="od_name" value="<?php echo $member['mb_name']; ?>" id="od_name" required class="frm_input required" maxlength="20"></td>
            </tr>
            <tr>
                <th scope="row"><label for="od_tel">전화번호<strong class="sound_only"> 필수</strong></label></th>
                <td><input type="text" name="od_tel" value="<?php echo $member['mb_tel']; ?>" id="od_tel" required class="frm_input required" maxlength="20"></td>
            </tr>
            <tr>
                <th scope="row"><label for="od_hp">핸드폰</label></th>
                <td><input type="text" name="od_hp" value="<?php echo $member['mb_hp']; ?>" id="od_hp" required class="frm_input required" maxlength="20"></td>
            </tr>
            <tr>
                <th scope="row"><label for="od_email">E-mail<strong class="sound_only"> 필수</strong></label></th>
                <td><input type="text" name="od_email" value="<?php echo $member['mb_email']; ?>" id="od_email" required class="frm_input required" size="30" maxlength="100"></td>
            </tr>
            <tr>
                <th scope="row"><label for="od_memo">전하실말씀</label></th>
                <td><textarea name="od_memo" id="od_memo"></textarea></td>
            </tr>
            </tbody>
            </table>
        </div>
    </section>
    <!-- } 구매하시는 분 입력 끝 -->

    <!-- 결제정보 입력 시작 { -->
    <?php
    $oc_cnt =  0;
    if($is_member && $tot_sell_price > 0) {
        // 주문쿠폰
        $sql = " select cp_id
                    from {$g5['g5_contents_coupon_table']}
                    where mb_id IN ( '{$member['mb_id']}', '전체회원' )
                      and cp_method = '2'
                      and cp_start <= '".G5_TIME_YMD."'
                      and cp_end >= '".G5_TIME_YMD."'
                      and cp_minimum <= '$tot_price' ";
        $res = sql_query($sql);

        for($k=0; $cp=sql_fetch_array($res); $k++) {
            if(cm_is_used_coupon($member['mb_id'], $cp['cp_id']))
                continue;

            $oc_cnt++;
        }
    }
    ?>

    <section id="sod_frm_pay">
        <h2>결제정보</h2>

        <div class="tbl_frm01 tbl_wrap">
            <table>
            <tbody>
            <?php if($oc_cnt > 0) { ?>
            <tr>
                <th scope="row">주문할인쿠폰</th>
                <td>
                    <input type="hidden" name="od_cp_id" value="">
                    <button type="button" id="od_coupon_btn" class="btn_frmline">쿠폰적용</button>
                </td>
            </tr>
            <tr>
                <th scope="row">주문할인금액</th>
                <td><span id="od_cp_price">0</span>원</td>
            </tr>
            <?php } ?>
            <tr>
                <th>총 주문금액</th>
                <td><span id="od_tot_price"><?php echo number_format($tot_price); ?></span>원</td>
            </tr>
            </tbody>
            </table>
        </div>

        <?php
        $multi_settle == 0;
        $checked = '';

        $temp_point = 0;
        // 회원이면서 포인트사용이면
        if ($is_member && $config['cf_use_point'])
        {
            // 포인트 결제 사용 포인트보다 회원의 포인트가 크다면
            if ($member['mb_point'] >= $setting['de_settle_min_point'])
            {
                $temp_point = (int)$setting['de_settle_max_point'];

                if($temp_point > (int)$tot_sell_price)
                    $temp_point = (int)$tot_sell_price;

                if($temp_point > (int)$member['mb_point'])
                    $temp_point = (int)$member['mb_point'];

                $point_unit = (int)$setting['de_settle_point_unit'];
                $temp_point = (int)((int)($temp_point / $point_unit) * $point_unit);
        ?>
            <div class="sod_frm_pt">
                <p>보유포인트(<?php echo cm_display_point($member['mb_point']); ?>)중 <strong id="use_max_point">최대 <?php echo cm_display_point($temp_point); ?></strong>까지 사용 가능</p>
                <input type="hidden" name="max_temp_point" value="<?php echo $temp_point; ?>">
                <label for="od_temp_point">사용 포인트</label>
                <input type="text" name="od_temp_point" value="0" id="od_temp_point" class="frm_input" size="10">점 (<?php echo $point_unit; ?>점 단위로 입력하세요.)
            </div>
        <?php
            $multi_settle++;
            }
        }

        $temp_cash = 0;
        $max_mb_cash = 0;
        // 회원이면서 캐시결제사용이면
        if ($is_member && $setting['de_cash_use'])
        {
            // 캐시금액
            $mb_cash = get_member_cash($member['mb_id']);

            if ($mb_cash > 0)
            {
                $temp_cash = (int)$mb_cash;
                $max_mb_cash = (int)$mb_cash;

                if($temp_cash > (int)$tot_sell_price)
                    $temp_cash = (int)$tot_sell_price;
        ?>
            <div class="sod_frm_pt">
                <p>보유캐시(<?php echo cm_display_price($mb_cash); ?>)중 <strong id="use_max_cash">최대 <?php echo cm_display_price($temp_cash); ?></strong>까지 사용 가능</p>
                <input type="hidden" name="max_temp_cash" value="<?php echo $temp_cash; ?>">
                <label for="od_temp_cash">사용 캐시</label>
                <input type="text" name="od_temp_cash" value="0" id="od_temp_cash" class="frm_input" size="10">원
            </div>
        <?php
            $multi_settle++;
            }
        }

        if ($setting['de_bank_use'] || $setting['de_vbank_use'] || $setting['de_iche_use'] || $setting['de_card_use'] || $setting['de_hp_use']) {
            echo '<fieldset id="sod_frm_paysel">';
            echo '<legend>결제방법 선택</legend>';
        }

        // 무통장입금 사용
        if ($setting['de_bank_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장" '.$checked.'> <label for="od_settle_bank">무통장입금</label>'.PHP_EOL;
            $checked = '';
        }

        // 가상계좌 사용
        if ($setting['de_vbank_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_vbank" name="od_settle_case" value="가상계좌" '.$checked.'> <label for="od_settle_vbank">가상계좌</label>'.PHP_EOL;
            $checked = '';
        }

        // 계좌이체 사용
        if ($setting['de_iche_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_iche" name="od_settle_case" value="계좌이체" '.$checked.'> <label for="od_settle_iche">계좌이체</label>'.PHP_EOL;
            $checked = '';
        }

        // 휴대폰 사용
        if ($setting['de_hp_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_hp" name="od_settle_case" value="휴대폰" '.$checked.'> <label for="od_settle_hp">휴대폰</label>'.PHP_EOL;
            $checked = '';
        }

        // 신용카드 사용
        if ($setting['de_card_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_card" name="od_settle_case" value="신용카드" '.$checked.'> <label for="od_settle_card">신용카드</label>'.PHP_EOL;
            $checked = '';
        }

        if ($setting['de_bank_use']) {
            // 은행계좌를 배열로 만든후
            $str = explode("\n", trim($setting['de_bank_account']));
            if (count($str) <= 1)
            {
                $bank_account = '<input type="hidden" name="od_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
            }
            else
            {
                $bank_account = '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
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
            echo '<label for="od_bank_account" class="sound_only">입금할 계좌</label>';
            echo $bank_account;
            echo '<br><label for="od_deposit_name">입금자명</label>';
            echo '<input type="text" name="od_deposit_name" id="od_deposit_name" class="frm_input" size="10" maxlength="20">';
            echo '</div>';
        }

        if ($setting['de_bank_use'] || $setting['de_vbank_use'] || $setting['de_iche_use'] || $setting['de_card_use'] || $setting['de_hp_use']) {
            echo '</fieldset>';
        }

        if (!$setting['de_card_point'])
            echo '<p id="sod_frm_pt_alert"><strong>무통장입금</strong> 이외의 결제 수단으로 결제하시는 경우 포인트를 적립해드리지 않습니다.</p>';

        if ($multi_settle == 0)
            echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';
        ?>
    </section>
    <!-- } 결제 정보 입력 끝 -->




    <?php
    // 결제대행사별 코드 include (주문버튼)
    require_once(G5_MCONTENTS_PATH.'/'.$setting['de_pg_service'].'/orderform.2.php');
    ?>
    <div id="show_progress" style="display:none;">
        <span style="display:block; text-align:center;margin-top:120px"><img src="<?php echo G5_CONTENTS_URL; ?>/img/loading.gif" alt="" ></span>
        <span style="display:block; text-align:center;margin-top:10px; font-size:14px">주문완료 중입니다. 잠시만 기다려 주십시오.</span>
    </div>
 
</div>
</form>
<script>
$(function() {
    var $cp_btn_el;
    var $cp_row_el;
    var zipcode = "";

    $(".cp_btn").click(function() {
        $cp_btn_el = $(this);
        $cp_row_el = $(this).closest("li");
        $("#cp_frm").remove();
        var it_id = $cp_btn_el.closest("li").find("input[name^=it_id]").val();

        $.post(
            "./orderitemcoupon.php",
            { it_id: it_id,  sw_direct: "<?php echo $sw_direct; ?>" },
            function(data) {
                $cp_btn_el.after(data);
            }
        );
    });

    $(".cp_apply").live("click", function() {
        var $el = $(this).closest("li");
        var cp_id = $el.find("input[name='f_cp_id[]']").val();
        var price = $el.find("input[name='f_cp_prc[]']").val();
        var subj = $el.find("input[name='f_cp_subj[]']").val();
        var sell_price;

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        // 이미 사용한 쿠폰이 있는지
        var cp_dup = false;
        var cp_dup_idx;
        var $cp_dup_el;
        $("input[name^=cp_id]").each(function(index) {
            var id = $(this).val();

            if(id == cp_id) {
                cp_dup_idx = index;
                cp_dup = true;
                $cp_dup_el = $(this).closest("tr");;

                return false;
            }
        });

        if(cp_dup) {
            var it_name = $("input[name='it_name["+cp_dup_idx+"]']").val();
            if(!confirm(subj+ "쿠폰은 "+it_name+"에 사용되었습니다.\n"+it_name+"의 쿠폰을 취소한 후 적용하시겠습니까?")) {
                return false;
            } else {
                coupon_cancel($cp_dup_el);
                $("#cp_frm").remove();
                $cp_dup_el.find(".cp_btn").text("적용").focus();
                $cp_dup_el.find(".cp_cancel").remove();
            }
        }

        var $s_el = $cp_row_el.find(".total_price");;
        sell_price = parseInt($cp_row_el.find("input[name^=it_price]").val());
        sell_price = sell_price - parseInt(price);
        if(sell_price < 0) {
            alert("쿠폰할인금액이 상품 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }
        $s_el.text(number_format(String(sell_price)));
        $cp_row_el.find("input[name^=cp_id]").val(cp_id);
        $cp_row_el.find("input[name^=cp_price]").val(price);

        calculate_total_price();
        $("#cp_frm").remove();
        $cp_btn_el.text("변경").focus();
        if(!$cp_row_el.find(".cp_cancel").size())
            $cp_btn_el.after("<button type=\"button\" class=\"cp_cancel btn_frmline\">취소</button>");
    });

    $("#cp_close").live("click", function() {
        $("#cp_frm").remove();
        $cp_btn_el.focus();
    });

    $(".cp_cancel").live("click", function() {
        coupon_cancel($(this).closest("li"));
        calculate_total_price();
        $("#cp_frm").remove();
        $(this).closest("li").find(".cp_btn").text("쿠폰적용").focus();
        $(this).remove();
    });

    $("#od_coupon_btn").click(function() {
        $("#od_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=org_od_price]").val()) - parseInt($("input[name=item_coupon]").val());
        if(price <= 0) {
            alert("상품금액이 0원이므로 쿠폰을 사용할 수 없습니다.");
            return false;
        }
        $.post(
            "./ordercoupon.php",
            { price: price },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(".od_cp_apply").live("click", function() {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='o_cp_id[]']").val();
        var price = parseInt($el.find("input[name='o_cp_prc[]']").val());
        var subj = $el.find("input[name='o_cp_subj[]']").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        var od_price = parseInt($("input[name=org_od_price]").val()) - item_coupon;

        if(price == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        if(od_price - price <= 0) {
            alert("쿠폰할인금액이 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }

        $("input[name=sc_cp_id]").val("");
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();

        $("input[name=od_price]").val(od_price - price);
        $("input[name=od_cp_id]").val(cp_id);
        $("input[name=od_coupon]").val(price);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(number_format(String(price)));
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰변경").focus();
        if(!$("#od_coupon_cancel").size())
            $("#od_coupon_btn").after("<button type=\"button\" id=\"od_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $("#od_coupon_close").live("click", function() {
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").focus();
    });

    $("#od_coupon_cancel").live("click", function() {
        var org_price = $("input[name=org_od_price]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        $("input[name=od_price]").val(org_price - item_coupon);
        $("input[name=sc_cp_id]").val("");
        $("input[name=od_coupon]").val(0);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();
    });

    $("#od_settle_bank").on("click", function() {
        $("[name=od_deposit_name]").val( $("[name=od_name]").val() );
        $("#settle_bank").show();
        $("#show_req_btn").hide();
        $("#show_pay_btn").show();
    });

    $("#od_settle_iche,#od_settle_card,#od_settle_vbank,#od_settle_hp").bind("click", function() {
        $("#settle_bank").hide();
        $("#show_req_btn").show();
        $("#show_pay_btn").hide();
    });
});

function coupon_cancel($el)
{
    var $dup_sell_el = $el.find(".total_price");
    var $dup_price_el = $el.find("input[name^=cp_price]");
    var org_sell_price = $el.find("input[name^=it_price]").val();

    $dup_sell_el.text(number_format(String(org_sell_price)));
    $dup_price_el.val(0);
    $el.find("input[name^=cp_id]").val("");
}

function calculate_total_price()
{
    var $it_prc = $("input[name^=it_price]");
    var $cp_prc = $("input[name^=cp_price]");
    var tot_sell_price = sell_price = tot_cp_price = 0;
    var it_price, cp_price, it_notax;
    var tot_mny = 0;

    $it_prc.each(function(index) {
        it_price = parseInt($(this).val());
        cp_price = parseInt($cp_prc.eq(index).val());
        sell_price += it_price;
        tot_cp_price += cp_price;
    });

    tot_sell_price = sell_price - tot_cp_price;

    $("#ct_tot_coupon").text(number_format(String(tot_cp_price))+" 원");
    $("#ct_tot_price").text(number_format(String(tot_sell_price))+" 원");

    $("input[name=good_mny]").val(tot_sell_price);
    $("input[name=od_price]").val(sell_price - tot_cp_price);
    $("input[name=item_coupon]").val(tot_cp_price);
    $("input[name=od_coupon]").val(0);
    <?php if($oc_cnt > 0) { ?>
    $("input[name=od_cp_id]").val("");
    $("#od_cp_price").text(0);
    if($("#od_coupon_cancel").size()) {
        $("#od_coupon_btn").text("쿠폰적용");
        $("#od_coupon_cancel").remove();
    }
    <?php } ?>
    $("input[name=od_temp_point]").val(0);
    $("input[name=od_temp_cash]").val(0);
    <?php if($temp_point > 0 && $is_member) { ?>
    calculate_temp_point();
    <?php } ?>
    <?php if($temp_cash > 0 && $is_member) { ?>
    calculate_temp_cash();
    <?php } ?>
    calculate_order_price();
}

function calculate_order_price()
{
    var sell_price = parseInt($("input[name=od_price]").val());

    $("input[name=good_mny]").val(sell_price);
    $("#od_tot_price").text(number_format(String(sell_price)));
    <?php if($temp_point > 0 && $is_member) { ?>
    calculate_temp_point();
    <?php } ?>
    <?php if($temp_cash > 0 && $is_member) { ?>
    calculate_temp_cash();
    <?php } ?>
}

function calculate_temp_point()
{
    var sell_price = parseInt($("input[name=od_price]").val());
    var mb_point = parseInt(<?php echo $member['mb_point']; ?>);
    var max_point = parseInt(<?php echo $setting['de_settle_max_point']; ?>);
    var point_unit = parseInt(<?php echo $setting['de_settle_point_unit']; ?>);
    var temp_point = max_point;

    if(temp_point > sell_price)
        temp_point = sell_price;

    if(temp_point > mb_point)
        temp_point = mb_point;

    temp_point = parseInt(temp_point / point_unit) * point_unit;

    $("#use_max_point").text("최대 "+number_format(String(temp_point))+"점");
    $("input[name=max_temp_point]").val(temp_point);
}

function calculate_temp_cash()
{
    var sell_price = parseInt($("input[name=od_price]").val());
    var max_cash = parseInt(<?php echo $max_mb_cash; ?>);
    var temp_cash = max_cash;

    if(temp_cash > sell_price)
        temp_cash = sell_price;

    $("#use_max_cash").text("최대 "+number_format(String(temp_cash))+"원");
    $("input[name=max_temp_cash]").val(temp_cash);
}

/* 결제방법에 따른 처리 후 결제등록요청 실행 */
var settle_method = "";
var settle_check = false;
var temp_point = 0;
var temp_cash = 0;
var tot_price = 0;

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

    if(tot_price == 0) {
        pf.submit();
        return;
    }

    if(settle_method == "무통장") {
        pf.submit();
        return;
    }

    <?php if($setting['de_pg_service'] == 'kcp') { ?>
    f.buyr_name.value = pf.od_name.value;
    f.buyr_mail.value = pf.od_email.value;
    f.buyr_tel1.value = pf.od_tel.value;
    f.buyr_tel2.value = pf.od_hp.value;
    f.rcvr_name.value = pf.od_name.value;
    f.rcvr_tel1.value = pf.od_tel.value;
    f.rcvr_tel2.value = pf.od_hp.value;
    f.rcvr_mail.value = pf.od_email.value;
    f.good_mny.value  = pf.good_mny.value;
    f.good_name.value = pf.good_name.value;
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
    f.LGD_BUYER.value = pf.od_name.value;
    f.LGD_BUYEREMAIL.value = pf.od_email.value;
    f.LGD_BUYERPHONE.value = pf.od_hp.value;
    f.LGD_AMOUNT.value = pf.good_mny.value;
    f.LGD_PRODUCTINFO.value = pf.LGD_PRODUCTINFO.value;
    f.LGD_RECEIVER.value = pf.od_name.value;
    f.LGD_RECEIVERPHONE.value = pf.od_hp.value;
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
    f.P_GOODS.value = pf.P_GOODS.value;
    f.P_UNAME.value = pf.od_name.value;
    f.P_MOBILE.value = pf.od_hp.value;
    f.P_EMAIL.value = pf.od_email.value;
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

// 주문폼 필드체크
function orderfield_check(f)
{
    errmsg = "";
    errfld = "";
    var deffld = "";

    check_field(f.od_name, "주문하시는 분 이름을 입력하십시오.");
    if (typeof(f.od_pwd) != 'undefined')
    {
        clear_field(f.od_pwd);
        if( (f.od_pwd.value.length<3) || (f.od_pwd.value.search(/([^A-Za-z0-9]+)/)!=-1) )
            error_field(f.od_pwd, "회원이 아니신 경우 주문서 조회시 필요한 비밀번호를 3자리 이상 입력해 주십시오.");
    }
    check_field(f.od_tel, "주문하시는 분 전화번호를 입력하십시오.");

    clear_field(f.od_email);
    if(f.od_email.value=='' || f.od_email.value.search(/(\S+)@(\S+)\.(\S+)/) == -1)
        error_field(f.od_email, "E-mail을 바르게 입력해 주십시오.");

    var settle_case = document.getElementsByName("od_settle_case");
    for (i=0; i<settle_case.length; i++)
    {
        if (settle_case[i].checked)
        {
            settle_check = true;
            settle_method = settle_case[i].value;
            break;
        }
    }

    var od_settle_bank = document.getElementById("od_settle_bank");
    if (od_settle_bank) {
        if (od_settle_bank.checked) {
            check_field(f.od_bank_account, "계좌번호를 선택하세요.");
            check_field(f.od_deposit_name, "입금자명을 입력하세요.");
        }
    }

    if (errmsg)
    {
        alert(errmsg);
        errfld.focus();
        return false;
    }

    return true;
}

// 결제체크
function payment_check(f)
{
    var od_price = parseInt(f.od_price.value);

    var max_point = 0;
    if (typeof(f.max_temp_point) != "undefined")
        max_point  = parseInt(f.max_temp_point.value);

    var max_cash = 0;
    if (typeof(f.max_temp_cash) != "undefined")
        max_cash  = parseInt(f.max_temp_cash.value);

    if (typeof(f.od_temp_point) != "undefined") {
        if (f.od_temp_point.value)
        {
            var point_unit = parseInt(<?php echo $setting['de_settle_point_unit']; ?>);
            temp_point = parseInt(f.od_temp_point.value);

            if (temp_point < 0) {
                alert("포인트를 0 이상 입력하세요.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > od_price) {
                alert("상품 주문금액보다 많이 포인트결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > <?php echo (int)$member['mb_point']; ?>) {
                alert("회원님의 포인트보다 많이 결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (temp_point > max_point) {
                alert(max_point + "점 이상 결제할 수 없습니다.");
                f.od_temp_point.select();
                return false;
            }

            if (parseInt(parseInt(temp_point / point_unit) * point_unit) != temp_point) {
                alert("포인트를 "+String(point_unit)+"점 단위로 입력하세요.");
                f.od_temp_point.select();
                return false;
            }
        }
    }

    if (typeof(f.od_temp_cash) != "undefined") {
        if (f.od_temp_cash.value)
        {
            temp_cash = parseInt(f.od_temp_cash.value);

            if (temp_cash < 0) {
                alert("캐시를 0 이상 입력하세요.");
                f.od_temp_cash.select();
                return false;
            }

            if (temp_cash > (od_price - temp_point)) {
                alert("상품 주문금액(포인트결제 제외) 보다 많이 캐시결제할 수 없습니다.");
                f.od_temp_cash.select();
                return false;
            }

            if (temp_cash > <?php echo (int)$max_mb_cash; ?>) {
                alert("회원님의 보유캐시보다 많이 결제할 수 없습니다.");
                f.od_temp_cash.select();
                return false;
            }

            if (temp_cash > max_cash) {
                alert(max_cash + "원 이상 결제할 수 없습니다.");
                f.od_temp_cash.select();
                return false;
            }
        }
    }

    tot_price = od_price - temp_point - temp_cash;

    // 추가 결제 금액이 0이면 submit
    if(tot_price == 0) {
        return true;
    }

    if (!settle_check)
    {
        alert("결제방식을 선택하십시오.");
        return false;
    }

    // pg 결제 금액에서 포인트 금액 차감
    if(settle_method != "무통장") {
        f.good_mny.value = tot_price;
    }

    if (document.getElementById("od_settle_iche")) {
        if (document.getElementById("od_settle_iche").checked) {
            if (tot_price < 150) {
                alert("계좌이체는 150원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_card")) {
        if (document.getElementById("od_settle_card").checked) {
            if (tot_price < 1000) {
                alert("신용카드는 1000원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_hp")) {
        if (document.getElementById("od_settle_hp").checked) {
            if (tot_price < 350) {
                alert("휴대폰은 350원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    return true;
}
</script>

<?php
include_once(G5_MCONTENTS_PATH.'/_tail.php');
?>