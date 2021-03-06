<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination {
	
	var $offset 			= '';
	var $next_link			= 'Next &rsaquo;';
	var $prev_link			= '&lsaquo; Previous';
	
	function MY_Pagination() {
		parent::CI_Pagination();
	}
	
	
	
	
	function limit() {
		return $this->per_page;
	}
	
	
	
	
	function offset(){
	
		$CI =& get_instance();
		$current_page = 1; 
		
		if($CI->uri->segment($this->uri_segment) != '')
			$current_page = preg_replace('/[^0-9]/', '', $CI->uri->segment($this->uri_segment));
			
		return (int) $this->per_page * ((int) $current_page - 1);
	}
	
	
    /**
     * Generate the pagination links
     *
     * @access    public
     * @return    string
     */
         
    function create_links()
    {
        // If our item count or per-page total is zero there is no need to continue.
        if ($this->total_rows == 0 OR $this->per_page == 0)
        {
           return '';
        }

        // Calculate the total number of pages
        $num_pages = ceil($this->total_rows / $this->per_page);

        // Is there only one page? Hm... nothing more to do here then.
        if ($num_pages == 1)
        {
            return '';
        }

        // Determine the current page number.        
        $CI =& get_instance();    
        if ($CI->uri->segment($this->uri_segment) != '')
        {
            $this->cur_page = $CI->uri->segment($this->uri_segment);
            
            // Prep the current page - no funny business!
            $this->cur_page = preg_replace('/[^0-9]/', '', $this->cur_page);
        }
        else
        {
            $this->cur_page = 1;
        }
        
                
        if ( ! is_numeric($this->cur_page))
        {
            $this->cur_page = 0;
        }
        
        // Is the page number beyond the result range?
        // If so we show the last page
        if ($this->cur_page > $num_pages)
        {
            $this->cur_page = $num_pages;
        }
        
        $uri_page_number = $this->cur_page;
        //$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);


        // Add a trailing slash to the base URL if needed
        $this->base_url = preg_replace("/(.+?)\/*$/", "\\1/",  $this->base_url);
		
		
		// Cater for searching.
		$query_string = '';
		$q = $CI->input->get('q');
		if(!empty($q))
			$query_string = '?q='.urlencode($q);
		
        
          // And here we go...
        $pagination = '';
        /*
        // Render the "First" link
        if ($this->cur_page > 1)
        {
            $pagination .= $this->first_tag_open.'<a href="'.substr($this->base_url, 0, -1).$query_string.'">'.$this->first_link.'</a>'.$this->first_tag_close;
        }
        else
            $pagination .= '<span class="disabled">'.$this->first_link.'</span>';
        
		*/
        // Render the "previous" link
        if ($this->cur_page > 1)
        {
            $prev = $this->cur_page - 1;
            $pagination .= $this->prev_tag_open.'<a href="'.$this->base_url.'page'.$prev.$query_string.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
        }
        else
        	$pagination .= '<span class="disabled">'.$this->prev_link.'</span>';
        
        
        if ($num_pages < 7 + ($this->num_links * 2))    //not enough pages to bother breaking it up
        {
            for ($counter = 1; $counter <= $num_pages; $counter++)
            {
                if ($counter == $this->cur_page)
                {
                    $pagination .= '<span class="current">'.$counter.'</span>'; // Current page
                }
                else
                {
                    $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$counter.$query_string.'">'.$counter.'</a>'.$this->num_tag_close;
                }
            }
        }
        elseif($num_pages > 5 + ($this->num_links * 2))    //enough pages to hide some
        {
            //close to beginning; only hide later pages
            if($this->cur_page < 1 + ($this->num_links * 2))
            {
                for ($counter = 1; $counter < 4 + ($this->num_links * 2); $counter++)
                {
                    if ($counter == $this->cur_page)
                    {
                        $pagination .= '<span class="current">'.$counter.'</span>'; // Current page
                    }
                    else
                    {
                        $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$counter.$query_string.'">'.$counter.'</a>'.$this->num_tag_close;
                    }
                }
                
                $pagination .= '<span class="spacer">...</span>';
                
                $num_pages_minus = $num_pages-1;
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$num_pages_minus.$query_string.'">'.$num_pages_minus.'</a>'.$this->num_tag_close;
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$num_pages.$query_string.'">'.$num_pages.'</a>'.$this->num_tag_close;    
            }
            //in middle; hide some front and some back
            elseif($num_pages - ($this->num_links * 2) > $this->cur_page && $this->cur_page > ($this->num_links * 2))
            {
                $one=1;
                $two=2;
        
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$one.$query_string.'">'.$one.'</a>'.$this->num_tag_close;
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$two.$query_string.'">'.$two.'</a>'.$this->num_tag_close;
                
                $pagination .= '<span class="spacer">...</span>';
                
                for ($counter = $this->cur_page - $this->num_links; $counter <= $this->cur_page + $this->num_links; $counter++)
                {
                    if ($counter == $this->cur_page)
                    {
                        $pagination.= '<span class="current">'.$counter.'</span>';
                    }
                    else
                    {
                        $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$counter.$query_string.'">'.$counter.'</a>'.$this->num_tag_close;
                    }
                }
                $pagination.= '<span class="spacer">...</span>';
        
                $num_pages_minus = $num_pages-1;
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$num_pages_minus.$query_string.'">'.$num_pages_minus.'</a>'.$this->num_tag_close;
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$num_pages.$query_string.'">'.$num_pages.'</a>'.$this->num_tag_close;    
            }
            //close to end; only hide early pages
            else
            {
                $one=1;
                $two=2;
        
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$one.$query_string.'">'.$one.'</a>'.$this->num_tag_close;
                $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$two.$query_string.'">'.$two.'</a>'.$this->num_tag_close;
                
                $pagination.= '<span class="spacer">...</span>';
                
                for ($counter = $num_pages - (2 + ($this->num_links * 2)); $counter <= $num_pages; $counter++)
                {
                    if ($counter == $this->cur_page)
                    {
                        $pagination.= '<span class="current">'.$counter.'</span>';
                    }
                    else
                    {
                        $pagination .= $this->num_tag_open.'<a href="'.$this->base_url.'page'.$counter.$query_string.'">'.$counter.'</a>'.$this->num_tag_close;
                    }
                }
            }
        }
        
        // Render the "next" link
        if ($this->cur_page < $counter - 1)
        {
            $next = $this->cur_page + 1;
            $pagination .= $this->next_tag_open.'<a href="'.$this->base_url.'page'.$next.$query_string.'">'.$this->next_link.'</a>'.$this->next_tag_close;
        }
        else
        	$pagination .= '<span class="disabled">'.$this->next_link.'</span>';
        
        /*
        // Render the "Last" link
        if ($this->cur_page < $counter - 1)
        {
            $pagination .= $this->last_tag_open.'<a href="'.$this->base_url.'page'.$num_pages.$query_string.'">'.$this->last_link.'</a>'.$this->last_tag_close;
        }
        else
            $pagination .= '<span class="disabled">'.$this->last_link.'</span>';
		*/

        // Kill double slashes.  Note: Sometimes we can end up with a double slash
        // in the penultimate link so we'll kill all double slashes.
        $pagination = preg_replace("#([^:])//+#", "\\1/", $pagination);

        // Add the wrapper HTML if exists
        $pagination = $this->full_tag_open.$pagination.$this->full_tag_close;
        
        return $pagination;        
    }
}

?>