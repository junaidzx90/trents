<?php
class Job_applications extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        $action = $this->current_action();

        $data = $this->table_data();
        usort($data, array(&$this, 'sort_data'));

        $perPage = 20;
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

    }
    
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns() {
        $columns = array(
            // 'cb' => '<input type="checkbox" />',
            'application_id' => 'Job ID',
            'driver' => 'Driver',
            'driver_phone' => 'Driver Phone',
            'client' => 'Client',
            'client_phone' => 'Client Phone',
            'rent_cost' => 'Expectation',
            'current_vat' => 'Current Vat',
            'submitted' => 'Submitted',
        );

        return $columns;
    }

    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns() {
        return array();
    }

    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns() {
        return array(
            'application_id' => array('application_id', true),
            'driver' => array('driver', true),
            'client' => array('client', true),
            'rent_cost' => array('rent_cost', true),
            'current_vat' => array('current_vat', true),
            'submitted' => array('submitted', true)
        );
    }

    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data() {
        global $wpdb;
        $data = array();
        
        $applications = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}job_progression WHERE status = 'applied'");
        if($applications){
            foreach($applications as $application){
                $dataArr = array(
                    'ID'                => $application->ID,
                    'application_id'    => strtolower(base64_encode($application->ID)),
                    'driver'            => ucfirst(get_user_by( 'ID', $application->driver_id )->display_name),
                    'driver_phone'      => get_user_meta($application->driver_id, 'user_phone', true),
                    'client'            => ucfirst(get_user_by( 'ID', $application->client_id )->display_name),
                    'client_phone'      => get_user_meta($application->client_id, 'user_phone', true),
                    'rent_cost'         => number_format($application->rent_cost),
                    'current_vat'       => $application->vat,
                    'submitted'         =>  date("Y/m/d", strtotime($application->created)).'<br>'
                                            .date("h:i A", strtotime($application->created))
                );

                $data[] = $dataArr;
            }
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
    public function column_default($item, $column_name) {
        switch ($column_name) {
            case $column_name:
                return $item[$column_name];
            default:
                return print_r($item, true);
        }
    }

    public function column_application_id($item) {
        $actions = array(
            // 'delete' => '<a href="">Delete</a>',
        );

        return sprintf('%1$s %2$s', $item['application_id'], $this->row_actions($actions));
    }

    public function get_bulk_actions() {
        $actions = array(
            'delete' => 'Delete',
        );
        // return $actions;
    }

    public function column_cb($item)
    {
        // return sprintf(
        //     '<input type="checkbox" name="application[]" value="%s" />', $item['ID']
        // );
    }

    // All form actions
    public function current_action() {
        global $wpdb;
        // if (isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'applications') {
        //     if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete' && isset($_REQUEST['application'])) {
        //         if(is_array($_REQUEST['application'])){
        //             $ids = $_REQUEST['application'];
        //             foreach($ids as $ID){
                        
        //             }
        //         }else{
        //             $ID = intval($_REQUEST['application']);

        //         }
        //     }
        // }
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data($a, $b) {
        // If no sort, default to user_login
        $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'application_id';
        // If no order, default to asc
        $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
        // Determine sort order
        $result = strnatcmp($a[$orderby], $b[$orderby]);
        // Send final sort direction to usort
        return ($order === 'asc') ? $result : -$result;
    }

} //class
