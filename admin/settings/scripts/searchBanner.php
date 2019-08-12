<?php

    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $search = '%' . $_GET['searchTerm'] . '%';
    $limit = $_GET['limit'];

    $searchContent = $mysqli->prepare("SELECT * FROM `banners` WHERE name LIKE ? ORDER BY id ASC LIMIT ?");
    $searchContent->bind_param('si', $search, $limit);
    $searchContent->execute();
    $result = $searchContent->get_result();
    
    $headerContent = 
        '<tr class="headers">
            <td style="width: 40px;">ID</td>
            <td style="text-align: left;">Details</td>
            <td style="width: 100px;">Actions</td>
        </tr>';

    $json = [$headerContent];

    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if($row['visible'] == 1) {
                $visibleContent = '<p class="icon" id="view"><img src="/admin/images/icons/view.png"></p>';
            }
            else {
                $visibleContent = '<p class="icon" id="hide"><img src="/admin/images/icons/hide.png"></p>';
            }
            
            $postName = $mysqli->query("SELECT name FROM `{$row['post_type']}` WHERE id = {$row['post_type_id']}")->fetch_array()[0]; 
            
            $rowContent = 
                '<tr class="bannerRow contentRow">
                    <td>
                        <span class="id">' . $row['id'] . '</span>
                    </td>

                    <td style="text-align: left;">
                        <h4>' . $row['name'] . '</h4>
                        <p>Displayed On: (' . ucwords(rtrim($row['post_type'], 's')) . ') ' . $postName . '</p>
                    </td>

                    <td>
                        ' . $visibleContent . '

                        <p class="icon" id="edit"><img src="/admin/images/icons/edit.png"></p>
                        <p class="icon" id="delete"><img src="/admin/images/icons/bin.png"></p>
                    </td>
                </tr>';

            array_push($json, $rowContent);
        }
    }
    else {
        $rowContent = 
            '<tr>
                <td colspan="3">No banners were found</td>
            </tr>';
            
            array_push($json, $rowContent);
    }
    
    echo json_encode(implode($json));

?>