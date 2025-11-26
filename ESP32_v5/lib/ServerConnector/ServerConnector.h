#ifndef SERVER_CONNECTOR_H
#define SERVER_CONNECTOR_H

#include <Arduino.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "TelegramBot.h"

class ServerConnector {
private:
    const char* host;
    int port;
    int idTarjeta;
    TelegramBot* bot;
    float tempMin, tempMax;
    float humMin, humMax;
    int freqMin, freqMax;
    bool automaticControl;
    float gateAngle;
    bool heater;

public:
    ServerConnector(const char* host, int port, int idTarjeta, TelegramBot* bot);

    // Connect to WiFi
    void connectWiFi(const char* ssid, const char* password);

    // Update thresholds from the database
    void updateThresholdsFromDB();

    // Save sensor data to the database
    void saveDataToDB(float temperatura, float humedad, int freqIn, int freqOut);

    // Update control and actuators from the database
    void updateControlAndActuatorsFromDB();

    // Update Telegram credentials from the server
    void updateTelegramCredentials();

    // Get minimum temperature
    float getTempMin() const { return tempMin; }

    // Get maximum temperature
    float getTempMax() const { return tempMax; }

    // Get minimum humidity
    float getHumMin() const { return humMin; }

    // Get maximum humidity
    float getHumMax() const { return humMax; }

    // Get minimum frequency
    int getFreqMin() const { return freqMin; }

    // Get maximum frequency
    int getFreqMax() const { return freqMax; }

    // Get automatic control status
    bool isAutomaticControl() const { return automaticControl; }

    // Get gate angle
    float getGateAngle() const { return gateAngle; }

    // Get heater status
    bool isHeaterOn() const { return heater; }
};

#endif
