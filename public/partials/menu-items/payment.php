<?php
global $wpdb;

$currId = get_current_user_id(  );
$myprogressions = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE driver_id = $currId AND `status` = 'complete'");

if(isset($_POST['submit_payment'])){
    if(isset($_POST['payjob']) && isset($_POST['transiction_number'])){
        $tripid = intval($_POST['payjob']);
        $transiction = sanitize_text_field($_POST['transiction_number']);

        $trip = $wpdb->get_row("SELECT rent_cost, vat FROM {$wpdb->prefix}job_progression WHERE ID = $tripid");

        if($trip){
            $vat = $trip->vat;
            $rent_cost = floatval($trip->rent_cost);

            $total_cost = floatval(get_total_cost_with_vat($vat, $rent_cost));
            $due = $total_cost - $rent_cost;

            if($due>0){
                $wpdb->insert($wpdb->prefix.'payment_history', array(
                    'progression_id' => $tripid,
                    'payment_status' => 'pending_payment',
                    'transiction' => $transiction,
                    'amount' => $due,
                    'created' => date("Y-m-d h:i:s A")
                ));

                $user_login = get_user_by( "ID", get_current_user_id(  ) )->user_login;
                wp_safe_redirect( home_url( "author/$user_login?page=payments" ) );
                exit;
            }
        }
    }
}
?>
<a href="?page=payments&action=payment" class="makepayment">Make a payment</a>
    <div id="payments_section">
        <table id="payment_table" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Payments</th>
                </tr>
            </thead>
            <tbody>
                <?php
               
                if($myprogressions){
                    foreach($myprogressions as $progress){
                        $payment = $wpdb->get_row("SELECT payment_status, created FROM {$wpdb->prefix}payment_history WHERE progression_id = {$progress->ID}");

                        $pstatus = (($payment) ? $payment->payment_status : '');
                        $pdate = (($payment) ? $payment->created : '');

                        $vat = $progress->vat;
                        $rent_cost = floatval($progress->rent_cost);
            
                        $total_cost = floatval(get_total_cost_with_vat($vat, $rent_cost));
                        $due = $total_cost - $rent_cost;
                        ?>
                        <tr>
                            <td><?php echo strtolower(base64_encode($progress->ID)) ?></td>
                            <td><?php echo $due ?> টাকা</td>
                            <td><?php echo get_payment_status($pstatus) ?></td>
                            <td><?php echo (($pdate) ? date("F j, Y g:i a", strtotime($pdate)) : '--+--') ?></td>
                        </tr>
                        <?php
                    }
                }   
                ?>
            </tbody>
        </table>
    </div>
<?php
if(isset($_GET['page']) && $_GET['page'] === "payments" && isset($_GET['action']) && $_GET['action'] === 'payment'){
    $progress_id = '';
    $due = '';
    if(isset($_GET['id']) && !empty($_GET['id'])){
        $progress_id = intval($_GET['id']);
        
        $trip = $wpdb->get_row("SELECT rent_cost, vat FROM {$wpdb->prefix}job_progression WHERE ID = $progress_id");

        if($trip){
            $vat = $trip->vat;
            $rent_cost = floatval($trip->rent_cost);

            $total_cost = floatval(get_total_cost_with_vat($vat, $rent_cost));
            $due = $total_cost - $rent_cost;
        }
    }

    if(!get_option('company_bkash')){
        return;
    }
    ?>
    <div :class="isDisabled ? 'isDisabled' : ''" id="payment_form">
        <form action="" method="post">
            <a href="?page=payments" class="close_pay_form">+</a>
            <h3>Payment with bkash</h3>

            <div class="payment-contents">

                <?php 
                if(empty($progress_id)){
                    ?>
                    <div class="job-box">
                        <h4>বকেয়া ট্রিপ সিলেক্ট করুন</h4>
                        <select required @change="paymentTripChange()" v-model="paymentTrip" name="payjob" id="pay-job">
                            <option value="">Select</option>
                            <?php
                            if($myprogressions){
                                foreach($myprogressions as $prog){
                                    $payment_status = $wpdb->get_var("SELECT payment_status FROM {$wpdb->prefix}payment_history WHERE progression_id = {$prog->ID}");

                                    if(!$payment_status){
                                        echo '<option value="'.$prog->ID.'">'.get_the_title( $prog->job_id ).'</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                }else{
                    ?>
                    <input type="hidden" id="pay-job" name="payjob" value="<?php echo $progress_id ?>">
                    <?php
                }
                ?>
                
                <div class="unpaid_amount">
                    <h1>বাকি <?php echo (($due > 0) ? $due : '{{currentDue}}') ?> tk</h1>
                </div>

                <p class="moneysendguide">বকেয়া টাকা সেন্ড মানি করার পর আমাদেরকে ট্রানজেকশন নাম্বারটি পাঠান</p>

                <div class="personal_bkash">
                    <h4><?php echo get_option('company_bkash') ?> <sup>Personal</sup></h4>
                </div>

                <?php
                if(get_option('company_bkash_qr_code')){
                    ?>
                    <div class="devider">
                        <p>OR</p>
                    </div>

                    <div class="bkash_qr">
                        <img src="<?php echo get_option('company_bkash_qr_code') ?>" alt="">
                    </div>
                    <?php
                }
                ?>

                <div class="transiction_number">
                    <input required placeholder="9d67rgr6nr" type="text" name="transiction_number" id="transiction_number">
                    <small>ট্রানজেকশন নাম্বারের সাথে বকেয়া টাকার মিল না থাকলে আবেদনটি বাতিল হতে পারে।</small>
                </div>

                <button name="submit_payment" id="submit_payment">সাবমিট করুন</button>
            </div>
        </form>
    </div>
    <?php
}