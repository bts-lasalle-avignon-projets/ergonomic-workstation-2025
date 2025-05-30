# Ergonomic-Workstation-2025

- [Ergonomic-Workstation-2025](#ergonomic-workstation-2025)
  - [Présentation](#présentation)
  - [Recette](#recette)
  - [Utilisation](#utilisation)
    - [Module de Création](#module-de-création-1)
  - [Changelog](#changelog)
  - [TODO](#todo)
    - [Module de Création](#module-de-création)
    - [Module de Guidage](#module-de-guidage)
  - [Diagrammes](#diagrammes)
    - [Cas d'utilisation](#cas-dutilisation)
    - [Séquence (x2)](#séquence-x2)
    - [Classe](#classe)
    - [Déploiement](#déploiement)
  - [Équipe de développement](#équipe-de-développement)

---

## Présentation

Il s’agit de digitaliser un poste de travail afin d’accompagner l’opérateur dans la réalisation d’un assemblage.

Le projet est structuré en **deux modules principaux** :

1. **Module de Création** – Déployé sur un **serveur Apache**, il permet aux superviseurs de **concevoir des prrocessus d'assemblage** en organisant des images et du texte dans un ordre spécifique.
2. **Module de Guidage** – Basé sur **Qt (Raspberry Pi)**, il affiche les processus d'assemblage aux opérateurs, leur permettant de **suivre des instructions en temps réel**.

## Recette

| **Module**         | **Étape**                                       | **À faire** | **En cours** | **Terminé** |
| ------------------ | ----------------------------------------------- | :-----------: | :------------: | :-----------: |
| **Création (IR1)** | Créer un processus                              |             |              | ✅           |
|                    | Produire des séquences d'opération              |             |              | ✅           |
|                    | Partager un processus                           |             |              | ✅           |
|                    | Visualiser les statistiques                     |             |              | ✅           |
|                    | Se connecter en tant que superviseur            |             |              | ✅           |
|                    | Afficher les processus créés                    |             |              | ✅           |
|                    | Supprimer et modifier un processus              |             |              | ✅           |
| **Guidage (IR2)**  | Affichage des étapes                            |             |      🟡       |             |
|                    | Validation des étapes par l'opérateur           |      ⬜      |              |             |
|                    | Enregistrement de la progression                |      ⬜      |              |             |
|                    | Connexion au backend pour récupérer les données |      ⬜      |              |             |
|                    | Interaction avec la base de données             |      ⬜      |              |             |

## Utilisation  
### Module de Création  

1. **Connexion**  
   Accédez au site web hébergé en local via un navigateur web.

2. **Accès au module**  
   Dans le menu, cliquez sur **Processus** pour accéder au module de création.  
   ![Capture d'écran du menu Processus](images/menuProcessus.png)

3. **Première utilisation**  
   Lors de la toute première connexion, seules deux options sont disponibles :  
   - **Créer** un nouveau processus  
   - **Importer** un processus existant  
   ![Capture d'écran des options Créer / Importer](images/premiereConnexions.png)

4. **Si des processus existent déjà**  
   Des options supplémentaires apparaîtront :  
   - **Ajouter des étapes** à un processus existant
   ![Capture d'écran des options supplémentaires](images/ajouterEtape.png)
   - **Exporter** un processus  

5. **Fonctionnalités désormais disponibles**  
   - **Modifier** un processus  
   - **Voir** les détails d’un processus  
   - **Supprimer** un processus
   - **Statistique** du processus

## Changelog

**v1.0 – Finalisation du module de création**

- Mise en place du serveur Apache et configuration PHP.
- Implémentation de la création d’un processus.
- Ajout de la gestion des étapes.
- Gestion de l’upload des images.
- Fonctionnalité de partage de processus.
- Affichage des processus créés.
- Suppression et modification des processus.
- Interface d’import/export via fichiers ZIP (JSON + images séparées).

## TODO

### Module de Création (v1.1 – améliorations futures)

  - [ ] Permettre un tri personnalisé des étapes par drag & drop.
  - [ ] Ajouter une vue graphique des statistiques d’utilisation des processus.
  - [ ] Possibilité de prévisualiser le diaporama avant exportation.
  - [ ] Support multi-utilisateur : journalisation des actions par utilisateur connecté.

### Module de Guidage

  - [ ] Configurer l’environnement de développement Qt
  - [ ] Créer une interface pour afficher les étapes
  - [ ] Permettre à l'opérateur de valider une étape
  - [ ] Gérer les erreurs
  - [ ] Sauvegarder la progression de l’opérateur

## Diagrammes

### Cas d'utilisation

#### Module de Création  
![diagrammeCasUtilisationCreation](images/diagrammeCasUtilisationModuleCreation.png)

#### Module de Guidage  
![diagrammeCasUtilisationGuidage](images/diagrammeCasUtilisationModuleGuidage.png)

### Séquence (x2)

#### Diagramme de séquence – Création d’un processus  
![diagrammeSequence1](images/sequenceCreation1.png)

#### Diagramme de séquence – Import / Export d’un processus  
![diagrammeSequence2](images/sequenceCreation2.png)

### Classe

#### Diagramme de classe – Module de Création  
![diagrammeClasseCreation](images/diagrammeClasseCreation.png)

### Déploiement

#### Architecture de déploiement - Module de création  
![diagrammeDeploiement](images/diagrammeDeploiementModuleCreation.png)

## Équipe de développement

- <a href= "https://github.com/clementBernard130">BERNARD Clément</a> (Module de création)
- <a href =https://github.com/ValentinBOUSQUET>BOUSQUET-SOLFRINI Valentin</a> (Module de guidage)

---
&copy; 2024-2025 LaSalle Avignon
