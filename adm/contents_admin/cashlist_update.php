<?php
$sub_menu = '600250';
include_once('./_common.php');

check_demo();
check_token();

$count = count($_POST['chk']);
if(!$count)
    alert($act_button.'하실 항목을 하나이상 선택해 주세요.');

if($act_button == '선택입금')
{
    auth_check($auth[$sub_menu], 'w');

    for ($i=0; $i<$count; $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $cs_id = $_POST['cs_id'][$k];

        $sql = " select cs_id, mb_id, cs_misu, cs_price, cs_cash_price, cs_status
                    from {$g5['g5_contents_cash_table']}
                    where cs_id = '$cs_id' ";
        $row = sql_fetch($sql);

        if(!$row['cs_id'])
            continue;

        if($row['cs_status'] == '입금')
            continue;

        $cs_receipt_price = $row['cs_price'];

        $sql = " update {$g5['g5_contents_cash_table']}
                    set cs_receipt_price = '$cs_receipt_price',
                        cs_receipt_time  = '".G5_TIME_YMDHIS."',
                        cs_misu          = '0',
                        cs_status        = '입금'
                    where cs_id = '$cs_id' ";
        sql_query($sql);

        $ch_memo = '무통장('.$row['cs_id'].') 충전';
        insert_cash($row['mb_id'], $row['cs_id'], $row['cs_cash_price'], $ch_memo);

        $csh_status = '입금';
    }
}
else if($act_button == '선택삭제')
{
    auth_check($auth[$sub_menu], 'd');

    for ($i=0; $i<$count; $i++)
    {
        // 실제 번호를 넘김
        $k = $_POST['chk'][$i];
        $cs_id = $_POST['cs_id'][$k];

        $sql = " select cs_id, cs_receipt_price, cs_receipt_time
                    from {$g5['g5_contents_cash_table']}
                    where cs_id = '$cs_id' ";
        $row = sql_fetch($sql);

        if(!$row['cs_id'])
            continue;

        if($row['cs_receipt_price'] > 0 || !cm_is_null_time($row['cs_receipt_time']))
            continue;

        $sql = " delete from {$g5['g5_contents_cash_table']} where cs_id = '$cs_id' ";
        sql_query($sql);
    }
}

$qstr  = "sort1=$sort1&amp;sort2=$sort2&amp;sfl=$sfl&amp;stx=$stx";
$qstr .= "&amp;cs_status=".urlencode($csh_status);
$qstr .= "&amp;cs_settle_case=".urlencode($csh_settle_case);
$qstr .= "&amp;cs_misu=$csh_misu";
$qstr .= "&amp;cs_refund_price=$csh_refund_price";
$qstr .= "&amp;fr_date=$csh_fr_date&amp;to_date=$csh_to_date";
$qstr .= "&amp;page=$page";

goto_url('./cashlist.php?'.$qstr);
?>
