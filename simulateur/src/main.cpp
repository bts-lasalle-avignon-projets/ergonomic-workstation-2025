/**
 * @file src/main.cpp
 * @brief Programme principal Ergonomic-Workstation 2025
 * @author Thierry Vaira
 * @version 1.0
 */
#include <Arduino.h>
#include <afficheur.h>

// Ergonomic-Workstation
#define NUMERO_PUPITRE 1
#define NB_BACS         8
#define PRECISION_PRISE 80 // en %
//#define BLUETOOTH      // Bluetooth sinon Serial

#ifdef BLUETOOTH
#include <BluetoothSerial.h>
#include "esp_bt_main.h"
#include "esp_bt_device.h"
#endif

// Brochages
#define GPIO_LED_ROUGE   5    //!<
#define GPIO_LED_ORANGE  17   //!<
#define GPIO_LED_VERTE   16   //!<
#define GPIO_SW1         12   //!< Validation d'étape
#define GPIO_SW2         14   //!< Simulation de prise
#define ADRESSE_I2C_OLED 0x3c //!< Adresse I2C de l'OLED
#define BROCHE_I2C_SDA   21   //!< Broche SDA
#define BROCHE_I2C_SCL   22   //!< Broche SCL

// Bluetooth
#ifdef BLUETOOTH
#define BLUETOOTH_SLAVE //!< esclave
// #define BLUETOOTH_MASTER //!< maître
#define DEBUG
BluetoothSerial ESPBluetooth;
#endif

// Protocole (cf. Google Drive)
#define EN_TETE_TRAME    "$"
#define DELIMITEUR_CHAMP ""
#define DELIMITEURS_FIN  "%"
#define DELIMITEUR_FIN   '%'

// Guidage -> Pupitre
/**
 * @enum TypeTrameRecue
 * @brief Les differents types de trame reçue
 */
enum TypeTrameRecue
{
    SELECTION_BAC = -1,
    DEBUT_PROCESSUS,
    FIN_PROCESSUS,
    ACQUITTEMENT,
    NB_TRAMES_RECUES
};

const String nomsTrameRecue[TypeTrameRecue::NB_TRAMES_RECUES] = {
    "D",
    "F",
    "A"
}; //!< nom des trames recues dans le protocole

// Variables globales
bool      processusEncours = false;   //!< le processus est en cours
int       nbBacs           = NB_BACS; //!< le nombre de bacs
int       numeroEtape      = 0;       //!< le numéro de l'étape
int       numeroBac        = 0;       //!< le numéro du bac
bool      refresh = false; //!< demande rafraichissement de l'écran OLED
bool      validationEncours = false; //!< une validation d'etape est en cours
bool      priseEncours      = false; //!< une prise de bac est déclenchée
bool      antiRebond        = false; //!< anti-rebond
Afficheur afficheur(ADRESSE_I2C_OLED,
                    BROCHE_I2C_SDA,
                    BROCHE_I2C_SCL);               //!< afficheur OLED SSD1306
String    entete        = String(EN_TETE_TRAME);   // caractère séparateur
String    delimiteurFin = String(DELIMITEURS_FIN); // fin de trame

// Les fonctions
/**
 * @brief Déclencher une prise dans un bac (interruption sur le bouton SW2)
 * @fn declencherPrise()
 */
void IRAM_ATTR declencherPrise()
{
    if(!processusEncours || antiRebond)
        return;

    priseEncours = true;
    antiRebond   = true;
}

/**
 * @brief Valider une étape (interruption sur le bouton SW1)
 * @fn validerEtape()
 */
void IRAM_ATTR validerEtape()
{
    if(!processusEncours || antiRebond)
        return;

    validationEncours = true;
    antiRebond        = true;
}

/**
 * @brief Envoie une trame réponse
 *
 */
void envoyerTrameReponse(char type)
{
    char trameEnvoi[64];
    sprintf((char*)trameEnvoi, "%s%c%%", entete.c_str(), type);
#ifdef BLUETOOTH
    ESPBluetooth.write((uint8_t*)trameEnvoi, strlen((char*)trameEnvoi));
    #ifdef DEBUG
    String trame = String(trameEnvoi);
    Serial.print("> ");
    Serial.println(trame);
#endif
#else
    Serial.write((uint8_t*)trameEnvoi, strlen((char*)trameEnvoi));
#endif
}

/**
 * @brief Lit une trame via le Bluetooth
 *
 * @fn lireTrame(String &trame)
 * @param trame la trame reçue
 * @return bool true si une trame a été lue, sinon false
 */
bool lireTrame(String& trame)
{
#ifdef BLUETOOTH
    if(ESPBluetooth.available())
    {
        trame.clear();
        trame = ESPBluetooth.readStringUntil(DELIMITEUR_FIN);
#ifdef DEBUG
        Serial.print("< ");
        Serial.println(trame);
#endif
        trame.concat(DELIMITEUR_FIN); // remet le DELIMITEUR_FIN
        return true;
    }
#else
    if(Serial.available())
    {
        trame.clear();
        trame = Serial.readStringUntil(DELIMITEUR_FIN);

        trame.concat(DELIMITEUR_FIN); // remet le DELIMITEUR_FIN
        return true;
    }
#endif
    return false;
}

/**
 * @brief Vérifie si la trame reçue est valide et retorune le type de la trame
 *
 * @fn verifierTrame(String &trame)
 * @param trame
 * @return TypeTrame le type de la trame
 */
TypeTrameRecue verifierTrame(String& trame)
{
    String format;

    for(int i = 0; i < TypeTrameRecue::NB_TRAMES_RECUES; i++)
    {
        format = entete + nomsTrameRecue[i];
        //#ifdef DEBUG
        //        Serial.print("Verification trame : ");
        //        Serial.print(format);
        //#endif

        if(trame.indexOf(format) != -1)
        {
            return (TypeTrameRecue)i;
        }
    }

    return SELECTION_BAC;
}

void reinitialiserAffichage()
{
    afficheur.setMessageLigne(Afficheur::Ligne1, ""); // Processus
    afficheur.setMessageLigne(Afficheur::Ligne2, ""); // Etape
    afficheur.setMessageLigne(Afficheur::Ligne3, ""); // Bac
    afficheur.setMessageLigne(Afficheur::Ligne4, ""); // Prise
    refresh = true;
}

/**
 * @brief Initialise les ressources du programme
 *
 * @fn setup
 *
 */
void setup()
{
    Serial.begin(115200);
    while(!Serial)
        ;

    pinMode(GPIO_LED_ROUGE, OUTPUT);
    pinMode(GPIO_LED_ORANGE, OUTPUT);
    pinMode(GPIO_LED_VERTE, OUTPUT);

    digitalWrite(GPIO_LED_ROUGE, LOW);
    digitalWrite(GPIO_LED_ORANGE, LOW);
    digitalWrite(GPIO_LED_VERTE, LOW);

    pinMode(GPIO_SW1, INPUT_PULLUP);
    pinMode(GPIO_SW2, INPUT_PULLUP);
    attachInterrupt(digitalPinToInterrupt(GPIO_SW1), validerEtape, FALLING);
    attachInterrupt(digitalPinToInterrupt(GPIO_SW2), declencherPrise, FALLING);

    afficheur.initialiser();

    String titre  = "          ";
    String stitre = "=====================";

#ifdef BLUETOOTH
#ifdef BLUETOOTH_SLAVE
    String nomBluetooth = "ergowork-p" + String(NUMERO_PUPITRE);
    ESPBluetooth.begin(nomBluetooth);
    const uint8_t* adresseESP32 = esp_bt_dev_get_address();
    char           str[18];
    sprintf(str,
            "%02x:%02x:%02x:%02x:%02x:%02x",
            adresseESP32[0],
            adresseESP32[1],
            adresseESP32[2],
            adresseESP32[3],
            adresseESP32[4],
            adresseESP32[5]);
    stitre = String("== ") + String(str) + String(" ==");
    titre += nomBluetooth;
#endif
#else
    String nom = "ergowork-p" + String(NUMERO_PUPITRE);
    titre += nom;
    stitre = String("===================");
#endif

#ifdef DEBUG
    Serial.println(titre);
    Serial.println(stitre);
#endif

    afficheur.setTitre(titre);
    afficheur.setSTitre(stitre);
    afficheur.afficher();

    // initialise le générateur pseudo-aléatoire
    // Serial.println(randomSeed(analogRead(34)));
    esp_random();
}

/**
 * @brief Boucle infinie d'exécution du programme
 *
 * @fn loop
 *
 */
void loop()
{
    String         trame;
    TypeTrameRecue typeTrame;
    char           strMessageDisplay[24];

    if(refresh)
    {
        afficheur.afficher();
        refresh = false;
    }

    if(antiRebond)
    {
        afficheur.afficher();
        delay(250);
        antiRebond = false;
    }

    // appui SW2 : prise de bac
    if(processusEncours && priseEncours)
    {
        priseEncours = false;
        refresh      = true;
        // delay(random(1000, 4000)); // temps pour prendre dans le bac
        int chance = random(0, 100) + 1;
        if(chance <= PRECISION_PRISE)
        {
            digitalWrite(GPIO_LED_ROUGE, LOW);
            digitalWrite(GPIO_LED_ORANGE, LOW);
            digitalWrite(GPIO_LED_VERTE, HIGH);
            envoyerTrameReponse('C');
            afficheur.setMessageLigne(Afficheur::Ligne4,
                                      String("Prise correcte"));
        }
        else
        {
            digitalWrite(GPIO_LED_ROUGE, HIGH);
            digitalWrite(GPIO_LED_ORANGE, LOW);
            digitalWrite(GPIO_LED_VERTE, LOW);
            envoyerTrameReponse('E');
            afficheur.setMessageLigne(Afficheur::Ligne4,
                                      String("Erreur prise"));
        }
    }

    // appui SW1 : validation d'étape
    if(processusEncours && validationEncours)
    {
        validationEncours = false;
        refresh           = true;
        envoyerTrameReponse('V');
        afficheur.setMessageLigne(Afficheur::Ligne4, String("Etape validee"));
    }

    if(lireTrame(trame))
    {
        typeTrame = verifierTrame(trame);
        refresh   = true;

        switch(typeTrame)
        {
            case SELECTION_BAC:
                if(processusEncours && trame.startsWith("$") &&
                   trame.endsWith("%") && trame.length() == 3)
                {
#ifdef DEBUG
                    Serial.println("Sélection bac !");
#endif
                    numeroBac = trame.charAt(1) - '0';
                    if(numeroBac >= 1 && numeroBac <= nbBacs)
                    {
                        envoyerTrameReponse('A'); // Acquittement
                        numeroEtape += 1;
#ifdef DEBUG
                        Serial.println("Bac sélectionné : " +
                                       String(numeroBac));
#endif
                        afficheur.setMessageLigne(Afficheur::Ligne1,
                                                  String("Processus en cours"));
                        sprintf(strMessageDisplay, "Etape %d", numeroEtape);
                        afficheur.setMessageLigne(Afficheur::Ligne2,
                                                  String(strMessageDisplay));
                        sprintf(strMessageDisplay, "Bac %d", numeroBac);
                        afficheur.setMessageLigne(Afficheur::Ligne3,
                                                  String(strMessageDisplay));
                        afficheur.setMessageLigne(Afficheur::Ligne4,
                                                  String(""));
                        afficheur.afficher();
                    }
                    else
                    {
#ifdef DEBUG
                        Serial.println("Erreur numéro de bac !");
#endif
                    }
                }
                else
                {
#ifdef DEBUG
                    Serial.println("Erreur trame invalide !");
#endif
                }
                break;
            case TypeTrameRecue::DEBUT_PROCESSUS:
                if(!processusEncours)
                {
#ifdef DEBUG
                    Serial.println("Début processus !");
#endif
                    processusEncours = true;
                    numeroEtape      = 0;
                    envoyerTrameReponse('A'); // Acquittement
                    reinitialiserAffichage();
                    digitalWrite(GPIO_LED_ROUGE, LOW);   // Prise
                    digitalWrite(GPIO_LED_ORANGE, HIGH); // Processus en cours
                    digitalWrite(GPIO_LED_VERTE, LOW);   // Prise
                    afficheur.setMessageLigne(Afficheur::Ligne1,
                                              String("Debut processus"));
                    afficheur.afficher();
                }
                break;
            case TypeTrameRecue::FIN_PROCESSUS:
                if(processusEncours)
                {
#ifdef DEBUG
                    Serial.println("Fin du processus !");
#endif
                    processusEncours = false;
                    numeroEtape      = 0;
                    envoyerTrameReponse('A'); // Acquittement
                    reinitialiserAffichage();
                    digitalWrite(GPIO_LED_ROUGE, LOW);
                    digitalWrite(GPIO_LED_ORANGE, LOW); // Processus terminé
                    digitalWrite(GPIO_LED_VERTE, LOW);
                    afficheur.setMessageLigne(Afficheur::Ligne1,
                                              String("Fin processus"));
                    afficheur.afficher();
                }
                break;
            case TypeTrameRecue::ACQUITTEMENT:
                if(processusEncours)
                {
#ifdef DEBUG
                    Serial.println("Acquittement !");
#endif
                }
            default:
#ifdef DEBUG
                Serial.println("Trame invalide !");
#endif
                break;
        }
    }
}
