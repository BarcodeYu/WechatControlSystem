#include <OneWire.h>
#include <DallasTemperature.h>
#include <SPI.h>
#include <Ethernet.h>

// 定义DS18B20数据口连接arduino的2号IO上
#define ONE_WIRE_BUS 2

// 初始连接在单总线上的单总线设备
OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature tempSensor(&oneWire);

int tempSensor_data = 0;
 

// assign a MAC address for the ethernet controller.
// fill in your address here:
byte mac[] = {
  0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED
};

char state;
// fill in an available IP address on your network here,
// for manual configuration:
IPAddress ip(192, 168, 3, 177);

// fill in your Domain Name Server address here:
IPAddress myDns(192, 168, 3, 1);

// initialize the library instance:
EthernetClient client;

char server[] = "1.barcode.applinzi.com";
//IPAddress server(64,131,82,241);

unsigned long lastConnectionTime = 0;             // last time you connected to the server, in milliseconds
const unsigned long postingInterval = 10L * 1000L; // delay between updates, in milliseconds
// the "L" is needed to use long type numbers

void setup() {
  // start serial port:
  Serial.begin(9600);
  tempSensor.begin(); //温度传感器开启
  while (!Serial) {
    ; // wait for serial port to connect. Needed for native USB port only
  }

  // give the ethernet module time to boot up:
  delay(1000);
  // start the Ethernet connection using a fixed IP address and DNS server:
  Ethernet.begin(mac, ip, myDns);
  // print the Ethernet board/shield's IP address:
  Serial.print("My IP address: ");
  Serial.println(Ethernet.localIP());
  pinMode(7, OUTPUT);   
  // 初始库
}

void loop() {
  // if there's incoming data from the net connection.
  // send it out the serial port.  This is for debugging
  // purposes only:
  
  
  if (client.available()) {
    state = client.read();
    if(state == '}'){
      Serial.println(state);
      digitalWrite(7, HIGH); 
       Serial.print("on");
      delay(9000);    
    }
    else if(state == '{'){
      Serial.println(state);
      digitalWrite(7, LOW);
       Serial.print("off");
      delay(9000); 
    }
  tempSensor.requestTemperatures();// 发送命令获取温度
  tempSensor_data =tempSensor.getTempCByIndex(0);  //读取温度值赋值给tempSensor_data
  //delay(1500);
   // Serial.print(state);
   Serial.write(state);
  }

  // if ten seconds have passed since your last connection,
  // then connect again and send data:
  if (millis() - lastConnectionTime > postingInterval) {
  httpRequest();
  }

}

// this method makes a HTTP connection to the server:

void httpRequest() {
  // close any connection before send a new request.
  // This will free the socket on the WiFi shield
  client.stop();

  // if there's a successful connection:
  if (client.connect(server, 80))
  {
    Serial.println("connecting...");
    // send the HTTP GET request:
    client.print("GET http://1.barcode.applinzi.com/down.php?token=barcode&data=");
    client.print(tempSensor_data);
    client.print("HTTP/1.1");
    client.println("Host:w.rdc.sae.sina.com.cn");
    client.println("User-Agent: arduino-ethernet");
    client.println("Connection: close");
    client.println();

    // note the time that the connection was made:
    lastConnectionTime = millis();
  } 
  else 
  {
    // if you couldn't make a connection:
    Serial.println("connection failed");
  }
}