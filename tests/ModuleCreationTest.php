<?php

use PHPUnit\Framework\TestCase;

class ModuleCreationTest extends TestCase
{
    private static $testEmail;
    private static $testPassword = 'azerty123';

    private $urlAccueil = 'http://localhost:8000/';
    private $urlProcessus = 'http://localhost:8000/index.php?controleur=processus';
    private $urlCreationProcessus = 'http://localhost:8000/index.php?controleur=processus&action=add';
    private $urlCreationProcessusId = 'http://localhost:8000/index.php?controleur=processus&action=view&id=1';
    private $urlRegister = 'http://localhost:8000/index.php?controleur=operateurs&action=register';
    private $urlLogin = 'http://localhost:8000/index.php?controleur=operateurs&action=login';

    public static function setUpBeforeClass(): void
    {
        self::$testEmail = 'test_' . rand(1000, 9999) . '@test.com';

        $postData = [
            'name' => 'Testeur',
            'email' => self::$testEmail,
            'password' => self::$testPassword,
            'submit' => 'true'
        ];

        $temp = new self();
        $temp->httpPost('http://localhost:8000/index.php?controleur=operateurs&action=register', $postData);
    }

    public function testLogin()
    {
        $loginData = [
            'email' => self::$testEmail,
            'password' => self::$testPassword,
            'submit' => 'true'
        ];
        $response = $this->httpPost($this->urlLogin, $loginData);
        $this->assertStringContainsString("You have logged in successfully", $response);
    }

    public function testPageAccueilIsAccessible()
    {
        $response = $this->httpGet($this->urlAccueil);
        $response = preg_replace('/\s+/', ' ', trim($response));
        $this->assertNotFalse($response, "La page Accueil n'est pas accessible.");
        $this->assertStringContainsString("Ergonomic Workstation", $response, "Le titre n'est pas présent sur la page.");
    }

    public function testUrlProcessusIsAccessible()
    {
        $response = $this->httpGet($this->urlProcessus);
        $response = preg_replace('/\s+/', ' ', trim($response));
        $this->assertNotFalse($response, "La page Processus n'est pas accessible.");
        $this->assertStringContainsString("href=\"/processus/add\"", $response, "Le lien pour Créer un processus n'est pas présent sur la page.");
        $this->assertStringContainsString("href=\"/processus/importZip\"", $response, "Le lien pour Importer un processus n'est pas présent sur la page.");
    }

    public function testCreerProcessus()
    {
        $postData = [
            "nomProcessus" => "Exemple de processus",
            "descriptionProcessus" => "Ceci est un processus d'assemblage de test !",
            "submit" => "Envoyer"
        ];
        $response = $this->httpPost($this->urlCreationProcessus, $postData);
        file_put_contents("test-output.html", $response);
        $this->assertStringContainsString("Processus ajouté avec succès !", $response, "Impossible de créer un processus.");
    }

    public function testUrlProcessusId()
    {
        $response = $this->httpGet($this->urlCreationProcessusId);
        $response = preg_replace('/\s+/', ' ', trim($response));
        $this->assertNotFalse($response, "La page du processus n'est pas accessible.");
    }

    private function httpGet($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function httpPost($url, $postData)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
}
