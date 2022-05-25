<?php
    global $wpdb;
    $data = array();
    $applications = null;

    if(current_user_can( 'driver' ) || current_user_can( 'partner' )){ 
        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'complete' AND driver_id = $author_id");
    }
    if(current_user_can( 'client' )){
        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'complete' AND client_id = $author_id");
    }

    if($applications){
        foreach($applications as $application){

            $payment = $wpdb->get_var("SELECT payment_status FROM {$wpdb->prefix}payment_history WHERE progression_id = {$application->ID}");

            $trucks = $this->trent_terms($application->job_id, 'trucks');
           
            $dataArr = array(
                'ID'                => $application->ID,
                'job_id'            => $application->job_id,
                'job_title'         => get_the_title( $application->job_id ),
                'application_id'    => strtolower(base64_encode($application->ID)),
                'current_vat'       => $application->vat,
                'trucks'            => $trucks,
                'payment'           => $payment,
                'total'             => number_format(get_total_cost_with_vat($application->vat, $application->rent_cost)),
                'submitted'         =>  $application->created
            );

            if(current_user_can( 'driver' ) || current_user_can( 'partner' )){ 
                $dataArr['client_name'] = ucfirst(get_user_by( 'ID', $application->client_id )->display_name);
                $dataArr['client_phone'] = get_user_meta($application->client_id, 'user_phone', true);
            }
            if(current_user_can( 'client' )){
                $dataArr['driver_name']  = ucfirst(get_user_by( 'ID', $application->driver_id )->display_name);
                $dataArr['driver_phone'] = get_user_meta($application->driver_id, 'user_phone', true);
            }
            
            $data[] = $dataArr;

        }
    }
?>
<p class="history__counts">Total (<?php echo sizeof($data) ?>)</p>
<ul class="trip__items">
    <?php
    if(sizeof($data) > 0){
        foreach($data as $application){
            ?>
            <li class="trip__item">
                <div class="trip__title toggle">
                    <div>
                        <?php
                        if($application['payment'] === 'paid'){
                            echo '<span style="color: #0170b9" class="paymentStatusIcon"><i title="পেমেন্ট পরিশোধিত" class="fas fa-check"></i></span>';
                        }else{
                            echo '<span style="color: #eb6363" class="paymentStatusIcon"><i title="পেমেন্ট অপরিশোধিত" class="fas fa-exclamation-triangle"></i></span>';
                        }
                        ?>
                        
                        <strong><?php echo $application['job_title'] ?></strong>
                    </div>
                    <i class="fas fa-caret-down"></i>
                </div>

                <table class="history_table trnone">
                    <tbody>
                        <tr>
                            <th>ট্রিপ আইডি</th>
                            <td style="user-select: all">#<?php echo $application['application_id'] ?></td>
                        </tr>
                        <tr>
                            <th>লোডের স্থান</th>
                            <td><?php echo __(get_post_meta($application['job_id'], 'tr_load_location', true), 'trents') ?></td>
                        </tr>
                        <tr>
                            <th>আনলোডের স্থান</th>
                            <td><?php echo __(get_post_meta($application['job_id'], 'tr_unload_location', true), 'trents') ?></td>
                        </tr>
                        <tr>
                            <th>লোডের সময়</th>
                            <td><?php echo __(englishToBanglaNumber(date("Y/m/d - h:i A", strtotime(get_post_meta($application['job_id'], 'tr_load_datetime', true)))), 'trents') ?></td>
                        </tr>
                        <tr>
                            <th>মালের ধরণ</th>
                            <td><?php echo __(get_goods_type(get_post_meta($application['job_id'], 'tr_goodstype', true)), 'trents') ?></td>
                        </tr>
                        <tr>
                            <th>ট্রাকের ধরণ</th>
                            <td><?php echo $application['trucks'] ?></td>
                        </tr>
                        <tr>
                            <th>মোট ভাড়া</th>
                            <td><?php echo $application['total'] ?> টাকা</td>
                        </tr>

                        <?php if(current_user_can( 'driver' ) || current_user_can( 'partner' )){ ?>
                            <tr>
                                <th>ভ্যাট</th>
                                <td><?php echo $application['current_vat'] ?>%</td>
                            </tr>
                            <tr>
                                <th>পেমেন্ট</th>
                                <td><?php echo (($application['payment']) ? get_payment_status($payment) : '<a href="?page=payments&action=payment&id='.$application['ID'].'">Make payment</a>') ?></td>
                            </tr>
                            <tr>
                                <th>ক্লাইন্ট নাম</th>
                                <td><?php echo $application['client_name'] ?></td>
                            </tr>
                            <tr>
                                <th>ক্লাইন্ট নাম্বার</th>
                                <td><a href="tel:<?php echo $application['client_phone'] ?>"><?php echo (($application['client_phone']) ? $application['client_phone'] : '<span class="nunval">NuN</span>') ?></a></td>
                            </tr>
                        <?php } ?>

                        <?php if(current_user_can( 'client' )){ ?>
                            <tr>
                                <th>ড্রাইভার নাম</th>
                                <td><?php echo $application['driver_name'] ?></td>
                            </tr>
                            <tr>
                                <th>ড্রাইভার নাম্বার</th>
                                <td><a href="tel:<?php echo $application['driver_phone'] ?>"><?php echo (($application['driver_phone']) ? $application['driver_phone'] : '<span class="nunval">NuN</span>') ?></a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </li>
            <?php
        }
    }
    ?>
</ul>