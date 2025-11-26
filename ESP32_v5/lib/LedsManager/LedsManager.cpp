#include "LedsManager.h"

// Constructor receives the LED pins
LedsManager::LedsManager(int pinTemp, int pinHum, int pinFreq) {
  ledTemp = pinTemp;
  ledHum = pinHum;
  ledFreq = pinFreq;
}

// Initialize. Define the pins as outputs and set a default value
void LedsManager::begin() {
  pinMode(ledTemp, OUTPUT);
  pinMode(ledHum, OUTPUT);
  pinMode(ledFreq, OUTPUT);
  digitalWrite(ledTemp, LOW);
  digitalWrite(ledHum, LOW);
  digitalWrite(ledFreq, LOW);
}

// Individual control for the temperature LED (RED)
void LedsManager::setTemp(bool on) {
  digitalWrite(ledTemp, on ? HIGH : LOW);
}

// Individual control for the humidity LED (BLUE)
void LedsManager::setHum(bool on) {
  digitalWrite(ledHum, on ? HIGH : LOW);
}

// Individual control for the activity LED (WHITE)
void LedsManager::setFreq(bool on) {
  digitalWrite(ledFreq, on ? HIGH : LOW);
}

// General LED control function
void LedsManager::update(float temp, float hum, int freqIn, int freqOut, float tempMin, float tempMax, float humMin, float humMax, int freqMin, int freqMax) {
  // Temperature LED control
  if (temp < tempMin || temp > tempMax)
    setTemp(true);
  else
    setTemp(false);

  // Humidity LED control
  if (hum < humMin || hum > humMax)
    setHum(true);
  else
    setHum(false);

  // Activity LED control
  if (freqIn < freqMin || freqIn > freqMax || freqOut < freqMin || freqOut > freqMax)
    setFreq(true);
  else
    setFreq(false);
}
