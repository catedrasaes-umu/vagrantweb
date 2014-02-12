<?php
/**
 * Yii RESTClient Components
 * 
 * Make REST requests to RESTful services with simple syntax.
 * Ported from CodeIgniter REST Class.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Philip Sturgeon
 * @created			04/06/2009
 * @license         http://philsturgeon.co.uk/code/dbad-license
 * @link			http://getsparks.org/packages/restclient/show
 */
class RESTClient extends CComponent
{
	public $supported_formats = array(
		'xml' 				=> 'application/xml',
		'json' 				=> 'application/json',
		'serialize' 		=> 'application/vnd.php.serialized',
		'php' 				=> 'text/plain',
    	'csv'				=> 'text/csv'
	);

    public $auto_detect_formats = array(
		'application/xml' 	=> 'xml',
		'text/xml' 			=> 'xml',
		'application/json' 	=> 'json',
		'text/json' 		=> 'json',
		'text/csv' 			=> 'csv',
		'application/csv' 	=> 'csv',
    	'application/vnd.php.serialized' => 'serialize'
	);

	private $rest_server;
	private $format;
	private $mime_type;
	
	private $http_auth = null;
	private $http_user = null;
	private $http_pass = null;

    private $response_string;
	private $_curl;
	//private $_headers;
	private $_headers = array();
	private $_response_headers = array();
    /**
     * Logs a message.
     *
     * @param string $message Message to be logged
     * @param string $level Level of the message (e.g. 'trace', 'warning',
     * 'error', 'info', see CLogger constants definitions)
     */
    public static function log($message, $level='error')
    {
        Yii::log($message, $level, __CLASS__);
    }

    /**
     * Dumps a variable or the object itself in terms of a string.
     *
     * @param mixed variable to be dumped
     */
    protected function dump($var='dump-the-object',$highlight=true)
    {
        if ($var === 'dump-the-object') {
            return CVarDumper::dumpAsString($this,$depth=15,$highlight);
        } else {
            return CVarDumper::dumpAsString($var,$depth=15,$highlight);
        }
    }
	
	function __construct($config = array())
    {
        Yii::log('REST Class Initialized');
		$this->_curl = new CURL();
		empty($config) OR $this->initialize($config);
    }

	function __destruct()
	{
		$this->_curl->set_default();
	}

    public function initialize($config)
    {
		$this->rest_server = @$config['server'];

		if (substr($this->rest_server, -1, 1) != '/')
		{
			$this->rest_server .= '/';
		}

		isset($config['http_auth']) && $this->http_auth = $config['http_auth'];
		isset($config['http_user']) && $this->http_user = $config['http_user'];
		isset($config['http_pass']) && $this->http_pass = $config['http_pass'];
    }


    public function get($uri, $params = array(), $format = NULL)
    {
        if ($params)
        {
        	$uri .= '?'.(is_array($params) ? http_build_query($params) : $params);
        }

    	return $this->_call('get', $uri, NULL, $format);
    }


    public function post($uri, $params = array(), $format = NULL)
    {
        return $this->_call('post', $uri, $params, $format);
    }


    public function put($uri, $params = array(), $format = NULL)
    {
        return $this->_call('put', $uri, $params, $format);
    }


    public function delete($uri, $params = array(), $format = NULL)
    {
        return $this->_call('delete', $uri, $params, $format);
    }

    public function api_key($key, $name = 'X-API-KEY')
	{
		$this->_curl->http_header($name, $key);
	}
	
	public function set_header($name, $value)
	{
		$this->_headers[$name] = $value;
	}

    public function language($lang)
	{
		if (is_array($lang))
		{
			$lang = implode(', ', $lang);
		}

		$this->_curl->http_header('Accept-Language', $lang);
	}

    private function _call($method, $uri, $params = array(), $format = NULL)
    {
    	$this->_set_headers();

        // Initialize cURL session
        $this->_curl->create($this->rest_server.$uri);

        // If authentication is enabled use it
        if ($this->http_auth != '' && $this->http_user != '')
        {
        	$this->_curl->http_login($this->http_user, $this->http_pass, $this->http_auth);
        }

        // We still want the response even if there is an error code over 400
        $this->_curl->option('failonerror', FALSE);

        // Call the correct method with parameters
        $this->_curl->{$method}($params);

		//Original fjsanpedro@gmail.com
        // Execute and return the response from the REST server
        //$response = $this->_curl->execute();
        
        //Modified fjsanpedro@gmail.com
        $aux = $this->_curl->execute();
		
		$response = $aux[0];
		$this->_response_headers = $aux[1];
		
        // Format and return
		if ($format !== NULL)
		{
			$this->format($format);
			return $this->_format_response($response);
		} else return $response;
    }

	//Modified fjsanpedro@gmail.com
	public function response_headers()
	{
		return $this->_response_headers;
	}

    // If a type is passed in that is not supported, use it as a mime type
    public function format($format)
	{
		if (array_key_exists($format, $this->supported_formats))
		{
			$this->format = $format;
			$this->mime_type = $this->supported_formats[$format];
		}

		else
		{
			$this->mime_type = $format;
		}

		return $this;
	}

	public function debug()
	{
		$request = $this->_curl->debug();

		echo "=============================================<br/>\n";
		echo "<h2>REST Test</h2>\n";
		echo "=============================================<br/>\n";
		echo "<h3>Request</h3>\n";
		echo $request['url']."<br/>\n";
		echo "=============================================<br/>\n";
		echo "<h3>Response</h3>\n";

		if ($this->response_string)
		{
			echo "<code>".nl2br(htmlentities($this->response_string))."</code><br/>\n\n";
		}

		else
		{
			echo "No response<br/>\n\n";
		}

		echo "=============================================<br/>\n";

		if ($this->_curl->error_string)
		{
			echo "<h3>Errors</h3>";
			echo "<strong>Code:</strong> ".$this->_curl->error_code."<br/>\n";
			echo "<strong>Message:</strong> ".$this->_curl->error_string."<br/>\n";
			echo "=============================================<br/>\n";
		}

		echo "<h3>Call details</h3>";
		echo "<pre>";
		print_r($this->_curl->info);
		echo "</pre>";

	}


	// Return HTTP status code
	public function status()
	{
		return $this->info('http_code');
	}

	// Return curl info by specified key, or whole array
	public function info($key = null)
	{
		return $key === null ? $this->_curl->info : @$this->_curl->info[$key];
	}

	// Set custom options
	public function option($code, $value)
	{
		$this->_curl->option($code, $value);
	}

	private function _set_headers()
	{
		if (! array_key_exists("Accept", $this->_headers)) $this->set_header("Accept", $this->mime_type);
		foreach ($this->_headers as $k => $v){
			$this->_curl->http_header(sprintf("%s: %s", $k, $v));
		}
	}

	private function _format_response($response)
	{
		$this->response_string =& $response;

		// It is a supported format, so just run its formatting method
		if (array_key_exists($this->format, $this->supported_formats))
		{
			return $this->{"_".$this->format}($response);
		}

		// Find out what format the data was returned in
		$returned_mime = @$this->_curl->info['content_type'];

		// If they sent through more than just mime, stip it off
		if (strpos($returned_mime, ';'))
		{
			list($returned_mime)=explode(';', $returned_mime);
		}

		$returned_mime = trim($returned_mime);

		if (array_key_exists($returned_mime, $this->auto_detect_formats))
		{
			return $this->{'_'.$this->auto_detect_formats[$returned_mime]}($response);
		}

		return $response;
	}


    // Format XML for output
    private function _xml($string)
    {
		return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
    }

    // Format HTML for output
    // This function is DODGY! Not perfect CSV support but works with my REST_Controller
    private function _csv($string)
    {
		$data = array();

		// Splits
		$rows = explode("\n", trim($string));
		$headings = explode(',', array_shift($rows));
		foreach( $rows as $row )
		{
			// The substr removes " from start and end
			$data_fields = explode('","', trim(substr($row, 1, -1)));

			if (count($data_fields) == count($headings))
			{
				$data[] = array_combine($headings, $data_fields);
			}

		}

		return $data;
    }

    // Encode as JSON
    private function _json($string)
    {
    	return json_decode(trim($string));
    }

    // Encode as Serialized array
    private function _serialize($string)
    {
    	return unserialize(trim($string));
    }

    // Encode raw PHP
    private function _php($string)
    {
    	$string = trim($string);
    	$populated = array();
    	eval("\$populated = \"$string\";");
    	return $populated;
    }
}
