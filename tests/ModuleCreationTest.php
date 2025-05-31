<?php

use PHPUnit\Framework\TestCase;

class ModuleCreationTest extends TestCase
{
    private $urlAccueil = 'http://localhost:8000/';
    private $urlProcessus = 'http://localhost:8000/index.php?controleur=processus';
    private $urlCreationProcessus = 'http://localhost:8000/index.php?controleur=processus&action=add';
    private $urlCreationProcessusId = 'http://localhost:8000/index.php?controleur=processus&action=view&id=1';

    /* Exemples : quelques tests */

    /** 🔹 Test 1 : Vérifier que la page Accueil est accessible */
    public function testPageAccueilIsAccessible()
    {
        $response = $this->httpGet($this->urlAccueil);
        $response = preg_replace('/\s+/', ' ', trim($response));
        $this->assertNotFalse($response, "La page Accueil n'est pas accessible.");
        $this->assertStringContainsString("Ergonomic Workstation", $response, "Le titre n'est pas présent sur la page.");
    }

    /** 🔹 Test 2 : Vérifier que la page Processus est accessible */
    public function testUrlProcessusIsAccessible()
    {
        $response = $this->httpGet($this->urlProcessus);
        $response = preg_replace('/\s+/', ' ', trim($response));
        $this->assertNotFalse($response, "La page Processus n'est pas accessible.");
        $this->assertStringContainsString("href=\"/processus/add\"", $response, "Le lien pour Créer un processus n'est pas présent sur la page.");
        $this->assertStringContainsString("href=\"/processus/importZip\"", $response, "Le lien pour Importer un processus n'est pas présent sur la page.");
        // etc ...
    }

    /** 🔹 Test 3 : Vérifier que l'on peut créer un processus */
    public function testCreerProcessus()
    {
        $postData = [
            "nomProcessus" => "Exemple de processus",
            "descriptionProcessus" => "Ceci est un processus d'assemblage de test !",
            "submit" => "Envoyer"
        ];
        $response = $this->httpPost($this->urlCreationProcessus, $postData);
        $this->assertStringContainsString("Processus ajouté avec succès !", $response, "Impossible de créer un processus.");
    }

    /** 🔹 Test 4 : Vérifier que l'on peut consulter un procesuus */
    public function testUrlProcessusId()
    {
        $response = $this->httpGet($this->urlCreationProcessusId);
        $response = preg_replace('/\s+/', ' ', trim($response));
        $this->assertNotFalse($response, "La page du processus n'est pas accessible.");
        // etc ...
    }

    /** 🔹 Fonction GET pour récupérer la page */
    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /** 🔹 Fonction POST pour soumettre un formulaire */
    private function httpPost($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}