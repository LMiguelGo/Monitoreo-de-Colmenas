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
#include "DualGateController.h"
#include "HeaterController.h"

// Define global constants
#define BOARD_ID 1                         // UNIQUE ID OF THE BOARD
#define alert_interval 60000               // Interval to send grouped alerts (ms)
#define load_data_interval 5000            // Interval to load frequency data (ms)
#define update_thresholds_interval 10000   // Interval to update thresholds from server (ms)
#define readAndUpdateInterval 2000         // Interval to read sensors and update LEDs and alerts (ms)
#define automatic_control_interval 4000    // Interval to update control and actuators (ms)

// Define timers
unsigned long lastAlertTime = 0;
unsigned long lastLoadDataTime = 0;
unsigned long lastThresholdsUpdateTime = 0;
unsigned long lastReadAndUpdateTime = 0;
unsigned long lastAutomaticControlTime = 0;

// Define PINs for sensors and LEDs
#define DHT_PIN 4                    // DHT22 sensor pin
#define LED_TEMP_PIN 2               // Temperature LED pin
#define LED_HUM_PIN 15               // Humidity LED pin
#define LED_FREQ_PIN 5               // Activity LED pin
#define PROXIMITY_SENSOR_A_PIN 18    // Proximity Sensor A pin
#define PROXIMITY_SENSOR_B_PIN 19    // Proximity Sensor B pin
#define UPPER_SERVO_PIN 13           // Upper gate servo pin
#define LOWER_SERVO_PIN 12           // Lower gate servo pin
#define HEATER_PIN 23                // Heater control pin

// Instances of sensors and managers
AlertsManager alertsManager(BOARD_ID);                                                   // Alerts manager
DHTSensor dhtSensor(DHT_PIN, DHT22);                                                     // DHT22 sensor instance
LedsManager ledsManager(LED_TEMP_PIN, LED_HUM_PIN, LED_FREQ_PIN);                        // LEDs manager instance
ProximitySensorPair proximitySensors(PROXIMITY_SENSOR_A_PIN, PROXIMITY_SENSOR_B_PIN);    // Proximity sensors instance

// Instances of actuators
DualGateController gateController(UPPER_SERVO_PIN, LOWER_SERVO_PIN);                   // Dual gate controller instance
HeaterController heaterController(HEATER_PIN);                                           // Heater controller instance


// Instances of Telegram bot and server connector
const char* host = "10.25.196.88";                            // IP address of the server
const int   port = 80;                                        // IP port of the server
TelegramBot telegramBot("", "");                              // Empty credentials, it will be updated later 
ServerConnector server(host, port, BOARD_ID, &telegramBot);   // Server connector instance

// WiFi credentials
const char* ssid = "Redmi 10";            // Wifi SSID
const char* password = "tauret20";        // Wifi Password


void setup() {
    Serial.begin(115200);

    // Small delay to allow Serial monitor to initialize
    delay(1000);

    Serial.println("\n\nINICIANDO SISTEMA DE MONITOREO DE COLMENAS...\n");

    // Connect to WiFi
    server.connectWiFi(ssid, password);
    
    // Small delay to ensure stable connection
    delay(1000); 

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
    // Current time
    unsigned long currentTime = millis();
    
    // Read DHT sensor and update LEDs
    if (currentTime - lastReadAndUpdateTime >= readAndUpdateInterval) {
        // Read DHT sensor
        dhtSensor.read();
        
        // Update LEDs status
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

        // Check and register alerts
        alertsManager.checkTemperature(dhtSensor.getTemperature(), server.getTempMin(), server.getTempMax());
        alertsManager.checkHumidity(dhtSensor.getHumidity(), server.getHumMin(), server.getHumMax());
        alertsManager.checkFrequency(proximitySensors.getFreqForward(), proximitySensors.getFreqBackward(), server.getFreqMin(), server.getFreqMax());

        lastReadAndUpdateTime = currentTime;
    }
    
    // Update proximity sensors state without blocking
    proximitySensors.update();
    
    // Load frequency data and send to server every load_data_interval
    if (currentTime - lastLoadDataTime >= load_data_interval) {
        // Get frequency and sensor data
        int freqIn = proximitySensors.getFreqForward();
        int freqOut = proximitySensors.getFreqBackward();
        float temperature = dhtSensor.getTemperature();
        float humidity = dhtSensor.getHumidity();
    
        // Save data to server
        server.saveDataToDB(temperature, humidity, freqIn, freqOut);  

        // Update timer
        lastLoadDataTime = currentTime;
    }
    
    // Update thresholds from server every update_thresholds_interval
    if (currentTime - lastThresholdsUpdateTime >= update_thresholds_interval) {
        server.updateThresholdsFromDB();
        lastThresholdsUpdateTime = currentTime;
    }
    
    // Send grouped alerts if the interval has passed
    if (currentTime - lastAlertTime >= alert_interval) {
        if (alertsManager.hasPendingAlerts()) {
            String groupedAlerts = alertsManager.getGroupedAlerts();
            telegramBot.sendMessage(groupedAlerts);
        }
        lastAlertTime = currentTime;
    }

    // Update control and actuators from server every automatic_control_interval
    if (currentTime - lastAutomaticControlTime >= automatic_control_interval) {
        server.updateControlAndActuatorsFromDB();
        lastAutomaticControlTime = currentTime;

        if (server.isAutomaticControl()) {
            // Automatic control
            if (dhtSensor.getTemperature() < server.getTempMin()) {
                heaterController.turnOn();
            } else {
                heaterController.turnOff();
            }    
            
            if (dhtSensor.getTemperature() > server.getTempMax()) {
                gateController.setAngle1(10);
                gateController.setAngle2(90);
            } else if (dhtSensor.getTemperature() < server.getTempMin()) {
                gateController.setAngle1(150);
                gateController.setAngle2(10);
            } else {
                int serverAngle = server.getGateAngle();
                gateController.setAngle1(serverAngle);
                int adjustedAngle = 90 * ((1 - (float)serverAngle / 150.0));
                gateController.setAngle2(adjustedAngle);
            }

        } else {
            // Manual control
            if (server.isHeaterOn()) {
                heaterController.turnOn();
            } else {
                heaterController.turnOff();
            }
            int serverAngle = server.getGateAngle();
            gateController.setAngle1(serverAngle);
            int adjustedAngle = 90 * ((1 - (float)serverAngle / 150.0));
            gateController.setAngle2(adjustedAngle);
        }
    }
}