#include "ProximitySensorPair.h"

ProximitySensorPair* ProximitySensorPair::instance = nullptr;

ProximitySensorPair::ProximitySensorPair(uint8_t pinA, uint8_t pinB)
    : sensorA_pin(pinA), sensorB_pin(pinB) {}

void ProximitySensorPair::begin() {
    instance = this;
    pinMode(sensorA_pin, INPUT_PULLUP);
    pinMode(sensorB_pin, INPUT_PULLUP);
    attachInterrupt(digitalPinToInterrupt(sensorA_pin), handleInterruptA, FALLING);
    attachInterrupt(digitalPinToInterrupt(sensorB_pin), handleInterruptB, FALLING);
}

void IRAM_ATTR ProximitySensorPair::handleInterruptA() {
    if (instance) instance->onSensorA();
}

void IRAM_ATTR ProximitySensorPair::handleInterruptB() {
    if (instance) instance->onSensorB();
}

void ProximitySensorPair::onSensorA() {
    stateA = true;
    lastA = millis();
}

void ProximitySensorPair::onSensorB() {
    stateB = true;
    lastB = millis();
}

void ProximitySensorPair::update() {
    unsigned long currentTime = millis();
    if (stateA && (currentTime - lastA > 300)) {
        Serial.println("Sensor A triggered");
        stateA = false;
    }
    if (stateB && (currentTime - lastB > 300)) {
        Serial.println("Sensor B triggered");
        stateB = false;
    }

    if (stateA && (lastA < lastB)) {
        if (stateB && (currentTime - lastB <= 600)) {
            forwardCount++;
            Serial.println("Forward detected: " + String(forwardCount));
            stateA = false;
            stateB = false;
        }
    }

    if (stateB && (lastB < lastA)) {
        if (stateA && (currentTime - lastA <= 600)) {
            backwardCount++;
            Serial.println("Backward detected: " + String(backwardCount));
            stateA = false;
            stateB = false;
        }
    }

    seconds = currentTime / 1000;
    if (seconds < 18000){
        freqForward = (forwardCount * 60) / (seconds ? seconds : 1);
        freqBackward = (backwardCount * 60) / (seconds ? seconds : 1);
    } else {
        forwardCount = 0;
        backwardCount = 0; 
        seconds = 0;
        freqForward = 0;
        freqBackward = 0;
    }
}
