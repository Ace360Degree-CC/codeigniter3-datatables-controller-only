public function fetch_datatables()
{
    // Get DataTables request parameters
    $draw = $this->input->get('draw');
    $start = $this->input->get('start');
    $length = $this->input->get('length');
    $search = $this->input->get('search')['value'];

    // Specify the table name
    $table = 'holidays';

    //Join Queries Goes Here

    // Get total records count
    $totalRecords = $this->db->count_all($table);

    // Select all columns from the specified table
    $this->db->from($table);

    // Implement search functionality if search value is provided
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like($table . '.name', $search); // Specify table name for 'name' column
        $this->db->or_like($table . '.day', $search); // Specify table name for 'day' column
        $this->db->or_like($table . '.holiday_type', $search); // Specify table name for 'day' column
        $this->db->or_like($table . '.holiday_for', $search); // Specify table name for 'day' column
        $this->db->or_like($table . '.branch', $search); // Specify table name for 'day' column
        $this->db->group_end();
    }

    // Get filtered records count
    

    // Apply pagination
    $this->db->limit($length, $start);

    // Execute the query
    $query = $this->db->get();
    
    $filteredRecords = $this->db->count_all_results();

    // Fetch the result set
    $output = $query->result_array();
    $data = array();
    $i=1;
    foreach($output as $row) {
        
        $number = $i;

        //Custom Column Codes Goes Here
        $action_td = '<td class="action-width">
                <a href="' . base_url() . 'admin/employee/holidays/view/' . $row['id'] . '" title="View">
                    <span class="btn btn-theme"><i class="fa fa-eye"></i></span>
                </a>
                <a href="' . base_url() . 'admin/employee/holidays/edit/' . $row['id'] . '" title="Edit">
                    <span class="btn btn-dark"><i class="fa fa-edit"></i></span>
                </a>
                <a title="Delete" data-toggle="modal" data-target="#commonDelete" onclick="set_common_delete(\'' . $row['id'] . '\',\'employee/holidays\');">
                    <span class="btn btn-danger"><i class="fa fa-trash"></i></span>
                </a>
            </td>';


        //Result Array Generated Here
        $customizedRow = array(
            $number,
            $row['name'],
            $row['date'],
            $row['day'],
            $row['holiday_type'],
            $row['holiday_for'],
            $row['branch'],
            $action_td,
        );
        $i++;
        // Optionally, you can add more customizations here

        $data[] = $customizedRow; // Add the customized row to the $data array
    }

    // Prepare response data
    $response = array(
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $filteredRecords,
        "data" => $data
    );

    // Convert response data to JSON and output
    echo json_encode($response);
}
