// Demonstrates how to get conferences list add new conference
// author Paweł Brydziński <pbrydzinski@clickmeeting.com>
// http://www.clickmeeting.com/
// usage: mcs example.cs && mono example.exe


using System;
using System.IO;
using System.Text;
using System.Collections;
using System.Collections.Generic;
using System.Net;

namespace ClickMeetingApi
{
    class Example
    {
        static String api_key = "API KEY";
        static String api_url = "https://api.clickmeeting.com/v1/";

        public static void Main(string[] args)
        {
            // getConferences();
            createConference();
        }

        public static void getConferences()
        {
            String url = api_url+"conferences";

            // send headers and content in one request
            System.Net.ServicePointManager.Expect100Continue = false;

            // initialize client
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "GET";
            request.ContentType = "application/x-www-form-urlencoded";
            request.Headers["X-Api-Key"] = api_key;

            String response_string = null;

            try
            {
                response_string = getStream((HttpWebResponse)request.GetResponse());
            }
            catch(WebException e)
            {
                // check for communication and response errors
                Console.WriteLine(e.Message);
                response_string = getStream((HttpWebResponse)e.Response);
            }
            catch (Exception e)
            {
                // check for communication and response errors
                Console.WriteLine(e.Message);
                Environment.Exit(0);
            }

            Console.Write(response_string);
        }

        public static void createConference()
        {
            String url = api_url+"conferences";

            //POST
            String _request = "";
            _request += "&name=APItest";
            _request += "&room_type=meeting";
            _request += "&permanent_room=1";
            _request += "&access_type=1";

            // send headers and content in one request
            System.Net.ServicePointManager.Expect100Continue = false;

            byte[] request_bytes = Encoding.UTF8.GetBytes(_request);

            // initialize client
            HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";
            request.Headers["X-Api-Key"] = api_key;
            request.ContentLength = request_bytes.Length;

            String response_string = null;

            try
            {
                Stream request_stream = request.GetRequestStream();
                request_stream.Write(request_bytes, 0, request_bytes.Length);
                request_stream.Close();

                response_string = getStream((HttpWebResponse)request.GetResponse());
            }
            catch(WebException e)
            {
                // check for communication and response errors
                Console.WriteLine(e.Message);
                response_string = getStream((HttpWebResponse)e.Response);
            }
            catch (Exception e)
            {
                // check for communication and response errors
                Console.WriteLine(e.Message);
                Environment.Exit(0);
            }

            Console.Write(response_string);
        }

        public static String getStream(HttpWebResponse response)
        {
            String response_string = null;
            Stream response_stream = response.GetResponseStream();

            StreamReader reader = new StreamReader(response_stream);
            response_string = reader.ReadToEnd();
            reader.Close();

            response_stream.Close();
            response.Close();

            return response_string;
        }
    }
}
