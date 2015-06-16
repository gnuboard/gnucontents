<?php
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가

if (!defined("_ORDERINQUIRY_")) exit; // 개별 페이지 접근 불가
?>

<!-- 주문 내역 목록 시작 { -->
<?php if (!$limit) { ?>총 <?php echo $cnt; ?> 건<?php } ?>

<div id="od_li">
    <ul>
    <?php
    $sql = " select *
               from {$g5['g5_contents_order_table']}
              where mb_id = '{$member['mb_id']}'
              order by od_id desc
              $limit ";
    $result = sql_query($sql);
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        // 주문상품
        $sql = " select it_name, ct_option
                    from {$g5['g5_contents_cart_table']}
                    where od_id = '{$row['od_id']}'
                    order by ct_id
                    limit 1 ";
        $ct = sql_fetch($sql);
        $ct_name = get_text($ct['it_name']).' '.get_text($ct['ct_option']);

        $sql = " select count(*) as cnt
                    from {$g5['g5_contents_cart_table']}
                    where od_id = '{$row['od_id']}' ";
        $ct2 = sql_fetch($sql);
        if($ct2['cnt'] > 1)
            $ct_name .= ' 외 '.($ct2['cnt'] - 1).'건';

        switch($row['od_status']) {
            case '주문':
                $od_status = '입금확인중';
                break;
            case '입금':
                $od_status = '입금완료';
                break;
            default:
                $od_status = $row['od_status'];
                break;
        }

        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);
    ?>
    <li>
        <div>
            <a href="<?php echo G5_CONTENTS_URL; ?>/orderinquiryview.php?od_id=<?php echo $row['od_id']; ?>" class="odli_num"><?php echo $row['od_id']; ?></a>
            <span class="odli_date"><?php echo substr($row['od_time'],2,14); ?></span>
        </div>
        <div class="odli_tit"><?php echo $ct_name; ?></div>
        <div class="odli_prc"><?php echo cm_display_price($row['od_receipt_price']); ?></div>
        <div class="odli_status"><?php echo $od_status; ?></div>
    </li>

    <?php
    }

    if ($i == 0)
        echo '<li class="empty_list">주문 내역이 없습니다.</li>';
    ?>
    </ul>
</div>
<!-- } 주문 내역 목록 끝 -->