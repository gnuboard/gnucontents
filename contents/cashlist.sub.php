<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (!defined("_CASHLIST_")) exit; // 개별 페이지 접근 불가
?>

<?php if (!$limit) { ?>총 <?php echo $cnt; ?> 건<?php } ?>

<div class="tbl_head01 tbl_wrap">
    <table>
    <thead>
    <tr>
        <th scope="col">주문번호</th>
        <th scope="col">충전일시</th>
        <th scope="col">결제방법</th>
        <th scope="col">결제금액</th>
        <th scope="col">충전캐시</th>
        <th scope="col">상태</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $sql = " select * from {$g5['g5_contents_cash_table']} where mb_id = '{$member['mb_id']}' order by cs_id desc $limit ";
    $result = sql_query($sql);

    for($i=0; $row=sql_fetch_array($result); $i++) {
        $cash_price = 0;
        if($row['cs_status'] == '입금')
            $cash_price = $row['cs_cash_price'];

        switch($row['cs_status']) {
            case '접수':
                $cs_status = '입금확인중';
                break;
            case '입금':
                $cs_status = '충전완료';
                break;
            default:
                $cs_status = $row['cs_status'];
                break;
        }
    ?>
    <tr>
        <td><a href="<?php echo G5_CONTENTS_URL; ?>/cashresult.php?cs_id=<?php echo $row['cs_id']; ?>"><?php echo $row['cs_id']; ?></a></td>
        <td><?php echo substr($row['cs_time'], 2); ?></td>
        <td><?php echo $row['cs_settle_case']; ?></td>
        <td class="t_r"><?php echo number_format($row['cs_receipt_price']); ?></td>
        <td class="t_r"><?php echo number_format($cash_price); ?></td>
        <td><?php echo $cs_status; ?></td>
    </tr>
    <?php
    }

    if($i == 0)
        echo '<tr><td colspan="6" class="empty_table">캐시충전 내역이 없습니다.</td></tr>';
    ?>
    </tbody>
    </table>
</div>