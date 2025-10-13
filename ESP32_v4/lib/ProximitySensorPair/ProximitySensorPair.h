#pragma once
#include <Arduino.h>

class ProximitySensorPair {
private:
    uint8_t sensorA_pin;
    uint8_t sensorB_pin;

    volatile unsigned long lastA = 0;
    volatile unsigned long lastB = 0;
    volatile bool stateA = false;
    volatile bool stateB = false;
    volatile int forwardCount = 0;
    volatile int backwardCount = 0;

    unsigned int freqForward = 0;
    unsigned int freqBackward = 0;
    unsigned long seconds = 0;


    static ProximitySensorPair* instance;

    static void IRAM_ATTR handleInterruptA();
    static void IRAM_ATTR handleInterruptB();

    void onSensorA();
    void onSensorB();

public:
    ProximitySensorPair(uint8_t pinA, uint8_t pinB);
    void begin();
    void update();
    unsigned int getFreqForward() const { return freqForward; }
    unsigned int getFreqBackward() const { return freqBackward; }
};
