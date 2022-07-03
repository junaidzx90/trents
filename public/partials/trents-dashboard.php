<?php
/* Disable WordPress Admin Bar for all users */
add_filter( 'show_admin_bar', '__return_false' );
wp_head(  );
wp_enqueue_script('trents');
?>
<style>
    html{
        margin: 0 !important;
    }
</style>

<?php
$page = 'profile';
if(isset($_GET['page'])){
    $page = $_GET['page'];
}
?>

<div id="rents_wrap">
    <!-- SidebarMenu -->
    <nav id="_sidebar">
        <div class="sidebar_contents">

            <div class="site_logo">
                <a href="<?php echo esc_url(home_url()) ?>">
                    <span class="arrow_icon"><i class="fas fa-long-arrow-alt-left"></i></span>
                    <img width="80" src="<?php echo esc_url(get_option('profile_logo')) ?>" alt="">
                </a>
            </div>

            <div class="menuitems">
                <?php 
                if(is_user_logged_in(  ) && get_current_user_id(  ) === $author_id){ ?>
                    <div class="_item">
                        <i class="fas fa-bus-alt"></i>
                        <a class="findjob" target="_b" href="<?php echo esc_url(get_post_type_archive_link( 'trent' )) ?>">
                            <span class="_progress">সমস্ত ট্রিপ্স</span>
                        </a>
                    </div>
                    <div class="<?php echo (($page === 'progress')? 'activclass': '') ?> _item">
                        <i class="fas fa-tasks"></i>
                        <a href="?page=progress">
                            <span class="_progress">আবেদন সমূহ</span>
                        </a>
                    </div>

                    <div class="<?php echo (($page === 'active')? 'activclass': '') ?> _item">
                        <i class="fas fa-peace"></i>
                        <a href="?page=active">
                            <span class="_activeJob">চলতি ট্রিপ</span>
                        </a>
                    </div>

                    <?php if(current_user_can( 'client' ) || current_user_can( 'partner' )){ ?>
                        <div class="<?php echo (($page === 'newjob')? 'activclass': '') ?> _item">
                            <i class="fas fa-plus"></i>
                            <a href="?page=newjob">
                                <span class="_createJob">ট্রাক চাই</span>
                            </a>
                        </div>
                    <?php } ?>

                    <div class="<?php echo (($page === 'triphistory')? 'activclass': '') ?> _item">
                        <i class="fas fa-history"></i>
                        <a href="?page=triphistory">
                            <span class="_triphistory">ট্রিপ হিস্টোরি</span>
                        </a>
                    </div>

                    <?php if(current_user_can( 'driver' )){ ?>
                    <div class="<?php echo (($page === 'payments')? 'activclass': '') ?> _item">
                        <i class="fas fa-credit-card"></i>
                        <a href="?page=payments">
                            <span class="_payments">পেমেন্ট</span>
                        </a>
                    </div>
                    <?php } ?>

                    <div class="<?php echo (($page === 'profile')? 'activclass': '') ?> _item">
                        <i class="fas fa-user-circle"></i>
                        <a href="?page=profile">
                            <span class="_profile_settings">প্রোফাইল</span>
                        </a>
                    </div>

                    <div class="_item">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="_logout"><a href="<?php echo wp_logout_url( get_post_type_archive_link( 'trent' ) ) ?>">লগ আউট</a></span>
                    </div>
                <?php } ?>
            </div>
        </div>
    </nav>

    <div id="_contents">
        <?php
        $profileAlerts = get_user_meta( get_current_user_id(  ), '_driver_profileAlerts', true );
        if(!empty($profileAlerts)){
            echo '<div class="profileAlert">'.$profileAlerts.'</div>';
        }
        ?>

        <?php 
        if(is_user_logged_in(  ) && get_current_user_id(  ) === $author_id){ 
            switch ($page) {
                case 'progress':
                    ?>
                    <div id="progress_contents">
                        <?php require_once 'menu-items/applications.php' ?>
                    </div>
                    <?php
                    break;
                case 'active':
                    ?>
                    <!-- activejob module -->
                    <div id="activejob_contents">
                        <!-- Active job module -->
                        <?php require_once 'menu-items/active-jobs.php' ?>
                    </div>
                    <?php
                    break;
                case 'newjob':
                    if(current_user_can( 'client' )){ ?>
                    <div id="createjob_contents">
                        <?php require_once 'menu-items/job-form.php' ?>
                    </div>
                    <?php }
                    break;
                case 'triphistory':
                    ?>
                    <div id="triphistory">
                        <?php require_once 'menu-items/trip-history.php' ?>
                    </div>
                    <?php
                    break;
                case 'payments':
                    if(current_user_can( 'driver' ) || current_user_can( 'partner' )){ ?>
                        <!-- payments module -->
                        <div id="payments_contents">
                            <?php require_once 'menu-items/payment.php'; ?>
                        </div>
                        <?php 
                    }
                    break;
                case 'profile':
                    ?>
                    <!-- Profile module -->
                    <div id="_profile">
                        <?php
                        $currentUser = get_user_by( 'ID', $author_id );
                        require_once 'menu-items/profile-card.php';
                        require_once 'menu-items/profile-edit.php';
                        ?>
                    </div>
                    <?php
                    break;
                
                default:
                    # code...
                    break;
            }
        }
    ?>
    </div>

</div>
<?php wp_footer(  ) ?>