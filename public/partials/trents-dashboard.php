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
                    <div @click="progress_menu" :class="(progress_page == true)?'_item activclass':'_item'">
                        <i class="fas fa-tasks"></i>
                        <span class="_progress">আবেদন সমূহ</span>
                    </div>

                    <div @click="activeJob_menu" :class="(activeJob_page == true)?'_item activclass':'_item'">
                        <i class="fas fa-peace"></i>
                        <span class="_activeJob">চলতি ট্রিপ</span>
                    </div>

                    <?php if(current_user_can( 'client' ) || current_user_can( 'partner' )){ ?>
                        <div @click="createJob_menu" :class="(createJob_page == true)?'_item activclass':'_item'">
                            <i class="fas fa-plus"></i>
                            <span class="_createJob">ট্রাক চাই</span>
                        </div>
                    <?php } ?>

                    <div @click="triphistory_menu" :class="(trip_history_page == true)?'_item activclass':'_item'">
                        <i class="fas fa-history"></i>
                        <span class="_triphistory">ট্রিপ হিস্টোরি</span>
                    </div>

                    <?php if(current_user_can( 'driver' )){ ?>
                    <div @click="payments_menu" :class="(payments_page == true)?'_item activclass':'_item'">
                        <i class="fas fa-credit-card"></i>
                        <span class="_payments">পেমেন্ট</span>
                    </div>
                    <?php } ?>

                    <div @click="profile_menu" :class="(profile_page == true)?'_item activclass':'_item'">
                        <i class="fas fa-user-circle"></i>
                        <span class="_profile_settings">প্রোফাইল</span>
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

        <?php if(is_user_logged_in(  ) && get_current_user_id(  ) === $author_id){ ?>
            <h3 class="rents_page_title">{{pageTitle}}</h3>
            <!-- progress module -->
            
            <!-- Applied jobs -->
            <div v-if="progress_page" id="progress_contents">
                <?php require_once 'menu-items/applications.php' ?>
            </div>
            
            <!-- activejob module -->
            <div v-if="activeJob_page" id="activejob_contents">
                <!-- Active job module -->
                <?php require_once 'menu-items/active-jobs.php' ?>
            </div>

            <!-- Create Job -->
            <?php if(current_user_can( 'client' )){ ?>
            <div v-if="createJob_page" id="createjob_contents">
                <?php require_once 'menu-items/job-form.php' ?>

                <div id="newjobsidebar">
                <?php 
                if ( is_active_sidebar( 'new-job-sidebar' ) ) :
                    echo '<div class="sidebar-widget">';
                    dynamic_sidebar( 'new-job-sidebar' );
                    echo '</div>';
                endif; 
                ?>
                </div>
            </div>
            <?php } ?>

            <div v-if="trip_history_page" id="triphistory">
                <?php require_once 'menu-items/trip-history.php' ?>
            </div>

            <?php if(current_user_can( 'driver' ) || current_user_can( 'partner' )){ ?>
            <!-- payments module -->
            <div v-if="payments_page" id="payments_contents">
                <?php require_once 'menu-items/payment.php'; ?>
            </div>
            <?php } ?>

            <!-- Profile module -->
            <div v-if="profile_page" id="_profile">
                <?php
                $currentUser = get_user_by( 'ID', $author_id );
                require_once 'menu-items/profile-card.php';
                require_once 'menu-items/profile-edit.php';
                ?>
            </div>
            <?php }else{
                print("Log outed user");
            } 
        ?>
    </div>

</div>
<?php wp_footer(  ) ?>