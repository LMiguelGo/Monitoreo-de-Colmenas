#ifndef LEDS_MANAGER_H
#define LEDS_MANAGER_H

#include <Arduino.h>

class LedsManager {
  private:
    int ledTemp;
    int ledHum;
    int ledFreq;

  public:
    // Constructor: recibe los pines de los LEDs
    LedsManager(int pinTemp, int pinHum, int pinFreq);

    // Inicializa los pines
    void begin();

    // Control individual
    void setTemp(bool on);
    void setHum(bool on);
    void setFreq(bool on);

    // Actualización basada en condiciones
    void update(float temp, float hum, int freqDir1, int freqDir2,
                float tempMin, float tempMax, float humMin, float humMax,
                int freqMin, int freqMax);
};

#endif
