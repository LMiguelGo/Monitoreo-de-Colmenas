#ifndef LEDS_MANAGER_H
#define LEDS_MANAGER_H

#include <Arduino.h>

class LedsManager {
  private:
    int ledTemp;    // Temperature LED pin
    int ledHum;     // Humidity LED pin
    int ledFreq;    // Activity LED pin

  public:
    // Constructor: receives the LED pins
    LedsManager(int pinTemp, int pinHum, int pinFreq);

    // Initializes the pins
    void begin();

    // Individual control
    void setTemp(bool on);
    void setHum(bool on);
    void setFreq(bool on);

    // Update based on conditions
    void update(float temp, float hum, int freqIn, int freqOut, float tempMin, float tempMax, float humMin, float humMax, int freqMin, int freqMax);
};

#endif
