const express = require('express');
const path = require('path');
const PHPFPM = require('express-php-fpm');

const app = express();
const PORT = 3000; // Choisissez un port pour votre serveur

// Utilisez express-php-fpm pour traiter les fichiers PHP
const php = new PHPFPM({
    documentRoot: path.join(__dirname),
    port: 9000 // Changez le port si nécessaire
});

// Route pour le fichier verification.php
app.get('/verification', (req, res) => {
    // Vous pouvez personnaliser ce chemin si votre fichier PHP est dans un autre répertoire
    php.run(req, res, path.join(__dirname, 'verification.php'));
});

// Servir les fichiers statiques (CSS, JavaScript, etc.)
app.use(express.static(path.join(__dirname, 'public')));

// Démarrer le serveur
app.listen(PORT, () => {
    console.log(`Serveur en écoute sur http://localhost:${PORT}`);
});