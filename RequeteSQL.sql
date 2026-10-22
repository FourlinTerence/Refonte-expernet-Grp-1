CREATE TABLE role (
    id_role TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(30) NOT NULL
);

CREATE TABLE utilisateur (
    id_utilisateur BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(15),
    ville VARCHAR(100),
    handicape BOOLEAN DEFAULT FALSE,
    territoire ENUM('REUNION', 'MAYOTTE'),
    actif BOOLEAN DEFAULT TRUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE utilisateur_role (
    id_user_role BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur BIGINT UNSIGNED NOT NULL,
    id_role TINYINT UNSIGNED NOT NULL,

    FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur),

    FOREIGN KEY (id_role)
        REFERENCES role(id_role)
);

CREATE TABLE authentification (
    id_auth BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur BIGINT UNSIGNED NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    mfa_active BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE filiere (
    id_filiere SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE formation (
    id_formation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_filiere SMALLINT UNSIGNED NOT NULL,

    intitule VARCHAR(200) NOT NULL,
    niveau_rncp TINYINT UNSIGNED,
    cpf_eligible BOOLEAN DEFAULT TRUE,

    description TEXT,
    prerequis TEXT,
    debouches TEXT,

    FOREIGN KEY (id_filiere)
        REFERENCES filiere(id_filiere)
);

CREATE TABLE module_formation (
    id_module INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_formation INT UNSIGNED NOT NULL,
    libelle VARCHAR(150) NOT NULL,

    FOREIGN KEY (id_formation)
        REFERENCES formation(id_formation)
);

CREATE TABLE campus (
    id_campus SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    territoire ENUM('REUNION','MAYOTTE'),
    adresse VARCHAR(255)
);

CREATE TABLE session_formation (
    id_session INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_formation INT UNSIGNED NOT NULL,
    id_campus SMALLINT UNSIGNED NOT NULL,

    date_debut DATE,
    date_fin DATE,

    nb_places SMALLINT UNSIGNED,

    FOREIGN KEY (id_formation)
        REFERENCES formation(id_formation),

    FOREIGN KEY (id_campus)
        REFERENCES campus(id_campus)
);

CREATE TABLE promotion (
    id_promotion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_session INT UNSIGNED NOT NULL,

    nom VARCHAR(100),
    annee YEAR,

    FOREIGN KEY (id_session)
        REFERENCES session_formation(id_session)
);

CREATE TABLE entreprise (
    id_entreprise INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    raison_sociale VARCHAR(150) NOT NULL,
    siret CHAR(14),
    email VARCHAR(150),
    telephone VARCHAR(15)
);

CREATE TABLE inscription (
    id_inscription BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_utilisateur BIGINT UNSIGNED NOT NULL,
    id_promotion INT UNSIGNED NOT NULL,
    id_entreprise INT UNSIGNED NULL,

    date_inscription DATETIME,

    statut ENUM(
        'INSCRIT',
        'ANNULE',
        'ABANDON',
        'DIPLOME'
    ) DEFAULT 'INSCRIT',

    FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur),

    FOREIGN KEY (id_promotion)
        REFERENCES promotion(id_promotion),

    FOREIGN KEY (id_entreprise)
        REFERENCES entreprise(id_entreprise)
);

CREATE TABLE emargement (
    id_emargement BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_inscription BIGINT UNSIGNED NOT NULL,

    date_jour DATE NOT NULL,
    heure_signature DATETIME,

    present BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (id_inscription)
        REFERENCES inscription(id_inscription)
);

CREATE TABLE examen (
    id_examen INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_session INT UNSIGNED NOT NULL,

    date_examen DATETIME,
    salle VARCHAR(100),

    FOREIGN KEY (id_session)
        REFERENCES session_formation(id_session)
);

CREATE TABLE competence (
    id_competence SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(150) NOT NULL
);

CREATE TABLE evaluation_competence (
    id_evaluation BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    id_examen INT UNSIGNED NOT NULL,
    id_competence SMALLINT UNSIGNED NOT NULL,
    id_utilisateur BIGINT UNSIGNED NOT NULL,

    note DECIMAL(5,2),
    commentaire TEXT,

    FOREIGN KEY (id_examen)
        REFERENCES examen(id_examen),

    FOREIGN KEY (id_competence)
        REFERENCES competence(id_competence),

    FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
);