https://prod.liveshare.vsengsaas.visualstudio.com/join?84736D34C0CC1D2A0B861FB321E8DE9EBE34
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['imagePath'])) {
    $imagePath = $_POST['imagePath'];
    ?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Détection et Correction de Texte en Français</title>
        <style>
            /* CSS amélioré pour la visibilité */
            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }
            
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                background-color: #f4f4f9;
                padding: 20px;
                min-height: 100vh;
            }
            
            .container {
                width: 100%;
                max-width: 600px;
                text-align: center;
                padding: 20px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }
            
            h1 {
                color: #333;
                font-size: 1.8em;
                margin-bottom: 15px;
            }
            
            img {
                margin: 15px 0;
                max-width: 100px;
            }
            
            button {
                background-color: #0d6efd;
                color: white;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 1em;
                margin: 5px;
            }
            
            #output {
                margin-top: 20px;
                text-align: left;
            }
            
            #detectedText {
                font-weight: bold;
                color: #333;
                background-color: #f9f9f9;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
                margin-top: 10px;
                overflow-wrap: break-word;
            }
            
            #correctionsList {
                list-style: none;
                padding: 0;
            }
            
            #correctionsList li {
                margin: 5px 0;
                color: #d9534f;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h1>Détection et Correction de Texte</h1>
            <p>Image vérifiée : <img src="<?= htmlspecialchars($imagePath) ?>" alt="Image vérifiée"></p>

            <div id="output">
                <h2>Texte Détecté :</h2>
                <p id="detectedText"></p>

                <h2>Erreurs et Corrections :</h2>
                <ul id="correctionsList"></ul>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
        <script>
            const imagePath = "<?= $imagePath ?>";

            function detectText(imagePath) {
                Tesseract.recognize(
                    imagePath,
                    'fra', {
                        logger: m => console.log(m)
                    }
                ).then(({ data: { text } }) => {
                    document.getElementById("detectedText").innerText = text;
                    detectErrorsWithLanguageTool(text);
                });
            }

            function detectErrorsWithLanguageTool(text) {
                const url = 'https://api.languagetoolplus.com/v2/check';
                const params = new URLSearchParams();
                const ignoreWords = [
                    "cocktail", "whiskey", "rhum", "gin", "vodka", "fastfood", "burger", "pizza", "pasta",
                    "beer", "wine", "soda", "coke", "fanta", "juice", "milkshake", "donut", "sandwich",
                ];
                
                const filteredText = text.replace(new RegExp(`\\b(${ignoreWords.join('|')})\\b`, 'gi'), ''); // Suppression des mots ignorés
                params.append("text", filteredText);
                params.append("language", "fr");

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString()
                })
                .then(response => response.json())
                .then(data => {
                    const correctionsList = document.getElementById("correctionsList");
                    correctionsList.innerHTML = ""; // Clear previous corrections

                    if (data.matches.length === 0) {
                        correctionsList.innerHTML = "<li>Aucune erreur détectée !</li>";
                    } else {
                        data.matches.forEach(match => {
                            const original = match.context.text.substr(match.context.offset, match.context.length);
                            const suggestion = match.replacements.length > 0 ? match.replacements[0].value : "Pas de suggestion";
                            const listItem = document.createElement("li");
                            listItem.textContent = `${original} ➔ ${suggestion}`;
                            correctionsList.appendChild(listItem);
                        });
                    }
                })
                .catch(error => console.error("Erreur avec l'API LanguageTool:", error));
            }

            detectText(imagePath);
        </script>
    </body>
    </html>
    <?php
} else {
    echo "Méthode non autorisée ou image manquante.";
}
