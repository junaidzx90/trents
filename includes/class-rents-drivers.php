<?php
class TR_Drivers extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $action = $this->current_action();

        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 10;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args(array(
            'total_items' => $totalItems,
            'per_page' => $perPage,
        ));

        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);
        $this->_column_headers = array($columns, $hidden, $sortable);
       
        $this->items = $data;
    }

    function display_tablenav($which){
        ?>
        <div class="tablenav <?php echo $which ?>"></div>
        <?php
    }
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'drivername' => 'Driver Name',
            'driver_email' => 'Email',
            'driver_phone' => 'Phone',
            'profile_status' => 'Profile Status',
            'job_done' => 'Job Done',
            'driver_ranks' => 'Ranks',
            'payment_due' => 'Payment Due',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
            'drivername' => array('drivername', true),
            'job_done' => array('job_done', true),
            'driver_ranks' => array('driver_ranks', true),
            'profile_status' => array('profile_status', true),
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        $data = array();
        
        $args = array(
            'role'    => 'driver',
            'orderby' => 'ID',
            'order'   => 'ASC'
        );
        $users = get_users( $args );

        foreach ( $users as $user ) {
            $driverJobs = $wpdb->get_results("SELECT ID FROM {$wpdb->prefix}job_progression WHERE status = 'complete' AND driver_id = {$user->ID}", ARRAY_A);

            $duePaymentsList = array();
            if(is_array($driverJobs)){
                foreach($driverJobs as $driverJob){
                    $paymentDue = $wpdb->get_var("SELECT progression_id FROM {$wpdb->prefix}payment_history WHERE progression_id = {$driverJob['ID']} AND payment_status = 'paid'");
                    if(!$paymentDue){
                        $duePaymentsList[] = $paymentDue;
                    }
                }
            }

            $userArr = array(
                'ID'    => $user->ID,
                'drivername' => $user->display_name,
                'driver_email' => $user->user_email,
                'driver_phone' => get_user_meta($user->ID, 'user_phone', true ),
                'profile_status' => ((get_user_meta( $user->ID, 'verified_account', true )) ? ucfirst(get_user_meta( $user->ID, 'verified_account', true )) : 'Unverified'),
                'job_done' => ((is_array($driverJobs)) ? sizeof($driverJobs) : 0),
                'driver_ranks' => ((get_user_meta($user->ID, 'user_profile_ranks', true)) ? get_user_meta($user->ID, 'user_profile_ranks', true).'%' : "100%"),
                'payment_due' => sizeof($duePaymentsList),
            );

            $data[] = $userArr;
        }

        return $data;
    }

    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default($item, $column_name)
    {
        switch ($column_name) {
            case $column_name:
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    public function column_drivername($item)
    {
        $actions = array(
            'edit' => '<a href="?post_type=trent&page=drivers&action=manage&id='.$item['ID'].'">Manage</a>',
            'delete' => '<a href="?post_type=trent&page=drivers&action=delete&id='.$item['ID'].'">Delete</a>',
        );

        return sprintf('%1$s %2$s', $item['drivername'], $this->row_actions($actions));
    }

    public function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete',
        );
        return $actions;
    }

    public function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="driver[]" value="%s" />', $item['ID']
        );
    }

    // All form actions
    public function current_action()
    {
        global $wpdb;
        if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'drivers') {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && isset($_REQUEST['driver'])) {
                if(is_array($_REQUEST['driver'])){
                    $ids = $_REQUEST['driver'];
                    foreach($ids as $ID){
                        
                    }
                }else{
                    $ID = intval($_REQUEST['driver']);
                    
                }
            }
        }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b)
    {
       // If no sort, default to user_login
       $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'drivername';
       // If no order, default to asc
       $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
       // Determine sort order
       $result = strnatcmp($a[$orderby], $b[$orderby]);
       // Send final sort direction to usort
       return ($order === 'asc') ? $result : -$result;
    }

} //class
