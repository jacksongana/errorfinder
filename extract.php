<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extraction de texte d'une image avec Tesseract.js</title>
    <!-- v5 -->
    <script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>
</head>

<body>
    <h2>Choisir une image pour extraire le texte</h2>
    <input type="file" id="imageInput" accept="image/*" required>
    <button onclick="extractText()">Extraire le texte</button>

    <h3>Texte extrait :</h3>
    <pre id="output"></pre>

    <script>
        function extractText() {
            const fileInput = document.getElementById('imageInput');
            const file = fileInput.files[0];

            if (file) {
                // Lire le fichier en tant qu'URL de données (base64)
                const reader = new FileReader();
                reader.onload = function(event) {
                    const imageDataUrl = event.target.result;

                    // Utiliser Tesseract.js pour extraire le texte de l'image
                    Tesseract.recognize(
                        imageDataUrl,
                        'fra', // Spécifiez 'fra' pour le français ou 'eng' pour l'anglais, par exemple
                        {
                            logger: info => console.log(info) // Optionnel : journal des étapes
                        }
                    ).then(({
                        data: {
                            text
                        }
                    }) => {
                        document.getElementById('output').textContent = text;
                    }).catch(error => {
                        console.error('Erreur:', error);
                        document.getElementById('output').textContent = "Erreur lors de l'extraction du texte.";
                    });
                };
                reader.readAsDataURL(file);
            } else {
                alert("Veuillez sélectionner une image.");
            }
        }
    </script>
</body>

</html>
