<div class="_profile_info">
    <div class="prfile_tabs">
        <?php 
        if(current_user_can( 'driver' ) || current_user_can( 'client' )){ 
            $tabselected = '';
            if(isset($_GET['tab']) && ($_GET['tab'] === '2' || $_GET['tab'] === '3')){
                $tabselected = 'truck';
            }
            ?>
            <input @change="profileTabs('')" type="radio" class="prfile_tabs__radio" name="prfile_tabs-example" <?php echo empty($tabselected) ? 'checked' : '' ?> id="tab1">
            <label for="tab1" class="prfile_tabs__label">প্রোফাইল সেটিং</label>
            <?php } ?>

            <div <?php echo ((current_user_can( 'client' )) ? 'style="display: block;margin-top: 0;"' : '') ?> class="prfile_tabs__content">
                <?php
                global $globalError;
                if($globalError !== null && !empty($globalError)){
                    echo '<div class="formError">'.$globalError.'</div>';
                }
                ?>
                <form method="post" action="" enctype="multipart/form-data">
                    <?php wp_nonce_field( '_nonce', 'profile_nonce' ); ?>
                    <div class="formInp">
                        <label for="username">নাম*</label>
                        <input required type="text" name="userfullname" id="username" placeholder="Full name" value="<?php echo ucfirst($currentUser->display_name) ?>">
                    </div>
                    <div style="opacity: .7" class="formInp">
                        <label for="useremail">ইমেইল ঠিকানা</label>
                        <input type="email" id="useremail" readonly placeholder="Email" value="<?php echo ucfirst($currentUser->user_email) ?>">
                    </div>
                    <div class="formInp">
                        <label for="userphone">ফোন নম্বর*</label>
                        <input required type="text" id="userphone" name="userphone" placeholder="Phone" value="<?php echo ((get_user_meta(get_current_user_id(  ), 'user_phone', true ))? get_user_meta(get_current_user_id(  ), 'user_phone', true ) : '') ?>">
                    </div>
                    <div class="formInp">
                        <label for="useraddress">স্থায়ী ঠিকানা</label>
                        <input type="text" id="useraddress" name="useraddress" placeholder="Address" value="<?php echo ((get_user_meta(get_current_user_id(), 'user_locations', true )) ? get_user_meta(get_current_user_id(), 'user_locations', true ):'' ) ?>">
                        <div class="loading-wrapp trnone">
                            <div class="bar one"></div>
                            <div class="bar two"></div>
                            <div class="bar three"></div>
                        </div>
                    </div>

                    <?php

                    $docs_submitted = get_user_meta(get_current_user_id(  ), 'driver_docs_type', true );

                    if(current_user_can( 'driver' ) && get_user_meta( get_current_user_id(  ), 'verified_account', true ) !== 'on'){
                        echo '<h3 class="upload_user_documents">ডকুমেন্টস</h3>';
                        
                        if(!empty($docs_submitted)){
                            echo '<div class="formInp">অনুমোদনের জন্য মুলতুবি রয়েছে.</div>';
                            echo '<input type="submit" id="user_profile_submit" name="user_profile_submit" value="পরিবর্তন করুন">';
                        }else{
                            ?>
                            <div class="formInp">
                                <label for="docs__type">ডকুমেন্টের ধরণ</label>
                                <select @change="getDocumentType()" v-model="driverDocs.documentType" name="tr_docs_type" id="tr_docs_type">
                                    <option value="nid-card">ভোটার কার্ড</option>
                                    <option value="passport">পাসপোর্ট</option>
                                    <option value="driving_license">ড্রাইভিং লাইসেন্স</option>
                                </select>
                            </div>
        
                            <div class="formInp">
                                <div class="uploadDocuments">
                                    <div v-if="driverDocs.isNidCard" class="nid-documents">
                                        <div class="front_side">
                                            <label for="nid_card_front_side">সামনের দিক</label>
                                            <input @change="previewDocuments(event)" type="file" required name="nid_card_front_file" id="nid_card_front_side">
                                            <span class="file_view"></span>
                                        </div>
                                        <div class="back_side">
                                            <label for="nid_card_back_side">পিছন দিক</label>
                                            <input @change="previewDocuments(event)" type="file" required name="nid_card_back_file" id="nid_card_back_side">
                                            <span class="file_view"></span>
                                        </div>
                                    </div>
        
                                    <div v-if="driverDocs.isPassport" class="passport_document">
                                        <label for="passport_document_file">পাসপোর্টের কপি</label>
                                        <input @change="previewDocuments(event)" type="file" required name="passport_document_file" id="passport_document_file">
                                        <span class="file_view"></span>
                                    </div>
        
                                    <div v-if="driverDocs.isDriving" class="driving_document">
                                        <div class="front_side">
                                            <label for="driving_document_front_side">সামনের দিক</label>
                                            <input @change="previewDocuments(event)" type="file" required name="driving_document_front_file" id="driving_document_front_side">
                                            <span class="file_view"></span>
                                        </div>
                                        <div class="back_side">
                                            <label for="driving_document_back_side">পিছন দিক</label>
                                            <input @change="previewDocuments(event)" type="file" required name="driving_document_back_file" id="driving_document_back_side">
                                            <span class="file_view"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="submit" id="user_profile_submit" name="user_profile_submit" value="ভেরিফাইয়ের জন্য পাঠান">
                            <?php
                        }
                    }else{
                        echo '<input type="submit" id="user_profile_submit" name="user_profile_submit" value="পরিবর্তন করুন">';
                    }
                    ?>
                </form>
            </div>

            <?php if(current_user_can( 'driver' )){ 
                $trucks = get_user_meta(get_current_user_id(  ), '_driver_trucks', true );
                ?>
                <input @change="profileTabs('2')" type="radio" class="prfile_tabs__radio" name="prfile_tabs-example" <?php echo $tabselected === 'truck' ? 'checked' : '' ?> id="tab2">
                <label for="tab2" class="prfile_tabs__label">ট্রাক</label>
                <div class="prfile_tabs__content">
                    <div class="truck_contents">
                        <?php
                            if($trucks && !empty($trucks)){
                                $trucks = unserialize($trucks);
                                ?>
                                <div class="trucks_list">
                                    <p>Total (<?php echo sizeof($trucks) ?>)</p>
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
                                        if(is_array($trucks)){
                                            foreach($trucks as $truckArr){
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
                            
                            // Add new truck
                            if(isset($_GET['page']) && $_GET['page'] === 'profile' && isset($_GET['tab']) && $_GET['tab'] === "3"){
                                ?>
                                <div id="new_truck_form">
                                    <div class="truck_form">
                                        <div class="pupup_head">
                                            <h3>New truck</h3>
                                            <span @click="closeNewTuckForm()" class="closeForm">+</span>
                                        </div>

                                        <form action="" method="post" enctype="multipart/form-data">
                                            <div class="formInp">
                                                <label for="truck_number">ট্রাক নাম্বার</label>
                                                <input required type="text" id="truck_number" name="truck_number" value="">
                                            </div>
                                            <div class="formInp">
                                                <label for="registration_date">রেজিস্ট্রেশনের তারিখ</label>
                                                <input required type="text" id="registration_date" name="registration_date" value="">
                                            </div>
                                            <div class="formInp">
                                                <label for="truck_owner">মালিক</label>
                                                <input required type="text" id="truck_owner" name="truck_owner" value="">
                                            </div>
                                            <div class="formInp">
                                                <?php
                                                    $terms = get_terms(
                                                        array(
                                                            'taxonomy'   => 'trucks',
                                                            'hide_empty' => false,
                                                        )
                                                    );

                                                    if($terms){
                                                        echo '<label for="truck_types">ট্রাকের ধরন</label>';
                                                        echo '<select required name="truck_types" id="truck_types">';
                                                        foreach($terms as $truck){
                                                            echo '<option value="'.$truck->term_id.'">'.$truck->name.'</option>';
                                                        }
                                                        echo '</select>';
                                                    }
                                                ?>
                                            </div>
                                            <div class="formInp">
                                                <label for="truck_self_photo">ট্রাকের একটি ছবি যোগ করুন</label>
                                                <input @change="truckImagesSelect(event)" required type="file" name="truck_self_photo" id="truck_self_photo">
                                            </div>
                                            <div class="formInp">
                                                <label for="truck_valid_docs">ট্রাকের একটি বৈধ নথি যোগ করুন</label>
                                                <input @change="truckImagesSelect(event)" required type="file" name="truck_valid_docs" id="truck_valid_docs">
                                            </div>

                                            <div class="formInp">
                                                <?php wp_nonce_field( '_nonce', 'newtruck_nonce' ) ?>
                                                <input name="new_truck_submitted" type="submit" value="যুক্ত করুন">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                        ?>
                        <a href="?page=profile&tab=3" class="add_turck_button">ট্রাক যুক্ত করুন</a>
                    </div>
                </div>
            <?php 
        } ?>
    </div>
</div>