<?php    

    if(empty($_FILES['file'])) {
        echo json_encode('No files selected.');
    }
    else {
        $files = [];
        $json = [];
        
        //Delete All Files In Temp Uploads
        $tempDir = glob($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/*');
        
        foreach($tempDir as $tempFile) {
            if(is_file($tempFile)) {
                unlink($tempFile);
            }
        }
        
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
            if(move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $index . '_' . $file['name'])) {
                //chmod($_SERVER['DOCUMENT_ROOT'] . '/admin/images/tempuploads/' . $index . '_' . $file['name'], 0666);
                //array_push($json, $file['ori_name'] . ': Upload Succeeded.<br>');
            }
            else {
                array_push($json, $file['ori_name'] . ': Upload Failed.<br>');
            }
        }
        echo json_encode(implode($json));   
    }

?>