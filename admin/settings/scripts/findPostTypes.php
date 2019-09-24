<?php

    require_once($_SERVER['DOCUMENT_ROOT'] . '/templates/database_connect.php');
    
    $json = [];

    //Get Pages
    $pages = $mysqli->query("SELECT name, url FROM pages");

    if($pages->num_rows > 0) {
        array_push($json, 
            '<option value="pages;Pages;" id="group">Pages</option>'      
        );
        
        while($row = $pages->fetch_assoc()) {
            array_push($json, 
                '<option value="pages;' . $row['name'] . ';' . $row['url'] . '">&nbsp;&nbsp;' . $row['name'] . '</option>'      
            );
        }
    }

    //Get Posts
    $posts = $mysqli->query("SELECT name, url FROM pages");

    if($posts->num_rows > 0) {
        array_push($json, 
            '<option value="posts;Posts;" id="group">Posts</option>'      
        );
        
        while($row = $pages->fetch_assoc()) {
            array_push($json, 
                '<option value="posts;' . $row['name'] . ';' . $row['url'] . '">&nbsp;&nbsp;' . $row['name'] . '</option>'      
            );
        }
    }

    //Get Custom
    $postTypes = $mysqli->query("SELECT name FROM `custom_posts`");

    if($postTypes->num_rows > 0) {
        while($row = $postTypes->fetch_assoc()) {
            $postType = str_replace('_', '-', $row['name']);
            $oPostType = $row['name'];

            array_push($json, 
                '<option value="' . $postType . ';' . ucwords(str_replace('-', ' ', $postType)) . ';" id="group">' . ucwords(str_replace('-', ' ', $postType)) . '</option>'      
            );
           
            $subPages = $mysqli->query("SELECT name, url FROM `{$oPostType}`");
            
            if($subPages->num_rows > 0) {
                while($row = $subPages->fetch_assoc()) {
                    array_push($json,
                        '<option value="' . $postType . ';' . $row['name'] . ';' . $row['url'] . '">&nbsp;&nbsp;' . $row['name'] . '</option>' 
                    );
                }
            }
        }
    }

    
    echo json_encode(implode($json));

?>