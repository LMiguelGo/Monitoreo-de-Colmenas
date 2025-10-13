#ifndef ALERTSMANAGER_H
#define ALERTSMANAGER_H

#include <Arduino.h>

class AlertsManager {
private:
    String alerts;                   // Acumulador de alertas agrupadas
    unsigned long lastAlertTime;     // Tiempo del último envío
    unsigned long alertInterval;     // Intervalo entre envíos agrupados
    int idTarjeta;                   // ID de la colmena o tarjeta

public:
    AlertsManager(int idTarjeta, unsigned long alertInterval = 60000);

    // --- ALERTAS INDIVIDUALES ---
    String checkTemperature(float temp, float tempMin, float tempMax);
    String checkHumidity(float hum, float humMin, float humMax);
    String checkFrequency(int freqIn, int freqOut, int freqMin, int freqMax);

    // --- ALERTAS AGRUPADAS ---
    void addAlert(const String& msg);
    bool shouldSend();          // Retorna true si ya pasó el tiempo de envío
    String getGroupedAlerts();  // Devuelve todas las alertas acumuladas
    void clearAlerts();         // Limpia el acumulador
};

#endif
