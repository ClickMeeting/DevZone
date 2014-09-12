#!/usr/bin/python
# coding=UTF-8

# Demonstrates how to get conferences list add new conference
# author Paweł Brydziński <pbrydzinski@implix.com>
# http://implix.com
# usage: python example.py

import pprint
import urllib
import urllib2
import json

class ClickMeetingApi:
    API_URL = 'https://api.clickmeeting.com/v1/'
    API_KEY = 'API KEY'

    def __init__(self):
        self.url = self.API_URL+'conferences'
        self.headers = { 'X-Api-Key' : self.API_KEY }
    def getConferences(self):
        print json.dumps(
        	json.loads(
        		urllib2.urlopen(
        			urllib2.Request(self.url, None, self.headers)
        		).read()
        	), indent=4
        )
    def addConference(self):
        params = urllib.urlencode({'name': 'APItest', 'room_type': 'meeting', 'permanent_room': 1, 'access_type': 1})
        print json.dumps(
        	json.loads(
        		urllib2.urlopen(
        			urllib2.Request(self.url, params, self.headers)
        		).read()
        	), indent=4
        )
f = ClickMeetingApi()
f.getConferences()
f.addConference()
