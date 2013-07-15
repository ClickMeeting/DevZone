<?php
/**
* Demonstrates how to get conferences list add new conference
* restClient.php
*
* @author Paweł Brydziński <pbrydzinski@implix.com>
* http://implix.com
*/
class restClient
{
    protected $url = null, $key = null, $format = 'json';
    private static $instance = null;

    // default options for curl
    protected $curl_options = array(
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 8
    );

    public $http_errors = array
    (
        500 => '500 Internal Server Error'
    );

    final private function __construct() {}
    final private function __clone() {}

    final public static function &instance(array $params)
    {
        $class_name = get_called_class();

        if (!isset(self::$instance))
        {
            self::$instance = new $class_name;
            if (false === extension_loaded('curl'))
            {
                throw new Exception('The curl extension must be loaded for using this class !');
            }

            // set an url to connect to
            self::$instance->url = $params['url'];
            self::$instance->key = $params['key'];
            if(!in_array($params['format'], array('json')))
            {
                self::$instance->format = $params['format'];
            }
        }

        return self::$instance;
    }

    protected function & getResponse($method = 'GET', $params)
    {
        // do the actual connection
        $curl = curl_init();
        // set URL
        curl_setopt($curl, CURLOPT_URL, $this->url.$params[0].'.'.$this->format.'?api_key='.$this->key);
        switch ($method) {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Expect:' ) );
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }
        if(!empty($params[1]))
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params[1]));
        }
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt_array($curl, $this->curl_options);
        // send the request
        $response = curl_exec($curl);
        // check http status code
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (isset($this->http_errors[$http_code]))
        {
            throw new Exception('Response Http Error - ' . $this->http_errors[$http_code]);
        }
        // check for curl error
        if (0 < curl_errno($curl))
        {
            throw new Exception('Unable to connect to '.$this->url . ' Error: ' . curl_error($curl));
        }
        // close the connection
        curl_close($curl);
        return $response;
    }


    public function conferences()
    {
        return $this->getResponse('GET', array('conferences'));
    }

    public function conference($id)
    {
        return $this->getResponse('GET', array('conferences/'.$id));
    }

    public function addConference(array $params)
    {
        return $this->getResponse('POST', array('conferences', $params));
    }

    public function editConference($id, array $params)
    {
        return $this->getResponse('PUT', array('conferences/'.$id, $params));
    }

    public function deleteConference($id)
    {
        return $this->getResponse('DELETE', array('conferences/'.$id));
    }

    public function conferenceAutologinHash($id, array $params)
    {
        return $this->getResponse('POST', array('conferences/'.$id.'/room/autologin_hash', $params));
    }
}

date_default_timezone_set('UTC');

try {

    $client = restClient::instance(array(
        'url'    => 'https://api.clickmeeting.com/v1/', // API URL
        'key'    => '', // API KEY
        'format' => 'json'
    ));

    // new conference data
    $data = array(
        'name' => 'New api room 1',
        'room_type' => 'meeting',
        'permanent_room' => 0,
        'access_type' => 1,
        'lobby_description' => 'This is test for room created by API.',
        'starts_at' => date('Y-m-d H:i', strtotime('+2 days')),
        'duration' => '1',
    );

    // add conference
    $conference = $client->addConference($data);
    print_r($conference);

    // conference id
    $conference_id = $conference->room->id;

    // get conferences
    print_r($client->conferences());

    // get conference by id
    print_r($client->conference($conference_id));

    // edit conference
    $data['name'] = 'New api room 2';
    print_r($client->editConference($conference_id, $data));

    // get conference autologin hash
    print_r($client->conferenceAutologinHash($conference_id, array(
        'email' => 'test@gmail.com', // (STRING) PUT HERE YOUR EMAIL
        'nickname' => 'test', // (STRING) PUT HERE YOUR NICKNAME
        'role' => 'listener',
    )));

    // delete conference
    print_r($client->deleteConference($conference_id));

}
catch (Exception $e)
{
    print_r($e->getMessage());
}

?>