#!/usr/bin/ruby

# Demonstrates how to get conferences list add new conference
# author Paweł Brydziński <pbrydzinski@implix.com>
# http://implix.com
# usage: ruby example.rb

require 'net/http'
require 'openssl'
require 'json'

class ClickMeetingApi

    API_KEY = 'API KEY'
    API_URL = 'https://api.clickmeeting.com/v1/'

    def getConferences
        request()
    end

    def addConference
        request({name: 'APItest', room_type: 'meeting', permanent_room: 1, access_type: 1}, 'POST')
    end

    def request(args={}, type='GET')
        uri = URI.parse(API_URL+'conferences')
        uri.query = URI.encode_www_form(args)

        if type =='POST'
            request = Net::HTTP::Post.new(uri.request_uri)
        else
            request = Net::HTTP::Get.new(uri.request_uri)
        end
        request.add_field("X-Api-Key", API_KEY)

        http = Net::HTTP.new(uri.host, uri.port)
        http.use_ssl = true
        http.verify_mode = OpenSSL::SSL::VERIFY_NONE

        response = http.request(request)
        puts JSON.pretty_generate(JSON.parse(response.body))
    end
end

ClickMeetingApi.new.addConference
ClickMeetingApi.new.getConferences