#ifndef SERVER_CONNECTOR_H
#define SERVER_CONNECTOR_H

#include <Arduino.h>
#include <WiFi.h>
#include "TelegramBot.h"

class ServerConnector {
private:
    const char* host;
    int port;
    int idTarjeta;

    TelegramBot* bot;

    // Umbrales
    float tempMin, tempMax;
    float humMin, humMax;
    int freqMin, freqMax;

    // Auxiliar para leer el body de una respuesta HTTP
    String getHttpResponse(WiFiClient &client);

public:
    ServerConnector(const char* host, int port, int idTarjeta, TelegramBot* bot);

    // --- WiFi ---
    void connectWiFi(const char* ssid, const char* password);

    // --- Telegram ---
    void updateTelegramCredentials();

    // --- Base de datos ---
    void updateThresholdsFromDB();
    void saveDataToDB(float temperatura, float humedad, int freqIn, int freqOut);

    // --- Getters ---
    float getTempMin() const { return tempMin; }
    float getTempMax() const { return tempMax; }
    float getHumMin() const { return humMin; }
    float getHumMax() const { return humMax; }
    int getFreqMin() const { return freqMin; }
    int getFreqMax() const { return freqMax; }
};

#endif
