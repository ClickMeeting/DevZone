<?php

try {
	include_once './restClient.php';
    $client = new restClient(array('api_key' => 'MY_API_KEY'));

    // new conference data
    $params = array(
  		'name' => 'test_room', // room name
  		'room_type' => 'meeting', // room type
  		'permanent_room' => 0, // permanent or time scheduled room
  		'access_type' => 3, // room access type, 1 – open to all
  		'registration' => array('enabled' => true),
	);
    
    $conference = $client->addConference($params);
    $conference_id = $conference->room->id;
    
    print_r($conference);

    print_r($client->conference($conference_id));
    
    print_r($client->conferences());

    $params['name'] = 'new_test_room';
    print_r($client->editConference($conference_id, $params));

    print_r($client->conferenceAutologinHash($conference_id, array(
        'email' => 'email@domain.com',
        'nickname' => 'my_nickname',
        'role' => 'listener',
    )));
    
    $params = array('how_many' => 2);
    print_r($client->generateConferenceTokens($conference_id, $params));
    
    print_r($client->conferenceTokens($conference_id));
    
    print_r($client->conferenceSessions($conference_id));
    
    $session_id = 'MY_SESSION_ID';
    print_r($client->conferenceSession($conference_id, $session_id));
    
    print_r($client->conferenceSessionAttendees($conference_id, $session_id));
    
    print_r($client->generateConferenceSessionPDF($conference_id, $session_id, 'en'));
    
    print_r($client->timeZoneList());
    
    print_r($client->countryTimeZoneList('en'));
    
    $params = array(
    		'registration' => array(
    				1 => 'John',
    				2 => 'Dee',
    				3 => 'example@domain.com',
    		),
    );
    
    print_r($client->addConferenceRegistration($conference_id, $params));
    
    print_r($client->conferenceRegistrations($conference_id, 'all'));
    
    print_r($client->fileLibrary());
    
    print_r($client->conferenceFileLibrary($conference_id));
    
    $file_id = 'MY_FILE_ID';
    print_r($client->fileLibraryFile($file_id));
    
   	print_r($client->fileLibraryContent($file_id));

    print_r($client->conferenceRecordings($conference_id));
    
    print_r($client->deleteConferenceRecordings($conference_id));
    
    $recording_id = 'MY_RECORDING_ID';
    print_r($client->deleteConferenceRecording($conference_id, $recording_id));
    
    print_r($client->chats());
    
    print_r($client->conferenceSessionChats($session_id));
    
    print_r($client->addFileLibraryFile('/path/to/file.png'));
    
    $params = array(
    	'attendees' => array(
    		array('email' => 'example@domain.com'),
   		),
    );
    print_r($client->sendConferenceEmailInvitations($conference_id, 'us', $params));
    
    print_r($client->deleteConference($conference_id));
}
catch (Exception $e)
{
    print_r($e->getMessage());
}