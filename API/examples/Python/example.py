from ClickMeetingRestClient import ClickMeetingRestClient

client = ClickMeetingRestClient({'api_key' : 'MY_API_KEY'})

try:
    room = client.addConference({
        'name': 'test_room', 
        'room_type': 'meeting', 
        'permanent_room': 0, 
        'access_type': 3, 
        'registration' : {'enabled': 1}})
        
    print room
    room_id = room['room']['id']
    
    print client.conference(room_id)
    
    print client.editConference(room_id, {'name':'new_test_room'})
    
    print client.conferences()
    
    params = {
        'email' : 'example@domain.com',
        'nickname' : 'my_nickname',
        'role' : 'listener'
    }
    print client.conferenceAutologinHash(room_id, params)
    
    params = {"how_many" : 2}
    print client.generateConferenceTokens(room_id, params)
    
    print client.conferenceTokens(room_id) 
    
    existing_session_id = 123 # existing session id
    existing_room_id = 456 # existing room id
    
    print client.conferenceSessions(existing_room_id)
    
    print client.conferenceSession(existing_room_id, existing_session_id)
    
    print client.conferenceSessionAttendees(existing_room_id, existing_session_id)
    
    print client.generateConferenceSessionPDF(existing_room_id, existing_session_id, 'en')
    
    print client.timeZoneList()
        
    print client.countryTimeZoneList('us')
    
    params = {
        "registration" : {
            1 : 'John',
            2 : 'Dee',
            3 : 'example@domain.com'
        }
    }
        
    print client.addConferenceRegistration(room_id, params)
    
    print client.conferenceRegistrations(room_id, 'all')
    
    print client.fileLibrary()
    
    print client.conferenceFileLibrary(room_id)
    
    file = client.addFileLibraryFile('/my/file.png')
    print file
    file_id = file["id"]
    
    print client.fileLibraryFile(file_id)
    
    print client.fileLibraryContent(file_id)
    
    print client.deleteFileLibraryFile(file_id)
    
    print client.conferenceRecordings(room_id)
    
    print client.conferenceSessionRegistrations(existing_room_id, existing_session_id, 'all')
        
    print client.deleteConferenceRecordings(room_id)
    
    existing_recording_id = 123
    print client.deleteConferenceRecording(existing_room_id, existing_recording_id)
        
    print client.chats()
        
    print client.conferenceSessionChats(existing_session_id)
    
    params = {
        'attendees' : [
            {'email' : 'example@domain.com'}
        ]
    }
    
    print client.sendConferenceEmailInvitations(room_id, 'us', params)
    
    print client.deleteConference(room_id)

except Exception, e:
    print e