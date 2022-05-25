<?php
global $wpdb;
$driver_id = intval($_GET['id']);
if(empty($driver_id) && !is_int($driver_id)){
    return;
}
$user = get_user_by( "ID", $driver_id );

$profileAlerts = get_user_meta( $driver_id, '_driver_profileAlerts', true );

$username = ucfirst($user->display_name);
$useremail = $user->user_email;
$verified = get_user_meta( $driver_id, 'verified_account', true );
$joindate = date("F j, Y", strtotime($user->user_registered));
$userphone = get_user_meta($driver_id, 'user_phone', true );
$userranks = get_user_meta($driver_id, 'user_profile_ranks', true);
if(!$userranks){
    $userranks = 100;
}
$useraddr = get_user_meta($driver_id, 'user_locations', true );

$docs_submitted = get_user_meta($driver_id, 'driver_docs_type', true );
$driver_trucks = get_user_meta($driver_id, '_driver_trucks', true);

if(isset($_POST['update_driver_profile'])){
    if(isset($_POST['profileAlerts'])){
        $profileAlerts = sanitize_text_field( $_POST['profileAlerts'] );
        $profileAlerts = stripcslashes($profileAlerts);
        update_user_meta( $driver_id, '_driver_profileAlerts', $profileAlerts );
    }
    if(isset($_POST['verified_profile'])){
        $verified_profile = $_POST['verified_profile'];
        update_user_meta( $driver_id, 'verified_account', $verified_profile );
    }else{
        delete_user_meta( $driver_id, 'verified_account' );
    }
    if(isset($_POST['drivername'])){
        $drivername = sanitize_text_field( $_POST['drivername'] );
        $drivername = stripcslashes($drivername);
        wp_update_user( array( 'ID' => $driver_id, 'display_name' => $drivername ) );
    }
    if(isset($_POST['driveremail'])){
        $driveremail = sanitize_email( $_POST['driveremail'] );
        $driveremail = stripcslashes($driveremail);
        wp_update_user( array( 'ID' => $driver_id, 'user_email' => $driveremail ) );
    }
    if(isset($_POST['driverphone'])){
        $driverphone = sanitize_text_field( $_POST['driverphone'] );
        $driverphone = stripcslashes($driverphone);
        update_user_meta( $driver_id, 'user_phone', $driverphone );
    }
    if(isset($_POST['driveraddr'])){
        $driveraddr = sanitize_text_field( $_POST['driveraddr'] );
        $driveraddr = stripcslashes($driveraddr);
        update_user_meta( $driver_id, 'user_locations', $driveraddr );
    }

    wp_safe_redirect( admin_url( "edit.php?post_type=trent&page=drivers&action=manage&id=$driver_id" ) );
    exit;
}

$driverJobs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'complete' AND driver_id = $driver_id");

?>

<div id="driverProfile">
    <h3>Manage driver account</h3>
    <hr>
    <div class="profile_information">
        <form action="" method="post">
            <h4>Edit profile information</h4>

            <div class="mng_input">
                <label for="profileAlerts">Profile alert</label>
                <input type="text" name="profileAlerts" value="<?php echo $profileAlerts ?>">
                <p>This field for showing any warning or notices only to this profile.</p>
            </div>

            <p class="profileranks">Profile Status <strong><?php echo $userranks ?>%</strong></p>

            <div class="mng_input verification">
                <div class="verify_btn">
                    <label for="verified">Verified</label>
                    <input type="checkbox" <?php echo (($verified === 'on') ? 'checked' : '') ?> id="verified" name="verified_profile" value="on">
                    <small>Join date: <?php echo $joindate ?></small>
                </div>
            </div>

            <div class="mng_input">
                <label for="drivername">Name</label>
                <input type="text" name="drivername" value="<?php echo $username ?>">
            </div>
            <div class="mng_input">
                <label for="driveremail">Email</label>
                <input type="text" name="driveremail" value="<?php echo $useremail ?>">
            </div>
            <div class="mng_input">
                <label for="driverphone">Phone</label>
                <input type="text" name="driverphone" value="<?php echo $userphone ?>">
            </div>
            <div class="mng_input">
                <label for="driveraddr">Address</label>
                <input type="text" name="driveraddr" value="<?php echo $useraddr ?>">
            </div>

            <input type="submit" class="button-secondary" name="update_driver_profile" value="Save changes">
        </form>

        <div class="submittedDocuments">
            <div class="profile_docs">
                <h4>Profile Documents</h4>
                <?php
                if($docs_submitted){
                    ?>
                    <table>
                        <?php 
                        $types = null;
                        $documents = array();

                        switch ($docs_submitted) {
                            case 'nid-card':
                                $types = "Nid card";
                                $documents['front'] = get_user_meta($driver_id, 'nid_card_front_file', true);
                                $documents['back'] = get_user_meta($driver_id, 'nid_card_back_file', true);
                                break;
                            case 'passport':
                                $types = "Passport";
                                $documents['passport'] = get_user_meta($driver_id, 'passport_document_file', true);
                                break;
                            case 'driving_license':
                                $types = "Driving license";
                                $documents['front'] = get_user_meta($driver_id, 'driving_document_front_file', true);
                                $documents['back'] = get_user_meta($driver_id, 'driving_document_back_file', true);
                                break;
                        }
                        ?>
                        <tr>
                            <th>Document Type</th>
                            <td><?php echo $types ?></td>
                        </tr>
                        <?php 
                            if(sizeof($documents) > 0){
                                foreach($documents as $fileType => $document){
                                    echo '<tr>
                                        <th>'.ucfirst($fileType).'</th>
                                        <td>
                                            <a target="_blank" href="'.esc_url( $document ).'">'.basename($document).'</a>
                                        </td>
                                    </tr>';
                                }
                            }
                        ?>
                    </table>
                    <button data-type="<?php echo $docs_submitted ?>" data-id="<?php echo $driver_id ?>" id="reject_driver_profile_docs" class="button-secondary">Reject</button>
                    <?php
                }else{
                    echo '<div class="alert">No documents submitted!</div>';
                }
                ?>
            </div>

            <div class="truck_information">
                <h4>Trucks</h4>
                
                <?php
                    if($driver_trucks && !empty($driver_trucks)){
                        $driver_trucks = unserialize($driver_trucks);
                        ?>
                        <div class="trucks_list">
                            <p>Total (<?php echo sizeof($driver_trucks) ?>)</p>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ট্রাক নাম্বার</th>
                                        <th>নিবন্ধনের তারিখ</th>
                                        <th>মালিক</th>
                                        <th>ধরণ</th>
                                        <th>ট্রাকের ছবি</th>
                                        <th>ডকুমেন্ট</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(is_array($driver_trucks)){
                                    foreach($driver_trucks as $truckArr){
                                        ?>
                                        <tr>
                                            <td>
                                                <strong>ট্রাক নাম্বার</strong>
                                                <?php echo $truckArr['truck_number'] ?>
                                            </td>
                                            <td>
                                                <strong>নিবন্ধনের তারিখ</strong>
                                                <?php echo $truckArr['registration_date'] ?>
                                            </td>
                                            <td>
                                                <strong>মালিক</strong>
                                                <?php echo $truckArr['truck_owner'] ?>
                                            </td>
                                            <td>
                                                <strong>ধরণ</strong>
                                                <?php 
                                                    $term_id = $truckArr['truck_types'];
                                                    $term_name = get_term( $term_id )->name;
                                                    echo $term_name;
                                                ?>
                                            </td>
                                            <td>
                                                <strong>ট্রাকের ছবি</strong>
                                                <a target="_blank" href="<?php echo esc_url($truckArr['truck_self_photo']) ?>"><?php echo basename($truckArr['truck_self_photo']) ?></a>
                                            </td>
                                            <td>
                                                <strong>ডকুমেন্ট</strong>
                                                <a target="_blank" href="<?php echo esc_url($truckArr['truck_valid_docs']) ?>"><?php echo basename($truckArr['truck_valid_docs']) ?></a>
                                            </td>
                                        </tr>  
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                    }else{
                        echo '<div class="alert">No truck added.</div>';
                    }
                ?>
            </div>
            
            <div class="paymentInformations">
            <h4>Payment History</h4>
            <div class="paymentsTable">
                <table>
                    <thead>
                        <tr>
                            <th>Job ID</th>
                            <th>Start date</th>
                            <th>Finish date</th>
                            <th>Hired</th>
                            <th>Vat</th>
                            <th>Total</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($driverJobs){
                            foreach($driverJobs as $job){
                                $payment_status = $wpdb->get_var("SELECT payment_status FROM {$wpdb->prefix}payment_history WHERE progression_id = {$job->ID}");
                                ?>
                                 <tr>
                                    <td><?php echo strtolower(base64_encode($job->ID)) ?></td>
                                    <td><?php echo date("F j, Y, g:i a", strtotime($job->created)) ?></td>
                                    <td><?php echo date("F j, Y, g:i a", strtotime($job->deal_date)) ?></td>
                                    <td><?php echo $job->rent_cost ?>tk</td>
                                    <td><?php echo $job->vat ?>%</td>
                                    <td><?php echo number_format(get_total_cost_with_vat($job->vat, $job->rent_cost)) ?>tk</td>
                                    <td><?php echo get_payment_status($payment_status) ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
    </div>
</div>