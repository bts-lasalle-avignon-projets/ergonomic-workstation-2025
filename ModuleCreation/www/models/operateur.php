<?php

class OperateurModel extends Model
{
    public function register()
    {
        // Récupération et nettoyage des données POST
        $name     = htmlspecialchars(strip_tags($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $submit   = $_POST['submit'] ?? null;

        if ($submit) {
            if ($name === '' || $email === false || $password === '') {
                Messages::setMsg('Please fill in the required fields', 'error');
                return;
            }

            // Hash du mot de passe
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion dans la base de données
            $this->query('INSERT INTO Superviseur (name, email, password) VALUES (:name, :email, :password)');
            $this->bind(':name', $name);
            $this->bind(':email', $email);
            $this->bind(':password', $passwordHash);
            $this->execute();

            // Vérifier si l'insertion a réussi
            if ($this->getLastInsertId()) {
                Messages::setMsg('Compte superviseur créé. Vous pouvez maintenant vous connecter.', 'success');
                header('Location: ' . ROOT_PATH . 'operateurs/login');
                exit(0);
            }
        }

        return;
    }

    public function login()
    {
        // Récupération et nettoyage des données POST
        $email    = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $submit   = $_POST['submit'] ?? null;

        if ($submit) {
            if ($email === false || $password === '') {
                Messages::setMsg('Veuillez remplir les champs obligatoires', 'error');
                return;
            }

            // Vérification de l'email dans la base
            $this->query('SELECT * FROM Superviseur WHERE email = :email');
            $this->bind(':email', $email);
            $row = $this->getResult();

            if ($row) {
                if (password_verify($password, $row['password'])) {
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['user_data'] = [
                        "id"    => $row['id'],
                        "name"  => $row['name'],
                        "email" => $row['email']
                    ];
                    Messages::setMsg('Vous vous êtes connecté avec succès', 'success');
                    header('Location: ' . ROOT_PATH . 'processus');
                    exit(0);
                } else {
                    Messages::setMsg('Le mot de passe est incorrect', 'error');
                    return;
                }
            } else {
                Messages::setMsg('Superviseur introuvable', 'error');
            }
        }

        return;
    }

    public function verifierUtilisateur()
    {
        $this->query("SELECT * FROM Superviseur");
        $utilisateur = $this->getResults();

        if(!empty($utilisateur))
        {
            return true;
        }

        return false;
    }
}
