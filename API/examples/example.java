// Demonstrates how to get conferences list add new conference
// author Paweł Brydziński <pbrydzinski@implix.com>
// http://implix.com
// usage: javac example.java && java example


import java.net.*;
import java.io.*;
import java.util.*;
import java.text.*;

public class example
{
    static String api_key = "API KEY";
    static String api_url = "https://api.clickmeeting.com/v1/";

    public static void main(String[] args)
    {
        getConferences();
        createConference();
    }

    public static void getConferences()
    {
        try
        {
            URL url = new URL(api_url+"conferences");
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestProperty( "X-Api-Key", api_key);

            System.out.println(readOutput(connection));
        }
        catch(IOException e)
        {
           e.printStackTrace();
        }
    }

    public static void createConference()
    {
        try
        {
            Date date = new Date((new Date()).getTime()+(1000 * 60 * 60 * 24 *2));

            String data = "";
            data += "&name=APItest";
            data += "&room_type=meeting";
            data += "&permanent_room=0";
            data += "&access_type=1";
            data += "&lobby_description=This is test for room created by API.";
            data += "&starts_at="+(new SimpleDateFormat("yyyy-MM-dd hh:mm")).format(date)+"";
            data += "&duration=1";

            URL url = new URL(api_url+"conferences");
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();

            connection.setDoOutput(true);
            connection.setRequestMethod( "POST" );
            connection.setRequestProperty( "Content-Type", "application/x-www-form-urlencoded" );
            connection.setRequestProperty( "Content-Length", String.valueOf(data.length()));
            connection.setRequestProperty( "X-Api-Key", api_key);
            OutputStream os = connection.getOutputStream();
            os.write( data.getBytes() );

            System.out.println(readOutput(connection));
        }
        catch(IOException e)
        {
           e.printStackTrace();
        }
    }

    public static String readOutput(HttpURLConnection connection)
    {
        String string = "";
        try
        {
            BufferedReader in = new BufferedReader(new InputStreamReader(connection.getInputStream()));
            String current;
            while((current = in.readLine()) != null)
            {
               string += current;
            }
        }
        catch(IOException e)
        {
           e.printStackTrace();
        }
        return string;
    }

}
