<?php

/**
 * ClickMeeting REST client class
 *
 * @package api
 * @copyright Copyright (c) 2014, GetResponse Sp. z o.o.
 */
class restClient
{
	/**
	 * API url
	 * @var string
	 */
    protected $url = 'https://api.clickmeeting.com/v1/';
    
    /**
     * API key
     * @var string
     */
    protected $api_key = null;
    
    /**
     * Format
     * @var string
     */
    protected $format = null; // json, xml, printr, js

    /**
     * Curl options
     * @var options
     */
    protected $curl_options = array(
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 8
    );
    
    /**
     * Allowed formats
     * @var unknown
     */
    protected $formats = array('json', 'xml', 'js', 'printr');

    /**
     * HTTP errors
     * @var array
     */
    public $http_errors = array
    (
        500 => '500 Internal Server Error'
    );

    /**
     * Constructor
     * @param array $params
     * @throws Exception
     */
    public function __construct(array $params)
    {
		if (false === extension_loaded('curl'))
		{
			throw new Exception('The curl extension must be loaded for using this class!');
		}
		
        $this->url = isset($params['url']) ? $params['url'] : $this->url;
        $this->api_key = isset($params['api_key']) ? $params['api_key'] : $this->api_key;
        $this->format = isset($params['format']) && in_array(strtolower($params['format']), $this->formats) ? strtolower($params['format']) : $this->format;
    }

    /**
     * Get response
     * @param string $method
     * @param string $path
     * @param array $params
     * @param bool $format_response
     * @param bool $is_upload_file
     * @throws Exception
     * @return string|array
     */
    protected function &getResponse($method, $path, $params = null, $format_response = true, $is_upload_file = false)
    {
        // do the actual connection
        $curl = curl_init();
        
        // set URL
        curl_setopt($curl, CURLOPT_URL, $this->url.$path.'.'.(isset($this->format) ? $this->format : 'json'));
        
        // set api key
        $headers = array( 'X-Api-Key:' . $this->api_key);
        
        // is uplaoded file
        if (true == $is_upload_file) 
        {
        	$headers[] = 'Content-type: multipart/form-data';
        }
        
        switch ($method) {
            case 'GET':
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                $headers[] = 'Expect:';
                break;
            case 'PUT':
                if(empty($params))
                {
                    $headers[] = 'Content-Length: 0';
                }
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        // add params
        if (!empty($params))
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $is_upload_file ? $params : http_build_query($params));
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
        
        // check return format
        if (!isset($this->format) && true == $format_response)
        {
        	$response = json_decode($response);
        }        
        return $response;
    }

    /**
     * Get conferences
     * @param string $status
     */
    public function conferences($status = 'active')
    {
        return $this->getResponse('GET', 'conferences/'.$status);
    }

    /**
     * Get conference
     * @param unknown $room_id
     */
    public function conference($room_id)
    {
        return $this->getResponse('GET', 'conferences/'.$room_id);
    }

    /**
     * Add conference
     * @param array $params
     */
    public function addConference(array $params)
    {
        return $this->getResponse('POST', 'conferences', $params);
    }

    /**
     * Edit conference
     * @param int $room_id
     * @param array $params
     */
    public function editConference($room_id, array $params)
    {
        return $this->getResponse('PUT', 'conferences/'.$room_id, $params);
    }

    /**
     * Delete conference
     * @param int $room_id
     */
    public function deleteConference($room_id)
    {
        return $this->getResponse('DELETE', 'conferences/'.$room_id);
    }

    /**
     * Conference autologin hash
     * @param unknown $room_id
     * @param array $params
     */
    public function conferenceAutologinHash($room_id, array $params)
    {
        return $this->getResponse('POST', 'conferences/'.$room_id.'/room/autologin_hash', $params);
    }
    
    /**
     * Conference generate tokens
     * @param int $room_id
     * @param array $params
     */
    public function generateConferenceTokens($room_id, array $params)
    {
    	return $this->getResponse('POST', 'conferences/'.$room_id.'/tokens', $params);
    }
    
    /**
     * Get coference tokens
     * @param int $room_id
     */
    public function conferenceTokens($room_id)
    {
        return $this->getResponse('GET', 'conferences/'.$room_id.'/tokens');
    }
    
    /**
     * Get conference sessions
     * @param unknown $room_id
     */
    public function conferenceSessions($room_id)
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/sessions');
    }
    
    /**
     * Get conference session
     * @param int $room_id
     * @param int $session_id
     */
    public function conferenceSession($room_id, $session_id)
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/sessions/'.$session_id);
    }
    
    /**
     * Get conference session attendees
     * @param int $room_id
     * @param int $session_id
     */
    public function conferenceSessionAttendees($room_id, $session_id)
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/sessions/'.$session_id.'/attendees');
    }
    
    /**
     * Generate pdf report
     * @param int $room_id
     * @param int $session_id
     * @param string $lang
     */
    public function generateConferenceSessionPDF($room_id, $session_id, $lang = 'en')
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/sessions/'.$session_id.'/generate-pdf/'.$lang);
    }
    
    /**
     * Get timezone list
     */
    public function timeZoneList()
    {
    	return $this->getResponse('GET', 'time_zone_list');
    }
    
    /**
     * Get timezone by country
     * @param string $country
     */
    public function countryTimeZoneList($country)
    {
    	return $this->getResponse('GET', 'time_zone_list/'.$country);
    }
    
    /**
     * Add conference registration
     * @param int $room_id
     * @param array $params
     */
    public function addConferenceRegistration($room_id, $params)
    {
    	return $this->getResponse('POST', 'conferences/'.$room_id.'/registration', $params);
    }
    
    /**
     * Get conference registrants
     * @param int $room_id
     * @param string $status
     */
    public function conferenceRegistrations($room_id, $status)
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/registrations/'.$status);
    }
    
    /**
     * Get conference session registants
     * @param int $room_id
     * @param int $session_id
     * @param string $status
     */
    public function conferenceSessionRegistrations($room_id, $session_id, $status)
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/sessions'.$session_id.'/registrations/'.$status);
    }
    
    /**
     * Get files from library
     */
    public function fileLibrary()
    {
    	return $this->getResponse('GET', 'file-library');
    }
    
    /**
     * Get coference file library
     * @param int $room_id
     */
    public function conferenceFileLibrary($room_id)
    {
    	return $this->getResponse('GET', 'file-library/conferences'.$room_id);
    }
    
    /**
     * Get file details
     * @param int $file_id
     */
    public function fileLibraryFile($file_id)
    {
    	return $this->getResponse('GET', 'file-library/'.$file_id);
    }
    
    /**
     * Add file to library
     * @param string $file_path
     */
    public function addFileLibraryFile($file_path)
    {
    	return $this->getResponse('POST', 'file-library', array('uploaded' => '@'.$file_path), true, true);
    }
    
    /**
     * Download file
     * @param int $file_id
     */
    public function getFileLibraryContent($file_id)
    {
    	return $this->getResponse('GET', 'file-library/'.$file_id.'/download', null, false);
    }
    
    /**
     * Get conference recordings
     * @param int $room_id
     */
    public function conferenceRecordings($room_id)
    {
    	return $this->getResponse('GET', 'conferences/'.$room_id.'/recordings');
    }
    
    /**
     * Delete conference recordings
     * @param int $room_id
     */
    public function deleteConferenceRecordings($room_id)
    {
    	return $this->getResponse('DELETE', 'conferences/'.$room_id.'/recordings');
    }
    
    /**
     * Delete conference recording
     * @param int $room_id
     * @param int $recording_id
     */
    public function deleteConferenceRecording($room_id, $recording_id)
    {
    	return $this->getResponse('DELETE', 'conferences/'.$room_id.'/recordings/'.$recording_id);
    }
    
    /**
     * Get chats
     */
    public function chats()
    {
    	return $this->getResponse('GET', 'chats');
    }
    
    /**
     * Get chat record
     * @param int $session_id
     */
    public function conferenceSessionChats($session_id)
    {
    	return $this->getResponse('GET', 'chats/'.$session_id, null, false, true);
    }
    
    /**
     * Send invitation mail
     * @param int $room_id
     * @param string $lang
     * @param array $params
     * @return Ambigous <string, multitype:, mixed>
     */
    public function sendConferenceEmailInvitations($room_id, $lang = 'en', $params)
    {
    	return $this->getResponse('POST', 'conferences/'.$room_id.'/invitation/email/'.$lang, $params);
    }
}
