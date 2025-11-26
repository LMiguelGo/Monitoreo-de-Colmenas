#include "DHTSensor.h"

// Constructor: receives the pin, sensor type, and reading interval
DHTSensor::DHTSensor(uint8_t pin, uint8_t type):
    pin(pin),             // Sensor pin
    type(type),           // Sensor type (DHT11 or DHT22)
    dht(pin, type),       // Object from the official library
    temperature(NAN),     // Last temperature reading
    humidity(NAN) {}      // Last humidity reading

// Initialize the sensor
void DHTSensor::begin() {
    dht.begin();
}

// Read sensor values
void DHTSensor::read() {
    // Read current temperature and humidity values
    float newHumidity = dht.readHumidity();
    float newTemperature = dht.readTemperature();

    // Check that the readings are valid
    if (!isnan(newHumidity) && !isnan(newTemperature)) {
        humidity = newHumidity;
        temperature = newTemperature;
    }
}

// Method to return the last valid temperature value
float DHTSensor::getTemperature() const {
    return temperature;
}

// Method to return the last valid humidity value
float DHTSensor::getHumidity() const {
    return humidity;
}
