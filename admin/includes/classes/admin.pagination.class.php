<?php

    class pagination {
        public $firstPage = 1;
        public $lastPage;
        public $currentPage;
        public $itemLimit = 8;
        public $pageLimit = 9;
        public $showFirst = true;
        public $showLast = true;
        public $showNext = true;
        public $showPrev = true;
        public $showPageNumbers = true;
        public $i;
        public $offset;
        public $items = 0;
        public $prefix = '?';
        
        function __construct($last) {
            if($last != null) {
                $this->lastPage = $last;
                $this->items = $last;
            }
            
            if(isset($_GET['category']) && !isset($_GET['page'])) {
                $this->prefix = $_SERVER['REQUEST_URI'] . '/';
            }
            
            //Remove existing page query
            $this->prefix = preg_replace('/(\?|\&)page=[0-9]/', '', $_SERVER['REQUEST_URI']);
            
            //Change first & to ?
            $this->prefix = (strpos($this->prefix, '?') === false ? preg_replace('/\&/', '\?', $this->prefix, 1) : $this->prefix);
            
            //Append ? or & 
            $this->prefix = $this->prefix . (strpos($this->prefix, '?') !== false ? '&' : '?');
        }
        
        function setFirstPage($page = 1) {
            $this->firstPage = $page;
        }
        
        function setLastPage($page = 1) {
            $this->lastPage = $page;
        }
        
        function setItemLimit($limit = 10) {
            if($limit < 1) {
                $limit = 1;
            }
            
            $this->itemLimit = $limit;
        }
        
        function setPageLimit($limit = 9) {
            if($limit < 1) {
                $limit = 1;
            }
            
            $this->pageLimit = $limit;
        }
        
        function showPageNumbers($show = true) {
            $this->showPageNumbers = $show;
        }
        
        function showFirstLast($show = true) {
            $this->showFirst = $show;
            $this->showLast = $show;
        }
        
        function showNextPrevious($show = true) {
            $this->showNext = $show;
            $this->showPrev = $show;
        }
        
        function load() {
            if(isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] != null) {
                $this->currentPage = $_GET['page'];
            }
            else {
                $this->currentPage = $this->firstPage;
            }
            
            if($this->currentPage < $this->firstPage) {
                $this->currentPage = $this->firstPage;
            }
            
            if($this->currentPage > $this->pageLimit) {
                $this->i = $this->currentPage - $this->pageLimit;
            }
            else {
                $this->i = $this->firstPage;
            }
            
            if($this->lastPage != null) {
                $this->lastPage = ceil($this->lastPage / $this->itemLimit);
            }
            
            if(isset($_GET['page']) && $_GET['page'] > $this->lastPage) {
                $this->currentPage = $this->lastPage;
            }
            
            if($this->currentPage <= $this->firstPage) {
                $this->showFirst = false;
                $this->showPrev = false;
            }
            
            if($this->currentPage >= $this->lastPage) {
                $this->showLast = false;
                $this->showNext = false;
            }
            
            $this->offset = ($this->currentPage * $this->itemLimit) - $this->itemLimit;
        }
        
        function display() {
            if(($this->lastPage != null) && ($this->items > $this->itemLimit)) {
                if($this->currentPage <= $this->firstPage) {
                    $prevPage = $this->firstPage;
                }
                else {
                    $prevPage = $this->currentPage - 1;
                }
                
                if($this->currentPage >= $this->lastPage) {
                    $nextPage = $this->lastPage;
                }
                else {
                    $nextPage = $this->currentPage + 1;
                }
                
                $output = '<div class="pagination">';
                
                    if($this->showFirst == true) {
                        $output .= '<a href="' . $this->prefix . 'page=' . $this->firstPage . '"><< First</a>';
                    }
                
                    if($this->showPrev == true) {
                        $output .= '<a href="' . $this->prefix . 'page=' . $prevPage . '">< Prev</a>';
                    }
                
                    if($this->showPageNumbers == true) {
                        $end = $this->currentPage + $this->pageLimit;
                        
                        if($end >= $this->lastPage) {
                            $end = $this->lastPage;
                        }
                        
                        if($this->i <= $this->firstPage) {
                            $this->i = $this->firstPage;
                        }
                        
                        for($this->i; $this->i <= $end; $this->i++) {
                            $output .= '<a href="' . $this->prefix . 'page=' . $this->i . '">' . $this->i . '</a>';
                        }
                    }
                
                    if($this->showNext == true) {
                        $output .= '<a href="' . $this->prefix . 'page=' . $nextPage . '">Next ></a>';
                    }
                
                    if($this->showLast == true) {
                        $output .= '<a href="' . $this->prefix . 'page=' . $this->lastPage . '">Last >></a>';
                    }
                
                $output .= '</div>';
                
                return $output;
            }
        }
        
        function debug() {
            echo 'First Page: ' . $this->firstPage . '<br>' . 
                 'Last Page: ' . $this->lastPage . '<br>' . 
                 'Current Page: ' . $this->currentPage . '<br>' . 
                 'Items Per Page: ' . $this->itemLimit . '<br>' . 
                 'Page Numbers to Display: ' . $this->pageLimit . '<br>' . 
                 'Offset: ' . $this->offset . '<br>' . 
                 'Integer: ' . $this->i . '<br>' .
                 'Show First: ' . $this->showFirst . '<br>' . 
                 'Show Last: ' . $this->showLast . '<br>';
        }
    }

?>