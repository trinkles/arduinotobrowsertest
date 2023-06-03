#include <WiFi.h>
#include <WiFiClient.h>

const char* ssid = "yourwifi";
const char* password = "yourpass";

const char* server = "yourserver";
const int serverPort = 80; //default

const char* url = "/arduinotest.php"; 

const char* sensor1Name = "value1";
const char* sensor2Name = "value2";

void setup() {
  Serial.begin(115200);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");

  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  int sensorValue1 = analogRead(32);
  int sensorValue2 = analogRead(33);

  String requestUrl = String(url) + "?" + sensor1Name + "=" + String(sensorValue1) + "&" + sensor2Name + "=" + String(sensorValue2);

  WiFiClient client;
  if (client.connect(server, serverPort)) {
    Serial.println("Connected to server");
    Serial.println("Sending request to server");
    client.print(String("GET ") + requestUrl + " HTTP/1.1\r\n" + "Host: " + server + "\r\n" + "Connection: close\r\n\r\n");
    while (client.connected()) {
      if (client.available()) {
        String line = client.readStringUntil('\r');
        Serial.println(line);
      }
    }
    client.stop();
    Serial.println("Request completed");
  } else {
    Serial.println("Failed to connect to server");
  }

  delay(3000);
}