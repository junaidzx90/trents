<?php 
add_filter( 'show_admin_bar', '__return_false' );
get_header() ?>
<?php wp_enqueue_script('trent-jobs'); ?>

<div id="job-wrap" style="<?php echo ((get_option('jobpagesidebar') === 'enable') ? 'width: 75%' : 'width: 100%') ?>">
    <div class="headerinfo">
        <div>
            <h1 class="welcome-heading"><?php echo ((get_option('welcome_heading')) ? get_option('welcome_heading') : 'শুভ কামনা') ?></h1>
            <p><?php echo get_option('welcome_text'); ?></p>
        </div>
        <img width="150" src="<?php echo tRENTS_ROOT_URL.'/public/images/truck.jpg' ?>" alt="truck-decoraion">
    </div>

    <div id="activejobs">
        <h3 class="heading3">চলমান ট্রিপ</h3>
        <div class="job-page-contents">
            <div class="jobcontents">
                <?php
                $nojob = true;

                function trent_terms($post_id, $taxonomy){
                    $term_list = get_the_term_list( $post_id, $taxonomy, '', ', ' );
                    return apply_filters( 'the_terms', $term_list, $taxonomy, '', ', ' );
                }
			 
			 	$args = array(
					'post_type' => 'trent',
					'post_status' => array('publish'),
					'posts_per_page' => "-1",
					'meta_key' => 'tr_load_datetime',
					'orderby' => 'meta_value',
					'order' => 'ASC'
				);
			 
				// Execute query
				$jobObj = new WP_Query($args);

                if ( $jobObj->have_posts() ) :
                    while ( $jobObj->have_posts() ) : $jobObj->the_post();
                        $driver_id = get_current_user_id(  );
                        $job_id = get_post()->ID;
                        $trucks = trent_terms(get_post(), 'trucks');

                        $mysubmission = $wpdb->get_var("SELECT `status` FROM {$wpdb->prefix}job_progression WHERE job_id = $job_id AND driver_id = $driver_id");
                        
                        $not_deal = true;

                        // Check current job status
                        $job_status = $wpdb->get_var("SELECT `status` FROM {$wpdb->prefix}job_progression WHERE job_id = $job_id");

                        // Already In progress or complete
                        if( $job_status === 'active' || $job_status === 'complete' ){
                            $not_deal = false;
                        }

                        if($not_deal && empty($mysubmission)){
                            $nojob = false;
                            $post_author_id = get_post_field( 'post_author', get_post()->ID );
                            $authorname = get_user_by( "ID", $post_author_id )->display_name;
                            ?>
                            <div class="job <?php echo $mysubmission ?>" id="job-<?php echo get_post()->ID ?>">
                                <input type="hidden" class="job_ID" value="<?php echo get_post()->ID ?>">
                                
                                <h3 class="<?php echo (($mysubmission === 'applied' && (current_user_can( 'driver' )) || current_user_can( 'partner' ))? $mysubmission : (current_user_can( 'client' ) ? 'noteligable' : 'openjob')) ?> jobtitle"><?php __(the_title(), 'trents') ?></h3>
                                <p class="postedby">Posted by: <?php echo $authorname ?></p>
                                <div><p class="publish_times"><?php echo time_elapsed_string(get_the_date( "Y/m/d h:i:s" )) ?></p></div>

                                <div class="jobbody">
                                    <div class="jobinfos">

                                        <div class="infobox">
                                            <p class="infoshead"><?php echo __('লোডের স্থান', 'trents') ?></p>
                                            <p class="infosdetail">:&nbsp;<?php echo __(get_post_meta(get_post()->ID, 'tr_load_location', true ), 'trents') ?></p>
                                        </div>
                                        
                                        <div class="infobox">
                                            <p class="infoshead"><?php echo __('আনলোডের স্থান', 'trents') ?></p>
                                            <p class="infosdetail">:&nbsp;<?php echo __(get_post_meta(get_post()->ID, 'tr_unload_location', true ), 'trents') ?></p>
                                        </div>

                                        <div class="infobox">
                                            <p class="infoshead"><?php echo __('লোডের সময়', 'trents') ?></p>
                                            <p class="infosdetail loadtime-times">:&nbsp;<?php echo __(englishToBanglaNumber(date("Y/m/d - h:i A",strtotime(get_post_meta(get_post()->ID, 'tr_load_datetime', true )))), 'trents') ?></p>
                                        </div>

                                        <div class="infobox">
                                            <p class="infoshead"><?php echo __('মালের ধরণ', 'trents') ?></p>
                                            <p class="infosdetail typeofgoods">:&nbsp;<?php echo __(get_goods_type(get_post_meta(get_post()->ID, 'tr_goodstype', true)), 'trents') ?></p>
                                        </div>
                                        
                                        <div class="infobox">
                                            <p class="infoshead"><?php echo __('ট্রাকের ধরণ', 'trents') ?></p>
                                            <p class="infosdetail typeoftruck">:&nbsp;<?php echo$trucks ?></p>
                                        </div>

                                        <div class="infobox">
                                            <?php
                                            $terms = get_the_terms( get_post()->ID, 'trucks' );
                                            
                                            if($terms){
                                                foreach($terms as $term){
                                                    echo _e('<p class="trucktype">'.$term->name.'</p>', 'trents');
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="submitapplicationbox">
                                        <?php
                                        if($mysubmission === 'applied'){
                                            _e('<button class="applied submit-application">অপেক্ষমান</button>', 'trents');
                                        }else{
                                            if(current_user_can( 'driver' )){
                                                _e('<button class="openjob submit-application">ভাড়া জানান</button>', 'trents');
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>

                                <?php if(get_post_meta(get_post()->ID, 'tr_jobdescription', true)){ ?>
                                <p class="jobdesc"><?php echo __(stripcslashes(get_post_meta(get_post()->ID, 'tr_jobdescription', true)), 'trents') ?></p>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    endwhile;
                else :
                    $nojob = true;
                endif;

                if($nojob){
                    _e( '<div class="nojobarefound">Sorry, no jobs were found.</div>', 'trents' );
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Application Popup -->
    <div id="application-popup" class="popup-wrap trnone">
        <div class="popup-inside">
            <div class="popuphead"><span class="pop-closeicon">❮</span></div>

            <div class="popupcontents">
                <div class="tr-loader-icon">
                    <svg version="1.1" id="tr-loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="80px" height="80px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
                        <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
                        s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
                        c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z" />
                        <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
                        C22.32,8.481,24.301,9.057,26.013,10.047z">
                        <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 20 20" to="360 20 20" dur="0.9s" repeatCount="indefinite" />
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
if(get_option('jobpagesidebar') && get_option('jobpagesidebar') === 'enable'){
    if ( is_active_sidebar( 'trent-job-sidebar' ) ) :
        echo '<div class="sidebar-widget">';
        dynamic_sidebar( 'trent-job-sidebar' );
        echo '</div>';
    endif; 
}
?>

<?php get_footer() ?>