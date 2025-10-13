#include "AlertsManager.h"

AlertsManager::AlertsManager(int idTarjeta, unsigned long alertInterval)
    : idTarjeta(idTarjeta), alertInterval(alertInterval), lastAlertTime(0), alerts("") {}

// ===================================================
// ALERTAS INDIVIDUALES
// ===================================================

String AlertsManager::checkTemperature(float temp, float tempMin, float tempMax) {
    String msg = "";
    if (temp < tempMin) msg = "⚠ Colmena " + String(idTarjeta) + ": Temperatura BAJA (" + String(temp, 1) + " °C)";
    else if (temp > tempMax) msg = "⚠ Colmena " + String(idTarjeta) + ": Temperatura ALTA (" + String(temp, 1) + " °C)";
    return msg;
}

String AlertsManager::checkHumidity(float hum, float humMin, float humMax) {
    String msg = "";
    if (hum < humMin) msg = "⚠ Colmena " + String(idTarjeta) + ": Humedad BAJA (" + String(hum, 1) + " %)";
    else if (hum > humMax) msg = "⚠ Colmena " + String(idTarjeta) + ": Humedad ALTA (" + String(hum, 1) + " %)";
    return msg;
}

String AlertsManager::checkFrequency(int freqIn, int freqOut, int freqMin, int freqMax) {
    String msg = "";

    if (freqIn < freqMin) msg += "⚠ Colmena " + String(idTarjeta) + ": Frecuencia BAJA Dir1 (" + String(freqIn) + ")\n";
    else if (freqIn > freqMax) msg += "⚠ Colmena " + String(idTarjeta) + ": Frecuencia ALTA Dir1 (" + String(freqIn) + ")\n";

    if (freqOut < freqMin) msg += "⚠ Colmena " + String(idTarjeta) + ": Frecuencia BAJA Dir2 (" + String(freqOut) + ")\n";
    else if (freqOut > freqMax) msg += "⚠ Colmena " + String(idTarjeta) + ": Frecuencia ALTA Dir2 (" + String(freqOut) + ")\n";

    return msg;
}

// ===================================================
// ALERTAS AGRUPADAS
// ===================================================

void AlertsManager::addAlert(const String& msg) {
    if (msg.length() > 0) {
        alerts += msg + "\n";
    }
}

bool AlertsManager::shouldSend() {
    if (millis() - lastAlertTime >= alertInterval) {
        lastAlertTime = millis();
        return true;
    }
    return false;
}

String AlertsManager::getGroupedAlerts() {
    return alerts;
}

void AlertsManager::clearAlerts() {
    alerts = "";
}
