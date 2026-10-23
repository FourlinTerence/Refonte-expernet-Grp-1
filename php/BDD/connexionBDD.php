<?php
/**
 * ==========================================================================
 * CONNEXION BASE DE DONNÉES - EXPERNET CAMPUS
 * ==========================================================================
 * Classe principale de gestion de la base de données via PDO.
 * Inspirée du modèle MaConnexion du projet BlogCookery_Island,
 * adaptée à la structure de la base de données Expernet Campus.
 *
 * Tables gérées :
 *   - utilisateur       : comptes des apprenants et formateurs
 *   - authentification  : hash du mot de passe (séparé de l'identité)
 *   - utilisateur_role  : liaison utilisateur <-> rôle
 *   - role              : étudiant, formateur, admin, etc.
 *   - formation         : catalogue des formations
 *   - filiere           : pôle informatique / marketing
 *   - session_formation : sessions planifiées sur un campus
 *   - campus            : campus Réunion / Mayotte
 *   - promotion         : groupes d'apprenants par session
 *   - inscription       : inscription d'un utilisateur à une promotion
 *   - emargement        : suivi de présence journalier
 * ==========================================================================
 */

class ConnexionBDD {

    // -----------------------------------------------------------------------
    // Propriétés de connexion (privées : inaccessibles hors de la classe)
    // -----------------------------------------------------------------------
    private $nomBaseDeDonnees;
    private $motDePasse;
    private $nomUtilisateur;
    private $hote;
    private $connexionPDO; // Instance PDO partagée dans toute la classe

    /**
     * Constructeur : initialise et établit la connexion PDO à la BDD.
     *
     * @param string $nomBaseDeDonnees  Nom de la base de données MySQL
     * @param string $motDePasse        Mot de passe MySQL (vide par défaut pour XAMPP)
     * @param string $nomUtilisateur    Nom d'utilisateur MySQL (ex: "root")
     * @param string $hote              Hôte du serveur (ex: "localhost")
     */
    public function __construct($nomBaseDeDonnees, $motDePasse, $nomUtilisateur, $hote) {
        $this->nomBaseDeDonnees = $nomBaseDeDonnees;
        $this->motDePasse       = $motDePasse;
        $this->nomUtilisateur   = $nomUtilisateur;
        $this->hote             = $hote;

        try {
            // Construction du DSN (Data Source Name)
            $dsn = "mysql:host=$this->hote;dbname=$this->nomBaseDeDonnees;charset=utf8mb4";

            // Création de l'instance PDO
            $this->connexionPDO = new PDO($dsn, $this->nomUtilisateur, $this->motDePasse);

            // Active le mode d'erreur par exception pour faciliter le débogage
            $this->connexionPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            // En cas d'échec de connexion, on stoppe l'exécution
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }


    // =======================================================================
    // MÉTHODES SELECT (LECTURE)
    // =======================================================================

    /**
     * Sélectionne tous les enregistrements d'une table.
     * Utilisation simple, sans filtre.
     *
     * @param  string $table  Nom de la table à lire
     * @return array          Tableau associatif des résultats
     */
    public function select($table) {
        try {
            $requete = "SELECT * FROM $table";
            $resultat = $this->connexionPDO->query($requete);
            return $resultat->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur SELECT : " . $e->getMessage();
        }
    }

    /**
     * Recherche un utilisateur par son adresse email (pour la connexion).
     * L'email est unique dans la table utilisateur.
     *
     * @param  string $email  Email saisi dans le formulaire de connexion
     * @return array          Données de l'utilisateur si trouvé
     */
    public function selectUtilisateurParEmail($email) {
        try {
            $requete = "SELECT * FROM utilisateur WHERE email = :email";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':email', $email, PDO::PARAM_STR);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectUtilisateurParEmail : " . $e->getMessage();
        }
    }

    /**
     * Récupère le hash du mot de passe d'un utilisateur depuis la table authentification.
     * La table authentification est séparée de utilisateur pour plus de sécurité.
     *
     * @param  int   $id_utilisateur  Identifiant de l'utilisateur
     * @return array                  Ligne d'authentification (contient password_hash)
     */
    public function selectAuthentification($id_utilisateur) {
        try {
            $requete = "SELECT * FROM authentification WHERE id_utilisateur = :id_utilisateur";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectAuthentification : " . $e->getMessage();
        }
    }

    /**
     * Récupère le ou les rôles d'un utilisateur (étudiant, formateur, admin...).
     * Effectue une jointure entre utilisateur_role et role.
     *
     * @param  int   $id_utilisateur  Identifiant de l'utilisateur
     * @return array                  Tableau des rôles de l'utilisateur
     */
    public function selectRolesUtilisateur($id_utilisateur) {
        try {
            $requete = "SELECT role.id_role, role.libelle
                        FROM utilisateur_role
                        INNER JOIN role ON role.id_role = utilisateur_role.id_role
                        WHERE utilisateur_role.id_utilisateur = :id_utilisateur";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectRolesUtilisateur : " . $e->getMessage();
        }
    }

    /**
     * Récupère toutes les formations du catalogue, avec leur filière associée.
     * Jointure entre formation et filiere.
     *
     * @return array  Liste complète des formations avec nom de filière
     */
    public function selectFormations() {
        try {
            $requete = "SELECT formation.*, filiere.nom AS nom_filiere
                        FROM formation
                        INNER JOIN filiere ON filiere.id_filiere = formation.id_filiere
                        ORDER BY formation.niveau_rncp ASC";

            $resultat = $this->connexionPDO->query($requete);
            return $resultat->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectFormations : " . $e->getMessage();
        }
    }

    /**
     * Récupère les détails d'une formation par son identifiant.
     *
     * @param  int   $id_formation  Identifiant de la formation
     * @return array                Données complètes de la formation + filière
     */
    public function selectFormationParId($id_formation) {
        try {
            $requete = "SELECT formation.*, filiere.nom AS nom_filiere
                        FROM formation
                        INNER JOIN filiere ON filiere.id_filiere = formation.id_filiere
                        WHERE formation.id_formation = :id_formation";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':id_formation', $id_formation, PDO::PARAM_INT);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectFormationParId : " . $e->getMessage();
        }
    }

    /**
     * Récupère les modules (programme) d'une formation.
     *
     * @param  int   $id_formation  Identifiant de la formation
     * @return array                Liste des modules de la formation
     */
    public function selectModulesFormation($id_formation) {
        try {
            $requete = "SELECT * FROM module_formation
                        WHERE id_formation = :id_formation
                        ORDER BY id_module ASC";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':id_formation', $id_formation, PDO::PARAM_INT);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectModulesFormation : " . $e->getMessage();
        }
    }

    /**
     * Récupère la liste des émargements d'une inscription (pour un étudiant).
     * Permet de calculer le taux d'assiduité.
     *
     * @param  int   $id_inscription  Identifiant de l'inscription de l'étudiant
     * @return array                  Tableau des émargements (date, présence, heure)
     */
    public function selectEmargementsInscription($id_inscription) {
        try {
            $requete = "SELECT * FROM emargement
                        WHERE id_inscription = :id_inscription
                        ORDER BY date_jour DESC";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':id_inscription', $id_inscription, PDO::PARAM_INT);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectEmargementsInscription : " . $e->getMessage();
        }
    }

    /**
     * Récupère les étudiants inscrits dans une promotion (vue formateur).
     * Jointure entre inscription, utilisateur et promotion.
     *
     * @param  int   $id_promotion  Identifiant de la promotion
     * @return array                Liste des étudiants de la promotion
     */
    public function selectEtudiantsPromotion($id_promotion) {
        try {
            $requete = "SELECT utilisateur.id_utilisateur, utilisateur.nom, utilisateur.prenom,
                               utilisateur.email, inscription.statut, inscription.id_inscription
                        FROM inscription
                        INNER JOIN utilisateur ON utilisateur.id_utilisateur = inscription.id_utilisateur
                        WHERE inscription.id_promotion = :id_promotion
                        ORDER BY utilisateur.nom ASC";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindParam(':id_promotion', $id_promotion, PDO::PARAM_INT);
            $requete_preparee->execute();

            return $requete_preparee->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectEtudiantsPromotion : " . $e->getMessage();
        }
    }

    /**
     * Récupère les promotions suivies par un formateur (via ses inscriptions actives).
     * Pour la section "Promotions suivies" du dashboard formateur.
     *
     * @return array  Liste de toutes les promotions avec formation et campus
     */
    public function selectPromotions() {
        try {
            $requete = "SELECT promotion.*, formation.intitule AS formation_intitule,
                               campus.nom AS campus_nom
                        FROM promotion
                        INNER JOIN session_formation ON session_formation.id_session = promotion.id_session
                        INNER JOIN formation ON formation.id_formation = session_formation.id_formation
                        INNER JOIN campus ON campus.id_campus = session_formation.id_campus
                        ORDER BY promotion.annee DESC";

            $resultat = $this->connexionPDO->query($requete);
            return $resultat->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return "Erreur selectPromotions : " . $e->getMessage();
        }
    }


    // =======================================================================
    // MÉTHODES INSERT (ÉCRITURE)
    // =======================================================================

    /**
     * Insère un nouvel utilisateur dans la table utilisateur.
     * Appelée lors de l'inscription d'un candidat.
     * Le mot de passe n'est PAS stocké ici (voir insertionAuthentification).
     *
     * @param  string $nom        Nom de famille
     * @param  string $prenom     Prénom
     * @param  string $email      Adresse email (unique)
     * @param  string $telephone  Numéro de téléphone
     * @param  string $ville      Ville de résidence
     * @param  bool   $handicape  Situation de handicap (true/false)
     * @param  string $territoire ENUM 'REUNION' ou 'MAYOTTE'
     * @return int                L'ID du nouvel utilisateur créé (lastInsertId)
     */
    public function insertionUtilisateur($nom, $prenom, $email, $telephone, $ville, $handicape, $territoire) {
        try {
            $requete = "INSERT INTO utilisateur (nom, prenom, email, telephone, ville, handicape, territoire)
                        VALUES (:nom, :prenom, :email, :telephone, :ville, :handicape, :territoire)";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindParam(':nom',        $nom,        PDO::PARAM_STR);
            $requete_preparee->bindParam(':prenom',     $prenom,     PDO::PARAM_STR);
            $requete_preparee->bindParam(':email',      $email,      PDO::PARAM_STR);
            $requete_preparee->bindParam(':telephone',  $telephone,  PDO::PARAM_STR);
            $requete_preparee->bindParam(':ville',      $ville,      PDO::PARAM_STR);
            $requete_preparee->bindParam(':handicape',  $handicape,  PDO::PARAM_BOOL);
            $requete_preparee->bindParam(':territoire', $territoire, PDO::PARAM_STR);

            $requete_preparee->execute();

            // Retourne l'ID de l'utilisateur nouvellement créé
            return $this->connexionPDO->lastInsertId();

        } catch (PDOException $e) {
            return "Erreur insertionUtilisateur : " . $e->getMessage();
        }
    }

    /**
     * Insère le mot de passe hashé dans la table authentification.
     * Séparée de l'utilisateur pour respecter la structure de la BDD.
     * Doit être appelée juste après insertionUtilisateur().
     *
     * @param  int    $id_utilisateur  ID de l'utilisateur créé
     * @param  string $password_hash   Hash du mot de passe (via password_hash())
     * @return string                  Confirmation ou message d'erreur
     */
    public function insertionAuthentification($id_utilisateur, $password_hash) {
        try {
            $requete = "INSERT INTO authentification (id_utilisateur, password_hash)
                        VALUES (:id_utilisateur, :password_hash)";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $requete_preparee->bindParam(':password_hash',  $password_hash,  PDO::PARAM_STR);

            $requete_preparee->execute();
            return "Authentification enregistrée";

        } catch (PDOException $e) {
            return "Erreur insertionAuthentification : " . $e->getMessage();
        }
    }

    /**
     * Associe un rôle à un utilisateur dans la table utilisateur_role.
     * Doit être appelée après insertionUtilisateur().
     *
     * @param  int    $id_utilisateur  ID de l'utilisateur
     * @param  int    $id_role         ID du rôle (1=etudiant, 2=formateur, 3=admin...)
     * @return string                  Confirmation ou message d'erreur
     */
    public function insertionUtilisateurRole($id_utilisateur, $id_role) {
        try {
            $requete = "INSERT INTO utilisateur_role (id_utilisateur, id_role)
                        VALUES (:id_utilisateur, :id_role)";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $requete_preparee->bindParam(':id_role',        $id_role,        PDO::PARAM_INT);

            $requete_preparee->execute();
            return "Rôle attribué avec succès";

        } catch (PDOException $e) {
            return "Erreur insertionUtilisateurRole : " . $e->getMessage();
        }
    }

    /**
     * Enregistre l'émargement (signature de présence) d'un étudiant pour la journée.
     * Si une entrée existe déjà pour cette date, elle est mise à jour.
     *
     * @param  int    $id_inscription  ID d'inscription de l'étudiant
     * @param  string $date_jour       Date du jour (format Y-m-d)
     * @return string                  Confirmation ou message d'erreur
     */
    public function insertionEmargement($id_inscription, $date_jour) {
        try {
            // INSERT ... ON DUPLICATE KEY : évite les doublons pour la même journée
            $requete = "INSERT INTO emargement (id_inscription, date_jour, heure_signature, present)
                        VALUES (:id_inscription, :date_jour, NOW(), TRUE)
                        ON DUPLICATE KEY UPDATE heure_signature = NOW(), present = TRUE";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindParam(':id_inscription', $id_inscription, PDO::PARAM_INT);
            $requete_preparee->bindParam(':date_jour',      $date_jour,      PDO::PARAM_STR);

            $requete_preparee->execute();
            return "Émargement enregistré";

        } catch (PDOException $e) {
            return "Erreur insertionEmargement : " . $e->getMessage();
        }
    }


    // =======================================================================
    // MÉTHODES UPDATE (MODIFICATION)
    // =======================================================================

    /**
     * Met à jour les informations du profil d'un utilisateur.
     *
     * @param  int    $id_utilisateur  ID de l'utilisateur à modifier
     * @param  string $telephone       Nouveau numéro de téléphone
     * @param  string $ville           Nouvelle ville de résidence
     * @return string                  Confirmation ou message d'erreur
     */
    public function majProfilUtilisateur($id_utilisateur, $telephone, $ville) {
        try {
            $requete = "UPDATE utilisateur
                        SET telephone = :telephone, ville = :ville
                        WHERE id_utilisateur = :id_utilisateur";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindValue(':telephone',      $telephone,      PDO::PARAM_STR);
            $requete_preparee->bindValue(':ville',          $ville,          PDO::PARAM_STR);
            $requete_preparee->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);

            $requete_preparee->execute();
            return "Profil mis à jour";

        } catch (PDOException $e) {
            return "Erreur majProfilUtilisateur : " . $e->getMessage();
        }
    }

    /**
     * Met à jour le mot de passe hashé d'un utilisateur.
     * Appelée lors d'une réinitialisation de mot de passe.
     *
     * @param  int    $id_utilisateur  ID de l'utilisateur
     * @param  string $nouveau_hash    Nouveau hash du mot de passe
     * @return string                  Confirmation ou message d'erreur
     */
    public function majMotDePasse($id_utilisateur, $nouveau_hash) {
        try {
            $requete = "UPDATE authentification
                        SET password_hash = :password_hash
                        WHERE id_utilisateur = :id_utilisateur";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindValue(':password_hash',  $nouveau_hash,   PDO::PARAM_STR);
            $requete_preparee->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);

            $requete_preparee->execute();
            return "Mot de passe mis à jour";

        } catch (PDOException $e) {
            return "Erreur majMotDePasse : " . $e->getMessage();
        }
    }

    /**
     * Active ou désactive un compte utilisateur.
     * Un compte désactivé (actif = FALSE) ne peut plus se connecter.
     *
     * @param  int  $id_utilisateur  ID de l'utilisateur
     * @param  bool $actif           true = actif, false = désactivé
     * @return string                Confirmation ou message d'erreur
     */
    public function majStatutCompte($id_utilisateur, $actif) {
        try {
            $requete = "UPDATE utilisateur
                        SET actif = :actif
                        WHERE id_utilisateur = :id_utilisateur";

            $requete_preparee = $this->connexionPDO->prepare($requete);

            $requete_preparee->bindValue(':actif',          $actif,          PDO::PARAM_BOOL);
            $requete_preparee->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);

            $requete_preparee->execute();
            return "Statut du compte mis à jour";

        } catch (PDOException $e) {
            return "Erreur majStatutCompte : " . $e->getMessage();
        }
    }


    // =======================================================================
    // MÉTHODES DELETE (SUPPRESSION)
    // =======================================================================

    /**
     * Supprime un utilisateur et ses données liées.
     * ATTENTION : opération irréversible. À utiliser avec précaution.
     * Nécessite que les contraintes de clé étrangère soient gérées (CASCADE ou suppression préalable).
     *
     * @param  int    $id_utilisateur  ID de l'utilisateur à supprimer
     * @return string                  Confirmation ou message d'erreur
     */
    public function deleteUtilisateur($id_utilisateur) {
        try {
            $requete = "DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur";

            $requete_preparee = $this->connexionPDO->prepare($requete);
            $requete_preparee->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
            $requete_preparee->execute();

            return "Utilisateur supprimé";

        } catch (PDOException $e) {
            return "Erreur deleteUtilisateur : " . $e->getMessage();
        }
    }

}


// =============================================================================
// INSTANCIATION DE LA CONNEXION
// Paramètres adaptés à votre environnement XAMPP local.
// =============================================================================

// Nom de votre base de données créée dans phpMyAdmin
$nomBDD = "expernet_campus";

// Mot de passe MySQL (vide par défaut dans XAMPP)
$motDePasse = "";

// Utilisateur MySQL par défaut dans XAMPP
$utilisateur = "root";

// Hôte du serveur MySQL
$hote = "localhost";

// Création de l'instance de connexion (disponible pour tous les fichiers qui incluent ce fichier)
$bdd = new ConnexionBDD($nomBDD, $motDePasse, $utilisateur, $hote);

?>
