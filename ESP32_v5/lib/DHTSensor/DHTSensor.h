#ifndef DHT_SENSOR_H
#define DHT_SENSOR_H

#include <Arduino.h>
#include <DHT.h>

class DHTSensor {
private:
    uint8_t pin;                 // Sensor pin
    uint8_t type;                // Sensor type (DHT11 or DHT22)
    DHT dht;                     // Object from the official library
    float temperature;           // Last temperature reading
    float humidity;              // Last humidity reading

public:
    // Constructor: receives the pin, sensor type, and reading interval
    DHTSensor(uint8_t pin, uint8_t type = DHT22);

    // Initializes the sensor
    void begin();

    // Reads temperature and humidity
    void read();

    // Returns the most recent valid temperature
    float getTemperature() const;

    // Returns the most recent valid humidity
    float getHumidity() const;
};

#endif
