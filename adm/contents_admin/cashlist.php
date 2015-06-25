<?php
$sub_menu = '600250';
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$token = get_token();

$where = array();

$sql_search = "";
if ($stx != "") {
    if ($sfl != "") {
        $where[] = " $sfl like '%$stx%' ";
    }

    if ($save_stx != $stx) {
        $page = 1;
    }
}

if ($cs_status) {
    $where[] = " cs_status = '$cs_status' ";

    switch ($cs_status) {
        case '입금' :   // 입금완료
            $sort1 = "cs_receipt_time";
            $sort2 = "desc";
            break;
        default:
            $sort1 = "cs_id";
            $sort2 = "desc";
            break;
    }
}

if ($cs_settle_case) {
    $where[] = " cs_settle_case = '$cs_settle_case' ";
}

if ($cs_misu) {
    $where[] = " cs_misu != 0 ";
}

if ($cs_refund_price) {
    $where[] = " cs_refund_price != 0 ";
}

if ($fr_date && $to_date) {
    $where[] = " cs_time between '$fr_date 00:00:00' and '$to_date 23:59:59' ";
}

if ($where) {
    $sql_search = ' where '.implode(' and ', $where);
}

if ($sfl == "")  $sfl = "mb_id";
if ($sort1 == "") $sort1 = "cs_id";
if ($sort2 == "") $sort2 = "desc";

$sql_common = " from {$g5['g5_contents_cash_table']} $sql_search ";

$sql = " select count(cs_id) as cnt " . $sql_common;
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = $config['cf_page_rows'];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) { $page = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$sql  = " select *
           $sql_common
           order by $sort1 $sort2
           limit $from_record, $rows ";
$result = sql_query($sql);

$qstr1 = "cs_status=".urlencode($cs_status)."&amp;cs_settle_case=".urlencode($cs_settle_case)."&amp;cs_misu=$cs_misu&amp;cs_refund_price=$cs_refund_price&amp;fr_date=$fr_date&amp;to_date=$to_date&amp;sfl=$sfl&amp;stx=$stx&amp;save_stx=$stx";
$qstr = "$qstr1&amp;sort1=$sort1&amp;sort2=$sort2&amp;page=$page";

$g5['title'] = '캐시충전내역';
include_once (G5_ADMIN_PATH.'/admin.head.php');

$colspan = 11;
?>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
<span>
    전체 <?php echo number_format($total_count) ?> 개
</span>
<select name="sfl" title="검색대상">
    <option value="mb_id"<?php echo get_selected($_GET['sfl'], "mb_id"); ?>>회원아이디</option>
    <option value="cs_name"<?php echo get_selected($_GET['sfl'], "cs_name"); ?>>이름</option>
    <option value="cs_id"<?php echo get_selected($_GET['sfl'], "cs_id"); ?>>주문번호</option>
</select>
<label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
<input type="text" name="stx" value="<?php echo $stx ?>" id="stx" required class="required frm_input">
<input type="submit" class="btn_submit" value="검색">
</form>

<form class="local_sch02 local_sch">
<div>
    <strong>주문상태</strong>
    <input type="radio" name="cs_status" value="" id="cs_status_all"    <?php echo get_checked($cs_status, '');     ?>>
    <label for="cs_status_all">전체</label>
    <input type="radio" name="cs_status" value="접수" id="cs_status_odr" <?php echo get_checked($cs_status, '접수'); ?>>
    <label for="cs_status_odr">접수</label>
    <input type="radio" name="cs_status" value="입금" id="cs_status_cpl" <?php echo get_checked($cs_status, '입금'); ?>>
    <label for="cs_status_cpl">입금</label>
    <input type="radio" name="cs_status" value="취소" id="cs_status_ccl" <?php echo get_checked($cs_status, '취소'); ?>>
    <label for="cs_status_ccl">취소</label>
</div>

<div>
    <strong>결제수단</strong>
    <input type="radio" name="cs_settle_case" value="" id="cs_settle_case01"        <?php echo get_checked($cs_settle_case, '');          ?>>
    <label for="cs_settle_case01">전체</label>
    <input type="radio" name="cs_settle_case" value="무통장" id="cs_settle_case02"   <?php echo get_checked($cs_settle_case, '무통장');    ?>>
    <label for="cs_settle_case02">무통장</label>
    <input type="radio" name="cs_settle_case" value="가상계좌" id="cs_settle_case03" <?php echo get_checked($cs_settle_case, '가상계좌');  ?>>
    <label for="cs_settle_case03">가상계좌</label>
    <input type="radio" name="cs_settle_case" value="계좌이체" id="cs_settle_case04" <?php echo get_checked($cs_settle_case, '계좌이체');  ?>>
    <label for="cs_settle_case04">계좌이체</label>
    <input type="radio" name="cs_settle_case" value="휴대폰" id="cs_settle_case05"   <?php echo get_checked($cs_settle_case, '휴대폰');    ?>>
    <label for="cs_settle_case05">휴대폰</label>
    <input type="radio" name="cs_settle_case" value="신용카드" id="cs_settle_case06" <?php echo get_checked($cs_settle_case, '신용카드');  ?>>
    <label for="cs_settle_case06">신용카드</label>
</div>

<div>
    <strong>기타선택</strong>
    <input type="checkbox" name="cs_misu" value="Y" id="cs_misu01" <?php echo get_checked($cs_misu, 'Y'); ?>>
    <label for="cs_misu01">미수금</label>
    <input type="checkbox" name="cs_refund_price" value="Y" id="cs_misu03" <?php echo get_checked($cs_refund_price, 'Y'); ?>>
    <label for="od_misu03">환불</label>
</div>

<div class="sch_last">
    <strong>주문일자</strong>
    <input type="text" id="fr_date"  name="fr_date" value="<?php echo $fr_date; ?>" class="frm_input" size="10" maxlength="10"> ~
    <input type="text" id="to_date"  name="to_date" value="<?php echo $to_date; ?>" class="frm_input" size="10" maxlength="10">
    <button type="button" onclick="javascript:set_date('오늘');">오늘</button>
    <button type="button" onclick="javascript:set_date('어제');">어제</button>
    <button type="button" onclick="javascript:set_date('이번주');">이번주</button>
    <button type="button" onclick="javascript:set_date('이번달');">이번달</button>
    <button type="button" onclick="javascript:set_date('지난주');">지난주</button>
    <button type="button" onclick="javascript:set_date('지난달');">지난달</button>
    <button type="button" onclick="javascript:set_date('전체');">전체</button>
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<form name="fcashlist" id="fcashlist" method="post" action="./cashlist_update.php" onsubmit="return fcashlist_submit(this);">
<input type="hidden" name="sort1" value="<?php echo $sort1; ?>">
<input type="hidden" name="sort2" value="<?php echo $sort2; ?>">
<input type="hidden" name="sfl" value="<?php echo $sfl; ?>">
<input type="hidden" name="stx" value="<?php echo $stx; ?>">
<input type="hidden" name="page" value="<?php echo $page; ?>">
<input type="hidden" name="csh_status" value="<?php echo $cs_status; ?>">
<input type="hidden" name="csh_settle_case" value="<?php echo $cs_settle_case; ?>">
<input type="hidden" name="csh_misu" value="<?php echo $cs_misu; ?>">
<input type="hidden" name="csh_refund_price" value="<?php echo $cs_refund_price; ?>">
<input type="hidden" name="csh_fr_date" value="<?php echo $fr_date; ?>">
<input type="hidden" name="csh_to_date" value="<?php echo $to_date; ?>">

<div class="tbl_head01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <thead>
    <tr>
        <th scope="col" rowspan="2">
            <label for="chkall" class="sound_only">캐시내역 전체</label>
            <input type="checkbox" name="chkall" value="1" id="chkall">
        </th>
        <th scope="col" rowspan="2">주문번호</th>
        <th scope="col" rowspan="2">이름</th>
        <th scope="col">휴대폰</th>
        <th scope="col" rowspan="2">충전금액</th>
        <th scope="col" rowspan="2">결제금액</th>
        <th scope="col" rowspan="2">미수금액</th>
        <th scope="col" rowspan="2">환불금액</th>
        <th scope="col" rowspan="2">결제방법</th>
        <th scope="col">결제일시</th>
        <th scope="col" rowspan="2">보기</th>
    </tr>
    <tr>
        <th scope="col">이메일</th>
        <th scope="col">주문일시</th>
    </tr>
    </thead>
    <tbody>
    <?php
    for ($i=0; $row=sql_fetch_array($result); $i++) {
        // 주문 번호에 device 표시
        $cs_mobile = '';
        if($row['cs_mobile'])
            $cs_mobile = '(M)';

        // 주문번호에 - 추가
        switch(strlen($row['cs_id'])) {
            case 16:
                $disp_cs_id = substr($row['cs_id'],0,8).'-'.substr($row['cs_id'],8);
                break;
            default:
                $disp_cs_id = substr($row['cs_id'],0,6).'-'.substr($row['cs_id'],6);
                break;
        }

        // 결제방법
        $settle_case = $row['cs_settle_case'];
        if($settle_case == '무통장') {
            $settle_case = $row['cs_deposit_name'].'<br>'.$row['cs_bank_account'];
        }

        $disabled = '';
        if($row['cs_misu'] == 0 && $row['cs_receipt_price'] > 0)
            $disabled = ' disabled="disabled"';

        $bg = 'bg'.($i%2);
        $td_color = 0;
        if($row['cs_refund_price'] > 0 || $row['cs_status'] == '취소') {
            $bg .= 'cancel';
            $td_color = 1;
        }
    ?>

    <tr class="<?php echo $bg; ?>">
        <td class="td_chk" rowspan="2">
            <input type="hidden" id="cs_id_<?php echo $i; ?>" name="cs_id[<?php echo $i; ?>]" value="<?php echo $row['cs_id']; ?>">
            <input type="checkbox" id="chk_<?php echo $i; ?>" name="chk[]" value="<?php echo $i; ?>" title="내역선택"<?php echo $disabled; ?>>
        </td>
        <td rowspan="2"><?php echo $disp_cs_id; ?><?php echo $cs_mobile; ?></td>
        <td rowspan="2"><?php echo get_text($row['cs_name']); ?></td>
        <td><?php echo $row['cs_hp']; ?></td>
        <td rowspan="2" class="td_num"><?php echo number_format($row['cs_cash_price']); ?></td>
        <td rowspan="2" class="td_num"><?php echo number_format($row['cs_receipt_price']); ?></td>
        <td rowspan="2" class="td_num"><?php echo number_format($row['cs_misu']); ?></td>
        <td rowspan="2" class="td_numcancel<?php echo $td_color; ?> td_num"><?php echo number_format($row['cs_refund_price']); ?></td>
        <td rowspan="2" ><?php echo $settle_case; ?></td>
        <td><?php echo (cm_is_null_time($row['cs_receipt_time']) ? '' : substr($row['cs_receipt_time'], 2)); ?></td>
        <td rowspan="2" class="td_mngsmall">
            <a href="./cashform.php?cs_id=<?php echo $row['cs_id']; ?>&amp;<?php echo $qstr; ?>" class="mng_mod"><span class="sound_only"><?php echo $row['cs_id']; ?> </span>보기</a>
        </td>
    </tr>
    <tr class="<?php echo $bg; ?>">
        <td><?php echo get_text($row['cs_email']); ?></td>
        <td><?php echo substr($row['cs_time'], 2); ?></td>
    </tr>

    <?php
    }

    if ($i == 0)
        echo '<tr><td colspan="'.$colspan.'" class="empty_table">자료가 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>

<div class="btn_list01 btn_list">
    <input type="submit" name="act_button" value="선택입금" onclick="document.pressed=this.value">
    <input type="submit" name="act_button" value="선택삭제" onclick="document.pressed=this.value">
</div>

</form>

<?php echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>

<script>
$(function() {
    $("#chkall").on("click", function() {
        var $chk = $("input[name='chk[]']").not(":disabled");

        if($(this).is(":checked")) {
            $chk.attr("checked", true);
        } else {
            $chk.attr("checked", false);
        }
    });
});

function fcashlist_submit(f)
{
    if (!is_checked("chk[]")) {
        alert(document.pressed+" 하실 항목을 하나 이상 선택하세요.");
        return false;
    }

    if(document.pressed == "선택삭제") {
        if(!confirm("선택한 자료를 정말 삭제하시겠습니까?")) {
            return false;
        }
    }

    return true;
}

function set_date(today)
{
    <?php
    $date_term = date('w', G5_SERVER_TIME);
    $week_term = $date_term + 7;
    $last_term = strtotime(date('Y-m-01', G5_SERVER_TIME));
    ?>
    if (today == "오늘") {
        document.getElementById("fr_date").value = "<?php echo G5_TIME_YMD; ?>";
        document.getElementById("to_date").value = "<?php echo G5_TIME_YMD; ?>";
    } else if (today == "어제") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME - 86400); ?>";
    } else if (today == "이번주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$date_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "이번달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', G5_SERVER_TIME); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', G5_SERVER_TIME); ?>";
    } else if (today == "지난주") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-d', strtotime('-'.$week_term.' days', G5_SERVER_TIME)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-d', strtotime('-'.($week_term - 6).' days', G5_SERVER_TIME)); ?>";
    } else if (today == "지난달") {
        document.getElementById("fr_date").value = "<?php echo date('Y-m-01', strtotime('-1 Month', $last_term)); ?>";
        document.getElementById("to_date").value = "<?php echo date('Y-m-t', strtotime('-1 Month', $last_term)); ?>";
    } else if (today == "전체") {
        document.getElementById("fr_date").value = "";
        document.getElementById("to_date").value = "";
    }
}
</script>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>