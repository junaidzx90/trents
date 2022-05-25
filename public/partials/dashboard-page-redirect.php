<?php
if(!is_user_logged_in(  )){
    wp_safe_redirect( get_post_type_archive_link( 'trent' ) );
    exit;
}
$user = get_user_by( 'ID', get_current_user_id(  ) );
wp_safe_redirect( home_url('/author/'.$user->user_login) );
exit;