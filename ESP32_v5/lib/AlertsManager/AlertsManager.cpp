#include "AlertsManager.h"

// Constructor, required card ID 
AlertsManager::AlertsManager(int cardId):
    cardId(cardId), 
    alerts("ALERTAS EN LAS COLMENA: " + String(cardId) + "\n") {}

// Inspection of temperature alert
void AlertsManager::checkTemperature(float temp, float tempMin, float tempMax) {
    if ((temp < tempMin) && !lowTemperatureAlert) {
        alertPending = true;
        lowTemperatureAlert = true;        
        alerts += "Temp. baja: " + String(temp) + "°C (Rango: " + String(tempMin) + "-" + String(tempMax) + "°C)\n";
    }
    else if ((temp > tempMax) && !highTemperatureAlert) {
        alertPending = true;
        highTemperatureAlert = true;
        alerts += "Temp. alta: " + String(temp) + "°C (Rango: " + String(tempMin) + "-" + String(tempMax) + "°C)\n";
    }
}

// Inspection of humidity alert
void AlertsManager::checkHumidity(float hum, float humMin, float humMax) {
    if ((hum < humMin) && !lowHumidityAlert) {
        alertPending = true;
        lowHumidityAlert = true;
        alerts += "Humedad baja: " + String(hum) + "% (Rango: " + String(humMin) + "-" + String(humMax) + "%)\n";
    }
    else if ((hum > humMax) && !highHumidityAlert) {
        alertPending = true;
        highHumidityAlert = true;
        alerts += "Humedad alta: " + String(hum) + "% (Rango: " + String(humMin) + "-" + String(humMax) + "%)\n";
    }
}


// Inspection of frequency alert
void AlertsManager::checkFrequency(int freqIn, int freqOut, int freqMin, int freqMax) {
    if ((freqOut < freqMin) && !lowOutgoingFreqAlert) {
        alertPending = true;
        lowOutgoingFreqAlert = true;
        alerts += "Freq. salida baja: " + String(freqOut) + "Hz (Rango: " + String(freqMin) + "-" + String(freqMax) + "Hz)\n";
    }
    else if ((freqOut > freqMax) && !highOutgoingFreqAlert) {
        alertPending = true;
        highOutgoingFreqAlert = true;
        alerts += "Freq. salida alta: " + String(freqOut) + "Hz (Rango: " + String(freqMin) + "-" + String(freqMax) + "Hz)\n";
    }

    if ((freqIn < freqMin) && !lowIncomingFreqAlert) {
        alertPending = true;
        lowIncomingFreqAlert = true;
        alerts += "Freq. entrada baja: " + String(freqIn) + "Hz (Rango: " + String(freqMin) + "-" + String(freqMax) + "Hz)\n";
    }
    else if ((freqIn > freqMax) && !highIncomingFreqAlert) {
        alertPending = true;
        highIncomingFreqAlert = true;
        alerts += "Freq. entrada alta: " + String(freqIn) + "Hz (Rango: " + String(freqMin) + "-" + String(freqMax) + "Hz)\n";
    }
}

// Return accumulated alerts and reset
String AlertsManager::getGroupedAlerts() {
    // Reset alert flags
    lowTemperatureAlert = false;
    highTemperatureAlert = false;
    lowHumidityAlert = false;
    highHumidityAlert = false;
    lowOutgoingFreqAlert = false;
    highOutgoingFreqAlert = false;
    lowIncomingFreqAlert = false;
    highIncomingFreqAlert = false;

    // Reset alert pending flag
    alertPending = false;

    // Return accumulated alerts and reset
    String groupedAlerts = alerts;
    alerts = "ALERTAS EN LAS COLMENA: " + String(cardId) + "\n";
    return groupedAlerts;
}

// Check if there are pending alerts
bool AlertsManager::hasPendingAlerts() {
    return alertPending;
}