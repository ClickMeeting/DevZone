<?php

use \Exception;

/**
 * Class ClickMeetingRestClient
 */
class ClickMeetingRestClient
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
     * @var array
     */
    protected $curl_options = [
        CURLOPT_ENCODING => 'gzip,deflate',
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 8
    ];

    /**
     * Error codes
     * @var array
     */
    protected $http_errors = array
    (
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        422 => '422 Unprocessable Entity',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
    );

    /**
     * Allowed formats
     * @var array
     */
    protected $formats = array('json', 'xml', 'js', 'printr');

    /**
     * Constructor
     * @param array $params
     * @throws Exception
     */
    public function __construct(array $params = null)
    {
        if (false === extension_loaded('curl')) {
            throw new Exception('The curl extension must be loaded for using this class!');
        }

        $this->url = isset($params['url']) ? $params['url'] : $this->url;
        $this->api_key = isset($params['api_key']) ? $params['api_key'] : $this->api_key;
        $this->format = isset($params['format']) && in_array(strtolower($params['format']), $this->formats) ? strtolower($params['format']) : $this->format;
    }

    /**
     * Get response
     *
     * @param string $method
     * @param string $path
     * @param array $params
     * @param bool $format_response
     * @param bool $is_upload_file
     * @return string|array
     * @throws Exception
     */
    protected function sendRequest($method, $path, $params = null, $format_response = true, $is_upload_file = false)
    {
        // do the actual connection
        $curl = curl_init();

        // set URL
        curl_setopt($curl, CURLOPT_URL, $this->url . $path . '.' . (isset($this->format) ? $this->format : 'json'));

        // set api key
        $headers = array('X-Api-Key:' . $this->api_key);

        // is uplaoded file
        if (true == $is_upload_file) {
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
                if (empty($params)) {
                    $headers[] = 'Content-Length: 0';
                }
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // add params
        if (!empty($params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $is_upload_file ? $params : http_build_query($params));
        }

        curl_setopt_array($curl, $this->curl_options);

        // send the request
        $response = curl_exec($curl);

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (isset($this->http_errors[$http_code])) {
            throw new Exception($response, $http_code);
        } elseif (!in_array($http_code, array(200, 201))) {
            throw new Exception('Response status code: ' . $http_code);
        }

        // check for curl error
        if (0 < curl_errno($curl)) {
            throw new Exception('Unable to connect to ' . $this->url . ' Error: ' . curl_error($curl));
        }

        // close the connection
        curl_close($curl);

        // check return format
        if (!isset($this->format) && true == $format_response) {
            $response = json_decode($response);
        }

        return $response;
    }

    /**
     * Get conferences
     *
     * @param string $status
     * @param int $page
     * @return mixed
     * @throws Exception
     */
    public function conferences($status = 'active', $page = 1)
    {
        return $this->sendRequest('GET', 'conferences/' . $status . '?page=' . $page);
    }

    /**
     * Get conference
     *
     * @param string $room_id
     * @return mixed
     * @throws Exception
     */
    public function conference($room_id)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id);
    }

    /**
     * Add conference
     *
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function addConference(array $params)
    {
        return $this->sendRequest('POST', 'conferences', $params);
    }

    /**
     * Edit conference
     *
     * @param int $room_id
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function editConference($room_id, array $params)
    {
        return $this->sendRequest('PUT', 'conferences/' . $room_id, $params);
    }

    /**
     * Delete conference
     *
     * @param int $room_id
     * @return mixed
     * @throws Exception
     */
    public function deleteConference($room_id)
    {
        return $this->sendRequest('DELETE', 'conferences/' . $room_id);
    }

    /**
     * Conference autologin hash
     *
     * @param string $room_id
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function conferenceAutologinHash($room_id, array $params)
    {
        return $this->sendRequest('POST', 'conferences/' . $room_id . '/room/autologin_hash', $params);
    }

    /**
     * Send invitation mail
     *
     * @param int $room_id
     * @param string $lang
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function sendConferenceEmailInvitations($room_id, $params, $lang = 'en')
    {
        return $this->sendRequest('POST', 'conferences/' . $room_id . '/invitation/email/' . $lang, $params);
    }

    /**
     * Conference skins
     *
     * @return mixed
     * @throws Exception
     */
    public function conferenceSkins()
    {
        return $this->sendRequest('GET', 'conferences/skins');
    }

    /**
     * Conference generate tokens
     *
     * @param int $room_id
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function generateConferenceTokens($room_id, array $params)
    {
        return $this->sendRequest('POST', 'conferences/' . $room_id . '/tokens', $params);
    }

    /**
     * Get coference tokens
     *
     * @param int $room_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceTokens($room_id)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/tokens');
    }

    /**
     * Get conference sessions
     *
     * @param string $room_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceSessions($room_id)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/sessions');
    }

    /**
     * Get conference session
     *
     * @param int $room_id
     * @param int $session_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceSession($room_id, $session_id)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/sessions/' . $session_id);
    }

    /**
     * Get conference session attendees
     *
     * @param int $room_id
     * @param int $session_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceSessionAttendees($room_id, $session_id)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/sessions/' . $session_id . '/attendees');
    }

    /**
     * Generate pdf report
     *
     * @param int $room_id
     * @param int $session_id
     * @param string $lang
     * @return mixed
     * @throws Exception
     */
    public function generateConferenceSessionPDF($room_id, $session_id, $lang = 'en')
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/sessions/' . $session_id . '/generate-pdf/' . $lang);
    }

    /**
     * Add new contact
     *
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function addContact($params)
    {
        return $this->sendRequest('POST', 'contacts', $params);
    }

    /**
     * Get timezone list
     *
     * @return mixed
     * @throws Exception
     */
    public function timeZoneList()
    {
        return $this->sendRequest('GET', 'time_zone_list');
    }

    /**
     * Get timezone by country
     *
     * @param string $country
     * @return mixed
     * @throws Exception
     */
    public function countryTimeZoneList($country)
    {
        return $this->sendRequest('GET', 'time_zone_list/' . $country);
    }

    /**
     * Get phone gateways
     *
     * @return mixed
     * @throws Exception
     */
    public function phoneGatewayList()
    {
        return $this->sendRequest('GET', 'phone_gateways');
    }

    /**
     * Add conference registration
     *
     * @param int $room_id
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function addConferenceRegistration($room_id, $params)
    {
        return $this->sendRequest('POST', 'conferences/' . $room_id . '/registration', $params);
    }

    /**
     * Get conference registrants
     *
     * @param int $room_id
     * @param string $status
     * @return mixed
     * @throws Exception
     */
    public function conferenceRegistrations($room_id, $status)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/registrations/' . $status);
    }

    /**
     * Get conference session registrants
     *
     * @param int $room_id
     * @param int $session_id
     * @param string $status
     * @return mixed
     * @throws Exception
     */
    public function conferenceSessionRegistrations($room_id, $session_id, $status)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/sessions' . $session_id . '/registrations/' . $status);
    }

    /**
     * Get files from library
     *
     * @return mixed
     * @throws Exception
     */
    public function fileLibrary()
    {
        return $this->sendRequest('GET', 'file-library');
    }

    /**
     * Get conference file library
     *
     * @param int $room_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceFileLibrary($room_id)
    {
        return $this->sendRequest('GET', 'file-library/conferences/' . $room_id);
    }

    /**
     * Get file details
     *
     * @param int $file_id
     * @return mixed
     * @throws Exception
     */
    public function fileLibraryFile($file_id)
    {
        return $this->sendRequest('GET', 'file-library/' . $file_id);
    }

    /**
     * Add file to library
     *
     * @param string $file_path
     * @return mixed
     * @throws Exception
     */
    public function addFileLibraryFile($file_path)
    {
        return $this->sendRequest('POST', 'file-library', array('uploaded' => '@' . $file_path), true, true);
    }

    /**
     * Download file
     *
     * @param int $file_id
     * @return mixed
     * @throws Exception
     */
    public function fileLibraryContent($file_id)
    {
        return $this->sendRequest('GET', 'file-library/' . $file_id . '/download', null, false);
    }

    /**
     * Delete file
     *
     * @param int $file_id
     * @return mixed
     * @throws Exception
     */
    public function deleteFileLibraryFile($file_id)
    {
        return $this->sendRequest('DELETE', 'file-library/' . $file_id);
    }

    /**
     * Get conference recordings
     *
     * @param int $room_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceRecordings($room_id)
    {
        return $this->sendRequest('GET', 'conferences/' . $room_id . '/recordings');
    }

    /**
     * Delete conference recordings
     *
     * @param int $room_id
     * @return mixed
     * @throws Exception
     */
    public function deleteConferenceRecordings($room_id)
    {
        return $this->sendRequest('DELETE', 'conferences/' . $room_id . '/recordings');
    }

    /**
     * Delete conference recording
     *
     * @param int $room_id
     * @param int $recording_id
     * @return mixed
     * @throws Exception
     */
    public function deleteConferenceRecording($room_id, $recording_id)
    {
        return $this->sendRequest('DELETE', 'conferences/' . $room_id . '/recordings/' . $recording_id);
    }

    /**
     * Get chats
     *
     * @return mixed
     * @throws Exception
     */
    public function chats()
    {
        return $this->sendRequest('GET', 'chats');
    }

    /**
     * Get chat record
     *
     * @param int $session_id
     * @return mixed
     * @throws Exception
     */
    public function conferenceSessionChats($session_id)
    {
        return $this->sendRequest('GET', 'chats/' . $session_id, null, false);
    }
}