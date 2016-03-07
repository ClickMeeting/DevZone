<?php

try {
    include_once './ClickMeetingRestClient.php';
    $client = new ClickMeetingRestClient(array('api_key' => 'MY_API_KEY'));

    // Conferences
    $params = array(
        'lobby_enabled'     => true,
        'lobby_description' => 'My meeting',
        'name'              => 'test_room',
        'room_type'         => 'meeting',
        'permanent_room'    => 0,
        'access_type'       => 3,
        'registration' => array(
            'template'=> 1,
            'enabled' => true
        ),
       'settings' => array(
            'show_on_personal_page'        => 1,
            'thank_you_emails_enabled'     => 1,
            'connection_tester_enabled'    => 1,
            'phonegateway_enabled'         => 1,
            'recorder_autostart_enabled'   => 1,
            'room_invite_button_enabled'   => 1,
            'social_media_sharing_enabled' => 1,
            'connection_status_enabled'    => 1,
            'thank_you_page_url'           => 'http://example.com/thank_you.html',
        ),
    );

    $conference = $client->addConference($params);
    $room_id = $conference->room->id;

    print_r($conference);

    print_r($client->conference($room_id));

    print_r($client->conferences());

    print_r($client->editConference($room_id, array('name' => 'new_test_room')));

    print_r($client->conferenceSkins());

    print_r($client->addContact([
        'email' => 'example@domain.com',
        'firstname' => 'John',
        'lastname' => 'Dee',
        'phone' => '+1234567890',
        'company' => 'My company',
        'country' => 'US']));

    print_r($client->conferenceAutologinHash($room_id, array(
        'email' => 'email@domain.com',
        'nickname' => 'my_nickname',
        'role' => 'listener'
    )));

    print_r($client->sendConferenceEmailInvitations($room_id, 'us', array(
        'attendees' => array(
            array('email' => 'example@domain.com')
        ),
        'template' => 'advanced', // basic | advanced
        'role' => 'listener',
    )));

    // Tokens
    print_r($client->generateConferenceTokens($room_id, array('how_many' => 2)));

    print_r($client->conferenceTokens($room_id));

    // Sessions
    print_r($client->conferenceSessions($room_id));

    $existing_room_id = 123;
    $existing_session_id = 456;
    print_r($client->conferenceSession($existing_room_id, $existing_session_id));

    print_r($client->conferenceSessionAttendees($existing_room_id, $existing_session_id));

    print_r($client->generateConferenceSessionPDF($existing_room_id, $existing_session_id, 'en'));

    // Timezones
    print_r($client->timeZoneList());

    print_r($client->countryTimeZoneList('us'));

    print_r($client->phoneGatewayList());

    // Registrations
    print_r($client->addConferenceRegistration($room_id, array(
        'registration' => array(
           1 => 'John',
           2 => 'Dee',
           3 => 'example@domain.com'
        ),
        'confirmation_email' => array(
            'enabled' => 1,
            'lang' => 'en',
        )
    )));

    print_r($client->conferenceRegistrations($room_id, 'all'));

    // File library
    $file = $client->addFileLibraryFile('/my/file.png');
    print_r($file);

    $file_id = $file->id;

    print_r($client->fileLibrary());

    print_r($client->conferenceFileLibrary($room_id));

    print_r($client->fileLibraryFile($file_id));

    print_r($client->fileLibraryContent($file_id));

    print_r($client->deleteFileLibraryFile($file_id));

    // Recordings
    print_r($client->conferenceRecordings($room_id));

    print_r($client->deleteConferenceRecordings($room_id));

    $recording_id = 123;
    print_r($client->deleteConferenceRecording($existing_room_id, $recording_id));

    // Chats
    print_r($client->chats());

    print_r($client->conferenceSessionChats($existing_session_id));

    print_r($client->deleteConference($room_id));
}
catch (Exception $e)
{
    print_r(json_decode($e->getMessage()));
}
