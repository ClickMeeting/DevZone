
require_relative 'ClickMeetingRestClient'

client = ClickMeeting::ClickMeetingRestClient.new({"api_key" => "MY_API_KEY"})

params = {
    'name' => 'test_room',
    'room_type' => 'meeting',
    'permanent_room' => 0,
    'access_type' => 3,
    'lobby_enabled' => 1,
    'registration' => {
        'enabled' => 1
    },
    'settings' => {
        'show_on_personal_page' => 1,
        'thank_you_emails_enabled' => 1,
        'connection_tester_enabled'=> 1,
        'phonegateway_enabled' => 1,
        'recorder_autostart_enabled' => 1,
        'room_invite_button_enabled' => 1,
        'social_media_sharing_enabled' => 1,
        'connection_status_enabled' => 1,
        'thank_you_page_url' => 'http://example.com/thank_you.html'
    } 
}

begin

    conference = client.addConference(params)
    
    room_id = conference["room"]["id"]
    
    puts client.conference(room_id)
    
    puts client.conferences()
    
    params["name"] = "new_test_room";
    puts client.editConference(room_id, params)
    
    params = {
        'email' => 'example@domain.com',
        'nickname' => 'my_nickname',
        'role' => 'listener'
    }
    
    puts client.conferenceAutologinHash(room_id, params)
    
    params = {"how_many" => 2}
    puts client.generateConferenceTokens(room_id, params)
    
    puts client.conferenceTokens(room_id) 
    
    existing_room_id = 123
    existing_session_id = 456;
    puts client.conferenceSessions(existing_room_id)
    
    puts client.conferenceSession(existing_room_id, existing_session_id)
    
    puts client.conferenceSessionAttendees(existing_room_id, existing_session_id)
    
    puts client.generateConferenceSessionPDF(existing_room_id, existing_session_id, 'en')
    
    params = {
        "email" => "example@domain.com",
        "firstname" => "John",
        "lastname" => "Dee",
        "company" => "My company",
        "phone" => "+123456789",
    }
        
    puts client.addContact(params)

    puts client.timeZoneList()
        
    puts client.countryTimeZoneList('us')
   
    puts client.phoneGatewayList()
  
    puts client.conferenceSkins()
 
    params = {
    	"registration" => {
        	1 => 'John',
        	2 => 'Dee',
        	3 => 'example@domain.com'
        },
        "confirmation_email" => {
            'enabled' => 1,
            'lang' => 'en',
       }
    }
        
    puts client.addConferenceRegistration(room_id, params)
        
    puts client.conferenceRegistrations(room_id, 'all')
    
    puts client.conferenceSessionRegistrations(existing_room_id, existing_session_id, 'all')
    
    puts client.fileLibrary()
    
    puts client.conferenceFileLibrary(room_id)
        
    file = client.addFileLibraryFile('/my/file.png')
    puts file
    file_id = file["id"]
    
    puts client.fileLibraryFile(file_id)
        
    puts client.fileLibraryContent(file_id)
    
    puts client.deleteFileLibraryFile(file_id)
    
    puts client.conferenceRecordings(room_id)
    
    puts client.deleteConferenceRecordings(room_id)
    
    existing_recording_id = 123
    puts client.deleteConferenceRecording(existing_room_id, existing_recording_id)
        
    puts client.chats()
        
    puts client.conferenceSessionChats(existing_session_id)
    
    params = {
        'attendees' => [
        	{'email' => 'example@domain.com'}
       	],
        'template' => 'advanced', # basic / advanced
        'role' => 'listener',
    }
    
    puts client.sendConferenceEmailInvitations(room_id, 'en', params)
    
    puts client.deleteConference(room_id)
    
rescue ClickMeeting::ClientError => e
    # handle exceptions here
    print e
end
