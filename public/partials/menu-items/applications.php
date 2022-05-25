<div id="progress--jobs">
    <?php
    global $wpdb;
    if(current_user_can( 'client' ) || current_user_can( 'partner' )){
        $data = array();

        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'applied' AND client_id = $author_id ORDER BY job_id ASC");

        if($applications){
            foreach($applications as $application){
                
                $trucks = $this->trent_terms($application->job_id, 'trucks');

                $dataArr = array(
                    'ID'                => $application->ID,
                    'job_id'            => $application->job_id,
                    'job_title'         => get_the_title( $application->job_id ),
                    'application'       => $application->application,
                    'driver_name'       => ucfirst(get_user_by( 'ID', $application->driver_id )->display_name),
                    'cancellation'      => get_user_meta($application->driver_id, 'user_cancellation_rate', true),
                    'application_id'    => strtolower(base64_encode($application->ID)),
                    'trucks'            => $trucks,
                    'rent_cost'         => number_format($application->rent_cost),
                    'current_vat'       => $application->vat,
                    'total'             => number_format(get_total_cost_with_vat($application->vat, $application->rent_cost)),
                    'submitted'         =>  $application->created
                );

                $data[] = $dataArr;
            }
        }

        if(sizeof($data) > 0){
            foreach($data as $application){
                $job_author = get_post($application['job_id'])->post_author;

                if($author_id === intval($job_author)){ // Check job author is correct
                ?>
                    <div class="progress--job"> <!--Item-->
                        <strong class="job--card-title"><?php echo __($application['job_title'], 'trents') ?></strong>
                        
                        <div class="job--info">
                            <table class="job--info-table">
                                <tbody>
                                    <tr>
                                        <th>লোডের স্থান</th>
                                        <td><p class="location--from">: <?php echo __(get_post_meta($application['job_id'], 'tr_load_location', true), 'trents') ?></p></td>
                                    </tr>
                                    <tr>
                                        <th>আনলোডের স্থান</th>
                                        <td><p class="location--to">: <?php echo __(get_post_meta($application['job_id'], 'tr_unload_location', true), 'trents') ?></p></td>
                                    </tr>
                                    <tr>
                                        <th>লোডের সময়</th>
                                        <td>: <?php echo __(englishToBanglaNumber(date("Y/m/d - h:i A", strtotime(get_post_meta($application['job_id'], 'tr_load_datetime', true)))), 'trents') ?></td>
                                    </tr>
                                    <tr>
                                        <th>মালের ধরণ</th>
                                        <td><p class="location--to">: <?php echo __(get_goods_type(get_post_meta($application['job_id'], 'tr_goodstype', true)), 'trents') ?></p></td>
                                    </tr>
                                    <tr>
                                        <th>ট্রাকের ধরণ</th>
                                        <td><p class="truck-type">: <?php echo $application['trucks'] ?></p></td>
                                    </tr>
                                    <tr>
                                        <th>মোট ভাড়া</th>
                                        <td><p class="rent-cost--to">: <?php echo __($application['total'], 'trents') ?> টাকা</p></td>
                                    </tr>
                                    <tr>
                                        <th class="highlight">ড্রাইভার নাম</th>
                                        <td><p class="rent-cost--to">: <?php echo __($application['driver_name'], 'trents') ?></p></td>
                                    </tr>
                                    <tr>
                                        <th class="highlight">প্রোফাইল স্ট্যাটাস</th>
                                        <td><p class="rent-cost--to">: <?php echo (($application['cancellation']) ? $application['cancellation'] : '<span class="verygood">100%</span>') ?></p></td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="my--application"><?php echo __($application['application'], 'trents') ?></p>
                        </div>

                        <div class="job-item-foo">
                            <small class="rent-datetime"><?php echo time_elapsed_string(date("Y/m/d h:i:s", strtotime($application['submitted']))) ?></small>
                            <span class="rents-btn job-accept-btn" @click="approve_application(<?php echo $application['job_id'] ?>, <?php echo $application['ID'] ?>, event)">গ্রহণ</span>
                            <span class="rents-btn job-cancel-btn" @click="cancel_application_request(<?php echo $application['job_id'] ?>, <?php echo $application['ID'] ?>, event)">উপেক্ষা করুন</span>
                        </div>
                    </div> <!--/Item-->
                    <?php
                }
            }
        }else{
            print_r("কোনো আবেদন জমা নেই");
        }
    }
    
    if(current_user_can( 'driver' )){
        $data = array();

        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'applied' AND driver_id = $author_id");
        if($applications){
            foreach($applications as $application){
                $trucks = $this->trent_terms($application->job_id, 'trucks');

                $dataArr = array(
                    'ID'                => $application->ID,
                    'job_id'            => $application->job_id,
                    'job_title'         => get_the_title( $application->job_id ),
                    'application'       => $application->application,
                    'application_id'    => strtolower(base64_encode($application->ID)),
                    'trucks'            => $trucks,
                    'rent_cost'         => number_format($application->rent_cost),
                    'current_vat'       => $application->vat,
                    'total'             => number_format(get_total_cost_with_vat($application->vat, $application->rent_cost)),
                    'submitted'         =>  $application->created
                );
    
                $data[] = $dataArr;
            }
        }
    
        if(sizeof($data) > 0){
            foreach($data as $application){
                ?>
                <div class="progress--job"> <!--Item-->
                    <strong class="job--card-title"><?php echo __($application['job_title'], 'trents') ?>
                        <i class="info-icon fas fa-info-circle">
                            <?php
                            $application_count = $wpdb->query("SELECT * FROM {$wpdb->prefix}job_progression WHERE job_id = {$application['job_id']} AND status = 'applied'");
                            $current_vat = ((get_option('company_vat_cb')) ? get_option('company_vat_cb') : 5);
                            ?>
                            <span class="rents-tooltip">
                                <div><b>আবেদনের সংখ্যা: </b><small><?php echo (($application_count) ? $application_count : 0) ?></small> টি</div>
                                <div><b>বর্তমান ভ্যাট: </b><small><?php echo $current_vat ?>%</small></div>
                                <div><b>মোট ভাড়া: </b><small><?php echo $application['total'] ?></small> টাকা</div>
                            </span>
                        </i>
                    </strong>
                    
                    <div class="job--info">
                        <table class="job--info-table">
                            <tbody>
                                <tr>
                                    <th>লোডের স্থান</th>
                                    <td><p class="location--from">: <?php echo __(get_post_meta($application['job_id'], 'tr_load_location', true), 'trents') ?></p></td>
                                </tr>
                                <tr>
                                    <th>আনলোডের স্থান</th>
                                    <td><p class="location--to">: <?php echo __(get_post_meta($application['job_id'], 'tr_unload_location', true), 'trents') ?></p></td>
                                </tr>
                                <tr>
                                    <th>লোডের সময়</th>
                                    <td>: <?php echo __(englishToBanglaNumber(date("Y/m/d - h:i A", strtotime(get_post_meta($application['job_id'], 'tr_load_datetime', true)))), 'trents') ?></td>
                                </tr>
                                <tr>
                                    <th>মালের ধরণ</th>
                                    <td><p class="location--to">: <?php echo __(get_goods_type(get_post_meta($application['job_id'], 'tr_goodstype', true)), 'trents') ?></p></td>
                                </tr>
                                <tr>
                                    <th>ট্রাকের ধরণ</th>
                                    <td><p class="truck-type">: <?php echo $application['trucks'] ?></p></td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="my--application"><?php echo __($application['application'], 'trents') ?></p>
                    </div>
    
                    <div class="job-item-foo">
                        <small class="rent-datetime"><?php echo time_elapsed_string(date("Y/m/d h:i:s", strtotime($application['submitted']))) ?></small>
                        <span class="rents-btn job-cancel-btn" @click="cancel_application_request(<?php echo $application['job_id'] ?>, <?php echo $application['ID'] ?>, event)">বাতিল করুন</span>
                    </div>
                </div> <!--/Item-->
                <?php
            }
        }else{
            print_r("কোনো ট্রিপ নেই");
        }
    }
    
    ?>
</div>