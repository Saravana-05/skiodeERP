 <?php 
// Saravanan changes
session_start();
include_once '../connect_db.php';
$result = "";

$customer_mobile_no_txt = isset($_POST['customer_mobile_no_txt']) ? trim($_POST['customer_mobile_no_txt']) : '';
$customer_name_txt      = isset($_POST['customer_name_txt'])      ? trim($_POST['customer_name_txt'])      : '';

if($customer_mobile_no_txt !== '' || $customer_name_txt !== '')
{
    $conditions = [];

    if($customer_mobile_no_txt !== '')
        $conditions[] = "customer_mobile_no = '" . mysqli_real_escape_string($connection, $customer_mobile_no_txt) . "'";

    if($customer_name_txt !== '')
        $conditions[] = "customer_name LIKE '%" . mysqli_real_escape_string($connection, $customer_name_txt) . "%'";

    $where = implode(" OR ", $conditions);

    $sql = "SELECT * FROM jobcard_master 
            WHERE customer_type='General' AND ($where) 
            ORDER BY jobcard_id DESC 
            LIMIT 10";

    if($query = mysqli_query($connection, $sql))
    {
        $result .= '<table width="100%" class="table" id="resultDetailTable">';
        $result .= '<thead><tr>
                        <th>Job Card No</th>
                        <th>Dt Tm</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>App.Amt</th>
                        <th>Adv.Amt</th>
                        <th>Print</th>
                        <th>SQ.No</th>
                        <th>Discount</th>
                    </tr></thead>';
        $result .= '<tbody>';

        while($row = $query->fetch_assoc())
        {
            $result .= '<tr>';
            $result .= '<td>' . $row["jobcard_no"]      . '</td>';
            $result .= '<td>' . date("d-m-Y h:i:s", strtotime($row["created_dt_tm"])) . '</td>';
            $result .= '<td>' . $row["customer_name"]   . '</td>';
            $result .= '<td>' . $row["customer_mobile_no"] . '</td>';  // helpful when searching by name
            $result .= '<td>' . $row["approximate_amount"] . '</td>';
            $result .= '<td>' . $row["advance_amount"]  . '</td>';
            $result .= '<td><a class="btn btn-sm btn-primary m-1" target="_blank" 
                            href="jc_print_out.php?jc=' . $row["jobcard_no"] . '">Print</a></td>';

            // Sales quotation lookup
            $sq_sql = "SELECT * FROM sales_quotation_master 
                       WHERE jobcard_nos LIKE '%" . mysqli_real_escape_string($connection, $row["jobcard_no"]) . "%'";

            $sq_no  = '';
            $sq_dis = '';
            if($sq_qry = mysqli_query($connection, $sq_sql))
            {
                if($sq_row = mysqli_fetch_array($sq_qry))
                {
                    $sq_no  = $sq_row["quotation_no"];
                    $sq_dis = $sq_row["discount"];
                }
            }
            $result .= '<td>' . $sq_no  . '</td>';
            $result .= '<td>' . $sq_dis . '</td>';

            $result .= '</tr>';
        }

        $result .= '</tbody></table>';
    }
}

echo $result;
// saravanan changes end
?> 