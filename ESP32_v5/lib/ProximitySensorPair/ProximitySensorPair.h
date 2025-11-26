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

    // Interrupt for Sensor A
    static void IRAM_ATTR handleInterruptA();

    // Interrupt for Sensor B
    static void IRAM_ATTR handleInterruptB();

    // Executed when there is an interrupt on Sensor A
    void onSensorA();

    // Executed when there is an interrupt on Sensor B
    void onSensorB();

public:
    // Constructor
    ProximitySensorPair(uint8_t pinA, uint8_t pinB);

    // Object initializer
    void begin();

    // Continuous update
    void update();

    // Return the output frequency values
    unsigned int getFreqForward() const { return freqForward; }

    // Return the input frequency values
    unsigned int getFreqBackward() const { return freqBackward; }
};
