# Simulateur Ergonomic-Workstation 2025

## Présentation du protocole implanté dans le simulateur ESP'ACE

Ce document présente rapidement le fonctionnement du simulateur ainsi que le protocole implémenté.

> Le protocole complet est disponible dans Google Drive.

## Configuration du simulateur

Valeurs par défaut :

```cpp
#define NUMERO_PUPITRE  1
#define NB_BACS         8
#define PRECISION_PRISE 80 // en %
```

Utilisation du simulateur :

- Le bouton `SW1` permet de valider une étape
- Le bouton `SW2` permet de simuler une prise (correcte ou erreur) dans un bac

> Le niveau de précision de l'opérateur peut être réglé avec la constante `PRECISION_PRISE`.

Choix de la communication (par défaut port série):

```cpp
//#define BLUETOOTH      // Bluetooth sinon Serial
```

## Identification

Nom : \"ergowork-pn\" où \"n\" est le numéro de pupitre (par exemple `ergowork-p1` pour le pupitre 1)

## Protocole

- Trame ASCII
- Délimiteur de début : **$**
- Délimiteur de fin : **%**

Les différents types de message échangés :

- `D` (début) : **D**ébut d'un processus d’assemblage (Guidage → Détection)
- `n` (numéro de bac) : sélectionne le numéro de bac (Guidage → Détection)
- `F` (fin) : **F**in d’étape ou de processus d’assemblage (Guidage → Détection)
- `C` (correct) : prise **C**orrecte de l’opérateur (Détection → Guidage)
- `E` (erreur) : **E**rreur de prise de l’opérateur (Détection → Guidage)
- `V` (valide) : **V**alide une étape d’un processus (Détection → Guidage)
- `A` (acquittement) : acquitte un message reçu (bidirectionnel)

- Module de Guidage → Module de Détection
  - Trame de sélection de bac : `$bacselectionne%`
  - Trame de début processus : `$D%`
  - Trame de fin étape/processus : `$F%`
  - Trame d’acquittement : `$A%`

- Module de Détection → Module de Guidage
  - Trame de prise correcte : `$C%`
  - Trame d’erreur de prise : `$E%`
  - Trame de validation opérateur : `$V%`
  - Trame d’acquittement : `$A%`

## platform.ini

```ini
; PlatformIO Project Configuration File

[env:esp32_pupitre_1]
platform = espressif32
;board = esp32dev
board = lolin32
framework = arduino
;platform_packages = platformio/framework-arduinoespressif32@^3.20006.0
lib_deps =
  thingpulse/ESP8266 and ESP32 OLED driver for SSD1306 displays @ ^4.2.0
upload_port = /dev/ttyUSB0
upload_speed = 115200
monitor_port = /dev/ttyUSB0
monitor_speed = 115200
```

## Auteur

- Thierry Vaira <<tvaira@free.fr>>
