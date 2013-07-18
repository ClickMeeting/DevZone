#!/usr/bin/ruby

# Demonstrates how to get conferences list add new conference
# author Paweł Brydziński <pbrydzinski@implix.com>
# http://implix.com
# usage: ruby example.rb


require 'net/http'
require 'json'

class ClickMeetingApi

    @@api_key = 'API_KEY'
    @@api_url = 'https://api.clickmeeting.com/v1/'

    def getConferences
        request({api_key: @@api_key})
    end

    def addConference
        request({api_key: @@api_key, name: 'APItest1', room_type: 'meeting', permanent_room: 1, access_type: 1}, 'POST')
    end

    def request(args={}, type='GET')
        uri = URI.parse(@@api_url+'conferences')
        uri.query = URI.encode_www_form(args)

        http = Net::HTTP.new(uri.host, uri.port)
        http.use_ssl = true
        http.verify_mode = OpenSSL::SSL::VERIFY_NONE

        if type =='POST'
            request = Net::HTTP::Post.new(uri.request_uri)
        else
            request = Net::HTTP::Get.new(uri.request_uri)
        end

        response = http.request(request)
        puts JSON.pretty_generate(JSON.parse(response.body))
    end
end

ClickMeetingApi.new.addConference
ClickMeetingApi.new.getConferences