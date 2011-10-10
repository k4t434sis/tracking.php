<?php
	function strpos_arr($haystack, $needle)
    {
		if (!is_array($needle))
        {
        	$needle = array($needle);
        }
			foreach ($needle as $what)
            {
				if (($pos = strpos($haystack, $what)) !==false)
                {
                	return $pos;
                }
			}
			return false;
	}

     function getKeywords($raw_query)
     {
     	$_raw_query = $raw_query;
     	// Array of sites that use the q= query syntax

        $q_query_sites = array('google', 'bing.com', 'ask.com', 'search.aol.com', 'ehow.com', 'buzzle.com', 'optimum.net', 'metacrawler.com', 'search-results.com', 'search.avg.com', 'search.comcast.net', 'search.rr.com', '29searchengines.com');
        
        // Grab the http referer and filter the keywords out
        if (strpos_arr($raw_query, $q_query_sites) !== false)
        {
            $referer	= explode('/', $raw_query);
            $referer	= $referer[2];
            $query		= strstr($raw_query, 'q=');
            $query		= explode('&', $query);
            $query		= str_replace('q=', '', $query[0]);
            $keywords	= urldecode($query);
            $keywords	= explode(' ', $keywords);
            
            if ($referer == 'www.search-results.com')
            {
            	$referer = 'search.imesh.com';
            }
            
            if ($referer == 'search.comcast.net')
            {
                $x = sizeof($keywords);
                $keywords[($x-1)] = substr($keywords[($x-1)], 0, -1);
            }
        }
        elseif (strpos($raw_query, 'search.yahoo.com')) 
        {
            $referer	= 'search.yahoo.com';
            $query		= preg_match('/(p=(.*)&(toggle=|fr2=)|p=(.*)$)/', $raw_query, $matches);
            $matches[1]	= str_replace('%20', '+', $matches[1]);
            $_keywords	= explode('+', $matches[1]);
            
            foreach ($_keywords as $k)
            {
                if (strpos($k, 'p=') !== false)
                {
                    $k = explode('p=', $k);
                    $s = sizeof($k);
                    $k = $k[$s-1];
                }
                elseif (strpos($k, '&') !== false)
                {
                    $k = explode('&', $k);
                    $k = $k[0];
                }

                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'search.mywebsearch.com') !== false)
        {
            $referer	= 'search.mywebsearch.com';
            $query		= explode('searchfor=', $raw_query);
            $query		= explode('&', $query[1]);
            $_keywords	= explode('+', $query[0]);
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'yellowpages.superpages.com') !== false)
        {
            $referer	= 'yellowpages.superpages.com';     
            $query		= explode('?', $raw_query);
            $query		= explode('&', $query[1]);
            
            foreach ($query as $q)
            {
                if ($t = preg_match('/^C=(.*)$/', $q, $matches))
                {
                    $keywords[]	= urldecode($matches[1]);
                }
                elseif ($t = preg_match('/^N=(.*)$/', $q, $matches))
                {
                    $keywords[]	= urldecode($matches[1]);
                }
                elseif ($t = preg_match('/^E=(.*)$/', $q, $matches))
                {
                    $keywords[]	= urldecode($matches[1]);
                }
                elseif ($t = preg_match('/^L=(.*)$/', $q, $matches))
                {
                    $keywords[]	= urldecode($matches[1]);
                }
                
                $keywords = array_filter($keywords);
            }
            
            if (sizeof($keywords) == 1)
            {
                $keywords = explode(' ', $keywords[0]);
            }
        }
        elseif (strpos($raw_query, 'hotfrog.com') !== false)
        {
            $referer	= 'hotfrog.com';
            $query		= explode('?', $raw_query);
            $query		= explode('&', $query[1]);
            $query[0]	= urldecode(str_replace(array('hfsearch=', '+'), array('', ' '), $query[0]));
            $query[1]	= urldecode(str_replace(array('nearto=', '+'), array('', ' '), $query[1]));
            $query		= $query[0] . ' ' . $query[1];
            $_keywords	= explode(' ', $query);
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'webcrawler.com') !== false)
        {
            $referer	= 'webcrawler.com';
            $query		= str_replace('http://webcrawler.com/webcrawler_yaylf/ws/results/Web/', '', $raw_query);
            $query		= explode('/', $query);
            $_keywords	= explode(' ', urldecode($query[0]));
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'localsearch.com') !== false)
        {
            $referer	= 'localsearch.com';
            $_query 	= str_replace(array('business/', 'all/', 'people/', 'web/'), '', $raw_query);
            $query		= str_replace('http://www.localsearch.com/', '', $_query);
            $query		= substr($query, 0, -1);
            $query		= str_replace('/', '-', $query);
            $_keywords	= explode('-', $query);
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'switchboard.com') !== false)
        {
            $referer	= 'switchboard.com';
            $query		= explode('KW=', $raw_query);
            $query		= explode('&', $query[1]);
            
            foreach ($query as $q)
            {
                if (strpos($q, 'LO=') !== false)
                {
                    $_query		= str_replace('LO=', '', $q);
                }
            }
            
            $query[0]	= $query[0] . '+' . $_query;
            $_keywords	= explode('+', $query[0]);

            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'whitepages.com/business') !== false)
        {
            $referer	= 'whitepages.com';
            $query		= explode('?', $raw_query);
            $query		= explode('&', urldecode($query[1]));
            $query[0]	= str_replace('key=', '', $query[0]);
            $query[1]	= str_replace('where=', '', $query[1]);
            $_keywords	= explode(' ', $query[0] . ' ' . $query[1]);
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'search.myway.com') !== false
             || strpos($raw_query, 'searcher.com')	   !== false)
        {
            strpos($raw_query, 'search.myway.com') 
            ? $referer = 'search.myway.com' 
            : $referer = 'searcher.com';
            
            strpos($raw_query, 'search.myway.com')
            ? $query		= explode('searchfor=', $raw_query)
            : $query		= explode('search=', $raw_query);
            
            $query		= explode('&', $query[1]);
            $_keywords	= explode('+', $query[0]);
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'yandex.com') !== false)
        {
            $referer	= 'yandex.com';
            $query		= explode('?', $raw_query);
            $query		= explode('&', $query[1]);
            $_keywords	= explode('+', str_replace('text=', '', $query[0]));
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'dexknows.com') !== false)
        {
            $referer	= 'dexknows.com';
            $query		= preg_match('/\?what=(.*)\b&(suggestedWhat|where)/', $raw_query, $matches);
            $query		= $matches[1];
            $_query		= preg_match('/\&where=(.*)\b&(suggestedWhere|dksession)/', $raw_query, $_matches);
            $_query		= $_matches[1];
            $_keywords	= explode('+', $query . '+' . $_query);            

            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'brownbook.net') !== false)
        {
            $referer	= 'brownbook.net';
            $query		= strstr($raw_query, 'tag=');
            $query		= explode('&', $query);
            $query		= str_replace('tag=', '', $query[0]);
            $_keywords	= explode('+', $query);
            
            foreach ($_keywords as $k)
            {
                $keywords[] = urldecode($k);
            }
        }
        elseif (strpos($raw_query, 'blekko.com') !== false)
        {
            $referer	= 'blekko.com';
            $keywords	= explode('+', str_replace('http://blekko.com/ws/', '', $raw_query));
        }
        elseif (strpos($raw_query, 'yellowpages.com') !== false)
        {
            $referer	= 'yellowpages.com';
            $query		= str_replace('http://www.yellowpages.com/', '', $raw_query);
            $_keywords	= explode('?', $query);
            $_keywords	= explode('/', $_keywords[0]);
            $_keywords	= $_keywords[1] . '-' . $_keywords[0];
            $keywords = explode(' ', urldecode(str_replace('-', ' ', $_keywords)));
        }
        elseif (strpos($raw_query, 'search.juno.com') !== false)
        {
        	$referer	= 'search.juno.com';
            $keywords	= explode('&', $raw_query);            
            $keywords 	= explode('+', str_replace('query=', '', $keywords[2]));
        }
        
        $keywords['final'] = '';
        
        foreach ($keywords as $k)
        {
            $keywords['final'] .= $k . ' ';
        }

        (string) $keywords = $keywords['final'];
        
        if (!isset($referer))
        {
        	$referer = $_raw_query;

            if ($referer == ' ' || $referer == '')
            {
            	$referer = 'Direct Traffic';
            }
            else
            {
            	$referer = explode('/', $referer);
                $referer = '[referal]' . $referer[2];
            }
        }
        
        
        $referer = str_replace('www.', '', $referer);	// just in case that www escapes this filter
                                                       //  we need to keep out duplicate db referer entries
         
         // Make sure that if user didn't get here from a search engine that we
        //  count this as direct traffic
        if (is_array($keywords) || NULL == $keywords)
        {
            $keywords = NULL;
        }
        
        $response['referer']	= $referer;
        $response['keywords']	= $keywords;
        
        return $response;
    }

	/*********************************

    	END KEYWORTD TRACKING

    *********************************/

    

    /*********************************

    	START BROWSER/OS TRACKING

    *********************************/
	function getBrowserOs($ua)
    {
	    // Pulls the User Agent and Operating System from the user_agent string
        $os_f	= false;
        $br_f	= false;
        
        if (strpos($ua, 'Windows') !== false)
        {
            if (strpos($ua, 'Windows NT 5.1') !== false)
            {
                $os = 'Windows XP';
            }
            elseif (strpos($ua, 'Windows NT 5.2') !== false)
            {
                $os = 'Windows Server 2003/Windows XP x64 Edition';
            }
            elseif (strpos($ua, 'Windows NT 6.0') !== false)
            {
                $os = 'Windows Vista';
            }
            elseif (strpos($ua, 'Windows NT 6.1') !== false)
            {
                $os = 'Windows 7';
            }
        }
        elseif (strpos($ua, 'Linux') !== false		// Android is also a linux OS
             && strpos($ua, 'Android') === false)  // Make sure this is really linux
        {
            $os = 'Linux';
        }
        elseif (strpos($ua, 'OS X') !== false)
        {
            if (strpos($ua, 'iPad') !== false)
            {
                $os = 'iPad';
            }
            elseif (strpos($ua, 'iPhone') !== false)
            {
                $os = 'iPhone';
            }
            else
            {
                $os = 'Mac';
            }
        }
        elseif (strpos($ua, 'Android') !== false)
        {
            if (strpos($ua, 'Android 1.6') !== false)
            {
                $os = 'Android 1.6';
            }
            elseif (strpos($ua, 'Android 2.1-update1') !== false)
            {
                $os = 'Android 2.1-update1';
            }
            elseif (strpos($ua, 'Android 2.2') !== false)
            {
                $os = 'Android 2.2';
            }
        }
        elseif (strpos($ua, 'BlackBerry') !== false)
        {
            $os = 'Blackberry';
        }
        else
        {
            $os = 'Unrecognized OS, possibly UNIX based';
            $os_f = true;
        }
        
        // Let's now get the browser and browser version from the UA string
        if (strpos($ua, 'Firefox') !== false)
        {
            $br = 'Firefox v'.substr($ua, strpos($ua, 'Firefox') + 8, 3);
        }
        elseif (strpos($ua, 'MSIE') !== false)
        {
            $br = 'Internet Explorer v'.substr($ua, strpos($ua, 'MSIE') + 5, 3);
        }
        elseif (strpos($ua, 'Opera') !== false)
        {
            $br = 'Opera v'.substr($ua, strpos($ua, 'Opera') + 6, 3);
        }
        elseif (strpos($ua, 'Chrome') !== false)
        {
            $br = 'Chrome v'.substr($ua, strpos($ua, 'Chrome') + 7, 2);
        }
        elseif (strpos($ua, 'Safari') !== false)
        {
            $br = 'Safari v'.substr($ua, strpos($ua, 'Version') + 8, 3);
        }
        else
        {
            if (strpos($ua, 'BlackBerry') !== false)
            {
                $br = 'Blackberry proprietary browser';
            }
            else
            {
                $br = 'Unrecognized Browser';
                $br_f = true;
            }
        }
        
        
        $user_agent	 = 'Operating System: '.$os."\r\n";
        $user_agent .= 'Browser Platform: '.$br."\r\n";
        $user_agent .= 'HTTP User Agent String: '.$ua;
        
        $response['os']				= $os;
        $response['browser']		= $br;
        $response['ua-formatted']	= $user_agent;

        return $response;
    }

    /*********************************

    	END BROWSER/OS TRACKING

    *********************************/
?>