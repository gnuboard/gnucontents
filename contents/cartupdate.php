<?php
include_once('./_common.php');

//print_r2($_POST); exit;

if($is_guest)
    alert('회원 로그인 후 이용해 주십시오.');

// cart id 설정
cm_set_cart_id($sw_direct);

if($sw_direct)
    $tmp_cart_id = get_session('ss_cm_cart_direct');
else
    $tmp_cart_id = get_session('ss_cm_cart_id');

// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$tmp_cart_id)
{
    alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
}

if($act == "buy")
{
    if(!count($_POST['ct_chk']))
        alert("주문하실 상품을 하나이상 선택해 주십시오.");

    $fldcnt = count($_POST['it_id']);
    for($i=0; $i<$fldcnt; $i++) {
        $ct_chk = $_POST['ct_chk'][$i];
        if($ct_chk) {
            $it_id = $_POST['it_id'][$i];
            $sql = " update {$g5['g5_contents_cart_table']}
                        set ct_select = '1'
                        where it_id = '$it_id' and od_id = '$tmp_cart_id' ";
            sql_query($sql);
        }
    }

    goto_url(G5_CONTENTS_URL.'/orderform.php');
}
else if ($act == "alldelete") // 모두 삭제이면
{
    $sql = " delete from {$g5['g5_contents_cart_table']}
              where od_id = '$tmp_cart_id' ";
    sql_query($sql);
}
else if ($act == "seldelete") // 선택삭제
{
    if(!count($_POST['ct_chk']))
        alert("삭제하실 상품을 하나이상 선택해 주십시오.");

    $fldcnt = count($_POST['it_id']);
    for($i=0; $i<$fldcnt; $i++) {
        $ct_chk = $_POST['ct_chk'][$i];
        if($ct_chk) {
            $it_id = $_POST['it_id'][$i];
            $sql = " delete from {$g5['g5_contents_cart_table']} where it_id = '$it_id' and od_id = '$tmp_cart_id' ";
            sql_query($sql);
        }
    }
}
else // 장바구니에 담기
{
    $it_id     = $_POST['it_id'];
    $chk_count = count($_POST['io_chk']);

    if(!$chk_count)
        alert('상품의 옵션을 하나이상 선택해 주십시오.');

    // 상품정보
    $sql = " select * from {$g5['g5_contents_item_table']} where it_id = '$it_id' ";
    $it = sql_fetch($sql);
    if(!$it['it_id'])
        alert('상품정보가 존재하지 않습니다.');

    for($i=0; $i<$chk_count; $i++) {
        $k = $_POST['io_chk'][$i];

        if ($_POST['ct_qty'][$k] < 1)
            alert('수량은 1 이상 입력해 주십시오.');
    }

    // 바로구매에 있던 장바구니 자료를 지운다.
    if($sw_direct)
        sql_query(" delete from {$g5['g5_contents_cart_table']} where od_id = '$tmp_cart_id' and ct_direct = 1 ", false);

    // 옵션수정일 때 기존 장바구니 자료를 먼저 삭제
    if($act == 'optionmod')
        sql_query(" delete from {$g5['g5_contents_cart_table']} where od_id = '$tmp_cart_id' and it_id = '$it_id' ");

    // 장바구니에 Insert
    // 바로구매일 경우 장바구니가 체크된것으로 강제 설정
    if($sw_direct)
        $ct_select = 1;
    else
        $ct_select = 0;

    // 옵션정보를 배열에 저장
    $opt_count = 0;
    $opt_list = array();
    $sql = " select * from {$g5['g5_contents_item_option_table']} where it_id = '$it_id' order by io_no asc ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $opt_list[$row['io_id']]['id']    = $row['io_id'];
        $opt_list[$row['io_id']]['price'] = $row['io_price'];
        $opt_list[$row['io_id']]['use']   = $row['io_use'];
        $opt_list[$row['io_id']]['name']  = $row['io_name'];
    }

    // 장바구니에 Insert
    $ct_count = 0;
    $comma = '';
    $sql = " INSERT INTO {$g5['g5_contents_cart_table']}
                    ( od_id, mb_id, it_id, it_name, ct_status, ct_price, ct_point, ct_point_use, ct_option, ct_qty, io_id, io_price, ct_time, ct_ip, ct_direct, ct_select )
                VALUES ";

    for($i=0; $i<$chk_count; $i++) {
        $k = $_POST['io_chk'][$i];

        $io_id = $_POST['io_id'][$k];

        // 선택옵션정보가 존재하는데 선택된 옵션이 없으면 건너뜀
        if($io_id == '')
            continue;

        // 구매할 수 없는 옵션은 건너뜀
        if(!$opt_list[$io_id]['use'])
            continue;

        $io_price = $opt_list[$io_id]['price'];
        $ct_qty   = $_POST['ct_qty'][$k];

        // 구매가격이 음수인지 체크
        if((int)$it['it_price'] + (int)$io_price < 0)
            alert('구매금액이 음수인 상품은 구매할 수 없습니다.');

        // 동일옵션의 상품이 있으면 수량 더함
        $sql2 = " select ct_id
                    from {$g5['g5_contents_cart_table']}
                    where od_id = '$tmp_cart_id'
                      and it_id = '$it_id'
                      and io_id = '$io_id'
                      and ct_status = '쇼핑' ";
        $row2 = sql_fetch($sql2);
        if($row2['ct_id']) {
            $sql3 = " update {$g5['g5_contents_cart_table']}
                        set ct_qty = ct_qty + '$ct_qty'
                        where ct_id = '{$row2['ct_id']}' ";
            sql_query($sql3);
            continue;
        }

        // 포인트
        $point = 0;
        if($config['cf_use_point']) {
            $point = cm_get_item_point($it, $io_id);

            if($point < 0)
                $point = 0;
        }

        // 선택옵션
        $ct_option = addslashes($opt_list[$io_id]['name']);

        $sql .= $comma."( '$tmp_cart_id', '{$member['mb_id']}', '{$it['it_id']}', '".addslashes($it['it_name'])."', '쇼핑', '{$it['it_price']}', '$point', '0', '$ct_option', '$ct_qty', '$io_id', '$io_price', '".G5_TIME_YMDHIS."', '$REMOTE_ADDR', '$sw_direct', '$ct_select' )";
        $comma = ' , ';
        $ct_count++;
    }

    if($ct_count > 0)
        sql_query($sql);
}

// 바로 구매일 경우
if ($sw_direct)
{
    goto_url(G5_CONTENTS_URL."/orderform.php?sw_direct=$sw_direct");
}
else
{
    goto_url(G5_CONTENTS_URL.'/cart.php');
}
?>
