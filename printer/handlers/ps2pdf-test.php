<?php

	  //.....................using curl
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
      curl_setopt($ch, CURLOPT_URL, "http://ps2pdf.com/convert.cgi");
	  curl_setopt($ch, CURLOPT_REFERER, "http://ps2pdf.com/convert.cgi");
	  curl_setopt($ch, CURLOPT_COOKIE, "ukey=G314J828");
      curl_setopt($ch, CURLOPT_POST, true);
      // same as <input type="file" name="file_box">
      $post = array(
        "inputfile"=>'@'.$_SERVER['DOCUMENT_ROOT'] .'/jobs/mytest.ps'
      );
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 
      $response = curl_exec($ch);
	  echo $response;
	  die();

      include('http_client/http.php');

	  set_time_limit(0);
	  
	  $http=new http_client;
	  $http->timeout=0;
	  $http->data_timeout=0;
	  $http->debug=0;
	  $http->html_debug=1;	  
	  
	  $url="http://ps2pdf.com/convert.cgi";
	  $error=$http->GetRequestArguments($url,$arguments);
	  $arguments["RequestMethod"]="POST";
	  /*$arguments["PostValues"]=array(
		"somefield"=>"Upload forms",
		"MAX_FILE_SIZE"=>"1000000"
	  );*/
	  
	  $filename = $_SERVER['DOCUMENT_ROOT'] .'/jobs/job-25-79.103.66.105-billy-def_pic.gif.ps';
	  echo $filename;
	  
	  $arguments["PostFiles"]=array(
		"inputfile"=>array(
			"filename"=>$filename,
			"Name"=>"inputfile",
			"Content-Type"=>"automatic/name",
		)/*,
		"anotherfile"=>array(
			"FileName"=>"test_http_post.php",
			"Content-Type"=>"automatic/name",
		)*/
	  );
	  $arguments["Referer"]="http://www.alltheweb.com/";
	  $ret .= "<H2><LI>Opening connection to:</H2>\n<PRE>".HtmlEntities($arguments["HostName"])."</PRE>\n";
	  flush();
	  $error=$http->Open($arguments);	  
	  
	  if($error=="")
	  {
		$error=$http->SendRequest($arguments);
		if($error=="")
		{
			$ret .= "<H2><LI>Request:</LI</H2>\n<PRE>\n".HtmlEntities($http->request)."</PRE>\n";
			$ret .= "<H2><LI>Request headers:</LI</H2>\n<PRE>\n";
			for(Reset($http->request_headers),$header=0;$header<count($http->request_headers);Next($http->request_headers),$header++)
			{
				$header_name=Key($http->request_headers);
				if(GetType($http->request_headers[$header_name])=="array")
				{
					for($header_value=0;$header_value<count($http->request_headers[$header_name]);$header_value++)
						$ret .= $header_name.": ".$http->request_headers[$header_name][$header_value]."\r\n";
				}
				else
					$ret .= $header_name.": ".$http->request_headers[$header_name]."\r\n";
			}
			$ret .= "</PRE>\n";
			$ret .= "<H2><LI>Request body:</LI</H2>\n<PRE>\n".HtmlEntities($http->request_body)."</PRE>\n";
			flush();

			$headers=array();
			$error=$http->ReadReplyHeaders($headers);
			if($error=="")
			{
				$ret .= "<H2><LI>Response headers:</LI</H2>\n<PRE>\n";
				for(Reset($headers),$header=0;$header<count($headers);Next($headers),$header++)
				{
					$header_name=Key($headers);
					if(GetType($headers[$header_name])=="array")
					{
						for($header_value=0;$header_value<count($headers[$header_name]);$header_value++)
							$ret .= $header_name.": ".$headers[$header_name][$header_value]."\r\n";
					}
					else
						$ret .= $header_name.": ".$headers[$header_name]."\r\n";
				}
				$ret .= "</PRE>\n";
				flush();

				$ret .= "<H2><LI>Response body:</LI</H2>\n<PRE>\n";
				for(;;)
				{
					$error=$http->ReadReplyBody($body,1000);
					if($error!=""
					|| strlen($body)==0)
						break;
					$ret .= HtmlSpecialChars($body);
				}
				$ret .= "</PRE>\n";
				flush();
			}
		}
		$http->Close();
	  }
	  if(strlen($error))
		$ret .= "<CENTER><H2>Error: ".$error."</H2><CENTER>\n";	  
    
	echo $ret;
?>