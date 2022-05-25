<div id="progress--jobs">
<?php
    global $wpdb;
    if(current_user_can( 'client' )){
        $data = array();

        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'active' OR status = 'pending_for_finish' AND client_id = $author_id ORDER BY job_id ASC");

        if($applications){
            foreach($applications as $application){
                $trucks = $this->trent_terms($application->job_id, 'trucks');
                $dataArr = array(
                    'ID'                => $application->ID,
                    'job_id'            => $application->job_id,
                    'job_title'         => get_the_title( $application->job_id ),
                    'application'       => $application->application,
                    'driver_name'       => ucfirst(get_user_by( 'ID', $application->driver_id )->display_name),
                    'driver_id'         => $application->driver_id,
                    'client_id'         => $application->client_id,
                    'driver_phone'      => get_user_meta($application->driver_id, 'user_phone', true),
                    'application_id'    => strtolower(base64_encode($application->ID)),
                    'trucks'            => $trucks,
                    'rent_cost'         => number_format($application->rent_cost),
                    'current_vat'       => $application->vat,
                    'status'            => $application->status,
                    'total'             => number_format(get_total_cost_with_vat($application->vat, $application->rent_cost)),
                    'deal_date'         =>  $application->deal_date
                );

                $data[] = $dataArr;
            }
        }

        if(sizeof($data) > 0){
            foreach($data as $application){
                $job_author = get_post($application['job_id'])->post_author;

                if($author_id === intval($job_author)){ // Check job author is correct
                ?>
                    <div class="active--job"> <!--Item-->
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
                                        <th class="highlight">ড্রাইভার নাম্বার</th>
                                        <td>
                                            <p class="rent-cost--to">: 
                                                <a class="teliphoneTag" href="tel:<?php echo $application['driver_phone'] ?>"><?php echo (($application['driver_phone']) ? $application['driver_phone'] : '<span class="nunval">NuN</span>') ?></a>
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="my--application"><?php echo __($application['application'], 'trents') ?></p>
                        </div>

                        <div class="job-item-foo">
                            <small class="rent-datetime"><?php echo time_elapsed_string(date("Y/m/d h:i:s", strtotime($application['deal_date']))) ?></small>

                            <?php
                            $curUId = get_current_user_id(  );

                            if($application['status'] === 'active'){
                                $mycancelRequest = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}applications_canceled WHERE application_id = {$application['ID']} AND cancelled_by = $curUId AND cancel_status = 'pending_for_cancel'");

                                if($mycancelRequest){
                                    echo '<span>বাতিলের জন্য অপেক্ষমান</span>';
                                }else{ 
                                    $requestedForCancel = $wpdb->get_var("SELECT reason FROM {$wpdb->prefix}applications_canceled WHERE application_id = {$application['ID']} AND cancelled_by = {$application['driver_id']}");

                                    if($requestedForCancel){
                                        ?>
                                        <span @click="approveCancelletion(<?php echo $application['ID'] ?>, '<?php echo $requestedForCancel ?>')" class="rents-btn running-job-cancel-btn">বাতিলের আবেদন করা হয়েছে</span>
                                        <?php
                                    }else{
                                        ?>
                                        <span class="rents-btn running-job-cancel-btn" @click="openCancelForm(<?php echo $application['ID'] ?>, <?php echo $application['driver_id'] ?>)">বাতিলের জন্য আবেদন</span>
                                        <?php 
                                    }
                                }
                            }

                            // Driver requested for approve delivery
                            if($application['status'] === 'pending_for_finish'){
                                ?>
                                <span class="rents-btn job-finished-btn" @click="current_job_finished(<?php echo $application['job_id'] ?>, <?php echo $application['ID'] ?>, 'client', event)">সম্পন্ন গ্রহণ</span>
                                <?php
                            }
                            ?>
                        </div>
                    </div> <!--/Item-->
                    <?php
                }
            }
        }else{
            print_r("কোনো আবেদন জমা নেই");
        }
    }
    
    if(current_user_can( 'driver' ) || current_user_can( 'partner' )){
        $data = array();

        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'active' OR status = 'pending_for_finish' AND driver_id = $author_id");
        if($applications){
            foreach($applications as $application){
                $trucks = $this->trent_terms($application->job_id, 'trucks');
                $dataArr = array(
                    'ID'                => $application->ID,
                    'job_id'            => $application->job_id,
                    'job_title'         => get_the_title( $application->job_id ),
                    'application'       => $application->application,
                    'client_name'       => ucfirst(get_user_by( 'ID', $application->client_id )->display_name),
                    'client_phone'      => get_user_meta($application->client_id, 'user_phone', true),
                    'client_id'         => $application->client_id,
                    'application_id'    => strtolower(base64_encode($application->ID)),
                    'trucks'            => $trucks,
                    'rent_cost'         => number_format($application->rent_cost),
                    'current_vat'       => $application->vat,
                    'status'            => $application->status,
                    'total'             => number_format(get_total_cost_with_vat($application->vat, $application->rent_cost)),
                    'deal_date'         =>  $application->deal_date
                );
    
                $data[] = $dataArr;
            }
        }
    
        if(sizeof($data) > 0){
            foreach($data as $application){
                ?>
                <div class="active--job"> <!--Item-->
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
                                    <td><p class="location--to">: <?php echo __($application['total'], 'tr_goodstype') ?> টাকা</p></td>
                                </tr>
                                <tr>
                                    <th class="highlight">ক্লাইন্ট নাম</th>
                                    <td><p class="rent-cost--to">: <?php echo __($application['client_name'], 'trents') ?></p></td>
                                </tr>
                                <tr>
                                    <th class="highlight">ক্লাইন্ট নাম্বার</th>
                                    <td>
                                        <p class="rent-cost--to">: 
                                            <a class="teliphoneTag" href="tel:<?php echo $application['client_phone'] ?>"><?php echo (($application['client_phone']) ? $application['client_phone'] : '<span class="nunval">NuN</span>') ?></a>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="my--application"><?php echo __($application['application'], 'trents') ?></p>
                    </div>
    
                    <div class="job-item-foo">
                        <small class="rent-datetime"><?php echo time_elapsed_string(date("Y/m/d h:i:s", strtotime($application['deal_date']))) ?></small>
                        <?php
                        $curUId = get_current_user_id(  );

                        if($application['status'] === 'active'){
                            $mycancelRequest = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}applications_canceled WHERE application_id = {$application['ID']} AND cancelled_by = $curUId  AND cancel_status = 'pending_for_cancel'");

                            if($mycancelRequest){
                                echo '<span>বাতিলের জন্য অপেক্ষমান</span>';
                            }else{ 
                                $requestedForCancel = $wpdb->get_var("SELECT reason FROM {$wpdb->prefix}applications_canceled WHERE application_id = {$application['ID']} AND cancelled_by = {$application['client_id']} AND cancel_status = 'pending_for_cancel'");

                                if($requestedForCancel){
                                    ?>
                                    <span @click="approveCancelletion(<?php echo $application['ID'] ?>, '<?php echo $requestedForCancel ?>')" class="rents-btn running-job-cancel-btn">বাতিলের আবেদন করা হয়েছে</span>
                                    <?php
                                }else{
                                    ?>
                                    <span class="rents-btn job-finished-btn" @click="current_job_finished(<?php echo $application['job_id'] ?>, <?php echo $application['ID'] ?>, 'driver', event)">সম্পন্ন</span>
    
                                    <span class="rents-btn running-job-cancel-btn" @click="openCancelForm(<?php echo $application['ID'] ?>, <?php echo $application['client_id'] ?>)">বাতিলের জন্য আবেদন</span>
                                    <?php 
                                }
                            }
                        }

                        if($application['status'] === 'pending_for_finish'){
                            echo 'সম্পন্নের জন্য অপেক্ষমান';
                        }
                        ?>
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

<div v-if="cancelJob.isCancelJobForm" class="apply_for_cancel">
    <div class="afc_contents">
        
        <div v-if="cancelJob.isApprovalForm" class="clientApprovalBox">
            <span @click="closeCancelForm()" class="closeAFCform"><i class="fas fa-times"></i></span>
            <h3 class="heading3">বাতিলের জন্য আবেদন</h3>
            <div class="reason">
                <p>{{cancelJob.cancelReason}}</p>
            </div>
            <button @click="acceptCancellationRequest(event)" class="submit afcSubmitbtn">আবেদন মনজুর<span class="cancelLoader"></span></button>
        </div>
        
        <div v-if="cancelJob.isApprovalForm === false" class="reasonBox">
            <span @click="closeCancelForm()" class="closeAFCform"><i class="fas fa-times"></i></span>
            <h3 class="heading3">বাতিলের জন্য আবেদন</h3>
            <p>দ্রষ্টব্য: আবেদন সীকৃতি আপনার কাস্টমারের উপর বিদ্যমান।</p>
            <div class="reason">
                <label for="afc_reason">কারণ লিখুন</label>
                <textarea required id="afc_reason"></textarea>
            </div>
            <button @click="request_for_cancel(event)" class="submit afcSubmitbtn">জমা দিন <span class="cancelLoader"></span></button>
        </div>

    </div>
</div>