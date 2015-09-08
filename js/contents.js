$(function(){
    // 모두선택
    $(document).on("click", "#chk_opt_all", function() {
        if($(this).is(":checked"))
            $("input[name^=io_chk]").attr("checked", true);
        else
            $("input[name^=io_chk]").attr("checked", false);

        price_calculate();
    });

    // 선택시 가격 계산
    $(document).on("click", "input[name^=io_chk]", function() {
        price_calculate();
    });

    // 수량변경
    $(document).on("click", "button.change_qty", function() {
        var act = $.trim($(this).text());
        var $qty = $(this).closest("tr").find("input[name^=ct_qty]");
        var qty = parseInt($qty.val().replace(/[^0-9]/g, ""));
        if(isNaN(qty))
            qty = 1;

        if(act == '증가') {
            qty++;
            $qty.val(qty);
        } else if(act == '감소') {
            qty--;
            if(qty < 1) {
                alert("수량은 1이상 입력해 주십시오.");
                $qty.val(1);
                return false;
            }

            $qty.val(qty);
        }

        price_calculate();
    });

    // 수량입력
    $(document).on("keyup", "input[name^=ct_qty]", function() {
        var qty = parseInt($(this).val().replace(/[^0-9]/g, ""));
        if(isNaN(qty)) {
            alert("수량은 숫자만 입력해 주십시오.");
            $(this).val(1);
            return false;
        }

        if(qty < 1) {
            alert("수량은 1이상 입력해 주십시오.");
            $(this).val(1);
            return false;
        }

        price_calculate();
    });
});

// 구매가격 계산
function price_calculate()
{
    var tot_price = 0;
    var price = 0;
    var qty;
    var $sel = $("input[name^=io_chk]:checked");

    if($sel.size() > 0) {
        $sel.each(function() {
            price = parseInt($(this).closest("tr").find("input[name^=io_price]").val());
            qty = parseInt($(this).closest("tr").find("input[name^=ct_qty]").val());

            tot_price += (price * qty);
        });
    }

    $("#cit_opt_prc span").text(number_format(String(tot_price))+"원");
}

// 바로구매, 장바구니
function fitem_submit(f)
{
    if (document.pressed == "장바구니") {
        f.sw_direct.value = 0;
    } else { // 바로구매
        f.sw_direct.value = 1;
    }

    var $el_chk = $("input[name^=io_chk]:checked");

    if($el_chk.size() < 1) {
        alert("상품의 옵션을 하나이상 선택해 주십시오.");
        return false;
    }

    // 수량체크
    var is_qty = true;
    var ct_qty = 0;
    $el_chk.each(function() {
        ct_qty = parseInt($(this).closest("tr").find("input[name^=ct_qty]").val().replace(/[^0-9]/g, ""));
        if(isNaN(ct_qty))
            ct_qty = 0;

        if(ct_qty < 1) {
            is_qty = false;
            return false;
        }
    });

    if(!is_qty) {
        alert("수량을 1이상 입력해 주십시오.");
        return false;
    }

    return true;
}