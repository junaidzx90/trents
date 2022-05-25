<div class="_profile_side">
    <div class="_contents">
        <h3 class="_name"><?php echo ucfirst($currentUser->display_name) ?></h3>
        <h5 class="_user_accinfo"> 
            <span class="role"><?php echo ucfirst($currentUser->roles[0]) ?> Profile</span>
            <?php 
            if(current_user_can( 'driver' )){
                $verified = get_user_meta( get_current_user_id(  ), 'verified_account', true );
                echo '<span style="'.(($verified === 'on') ? 'color: ##00ffc4; border-color: ##00ffc4;' : 'color: red; border-color: red;').'" class="verified">'.(($verified === 'on') ? "Verified" : 'Unverified').'</span>';
            }
            ?>
        </h5>

        <div class="self_info">
            <table>
                <tr>
                    <th>Joined:</th>
                    <td><?php echo englishToBanglaNumber(date("d/m/Y", strtotime($currentUser->user_registered))) ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo $currentUser->user_email ?></td>
                </tr>
                <tr>
                    <th>Phone:</th>
                    <td><?php echo ((get_user_meta($author_id, 'user_phone', true ))? get_user_meta($author_id, 'user_phone', true ) : '<span class="nunval">NuN</span>') ?></td>
                </tr>

                <tr>
                    <th>Profile Status :</th>
                    <td>
                        <?php 
                        $author_ranks = get_user_meta($author_id, 'user_profile_ranks', true);
                        if(!$author_ranks){
                            $author_ranks = 100;
                        }
                        echo englishToBanglaNumber("$author_ranks%") 
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>Address:</th>
                    <td><?php echo ((get_user_meta($author_id, 'user_locations', true )) ? get_user_meta($author_id, 'user_locations', true ):'<span class="nunval">NuN</span>' ) ?></td>
                </tr>
                
            </table>
        </div>
    </div>
</div>