<?php

class rsdns{

 private $url;
 private $username;
 private $key;
 private $token = '0';
 public $expires = '0';
 public $account;
 private static $put    = 1;
 private static $post   = 2;
 private static $delete = 3;
 private static $get    = 4;

 #debugging true
 //public $debug = 2;

 public function __construct($username,$key,$uk)
 {
  session_start();

  $this->username = $username;
  $this->key = $key;

  #check to see if you have the token in this session. And that it's not expired yet

  if(isset($_SESSION['token']) && time() < strtotime($_SESSION['expires']))
  {
    if(isset($this->debug)){print "found the session<br />";}
    if(isset($this->debug)){print (strtotime($_SESSION['expires']) - time())/3600  . "<br />";}
    $this->token = $_SESSION['token'];
    $this->expires = $_SESSION['expires'];
    $this->account = $_SESSION['account'];
    $this->url = $_SESSION['url'];
  }
  else
  {
    //This section is to actually get a token if one is not in our session.
    if(isset($this->debug)){print "did not find the session<br />";}
    if(isset($uk) && $uk == 1)
    {
      $Auth_url = "https://lon.auth.api.rackspacecloud.com/v1.1/auth.json";
	  $this->url = "https://lon.dns.api.rackspacecloud.com/v1.0/";
    }
    else
    {
      $Auth_url = "https://auth.api.rackspacecloud.com/v1.1/auth.json";
	  $this->url = "https://dns.api.rackspacecloud.com/v1.0/";
    }

    $auth =  json_encode( 
			array('credentials'=> array(
						    'username'=>$this->username,
						    'key'=>$this->key	
						   )
			     )
			);
   
	// use curl!
    $ch = curl_init($Auth_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json; charset=utf-8"));
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $auth);
    $result = curl_exec($ch);
    curl_close($ch);


    $auth_obj = json_decode($result);

    #if(isset($this->debug)){$this->traverse($auth_obj,process_key_value);}
    #if(isset($this->debug)){print var_dump($result) . "<br />";}
	
    $pattern = '/\"id\":\"(.*?)\"/';
    $pattern2 = '/\"expires\":\"(.*?)\"/';
    $pattern3 = '/\/([0-9]{6})/';
	
    $num = preg_match($pattern, $result, $matches);
    preg_match($pattern2, $result, $matches2);
    preg_match($pattern3, $result, $matches3);
    
    # check to see if the token is cached.
    if( $num > 0) 
    {
      $this->token   = $matches[1];
      $this->expires = $matches2[1];
      $this->account = $matches3[1];
    }
    else
    {    die("Could not get Auth token:");   }


    $_SESSION['token']   = $this->token ;
    $_SESSION['expires'] = $this->expires ;
    $_SESSION['account'] = $this->account ;
    $_SESSION['url']     = $this->url ;
   }
  }

     //note is not capable of dealing with flat strings instead of key-value pairs.
     public function traverse($o,$pad=0){
      $padding = $pad;
      $space = '&nbsp;';
      if(! isset($o)){
	 print "Could not traverse " . "<br />";
      }else{
	foreach ($o as $name=>$value){
	      if    (  is_object  ($value)) { $type = 1; } 
	      elseif(  is_string  ($value)) { $type = 2; }
	      elseif(  is_array   ($value)) { $type = 3; }
	      elseif(  is_integer ($value)) { $type = 4; }
	      
	      switch ($type) {
		case 1:			#object
		      print str_repeat($space,$padding*20) . $name . "{<br />";
		      $this->traverse($value,$padding+1);
		      print str_repeat($space,$padding*20) . "}<br />";
		      break;
		case 2:			#string
		      $this->process_key_value($name,$value,$padding);
		      break;
		case 3:			#array
		      $this->process_array($name,$value,$padding);
		      break;
		case 4:			#integer
		      print str_repeat($space,$padding*20) . $name . " : " . $value .  "<br />";
		      break;
	      }
	}
      }
     }

    //called only on string pairs.
    private function process_key_value($key,$value,$padding){
	  print str_repeat('&nbsp;',$padding*20) . $key . " : " . $value . "<br />";
    }

    //called on Arrays to assist further traversing.
    private function process_array($name,$array,$pad){
      print str_repeat('&nbsp;',$pad*20) . $name . " : <br />";
      $pad += 1;
      print str_repeat('&nbsp;',$pad*20) . "[ <br />";
      foreach($array as $value) {
          //this is where i need to implement the tests for the contents of the array
	  //before traversing it becomes a problem.
	  if    (  is_object  ($value)) { $this->traverse($value,$pad); } 
	  elseif(  is_string  ($value)) { print str_repeat('&nbsp;',$pad*20) . $value; }
	  elseif(  is_array   ($value)) { $this->process_array($value,$pad); }
	  elseif(  is_integer ($value)) { print str_repeat('&nbsp;',$pad*20) . $value; }
	  print "<br />";
      }
      print str_repeat('&nbsp;',$pad*20) . "] <br />";
    }

    //Asynchronous calls only (PUT, POST, DELETE)
    private function send($request,$verb,$payload='',$payload_size=0){
	    //take the user info and run the request
	    //$debug = true;

	    if(isset($debug)){print $this->url . $this->account . '/' . $request ;}
	    if(isset($debug)){print "<br />verb " . $verb . "<br />";}
	    if(isset($debug)){print "token " . $this->token . "<br />";}

	    $ch = null;
	    $ch = curl_init($this->url . $this->account . '/' . $request);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(("X-Auth-Token: " . $this->token)));

	    //currently curl will encrypt but not verify the certificate properly.
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	    switch ($verb){
		    case 1:
			    print "1 <br />";
			    //CURLOPT_PUT 	TRUE to HTTP PUT a file. The file to PUT must be set with CURLOPT_INFILE and CURLOPT_INFILESIZE. 
			    curl_setopt($this->ch, CURLOPT_PUT, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(("X-Auth-Token: " . $this->token),('Content-Length: ' . strlen($payload))));
			    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
			    break;
		    case 2:
			    print "2 <br />";
			    //POST REQUEST	
			    curl_setopt($this->ch, CURLOPT_POST, true);
			    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $payload);
			    break;
		    case 3:
			    print "3 <br />";
			    // DELETE REQUEST
			    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "DELETE");
			    break;
		    case 4:
			    print "4 <br />";
			    break;
		    }

	    //return the results of the request
	    $result = curl_exec($ch);
		//get the http status code
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    if(isset($debug)){print "Curl error: " . var_dump(curl_getinfo($ch)) . "<br />";}
	    $answer_obj = json_decode($result);
	    if(isset($debug)){print curl_getinfo($ch); }
	    #if(isset($debug)){$this->traverse($answer_obj);}
	    if(isset($debug)){print var_dump($answer_obj) . "<br />";}	

	    curl_close($ch);
	    return $result;
    }
 

    private function status($callBackObject,$keepAlive=0,$ShowErrors=1,$ShowDetails=1){
		//$debug = true;
		$CallBack = json_decode($callBackObject);

		#if(isset($debug)){print $CallBack->status 		. "<br />";}
		#if(isset($debug)){print $CallBack->jobId 		. "<br />";}
		#if(isset($debug)){print $CallBack->callbackUrl 	. "<br />";}
		#if(isset($debug)){print $CallBack->requestUrl 	. "<br />";}		

		$url = $CallBack->callbackUrl;
		
    	if($ShowErrors == 1){
			$url = $url . "?showErrors=true";
		}else{
			$url = $url . "?showErrors=false";
		}
		if($ShowDetails == 1){
			$url = $url . "&showDetails=true";
		}

		//take the user info and run the request
		
		$ch = null;
		$finished = false;
		$count = 0;
	    while( ! $finished){
			$count ++;
			$ch = curl_init($url);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(("X-Auth-Token: " . $this->token)));
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			$result = curl_exec($ch);
			$answer_obj = json_decode($result);
			
			//get the http status code
			$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
			if(isset($debug)){print "Curl error: ";	var_dump(curl_error($ch)); print "<br />";}
			if(isset($debug)){print "HI: "; var_dump(curl_getinfo($ch)); print '<br />'; }
			if(isset($debug)){$this->traverse($answer_obj);}
			if(isset($debug)){print var_dump($answer_obj) . "<br />";}	

			curl_close($ch);
			
			if($answer_obj->status == 'ERROR' OR $answer_obj->status == 'COMPLETED' OR $count >= 10){
				$finished = true;
			}else{
				if($keepAlive){
					print '.';
					flush();	
				}
				sleep(2);
			}
		}
	    return $result;
   }
 
#+++++++++++++++++  LIMITS  ++++++++++++++++++++++++
   private function limits($addrequest=''){

    //build the request type
    $request = 'limits' . '/' . $addrequest;

    //get the answer for this type of request
    return $this->send($request,4);

   }
#==============  PUBLIC  ==========================
    public function limit_all(){
      //build specialized request type
      return $this->limits();
    }
    public function limit_types(){
      //build specialized request type
      return $this->limits('types');
    }
    public function limit_check($type){
      //build specialized request type
      return $this->limits($type);
    }
#++++++++++++++++  DOMAINS   ++++++++++++++++++++++
    private function domains($addrequest='',$verb,$payload=''){

	$request = 'domains' . $addrequest;
	return $this->send($request,$verb,$payload);

    }    
#==============  PUBLIC  ==========================
    public function domain_list(){
      //build specialized request type
      $request = '';
      return $this->domains($request,$this->get);
    }
    public function domain_search($search){
      $request = '?﻿name=' . $search;
      return $this->domains($request,$this->get);
    }
    public function domain_deatils($domainID,$showRecords=true,$showSubdomains=true){
      $domainID = '/' . $domainID;

      if($showRecords == true && $showSubdomains == true){
	$request = $domainID . '?showRecords=true&showSubdomains=true';
      }elseif($showRecords == true){
	$request = $domainID . '?showRecords=true&showSubdomains=false'; 
      }else{
	$request = $domainID . '?showRecords=false&showSubdomains=true';
      }

      return $this->domains($request,$this->get);
    }
    public function domain_changes($domainID,$time=0){
      $domainID = '/' . $domainID;

      if($time == 0){
	$request = $domainID . '/changes';
      }else{
	$time =  "?since=$time";
	$request = $domainID . '/changes' . $time;
      }
      return $this->domains($request,$this->get);
    }
    public function domain_export($domainID,$keepAlive=0){
      $domainID = '/' . $domainID;
      $request = $domainID . '/export';
      return $this->status($this->domains($request,$this->get),$keepAlive);

    }
	public function domain_create($config,$keepAlive=0){
	  $request = '';
	  $callBack_obj = $this->domains($request,$this->post,$config);
	  return $this->status($callBack_obj,$keepAlive);
	}
	public function domain_import($full_txt,$keepAlive=0){
	  $request = '/import';
	  $callBack_obj = $this->domains($request,$this->post,$full_txt);
	  return $this->status($callBack_obj,$keepAlive);
	}
	public function domain_modify($domainID,$config,$keepAlive=0){
	  $request = '/' . $domainID;
	  $callBack_obj = $this->domains($request,$this->put,$config);
	  return $this->status($callBack_obj,$keepAlive);
	}
	public function domain_modify_any($config,$keepAlive=0){
	  $request = '';
	  $callBack_obj = $this->domains($request,$this->put,$config); 
	  return $this->status($callBack_obj,$keepAlive);
	}
	public function domain_remove($domainID_array,$subDomains=0,$keepAlive=0){
	  if( ! is_array($domainID_array)){
		die('Only pass arrays of ID\'s to domain_remove');
	  }
	  if(count($domainID_array) == 1){
		$request = '/' . $domainID_array[0];
		if($subDomains==true){
			$request .= '?deleteSubdomains=true';
		}
	  }else{
	    $request = '?id=' . pop($domainID_array);
		foreach ($domainID_array as $ID){
			$request .= '&id=' . $ID;
		}
	    if($subDomains==true){
			$request .= '?deleteSubdomains=true';
	    }
	  }	  
	  $callBack_obj = $this->domains($request,$this->delete,$config);
	  return $this->status($callBack_obj,$keepAlive);
	}
#+++++++++++++++  SUB-DOMAINS +++++++++++++++++++++

    private function subdomains($addrequest='',$verb){

	$request = 'domains' . '/' . $addrequest;
	return $this->send($request,$this->get);

    }
#==============  PUBLIC  ==========================
    public function subdomain_list($domainID){

      $request = $domainID . '/' . 'subdomains';
      return $this->subdomains($request,$this->get);

    }
#++++++++++++++++  RECORDS  ++++++++++++++++++++++++

    private function records($addrequest='',$verb,$payload){
      $request = 'domains'.  '/' . $addrequest;
      return $this->send($request,$verb,$payload);
    }
#==============  PUBLIC  ==========================
    public function record_list($domainID){
      $request = $domainID . '/' . 'records';
      return $this->records($request,$this->get);
    }
    public function record_list_id($domainID,$recordID){
      $request = $domainID . '/records/' . $recordID;
      return $this->records($request,$this->get);
    }
    public function record_add($domainID,$config,$keepAlive){
      $request = $domainID . '/records';
      $callBack_obj = $this->records($request,$this->post,$config);
      return $this->status($callBack_obj,$keepAlive);
    }
    public function record_modify($domainID,$recordID,$config,$keepAlive){
      $request = $domainID . '/records/' . $recordID;
      $callBack_obj = $this->records($request,$this->put,$config);
      return $this->status($callBack_obj,$keepAlive);
    }
    public function record_modify_any($domainID,$config,$keepAlive){
      $request = $domainID . '/records';
      $callBack_obj = $this->records($request,$this->put,$config);
      return $this->status($callBack_obj,$keepAlive);
    }
    public function record_remove($domainID,$recordID,$config,$keepAlive){
      $request = $domainID . '/records/' . $recordID;
      $callBack_obj = $this->records($request,$this->delete,$config);
      return $this->status($callBack_obj,$keepAlive);
    }
    public function record_remove_any($domainID,$config,$keepAlive){
      $request = $domainID . '/records';
      $callBack_obj = $this->records($request,$this->delete,$config);
      return $this->status($callBack_obj,$keepAlive);
    }
#++++++++++++++++++++  END  +++++++++++++++++++++++
 }
?>