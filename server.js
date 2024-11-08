const express = require('express');
const path = require('path');

const app = express();
const PORT = 3000;

// Servir les fichiers statiques (JavaScript, CSS, etc.)
app.use(express.static(path.join(__dirname, 'public')));

app.listen(PORT, () => {
    console.log(`Serveur JavaScript en écoute sur http://localhost:${PORT}`);
});
