#include "LedsManager.h"

LedsManager::LedsManager(int pinTemp, int pinHum, int pinFreq) {
  ledTemp = pinTemp;
  ledHum = pinHum;
  ledFreq = pinFreq;
}

void LedsManager::begin() {
  pinMode(ledTemp, OUTPUT);
  pinMode(ledHum, OUTPUT);
  pinMode(ledFreq, OUTPUT);
  digitalWrite(ledTemp, LOW);
  digitalWrite(ledHum, LOW);
  digitalWrite(ledFreq, LOW);
}

void LedsManager::setTemp(bool on) {
  digitalWrite(ledTemp, on ? HIGH : LOW);
}

void LedsManager::setHum(bool on) {
  digitalWrite(ledHum, on ? HIGH : LOW);
}

void LedsManager::setFreq(bool on) {
  digitalWrite(ledFreq, on ? HIGH : LOW);
}

void LedsManager::update(float temp, float hum, int freqDir1, int freqDir2,
                         float tempMin, float tempMax, float humMin, float humMax,
                         int freqMin, int freqMax) {

  // LED de temperatura
  if (temp < tempMin || temp > tempMax)
    setTemp(true);
  else
    setTemp(false);

  // LED de humedad
  if (hum < humMin || hum > humMax)
    setHum(true);
  else
    setHum(false);

  // LED de frecuencia
  if (freqDir1 < freqMin || freqDir1 > freqMax ||
      freqDir2 < freqMin || freqDir2 > freqMax)
    setFreq(true);
  else
    setFreq(false);
}
