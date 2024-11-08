const express = require('express');
const path = require('path');
const multer = require('multer');
const Tesseract = require('tesseract.js');
const fs = require('fs');

const app = express();
const hostname = 'localhost';
const PORT = 3000;

// Mots à ignorer lors de la vérification
const ignoreWords = ["cocktail", "whiskey", "rhum", "gin", "vodka", "fastfood", "burger"];

// Configuration de multer pour accepter les images
const storage = multer.memoryStorage();
const upload = multer({ storage: storage });



// Endpoint pour le traitement de l'image
app.post('/verification.php', upload.single('image'), async(req, res) => {
    try {
        // Vérifiez si l'image a été téléchargée
        if (!req.file) {
            return res.status(400).json({ error: 'Aucune image fournie.' });
        }

        // Traitement de l'image avec Tesseract
        const { data: { text } } = await Tesseract.recognize(req.file.buffer, 'fra', { logger: m => console.log(m) });

        // Suppression des mots ignorés et nettoyage du texte
        const filteredText = text.toLowerCase().replace(new RegExp(`\\b(${ignoreWords.join('|')})\\b`, 'gi'), '').replace(/[^a-zA-Z\s]/g, '');

        // Appel à l'API LanguageTool
        const response = await fetch('https://api.languagetoolplus.com/v2/check', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ text: filteredText, language: 'fr' })
        });

        const result = await response.json();

        // Formatage des erreurs détectées
        const corrections = result.matches.map(match => {
            const errorWord = match.context.text.substring(match.context.offset, match.context.offset + match.context.length);
            const suggestions = match.replacements.map(r => r.value);
            return {
                incorrectText: errorWord,
                suggestions: suggestions
            };
        });

        // Envoi des données en réponse à Postman
        res.json({
            filteredText: filteredText,
            corrections: corrections.length > 0 ? corrections : 'Aucune faute détectée'
        });
    } catch (error) {
        console.error("Erreur lors du traitement :", error);
        res.status(500).json({ error: 'Erreur lors du traitement de l\'image ou de la vérification du texte.' });
    }
});

// Lancement du serveur
app.listen(PORT, () => {
    console.log(`Serveur en écoute sur http://localhost:${PORT}`);
});




