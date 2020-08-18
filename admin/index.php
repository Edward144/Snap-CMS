<?php require_once('includes/header.php'); ?>

<div class="container-fluid d-flex">                    
    <div class="row flex-grow-1">        
        <div class="col-lg-5 py-3 order-lg-1 flex-grow-1">
            <div class="jumbotron py-4 bg-light">
                <h3 class="mb-sm-3 mb-5">Content Created</h3>
                
                <div id="contentChart"></div>
            </div>
            
            <?php 
                $postTypes = $mysqli->query("SELECT id, name FROM `post_types` ORDER BY id ASC");
                
                if($postTypes->num_rows > 0) : 
                
                    while($type = $postTypes->fetch_assoc()) :
                        $content = $mysqli->query(
                            "SELECT content.id, content.name, content.last_edited, `users`.username, post_types.name AS post_type FROM `posts` AS content
                            LEFT OUTER JOIN `post_types` ON content.post_type_id = `post_types`.id
                            LEFT OUTER JOIN `users` ON `users`.id = content.last_edited_by
                            WHERE `post_types`.id = {$type['id']} ORDER BY `post_types`.id, content.last_edited DESC LIMIT 5"); 
            
                        if($content->num_rows > 0) : ?>
                            <div class="jumbotron py-4 bg-light"><h3 class="mb-3"><?php echo ucwords(str_replace('-', ' ', $type['name'])); ?>: Latest Edits</h3>
                                <ul class="list-group">
                                    <?php while($post = $content->fetch_assoc()) : ?>
                                        <li class="list-group-item">
                                            <div>
                                                <a href="<?php echo ROOT_DIR; ?>admin/manage-content/<?php echo strtolower(str_replace(' ', '-', $type['name'])) . '?id=' . $post['id']; ?>"><?php echo $post['name']; ?></a>
                                            </div>
                                            <div>Last edited at <?php echo date('d/m/Y H:i', strtotime($post['last_edited'])) . ' by ' . $post['username']; ?></div>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                    <?php endif; ?>
                <?php endwhile; ?> 
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?php echo ROOT_DIR; ?>js/apex-charts/apexcharts.min.js"></script>

<?php
    $postTypes = $mysqli->query("SELECT id, name FROM `post_types` ORDER BY id ASC"); 
    
    $series = [];
    $labels = [];

    if($postTypes->num_rows > 0) {
        while($type = $postTypes->fetch_assoc()) {            
            $count = $mysqli->query("SELECT COUNT(*) FROM `posts` WHERE post_type_id = {$type['id']}");
            $count = ($count->num_rows > 0 ? $count->fetch_array()[0] : 0);
            
            array_push($labels, ucwords(str_replace('-', ' ', $type['name'])));
            array_push($series, $count);
        }
    }
    
    $series = implode(',', $series);
    $labels =  '\'' . implode('\',\'', $labels) . '\'';
?>

<script>
    var options = {
        chart: {
            type: 'donut',
            width: '400px',
            toolbar: {
                show: false
            }
        },
        series: [<?php echo $series; ?>],
        labels: [<?php echo $labels; ?>],
        dataLabels: {
            formatter: function (val, opts) {
                return opts.w.config.series[opts.seriesIndex]
            },
        },
        responsive: [
            {
                breakpoint: 1700,
                options: {
                    chart: {
                        width: '300px'
                    },
                    legend: {
                        position: 'top'
                    }
                }
            },
            {
                breakpoint: 577,
                options: {
                    chart: {
                        width: '240px'
                    }
                }
            },
            {
                breakpoint: 321,
                options: {
                    chart: {
                        width: '200px'
                    }
                }
            }
        ]
    }
    
    var chart = new ApexCharts(document.getElementById("contentChart"), options);
    
    chart.render();
</script>

<?php require_once('includes/footer.php'); ?>