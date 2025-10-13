// Local includes
#include <Arduino.h>
#include <WiFi.h>

// Custom libraries
#include "AlertsManager.h"
#include "DHTSensor.h"
#include "LedsManager.h"
#include "ProximitySensorPair.h"
#include "ServerConnector.h"
#include "TelegramBot.h"

// Define global constants
#define BOARD_ID 1                      // Unique ID for this board
#define alert_interval 60000            // Interval to send grouped alerts (ms)
#define load_data_interval 10000        // Interval to load frequency data (ms)
#define update_thresholds_interval 5000 // Interval to update thresholds from server (ms)
#define update_leds_interval 2000       // Interval to update LEDs status (ms)

// Define timers
unsigned long lastAlertTime = 0;
unsigned long lastLoadDataTime = 0;
unsigned long lastThresholdsUpdateTime = 0;
unsigned long lastLedsUpdateTime = 0;

// Instances of sensors and managers
AlertsManager alertsManager(BOARD_ID, alert_interval);      // Alerts manager
DHTSensor dhtSensor(4, DHT11);                              // DHT instance
LedsManager ledsManager(2, 15, 2);                          // 3 LEDs instance
ProximitySensorPair proximitySensors(18, 19);               // 2 Proximity sensors instance

// Instances of Telegram bot and server connector
const char* host = "192.168.80.27";                         // IP address of the server
const int   port = 80;                                      // IP port of the server
TelegramBot telegramBot("", "");                            // Empty credentials, it will be updated later 
ServerConnector server(host, port, BOARD_ID, &telegramBot); // Server connector instance

// WiFi credentials
const char* ssid = "Jose";           // Wifi SSID
const char* password = "Viani1992";   // Wifi Password


void setup() {
  Serial.begin(115200);

  // Connect to WiFi
  server.connectWiFi(ssid, password);
 
  // Update Telegram credentials from server
  server.updateTelegramCredentials();

  // Update thresholds from server
  server.updateThresholdsFromDB();

  // Initialize sensors and managers
  dhtSensor.begin();
  ledsManager.begin();
  proximitySensors.begin();
}


void loop() {
    unsigned long currentTime = millis();
    
    // Read DHT sensor every 2 seconds
    if (currentTime - lastLedsUpdateTime >= 2000) {
        dhtSensor.read();
        lastLedsUpdateTime = currentTime;
    }
    
    // Update proximity sensors state
    proximitySensors.update();
    
    // Load frequency data and send to server every load_data_interval
    if (currentTime - lastLoadDataTime >= load_data_interval) {
        int freqIn = proximitySensors.getFreqForward();
        int freqOut = proximitySensors.getFreqBackward();
    
        // Save data to server
        server.saveDataToDB(dhtSensor.getTemperature(), dhtSensor.getHumidity(), freqIn, freqOut);
    
        // Check for individual alerts
        String tempAlert = alertsManager.checkTemperature(dhtSensor.getTemperature(), server.getTempMin(), server.getTempMax());
        String humAlert = alertsManager.checkHumidity(dhtSensor.getHumidity(), server.getHumMin(), server.getHumMax());
        String freqAlert = alertsManager.checkFrequency(freqIn, freqOut, server.getFreqMin(), server.getFreqMax());
    
        // Add alerts to the manager
        alertsManager.addAlert(tempAlert);
        alertsManager.addAlert(humAlert);
        alertsManager.addAlert(freqAlert);
    
        lastLoadDataTime = currentTime;
    }
    
    // Update thresholds from server every update_thresholds_interval
    if (currentTime - lastThresholdsUpdateTime >= update_thresholds_interval) {
        server.updateThresholdsFromDB();
        lastThresholdsUpdateTime = currentTime;
    }
    
    // Update LEDs status every update_leds_interval
    if (currentTime - lastLedsUpdateTime >= update_leds_interval) {
        ledsManager.update(
            dhtSensor.getTemperature(),
            dhtSensor.getHumidity(),
            proximitySensors.getFreqForward(),
            proximitySensors.getFreqBackward(),
            server.getTempMin(),
            server.getTempMax(),
            server.getHumMin(),
            server.getHumMax(),
            server.getFreqMin(),
            server.getFreqMax()
        );
        lastLedsUpdateTime = currentTime;
    }
    
    // Send grouped alerts if the interval has passed
    if (alertsManager.shouldSend()) {
        String groupedAlerts = alertsManager.getGroupedAlerts();
        if (groupedAlerts.length() > 0) {
            telegramBot.sendMessage(groupedAlerts);
            Serial.println("📨 Alertas enviadas");
        }
    }
}