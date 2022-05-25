<form action="" method="post">

    <?php
    global $globalError;
    if($globalError !== null && !empty($globalError)){
        echo '<div class="formError">'.$globalError.'</div>';
    }
    ?>

    <div class="formInp">
        <label for="job_title">টাইটেল</label>
        <input required type="text" autocomplete="off" name="job_title" id="job_title" placeholder="Job Title">
    </div>

    <div class="formInp">
        <label for="load_location">লোডের জায়গা</label>
        <input required type="text" autocomplete="off" name="load_location" id="load_location" placeholder="Address">
        <div class="loading-wrapp trnone">
            <div class="bar one"></div>
            <div class="bar two"></div>
            <div class="bar three"></div>
        </div>
    </div>

    <div class="formInp">
        <label for="unload_location">আনলোডের জায়গা</label>
        <input required type="text" autocomplete="off" name="unload_locatiion" id="unload_location" placeholder="Address">
        <div class="loading-wrapp trnone">
            <div class="bar one"></div>
            <div class="bar two"></div>
            <div class="bar three"></div>
        </div>
    </div>

    <div class="formInp">
        <label for="load_time">লোডের সময়</label>
        <input required type="datetime-local" name="load_time" id="load_time">
    </div>

    <div class="formInp">
        <?php apply_filters( 'goodstype', ''); ?>
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
        <label for="description">মালের বিবরণ</label>
        <textarea name="job_description" id="description" cols="30" rows="5"></textarea>
    </div>

    <input type="submit" class="btn btn-newjob" value="Publish" name="new_job_submission">
</form>