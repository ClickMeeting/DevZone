# ClickMeeting Rest Client

require 'rubygems'
require 'net/https'
require 'openssl'
require 'json'
require 'uri'
require 'cgi'

module ClickMeeting
    class ClientError < StandardError; end
    class ClickMeetingRestClient    
        def initialize(params)
            @formats = ["json", "xml", "js", "printr"]
            @api_key = params["api_key"] ? params["api_key"] : nil
            @url = params["url"] ? params["url"] : "https://api.clickmeeting.com/v1/"
            @format = (params["format"] && @formats.include?(params["format"].downcase) ) ? params["format"].downcase  : nil
        end

        def sendRequest(method, path, params = nil, format_response = true, is_upload_file = false)
            
            uri = URI.parse(@url+path+"."+(@format ? @format : "json"))
            
            case method
                when 'PUT'
                request = Net::HTTP::Put.new(uri.request_uri)
                when 'POST'
                request = Net::HTTP::Post.new(uri.request_uri)
                when 'GET'
                request = Net::HTTP::Get.new(uri.request_uri)
                when 'DELETE'
                request = Net::HTTP::Delete.new(uri.request_uri)
            end
            
            if is_upload_file
                boundary = "Ef84C7f9FS"+rand(1..999999).to_s
    
                file = params
                file_body = []
                file_body << "--#{boundary}\r\n"
                file_body << "Content-Disposition: form-data; name=\"uploaded\"; filename=\"#{File.basename(file)}\"\r\n"
                file_body << "Content-Type: text/plain\r\n"
                file_body << "\r\n"
                file_body << File.read(file)
                file_body << "\r\n--#{boundary}--\r\n"
    
                request.add_field("Content-Type", "multipart/form-data, boundary=#{boundary}")
    
            end
            
            if params
                request.body = is_upload_file ? file_body.join : hash_to_query(params)
            end
            
            request.add_field("X-Api-Key", @api_key)
            http = Net::HTTP.new(uri.host, uri.port)
            http.use_ssl = true
            http.verify_mode = OpenSSL::SSL::VERIFY_NONE
            
            response = http.request(request)

            raise ClientError.new("400 Bad Request") if response.code.to_i == 400
            raise ClientError.new("401 Unauthorized") if response.code.to_i == 401
            raise ClientError.new("404 Not Found") if response.code.to_i == 404
            raise ClientError.new("500 Internal Sever Error") if response.code.to_i == 500
            raise ClientError.new("501 Not implemented") if response.code.to_i == 501
            
            unless response.code.to_i == 200 or response.code.to_i == 201
                raise ClientError.new(response.body)
            end
            
            if !@format && format_response
                result = JSON.parse(response.body)
            else
                result = response.body
            end
            result
        end
        
        def url_encode(text)
            CGI.escape text.to_s.to_str
        end
    
        def hash_to_query(hash, prefix = nil)
            hash.collect do |key, value|
                if value.is_a?(Hash)
                    final_key = prefix ? "#{prefix}[#{key}]" : key
                    hash_to_query(value, final_key)
                elsif value.is_a?(Array)
                    final_key = prefix ? "#{prefix}[#{key}][]" : "#{key}[]"
                    hash_to_query(value, final_key)
                elsif key.is_a?(Hash)
                    final_key = prefix
                    hash_to_query(key, final_key)
                else
                    final_key = prefix ? "#{prefix}[#{key}]" : key
                    url_encode(final_key) + "=" + url_encode(value)
                end
            end.sort * '&'
        end
        
        def conferences(status = 'active', page = 1)
            sendRequest('GET', 'conferences/' + status + '?page=' + page)
        end
    
        def conference(room_id)
        
            sendRequest('GET', 'conferences/'+room_id.to_s)
        end
        
        def addConference(params)
        
            sendRequest('POST', 'conferences', params)
        end
        
        def editConference(room_id, params)
        
            sendRequest('PUT', 'conferences/'+room_id.to_s, params)
        end
     
        def deleteConference(room_id)
        
            sendRequest('DELETE', 'conferences/'+room_id.to_s)
        end
    
        def conferenceAutologinHash(room_id, params)
        
            sendRequest('POST', 'conferences/'+room_id.to_s+'/room/autologin_hash', params)
        end
    
        def generateConferenceTokens(room_id, params)
        
            sendRequest('POST', 'conferences/'+room_id.to_s+'/tokens', params)
        end
    
        def conferenceTokens(room_id)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/tokens')
        end
    
        def conferenceSessions(room_id)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/sessions')
        end
    
        def conferenceSession(room_id, session_id)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/sessions/'+session_id.to_s)
        end
        
        def conferenceSessionAttendees(room_id, session_id)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/sessions/'+session_id.to_s+'/attendees')
        end
    
        def generateConferenceSessionPDF(room_id, session_id, lang = 'en')
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/sessions/'+session_id.to_s+'/generate-pdf/'+lang)
        end
        
        def addContact(params)
        
            sendRequest('POST', 'contacts', params)
        end

        def timeZoneList()
        
            sendRequest('GET', 'time_zone_list')
        end
    
        def countryTimeZoneList(country)
        
            sendRequest('GET', 'time_zone_list/'+country)
        end

        def phoneGatewayList()

            sendRequest('GET', 'phone_gateways')
        end

        def conferenceSkins()

            sendRequest('GET', 'conferences/skins')
        end
    
        def addConferenceRegistration(room_id, params)
        
            sendRequest('POST', 'conferences/'+room_id.to_s+'/registration', params)
        end
    
        def conferenceRegistrations(room_id, status)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/registrations/'+status)
        end
    
        def conferenceSessionRegistrations(room_id, session_id, status)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/sessions'+session_id.to_s+'/registrations/'+status)
        end
    
        def fileLibrary()
        
            sendRequest('GET', 'file-library')
        end
        
        def conferenceFileLibrary(room_id)
        
            sendRequest('GET', 'file-library/conferences/'+room_id.to_s)
        end
        
        def fileLibraryFile(file_id)
        
            sendRequest('GET', 'file-library/'+file_id.to_s)
        end
        
        def deleteFileLibraryFile(file_id)
        
            sendRequest('DELETE', 'file-library/'+file_id.to_s)
        end
        
        def addFileLibraryFile(file_path)
        
            sendRequest('POST', 'file-library', file_path, true, true)
        end
        
        def fileLibraryContent(file_id)
        
            sendRequest('GET', 'file-library/'+file_id.to_s+'/download', nil, false)
        end
        
        def conferenceRecordings(room_id)
        
            sendRequest('GET', 'conferences/'+room_id.to_s+'/recordings')
        end
        
        def deleteConferenceRecordings(room_id)
        
            sendRequest('DELETE', 'conferences/'+room_id.to_s+'/recordings')
        end
        
        def deleteConferenceRecording(room_id, recording_id)
        
            sendRequest('DELETE', 'conferences/'+room_id.to_s+'/recordings/'+recording_id.to_s)
        end
        
        def chats()
        
            sendRequest('GET', 'chats')
        end
        
        def conferenceSessionChats(session_id)
        
            sendRequest('GET', 'chats/'+session_id.to_s, nil, false)
        end
        
        def sendConferenceEmailInvitations(room_id, lang = 'en', params)
        
            sendRequest('POST', 'conferences/'+room_id.to_s+'/invitation/email/'+lang, params)
        end
        
    end
end
