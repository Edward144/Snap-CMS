<?php

    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');

    $type = $_GET['type'] . 's';
    $search = '%' . $_GET['searchTerm'] . '%';
    $limit = $_GET['limit'];

    $searchContent = $mysqli->prepare("SELECT * FROM `{$type}` WHERE name LIKE ? OR description LIKE ? OR author LIKE ? OR url LIKE ? OR content LIKE ? ORDER BY id ASC LIMIT ?");
    $searchContent->bind_param('sssssi', $search, $search, $search, $search, $search, $limit);
    $searchContent->execute();
    $result = $searchContent->get_result();
    
    $headerContent = 
        '<tr class="headers">
            <td style="width: 50px;">ID</td>
            <td style="text-align: left;">Details</td>
            <td style="width: 180px;">Published</td>
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

            $rowContent = 
                '<tr class="' . $type . 'Row contentRow">
                    <td>
                        <span class="id">' . $row['id'] . '</span>
                    </td>

                    <td style="text-align: left;">
                        <h4>' . $row['name'] . '</h4>
                        <p>' . $row['description'] . '</p>
                        <p style="font-size: 0.75em;">URL: ' . $row['url'] . '</p>
                    </td>

                    <td>
                        <p>' . $row['author'] . '</p>
                        <p>' . date('d/m/Y H:i:s', strtotime($row['date_posted'])) . '</p>
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
                <td colspan="4">No ' . $type . ' were found</td>
            </tr>';
            
            array_push($json, $rowContent);
    }
    
    echo json_encode(implode($json));

?>