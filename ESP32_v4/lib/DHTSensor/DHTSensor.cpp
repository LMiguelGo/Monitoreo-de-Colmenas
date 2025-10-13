#include "DHTSensor.h"

// Constructor
DHTSensor::DHTSensor(uint8_t pin, uint8_t type):
    pin(pin), type(type), dht(pin, type), temperature(NAN), humidity(NAN) {}

// Inicializa el sensor
void DHTSensor::begin() {
    dht.begin();
}

// Lee los valores del sensor
void DHTSensor::read() {
    // Lectura de los valores actuales
    float newHumidity = dht.readHumidity();
    float newTemperature = dht.readTemperature();

    // Verificar que las lecturas sean válidas
    if (!isnan(newHumidity) && !isnan(newTemperature)) {
        humidity = newHumidity;
        temperature = newTemperature;
    }
}

// Devuelve la temperatura actual
float DHTSensor::getTemperature() const {
    return temperature;
}

// Devuelve la humedad actual
float DHTSensor::getHumidity() const {
    return humidity;
}
