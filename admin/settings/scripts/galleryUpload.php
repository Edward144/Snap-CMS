<?php    

    if(empty($_FILES['file'])) {
        echo json_encode('No files selected.');
    }
    else {
        $files = [];
        $json = [];
        
        //Add File Name To Array
        foreach($_FILES['file']['name'] as $index => $name) {
            $oName = $name;
            $name = str_replace("'", "", $name);
            $name = preg_replace('/\s+/', '_', $name);
            
            $files[$index]['name'] = $name;
            $files[$index]['ori_name'] = $oName;
        }
        
        //Add File Temp Name To Array
        foreach($_FILES['file']['tmp_name'] as $index => $tmp) {
            $files[$index]['tmp_name'] = $tmp;
        }
        
        
        //Loop Files Array & Move Files To Temp Uploads
        foreach($files as $index => $file) {
            if(strpos($file['name'], '.') !== false) {
                $extensions = ['jpg', 'webp', 'png', 'gif'];
                
                if(in_array(explode('.', strtolower($file['name']))[1], $extensions)) {
                    if(move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $file['name'])) {
                        //chmod($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $index . '_' . $file['name'], 0666);
                        //array_push($json, $file['ori_name'] . ': Upload Succeeded.<br>');
                    }
                    else {
                        array_push($json, $file['ori_name'] . ': Upload failed, unknown error.<br>');
                    }
                }
                else {
                    array_push($json, $file['ori_name'] . ': Upload failed, file does not appear to be an image.<br>');
                }
            }
            else {
                array_push($json, $file['ori_name'] . ': Upload failed, check file extension.<br>');
            }
        }
        echo json_encode(implode($json));   
    }

?>