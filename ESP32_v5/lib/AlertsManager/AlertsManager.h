#ifndef ALERTSMANAGER_H
#define ALERTSMANAGER_H

#include <Arduino.h>

class AlertsManager {
private:
    String alerts;                // Acumulador de alertas
    int cardId;                   // ID de la colmena o tarjeta
    bool alertPending;            // Indica si hay alertas pendientes de envío
    bool lowTemperatureAlert;     // Indica si se ha generado una alerta de temperatura baja
    bool highTemperatureAlert;    // Indica si se ha generado una alerta de temperatura alta
    bool lowHumidityAlert;        // Indica si se ha generado una alerta de humedad baja
    bool highHumidityAlert;       // Indica si se ha generado una alerta de humedad alta
    bool highOutgoingFreqAlert;   // Indica si se ha generado una alerta de frecuencia alta de salida
    bool lowOutgoingFreqAlert;    // Indica si se ha generado una alerta de frecuencia baja de salida
    bool highIncomingFreqAlert;   // Indica si se ha generado una alerta de frecuencia alta de entrada
    bool lowIncomingFreqAlert;    // Indica si se ha generado una alerta de frecuencia baja de entrada

public:
    // Constructor, required card ID
    AlertsManager(int cardId);

    // Check temperature alert
    void checkTemperature(float temp, float tempMin, float tempMax);

    // Check humidity alert
    void checkHumidity(float hum, float humMin, float humMax);

    // Check frequency alert
    void checkFrequency(int freqIn, int freqOut, int freqMin, int freqMax);

    // Add an alert message to the accumulator
    String getGroupedAlerts();

    // Check if there are pending alerts
    bool hasPendingAlerts();    
};

#endif
