Abstract
------------
A supervisory control and data acquisition system built around a Raspberry PI, and arduino devices.Very similar to a home automation system ,but  built with the potential to control  A/C systems, Solar Panels, Wind Turbines, Hydroponics systems, Pumps and more. 

##Source Code 
https://github.com/hlopez058/homescada/

Background
-----------------
SCADA stands for "Supervisory Control and Data Acquisition" , in industrial systems and manufacturing plants it is a common term. The controlling of lights or recording camera footage is used in many home automation systems. What we desire to build is a step beyond. We are assuming that the common home will have more advanced systems such as Hydroponics,Solar Energy,Thermal Heaters, even Wind Turbines. These systems go beyond the typical automation and require the use of real time sensor data and real time responses to protect equipment and react to the environment. Industrial systems solve this problem using high-end servers with industrial PLC's and control devices. These industrial SCADA systems can costs well over 10's of thousands of dollars.

Getting Started 
--------------------

We decided to start out small, our first Goal was to bring up a raspberry pi and connect it to a set of led lights and control it as a webserver. 

###Webserver Setup & Web UI control
* Created python scripts that turned on/off gpio pins of rpi. 
* Installed nginx webserver on the raspberry pi. (root/usr/share/nginx/www)
* Created PHP webpage that would launch python scripts
* Hosted PHP webpage on nginx webserver
* Logged on to wifi and access ip address of rpi. opened webpage and launched py. scripts
* Created a color picker for the website

By completing the software setup we learned how to bring up the raspberry pi as a webserver and control LED lights strip, from a webpages color picker. 

### The Huzzah as a Remote Control

Our next step was to try to seperate the direct gpio control of the LED strip by the Rpi. and make it so that we can control it over the wifi using a $10 micro webserver by Adafruit called the "Huzzah" . It is based off the ESP8266 board. One of our hacker freinds at the Hacklab turned us on to it. 

The Huzzah works with Lua scripts but it can also be setup to work with arduino sketches. 

<code>
file.open("initled.lua","w");
file.writeline([[red=4;green=5;blue=6;]])
file.writeline([[
pwm.setup(red,500,512)
pwm.setup(green,500,512)
pwm.setup(blue,500,512)

pwm.start(red)
pwm.start(green)
pwm.start(blue)

function led(r,g,b)
 pwm.setduty(red,r)
 pwm.setduty(green,g)
 pwm.setduty(blue,b)
end
]])
file.close();

dofile("initled.lua")

file.open("init.lua","w+")
file.writeline([[sv=net.createServer(net.TCP, 30)]])
file.writeline([[sv:listen(80,function(c)
  c:on("receive", function(c, pl) print("connection made");
  end)
  c:send("The lua based server is working!!! ")
  end)]])
file.close()
</code>

#### Huzzah Problems
We did not like how the only way to load scripts and and do things in the huzzah was through the Lua scripting over a tty terminal. The terminal kept locking up and not displaying characters. We finally got fed up and decided to try the Arduino port for the Huzzah board wich would allow the loading of arduino sketches to program the Huzzah. 

#### Huzzah Arduino Fusion (Haaa)

* Install Arduino 1.6.5 from the Arduino website. https://www.arduino.cc/en/Main/Software
* Start Arduino and open Preferences window.
* Enter http://arduino.esp8266.com/stable/package_esp8266com_index.json into Additional Board Manager URLs field. You can add multiple URLs, separating them with commas.
* Open Boards Manager from Tools > Board menu and install esp8266 platform (and don't forget to select your ESP8266 board from Tools > Board menu after installation).

Once the setup is ready and installed. I chose the huzzah board option in the arduino ide. 


Now you'll need to put the board into bootload mode. You'll have to do this before each upload. There is no timeout for bootload mode, so you don't have to rush!

* Hold down the GPIO0 button, the red LED will be lit
* While holding down GPIO0, click the RESET button

* When you release the RESET button, the red LED will be lit dimly, this means its ready to bootload

Once the ESP board is in bootload mode, upload the sketch via the IDE

We successfully tested the blinking lights, and now we attempted to load the wifi setup on the Huzzah board. 

###Working Wifi Comms
We finally got the communication working between the Wifi RPi server and the Wifi Huzzah Server. 

By using an http GET requested by the Huzzah from the RPI host and url . The RPI provides a webpage on the requested URL that is built by a php script. The script reads the URL parameters and determines what it should reply with. In our case we had the script read an ID variable and reply with a string "GPIO06=1". The string is parsed by the Huzzah's script and then triggers the GPIO pin to go high. 

Our results so far are promising. With this we began our debate on what the best architecture for wifi communication would be.What kind of handshaking,protocols,client/server architecture would be best for a distributed automation system. 

###Architectural Challenges 
1 . **Huzzah Requires User/Password to enter Wifi.** We are thinking of solving this issue the same way most IoT devices solve it. Using a "calibration" step possibly with NFC or IR. 
2. **Wifi can become unreliable. Need a way to stay redundant.** 
3.**Talking the Industrial Protocols (DNP3/Modbus/OPC).**

###Learning How to Speak

#### Calibration through IR (To Be Done)
There will be a section on how we tackle the first architectural challenge. Getting wifi/credentials to the Huzzah to logon to the wifi in the first place. We will test out an IR sensor on the Huzzah and Rpi for calibrating and creating a host table on the Rpi of all known devices. Since we can first hash out our communication protocol without the need of calibration. (Just hardcode the wifi info) we will attempt to hash it out first.

#### WebServer Communicaiton
We established communication between the Huzzah and the Raspberry PI. We went over how we setup a Raspberry Pi webserver in previous sections. The next step was to program the Huzzah. We got the Huzzah online and running with a special "Board" package for Arduino's, provided in the Arduino 1.6.5 IDE. Our first sketch on the Huzzah was from the tutorial provided by Adafruit that configured the Huzzah as a webclient, pinging a host and reading back its response. We would like to modify that and go with a "Push" method. The RPi will have a host table of connected Huzzah's and the Huzzah will handle a POST/GET HTTP request from the RPi. Within the body of the request the Rpi would "push" a JSON object that could be interpreted by the Huzzah. 

The Arduino Sketch on the huzzah is nothing fancy. I started by using the "WebServer" example that comes with the Board package for ESP2866. It instantly adds all the libraries, and creates a "hello world" page for anyone on your network that navigates to the Huzzah's IP address. Below shows the setup for the Huzzahs template. I added the "onWrite" function when a client tries to navigate to the "/write" path .

<code>
void setup(void)
{
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  Serial.println("");
   // Wait for connection
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
  
  if (mdns.begin("esp8266", WiFi.localIP())) {
    Serial.println("MDNS responder started");
  }  
  server.on("/", handleRoot);
//Added the line below
  server.on("/write", handleWrite);

  server.begin();
  Serial.println("HTTP server started");
}


</code>

The "handleWrite" function in the arduino sketch is also almost a carbon copy of the functions provided in the template. I modified it a little to read the posted text back to the Rpi.

<code>
void handleWrite(){
  digitalWrite(led, 1);
  String message = "Writing\n\n";
  message += "URI: ";
  message += server.uri();
  message += "\nMethod: ";
  message += (server.method() == HTTP_GET)?"GET":"POST";
  message += "\nArguments: ";
  message += server.args();
  message += "\n";
  for (uint8_t i=0; i<server.args(); i++){
    message += " " + server.argName(i) + ": " + server.arg(i) + "\n";
  }  
  server.send(200, "text/plain", message);

//do something with data pushed from Rpi

}
</code>

Now that the hard part is over we needed to make a webpage on the Rpi server that would post an ajax request to the Huzzah and have the huzzah read the arguments and eventually do something with it. I wrote a javascript function that is kicked off by a button click. It dials the Huzzahs IP address "/write" function and passes in a json data object. Each argument is parsed by the Huzzah and then posted back as a response. The javascript just dumps it to a response div on the page for testing. 

<code>
function OnWrite() {
  
//Huzzah Path
  var uri = "http://192.168.0.9/write" 
  var res = encodeURI(uri);
  var ajaxurl = res;

  //Json object
  data =  {'action':'write','pin':'13','set':'1'} ;
  

  $.post(ajaxurl, data, function (response) {
      // Response div goes here.
      document.getElementById("write_response").innerHTML = response;
  });
}
</code>

The good news is that all this ended up working. The next step is to expand the json object to better model a "Push" notification that would make sense, and for the Huzzah code to parse out each argument and correlate it to some action. Actions, such as pin control, or low level logic. Eventually building a message handler that will make programming the Huzzah as easy as writing to GPIO pins on the Rpi.