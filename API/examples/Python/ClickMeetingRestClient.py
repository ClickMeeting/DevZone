 # ClickMeeting REST client

import urllib
import json
import requests
import types

class ClickMeetingRestClient:

    def __init__(self, params):
        self.api_key = params['api_key'] if params.has_key('api_key') else None
        
        formats = ['json', 'xml', 'js', 'printr']
        
        self.url = params['url'] if params.has_key('url') else 'https://api.clickmeeting.com/v1/'
        
        self.format = params['format'].lower() if params.has_key('format') and params['format'].lower() in formats else None
        
    def sendRequest(self, method, path, params = None, format_response = True, is_upload_file = False):
        
        url = '%s%s.%s' % (self.url, path, 'json' if self.format == None else self.format)
        
        headers = {'X-Api-Key' : self.api_key}
        
        files = None
        data = None
        
        if is_upload_file:
            files = {'uploaded': open(params, 'rb')}
        else:
            data = self.build_query(params)
            headers['Content-Type'] = 'application/x-www-form-urlencoded'
            
        response = requests.request(method=method.upper(), url=url, data=data, files=files, headers=headers, verify=True)
        response.raise_for_status()
        
        if self.format == None and format_response == True:
            response = response.json()
        else:
            response = response.content
        
        return response
        
    def conferences(self):
        return self.sendRequest('GET', 'conferences')
        
    def conference(self, room_id):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id)
        
    def addConference(self, params):
        return self.sendRequest('POST', 'conferences', params)
        
    def editConference(self, room_id, params):
        return self.sendRequest('PUT', 'conferences/'+'%s' % room_id, params)
        
    def deleteConference(self, room_id):
        return self.sendRequest('DELETE', 'conferences/'+'%s' % room_id)
        
    def conferenceAutologinHash(self, room_id, params):
        return self.sendRequest('POST', 'conferences/'+'%s' % room_id +'/room/autologin_hash', params)
        
    def generateConferenceTokens(self, room_id, params):
        return self.sendRequest('POST', 'conferences/'+ '%s' % room_id +'/tokens', params)
        
    def conferenceTokens(self, room_id):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/tokens')
        
    def conferenceSessions(self, room_id):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/sessions')
        
    def conferenceSession(self, room_id, session_id):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/sessions/'+'%s' % session_id)
        
    def conferenceSessionAttendees(self, room_id, session_id):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/sessions/'+'%s' % session_id+'/attendees')
        
    def generateConferenceSessionPDF(self, room_id, session_id, lang = 'en'):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/sessions/'+'%s' % session_id+'/generate-pdf/'+lang)
        
    def timeZoneList(self):
        return self.sendRequest('GET', 'time_zone_list')
        
    def countryTimeZoneList(self, country):
        return self.sendRequest('GET', 'time_zone_list/'+country)
    
    def phoneGatewayList(self):
        return self.sendRequest('GET', 'phone_gateways')
  
    def conferenceSkins(self):
        return self.sendRequest('GET', 'conferences/skins')
  
    def addConferenceRegistration(self, room_id, params):
        return self.sendRequest('POST', 'conferences/'+'%s' % room_id+'/registration', params)
        
    def conferenceRegistrations(self, room_id, status):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/registrations/'+status)
        
    def conferenceSessionRegistrations(self, room_id, session_id, status):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/sessions'+'%s' % session_id+'/registrations/'+status)
        
    def fileLibrary(self):
        return self.sendRequest('GET', 'file-library')
        
    def conferenceFileLibrary(self, room_id):
        return self.sendRequest('GET', 'file-library/conferences/'+'%s' % room_id)
        
    def fileLibraryFile(self, file_id):
        return self.sendRequest('GET', 'file-library/'+'%s' % file_id)
        
    def deleteFileLibraryFile(self, file_id):
        return self.sendRequest('DELETE', 'file-library/'+'%s' % file_id)
        
    def addFileLibraryFile(self, file_path):
        return self.sendRequest('POST', 'file-library', file_path, True, True)
        
    def fileLibraryContent(self, file_id):
        return self.sendRequest('GET', 'file-library/'+'%s' % file_id+'/download', None, False)
        
    def conferenceRecordings(self, room_id):
        return self.sendRequest('GET', 'conferences/'+'%s' % room_id+'/recordings')
        
    def deleteConferenceRecordings(self, room_id):
        return self.sendRequest('DELETE', 'conferences/'+'%s' % room_id+'/recordings')
        
    def deleteConferenceRecording(self, room_id, recording_id):
        return self.sendRequest('DELETE', 'conferences/'+'%s' % room_id+'/recordings/'+'%s' % recording_id)
        
    def chats(self):
        return self.sendRequest('GET', 'chats')
        
    def conferenceSessionChats(self, session_id):
        return self.sendRequest('GET', 'chats/'+'%s' % session_id, None, False)
        
    def sendConferenceEmailInvitations(self, room_id, lang='en', params=None):
        return self.sendRequest('POST', 'conferences/'+'%s' % room_id+'/invitation/email/'+lang, params)
        
    def build_query(self, params):
        def build_query_item(params, base_key=None):
            results = list()
            
            if(type(params).__name__ == 'dict'):
                for key, value in params.items():
                    if(base_key):
                        new_base = urllib.quote(unicode("%s[%s]" % (base_key, key)))
                        results += build_query_item(value, new_base)
                    else:
                        results += build_query_item(value, key)
            elif(type(params).__name__ == 'list'):
                for (index, value) in enumerate(params):
                    if(base_key):
                        results += build_query_item(value, "%s[]" % (base_key))
                    else:
                        results += build_query_item(value)
            else:
                quoted_item = urllib.quote(unicode(params))
                if(base_key):
                    results.append("%s=%s" % (base_key, quoted_item))
                else:
                    results.append(quoted_item)
            return results
            
        return '&'.join(build_query_item(params))
