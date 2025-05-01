Projet : OCR Proofreader
Type : Projet professionnel – Application de traitement d’image et correction de texte
Technologies : Node.js, API OCR Tesseract.js, LanguageTool API, MAMP, Adobe InDesign (plugin)

Description :
Dans le cadre de mon travail, j’ai développé une solution complète d’extraction de texte à partir d’images, de détection d’erreurs et de proposition de corrections. Ce projet a été décliné en plusieurs versions en fonction des besoins du client.

Fonctionnement général :
📤 Envoi d’une image : l'utilisateur envoie une image via une interface dédiée ou directement depuis un plugin InDesign.

🔍 Extraction du texte : l’API OCR Tesseract.js est utilisée pour extraire le texte de l’image.

🧠 Analyse linguistique : une fois le texte extrait, il est envoyé à l’API LanguageTool pour détecter les erreurs grammaticales et orthographiques.

✅ Corrections proposées : l’application retourne les erreurs détectées et propose des corrections.

💾 Mise à jour du texte : après validation, la version corrigée du texte est générée et peut être réutilisée dans un projet Adobe InDesign via un plugin personnalisé.

Technologies utilisées :
Node.js : gestion des requêtes et traitement du texte (OCR + correction)

Tesseract.js : API OCR pour extraire le texte à partir des images

LanguageTool : API de correction grammaticale et orthographique

MAMP : environnement local pour exécuter le serveur

Plugin InDesign : intégration directe dans Adobe InDesign pour corriger automatiquement le texte dans les documents de mise en page
