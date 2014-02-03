<?php

/************************************

NAME.COM PHP API CLIENT
version: 2010-11-30

*************************
* SIMPLE USAGE EXAMPLES *
*************************

$api = new NameComApi();
$api->username('<username>');
$api->apiToken('<api_token>');
$response = $api->check_domain('mynewdomain');
...

* OR *

$api = new NameComApi();
$api->login('<username>', '<api_token>');
$response = $api->check_domain('mynewdomain');
...
$api->logout();


*********
* HELLO *
*********

$response = $api->hello();

***************
* GET_ACCOUNT *
***************

$response = $api->get_account();

****************
* LIST_DOMAINS *
****************

$response = $api->list_domains();

*****************************
* UPDATE_DOMAIN_NAMESERVERS *
*****************************

$response = $api->update_domain_nameservers('mynewdomain.com', array('ns1.name.com', 'ns2.name.com', 'ns3.name.com', 'ns4.name.com'));

**************************
* UPDATE_DOMAIN_CONTACTS *
**************************

$contacts = array(array('type' => array('registrant', 'administrative', 'technical', 'billing'),
                        'first_name' => 'John',
                        'last_name' => 'Doe',
			'organization' => 'Name.com',
                        'address_1' => '125 Main St',
                        'address_2' => 'Suite 300',
                        'city' => 'Denver',
                        'state' => 'CO',
			'zip' => '80230',
                        'country' => 'US',
                        'phone' => '+1.3035555555',
                        'fax' => '+1.3035555556',
                        'email' => 'john@example.net',
                        )
                  );

$response = $api->update_domain_contacts('mynewdomain.com', $contacts);

***************
* LOCK_DOMAIN *
***************

$response = $api->lock_domain('mynewdomain.com');

*****************
* UNLOCK_DOMAIN *
*****************

$response = $api->unlock_domain('mynewdomain.com');

*********************
* CREATE_DNS_RECORD *
*********************

$response = $api->create_dns_record('mynewdomain.com', 'www', 'A', '127.0.0.1', 300);
$response = $api->create_dns_record('mynewdomain.com', 'mail', 'MX', 'mx3.name.com', 300, 10);

********************
* LIST_DNS_RECORDS *
********************

$response = $api->list_dns_records('mynewdomain.com');

*********************
* DELETE_DNS_RECORD *
*********************

$response = $api->delete_dns_record('mynewdomain.com', 1234);

****************
* CHECK_DOMAIN *
****************

$response = $api->check_domain('mynewsearch');
$response = $api->check_domain('mynewsearch', array('com', 'net', 'org'), array('availability','suggested'));

*****************
* CREATE_DOMAIN *
*****************

$nameservers = array('ns1.name.com', 'ns2.name.com', 'ns3.name.com', 'ns4.name.com');
$contacts = array(array('type' => array('registrant', 'administrative', 'technical', 'billing'),
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'organization' => 'Name.com',
			'address_1' => '100 Main St.',
                        'address_2' => 'Suite 300',
                        'city' => 'Denver',
			'state' => 'CO',
                        'zip' => '80230',
                        'country' => 'US',
                        'phone' => '+1.3035555555',
			'fax' => '+1.3035555556',
                        'email' => 'john@example.net',
                        ));

$response = $api->create_domain('mynewdomain.com', 1, $nameservers, $contacts);

**************
* GET_DOMAIN *
**************

$response = $api->get_domain('mynewdomain.com');

**************************************/ 
 
class NameComApi
{
  public function __call($name, $arguments)
  {
    $class = "NameCom_$name";

    if(class_exists($class))
    {
      $reflection = new ReflectionClass($class);
      $object = $reflection->newInstanceArgs($arguments);
      return $object->submit();
    }
    else
      throw new Exception('Call to undefined method ' . get_class($this) . "::$name()");
  }

  public function sessionToken($session_token = NULL)
  {
    NameComRequest::sessionToken($session_token);
  }

  public function username($username = NULL)
  {
    NameComRequest::username($username);
  }

  public function apiToken($api_token = NULL)
  {
    NameComRequest::apiToken($api_token);
  }

  public function baseUrl($base_url = NULL)
  {
    NameComRequest::baseUrl($base_url);
  }
}

abstract class NameComRequest
{
  static $DEBUG = '0';
  static $BASE_URL = 'https://api.dev.name.com/api';
  static $SESSION_TOKEN = NULL;
  static $USERNAME = NULL;
  static $API_TOKEN = NULL;

  public $method = NULL;
  public $path = NULL;
  public $parameters = array();
  public $query_string = NULL;
  public $post = NULL;
  public $TIMEOUT = 15;

  public $response = NULL;

  public function __construct()
  {
  }

  function submit()
  {
    $ch = curl_init();
    $headers = array();

    $path = $this->path;
    if($this->parameters)
      $path .= '/' . implode('/', $this->parameters);

    curl_setopt($ch, CURLOPT_URL, $request = self::$BASE_URL . $path . (($this->query_string)?'?' . $this->query_string:''));
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->TIMEOUT);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->TIMEOUT);

    if(self::$DEBUG)
      echo("#DEBUG REQUEST   : $this->method $request\n");

    if(isset(self::$SESSION_TOKEN))
    {
      if(self::$DEBUG)
	echo("#DEBUG SESSION   : " . self::$SESSION_TOKEN . "\n");

      $headers[] = 'Api-Session-Token: ' . self::$SESSION_TOKEN;
    }

    if(isset(self::$USERNAME) && isset(self::$API_TOKEN))
    {
      if(self::$DEBUG)
      {
	echo("#DEBUG USERNAME  : " . self::$USERNAME . "\n");
	echo("#DEBUG API_TOKEN : " . self::$API_TOKEN . "\n");
      }

      $headers[] = 'Api-Username: ' . self::$USERNAME;
      $headers[] = 'Api-Token: ' . self::$API_TOKEN;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if('POST' == $this->method)
    {
      curl_setopt($ch, CURLOPT_POST, 1);
      if(isset($this->post))
      {
	$post = json_encode($this->post);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	if(self::$DEBUG)
	  echo("#DEBUG POST      : $post\n");
      }
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $this->raw = curl_exec($ch);

    if(self::$DEBUG)
      echo("#DEBUG RESPONSE  : $this->raw\n\n");

    curl_close($ch);

    return $this->response = json_decode($this->raw);
  }

  static function sessionToken($session_token = NULL)
  {
    if($session_token !== NULL)
      self::$SESSION_TOKEN = $session_token;
    else
      return self::$SESSION_TOKEN;
  }

  static function username($username = NULL)
  {
    if($username !== NULL)
      self::$USERNAME = $username;
    else
      return self::$USERNAME;
  }

  static function apiToken($api_token = NULL)
  {
    if($api_token !== NULL)
      self::$API_TOKEN = $api_token;
    else
      return self::$API_TOKEN;
  }

  static function baseUrl($base_url = NULL)
  {
    if($base_url !== NULL)
      self::$BASE_URL = $base_url;
    else
      return self::$BASE_URL;
  }
}

class NameCom_login extends NameComRequest
{
  public $method = 'POST';
  public $path = '/login';

  function __construct($username, $api_token)
  {
    $this->post = array('username' => $username,
			'api_token' => $api_token);
  }

  function submit()
  {
    parent::submit();

    if($this->response->result->code == 100)
      self::$SESSION_TOKEN = $this->response->session_token;

    return $this->response;
  }
}

class NameCom_hello extends NameComRequest
{
  public $method = 'GET';
  public $path = '/hello';
}

class NameCom_logout extends NameComRequest
{
  public $method = 'GET';
  public $path = '/logout';
}

class NameCom_get_account extends NameComRequest
{
  public $method = 'GET';
  public $path = '/account/get';
}

class NameCom_list_domains extends NameComRequest
{
  public $method = 'GET';
  public $path = '/domain/list';

  function __construct($username = NULL)
  {
    if(isset($username))
      $this->parameters = array($username);
  }
}

class NameCom_update_domain_nameservers extends NameComRequest
{
  public $method = 'POST';
  public $path = '/domain/update_nameservers';

  function __construct($domain_name, $nameservers)
  {
    $this->parameters = array($domain_name);
    $this->post = array('nameservers' => $nameservers);
  }
}

class NameCom_update_domain_contacts extends NameComRequest
{
  public $method = 'POST';
  public $path = '/domain/update_contacts';

  function __construct($domain_name, $contacts)
  {
    $this->parameters = array($domain_name);
    $this->post = array('contacts' => $contacts);
  }
}

class NameCom_lock_domain extends NameComRequest
{
  public $method = 'GET';
  public $path = '/domain/lock';

  function __construct($domain_name)
  {
    $this->parameters = array($domain_name);
  }
}

class NameCom_unlock_domain extends NameComRequest
{
  public $method = 'GET';
  public $path = '/domain/unlock';

  function __construct($domain_name)
  {
    $this->parameters = array($domain_name);
  }
}

class NameCom_create_dns_record extends NameComRequest
{
  public $method = 'POST';
  public $path = '/dns/create';

  function __construct($domain_name, $hostname, $type, $content, $ttl, $priority = NULL)
  {
    $this->parameters = array($domain_name);

    $this->post = array('hostname' => $hostname,
                        'type' => $type,
                        'content' => $content,
                        'ttl' => $ttl,
                        );

    if(isset($priority))
      $this->post['priority'] = $priority;
  }
}
class NameCom_add_dns_record extends NameCom_create_dns_record { }

class NameCom_list_dns_records extends NameComRequest
{
  public $method = 'GET';
  public $path = '/dns/list';

  function __construct($domain_name)
  {
    $this->parameters = array($domain_name);
  }
}

class NameCom_delete_dns_record extends NameComRequest
{
  public $method = 'POST';
  public $path = '/dns/delete';

  function __construct($domain_name, $record_id)
  {
    $this->parameters = array($domain_name);
    $this->post = array('record_id' => $record_id);
  }
}
class NameCom_remove_dns_record extends NameCom_delete_dns_record { }

class NameCom_check_domain extends NameComRequest
{
  public $method = 'POST';
  public $path = '/domain/check';

  function __construct($keyword, $tlds = NULL, $services = NULL)
  {
    /*
    $this->path = "/domain/power_check/$keyword";

    if(isset($tlds))
    {
      $this->path .= '/' . (implode(',', $tlds) ?: 'null');

      if(isset($services))
	$this->path .= '/' . implode(',', $services);
    }
    */

    $this->post = array('keyword' => $keyword);
    if(isset($tlds))
      $this->post['tlds'] = $tlds;
    if(isset($services))
      $this->post['services'] = $services;
  }
}

class NameCom_create_domain extends NameComRequest
{
  public $method = 'POST';
  public $path = '/domain/create';

  function __construct($domain_name, $period, $nameservers, $contacts, $username = null)
  {
    // If a single contact array is passed to this function,
    // this will place that into an array (as expected by API).
    if(isset($contacts['type']))
      $contacts = array($contacts);

    $this->post = array('domain_name' => $domain_name,
			'period' => $period,
			'nameservers' => $nameservers,
			'contacts' => $contacts,
			);

    if(isset($username))
      $this->post['username'] = $username;
  }
}

class NameCom_get_domain extends NameComRequest
{
  public $method = 'GET';
  public $path = '/domain/get';

  function __construct($domain_name)
  {
    $this->parameters = array($domain_name);
  }
}
