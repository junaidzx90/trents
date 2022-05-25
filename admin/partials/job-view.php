<?php
global $wpdb;
$progress_id = intval($_GET['id']);
$application = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}job_progression WHERE ID = $progress_id");
if(!$application){
    return;
}
$trucks = $this->trent_terms($application->job_id, 'trucks');

$drivereml = get_user_by( 'ID', $application->driver_id )->user_email;
$clienteml = get_user_by( 'ID', $application->client_id )->user_email;

$dataArr = array(
    'job_id'            => $application->job_id,
    'job_title'         => get_the_title( $application->job_id ),
    'trucks'            => $trucks,
    'rent_cost'         => $application->rent_cost,
    'current_vat'       => $application->vat,
    'load_location'       => get_post_meta($application->job_id, 'tr_load_location', true),
    'unload_location'       => get_post_meta($application->job_id, 'tr_unload_location', true),
    'load_time'       => get_post_meta($application->job_id, 'tr_load_datetime', true),
    'goods_type'       => get_post_meta($application->job_id, 'tr_goodstype', true),
    'total'             => number_format(get_total_cost_with_vat($application->vat, $application->rent_cost)),
    'driver_name'            => ucfirst(get_user_by( 'ID', $application->driver_id )->display_name),
    'driver_rank'            => get_user_meta($application->driver_id, 'user_profile_ranks', true),
    'driver_phone'      => '<a href="tel:'.get_user_meta($application->driver_id, 'user_phone', true).'">'.get_user_meta($application->driver_id, 'user_phone', true).'</a>',
    'driver_email'      => '<a href="mailto: '.$drivereml.'">'.$drivereml.'</a>',
    'client_name'            => ucfirst(get_user_by( 'ID', $application->client_id )->display_name),
    'client_rank'            => get_user_meta($application->client_id, 'user_profile_ranks', true),
    'client_phone'      => '<a href="tel:'.get_user_meta($application->client_id, 'user_phone', true).'">'.get_user_meta($application->client_id, 'user_phone', true).'</a>',
    'client_email'      => '<a href="mailto: '.$clienteml.'">'.$clienteml.'</a>',
);

?>
<div id="job-view">
    <h3>Job view</h3>
    <hr>

    <div class="job_contents">
        
        <div class="information-section">
            <h4>Job Information</h4>
            <table>
                <tr>
                    <th>Job title</th>
                    <td><?php echo $dataArr['job_title']; ?></td>
                </tr>
                <tr>
                    <th>Load point</th>
                    <td><?php echo $dataArr['load_location']; ?></td>
                </tr>
                <tr>
                    <th>Unload point</th>
                    <td><?php echo $dataArr['unload_location']; ?></td>
                </tr>
                <tr>
                    <th>Load time</th>
                    <td><?php echo englishToBanglaNumber(date("Y/m/d - h:i A", strtotime($dataArr['load_time']))); ?></td>
                </tr>
                <tr>
                    <th>Type of goods</th>
                    <td><?php echo get_goods_type($dataArr['goods_type']); ?></td>
                </tr>
                <tr>
                    <th>Type of truck</th>
                    <td><?php echo $dataArr['trucks']; ?></td>
                </tr>
                <tr>
                    <th>Vat</th>
                    <td><?php echo $dataArr['current_vat']; ?>%</td>
                </tr>
                <tr>
                    <th>Hired</th>
                    <td><?php echo number_format($dataArr['rent_cost']).' + '.$dataArr['current_vat'].'% = '.$dataArr['total']; ?>tk</td>
                </tr>
            </table>
        </div>

        <div class="information-section">
            <h4>Client Information</h4>
            <table>
                <tr>
                    <th>Name</th>
                    <td><?php echo $dataArr['client_name']; ?></td>
                </tr>
                <tr>
                    <th>Profile status</th>
                    <td><?php echo (($dataArr['client_rank']) ? $dataArr['client_rank'].'%' : '100%'); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $dataArr['client_email']; ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo $dataArr['client_phone']; ?></td>
                </tr>
            </table>
        </div>

        <div class="information-section">
            <h4>Driver Information</h4>
            <table>
                <tr>
                    <th>Name</th>
                    <td><?php echo $dataArr['driver_name']; ?></td>
                </tr>
                <tr>
                    <th>Profile status</th>
                    <td><?php echo (($dataArr['driver_rank']) ? $dataArr['driver_rank'] : '100%'); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo $dataArr['driver_email']; ?></td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td><?php echo $dataArr['driver_phone']; ?></td>
                </tr>
            </table>
        </div>

        <div class="information-section">
            <h4>Payment</h4>
            <?php
            $payment = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}payment_history WHERE progression_id = $progress_id");
            
            if($payment){
                $pstatus = (($payment) ? $payment->payment_status : '');
                $pdate = (($payment) ? $payment->created : '');
                $transiction = (($payment) ? $payment->transiction : '');
    
                $vat = intval($dataArr['current_vat']);
                $rent_cost = floatval($dataArr['rent_cost']);
    
                $total_cost = floatval(get_total_cost_with_vat($vat, $rent_cost));
                $due = $total_cost - $rent_cost;
                ?>
                <table>
                    <tr>
                        <th>Payment Status</th>
                        <td><?php echo get_payment_status($pstatus); ?></td>
                    </tr>
                    <tr>
                        <th>Transaction ID</th>
                        <td><?php echo $transiction ?></td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td><?php echo $due; ?>tk</td>
                    </tr>
                    <tr>
                        <th>Payment</th>
                        <td><?php echo  date("F j, Y, g:i a", strtotime($payment->created)) ?></td>
                    </tr>
                    <?php
                    if($payment->payment_status !== 'paid'){
                        ?>
                        <tr>
                            <th>Action</th>
                            <td>
                                <button data-id="<?php echo $payment->ID ?>" id="payment_paid" class="button-secondary">Paid</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    
                </table>
                <?php
            }else{
                echo "Not paid!";
            } ?>
        </div>

    </div>
</div>