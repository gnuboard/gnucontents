<?php
include_once('./_common.php');

if($is_guest)
    alert('회원 로그인 후 이용해 주십시오.');

// cart id 설정
cm_set_cart_id($sw_direct);

$s_cart_id = get_session('ss_cm_cart_id');

// 선택필드 초기화
$sql = " update {$g5['g5_contents_cart_table']} set ct_select = '0' where od_id = '$s_cart_id' ";
sql_query($sql);

$cart_action_url = G5_CONTENTS_URL.'/cartupdate.php';

if (G5_IS_MOBILE) {
    include_once(G5_MCONTENTS_PATH.'/cart.php');
    return;
}

$g5['title'] = '장바구니';
include_once('./_head.php');
?>

<script src="<?php echo G5_JS_URL ?>/contents.js"></script>

<!-- 장바구니 시작 { -->
<div id="cod_bsk">

    <form name="frmcartlist" id="cod_bsk_list" method="post" action="<?php echo $cart_action_url; ?>">
    <div class="tbl_head01 tbl_wrap">
        <table>
        <thead>
        <tr>
            <th scope="col" class="tbl_img">상품이미지</th>
            <th scope="col" class="tbl_tit">상품명</th>
            <th scope="col" class="tbl_qty">총수량</th>
            <th scope="col" class="tbl_pr">판매가</th>
            <th scope="col" class="tbl_al">소계</th>
            <th scope="col" class="tbl_pt">포인트</th>
            <th scope="col" class="tbl_chk">
                <label for="ct_all" class="sound_only">상품 전체</label>
                <input type="checkbox" name="ct_all" value="1" id="ct_all" checked="checked">
            </th>
        </tr>
        </thead>
        <tbody>

        <?php
        $tot_point = 0;
        $tot_sell_price = 0;

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
                  where a.od_id = '$s_cart_id' ";
        if($contetns['de_cart_keep_term']) {
            $ctime = date('Y-m-d', G5_SERVER_TIME - ($setting['de_cart_keep_term'] * 86400));
            $sql .= " and substring(a.ct_time, 1, 10) >= '$ctime' ";
        }
        $sql .= " group by a.it_id ";
        $sql .= " order by a.ct_id ";
        $result = sql_query($sql);

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

            if ($i==0) { // 계속쇼핑
                $continue_ca_id = $row['ca_id'];
            }

            $a1 = '<a href="./item.php?it_id='.$row['it_id'].'"><b>';
            $a2 = '</b></a>';
            $image = cm_get_it_image($row['it_id'], 70, 70);

            $it_name = $a1 . stripslashes($row['it_name']) . $a2;
            $it_options = cm_print_item_options($row['it_id'], $s_cart_id);
            if($it_options) {
                $mod_options = '<div class="cod_option_btn"><button type="button" class="mod_option">선택사항수정</button></div>';
                $it_name .= '<div class="cod_opt">'.$it_options.'</div>';
            }

            $point      = $sum['point'];
            $sell_price = $sum['price'];
        ?>

        <tr>
            <td class="cod_img"><?php echo $image; ?></td>
            <td class="cod_name">
                <input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
                <input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo get_text($row['it_name']); ?>">
                <?php echo $it_name.$mod_options; ?>
            </td>
            <td class="td_num"><?php echo number_format($sum['qty']); ?></td>
            <td class="td_numbig"><?php echo number_format($row['ct_price']); ?></td>
            <td class="td_numbig"><span id="sell_price_<?php echo $i; ?>"><?php echo number_format($sell_price); ?></span></td>
            <td class="td_numbig"><?php echo number_format($point); ?></td>
            <td class="td_chk">
                <label for="ct_chk_<?php echo $i; ?>" class="sound_only">상품</label>
                <input type="checkbox" name="ct_chk[<?php echo $i; ?>]" value="1" id="ct_chk_<?php echo $i; ?>" checked="checked">
            </td>
        </tr>

        <?php
            $tot_point      += $point;
            $tot_sell_price += $sell_price;
        } // for 끝

        if ($i == 0) {
            echo '<tr><td colspan="7" class="empty_table">장바구니에 담긴 상품이 없습니다.</td></tr>';
        }
        ?>
        </tbody>
        </table>
    </div>

    <?php
    $tot_price = $tot_sell_price; // 총계 = 주문상품금액합계
    if ($tot_price > 0) {
    ?>
    <dl id="cod_bsk_tot">
        <?php
        if ($tot_price > 0) {
        ?>

        <dt class="cod_bsk_cnt">총계 가격/포인트</dt>
        <dd class="cod_bsk_cnt"><?php echo number_format($tot_price); ?> 원 / <?php echo number_format($tot_point); ?> 점</dd>
        <?php }?>

    </dl>
    <?php } ?>

    <div id="cod_bsk_act">
        <?php if ($i == 0) {?>
        <a href="<?php echo G5_CONTENTS_URL; ?>/" class="btn01">쇼핑 계속하기</a>
        <?php } else {?>
        <input type="hidden" name="url" value="./orderform.php">
        <input type="hidden" name="records" value="<?php echo $i; ?>">
        <input type="hidden" name="act" value="">
        <a href="<?php echo G5_CONTENTS_URL; ?>/list.php?ca_id=<?php echo $continue_ca_id; ?>" class="btn01">쇼핑 계속하기</a>
        <button type="button" onclick="return form_check('buy');" class="btn_submit">주문하기</button>
        <button type="button" onclick="return form_check('seldelete');" class="btn01">선택삭제</button>
        <button type="button" onclick="return form_check('alldelete');" class="btn01">비우기</button>
        <?php }?>
    </div>

    </form>

</div>

<script>
$(function() {
    var close_btn_idx;

    // 선택사항수정
    $(".mod_option").click(function() {
        var it_id = $(this).closest("tr").find("input[name^=it_id]").val();
        var $this = $(this);
        close_btn_idx = $(".mod_option").index($(this));

        $.post(
            "./cartoption.php",
            { it_id: it_id },
            function(data) {
                $("#mod_option_frm").remove();
                $this.after("<div id=\"mod_option_frm\"></div>");
                $("#mod_option_frm").html(data);
            }
        );
    });

    // 모두선택
    $("input[name=ct_all]").click(function() {
        if($(this).is(":checked"))
            $("input[name^=ct_chk]").attr("checked", true);
        else
            $("input[name^=ct_chk]").attr("checked", false);
    });

    // 옵션수정 닫기
    $(document).on("click", "#mod_option_close", function() {
        $("#mod_option_frm").remove();
        $(".mod_option").eq(close_btn_idx).focus();
    });
    $("#win_mask").click(function () {
        $("#mod_option_frm").remove();
        $(".mod_option").eq(close_btn_idx).focus();
    });

});

function form_check(act) {
    var f = document.frmcartlist;
    var cnt = f.records.value;

    if (act == "buy")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("주문하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }
    else if (act == "alldelete")
    {
        f.act.value = act;
        f.submit();
    }
    else if (act == "seldelete")
    {
        if($("input[name^=ct_chk]:checked").size() < 1) {
            alert("삭제하실 상품을 하나이상 선택해 주십시오.");
            return false;
        }

        f.act.value = act;
        f.submit();
    }

    return true;
}
</script>
<!-- } 장바구니 끝 -->

<?php
include_once('./_tail.php');
?>