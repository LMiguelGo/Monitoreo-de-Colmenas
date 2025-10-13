#ifndef DHT_SENSOR_H
#define DHT_SENSOR_H

#include <Arduino.h>
#include <DHT.h>

class DHTSensor {
private:
    uint8_t pin;                 // Pin del sensor
    uint8_t type;                // Tipo de sensor (DHT11 o DHT22)
    DHT dht;                     // Objeto de la librería oficial
    float temperature;           // Última lectura de temperatura
    float humidity;              // Última lectura de humedad

public:
    // Constructor: recibe el pin, tipo de sensor y el intervalo de lectura
    DHTSensor(uint8_t pin, uint8_t type = DHT11);

    // Inicializa el sensor
    void begin();

    // Lee temperatura y humedad
    void read();

    // Devuelve la temperatura más reciente
    float getTemperature() const;

    // Devuelve la humedad más reciente
    float getHumidity() const;

    // Devuelve true si se leyó correctamente el sensor
    bool isValid() const;
};

#endif
