<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $imagePath = $_POST['imagePath'];
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Vérification de Texte</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { width: 80%; margin: 20px auto; }
            .alert { padding: 10px; margin-top: 10px; border-radius: 4px; color: black; }
            .alert-danger { background-color: #f9f9f9; color: red; }
            .alert-success { background-color: #28a745; color: white; }
            .incorrect { color: orange; font-weight: bold; }
            .correction { color: black; font-weight: bold; }
            .suggestion { color: green; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Vérification de Texte</h1>
            <p>Image vérifiée : <img src="<?= htmlspecialchars($imagePath) ?>" width="100"></p>
            <div id="errorText" class="alert alert-danger" style="display: none;"></div>
        </div>

        <script>
            const ignoreWords = ["cocktail", "whiskey", "rhum", "gin", "vodka", "fastfood", "burger"];
            const imagePath = "<?= $imagePath ?>";

            Tesseract.recognize(imagePath, 'fra', { logger: m => console.log(m) })
                .then(async ({ data: { text } }) => {
                    text = text.toLowerCase().replace(/[\r\n]+/g, ' ').trim();
                    const wordsToIgnore = new RegExp(`\\b(${ignoreWords.join('|')})\\b`, 'gi');
                    const filteredText = text.replace(wordsToIgnore, '');
                    const alphaOnlyText = filteredText.replace(/[^a-zA-Z\s]/g, '');

                    const response = await fetch("https://api.languagetoolplus.com/v2/check", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: new URLSearchParams({
                            text: alphaOnlyText,
                            language: "fr"
                        })
                    });
                    const result = await response.json();
                    const errorText = document.getElementById('errorText');
                    
                    if (result.matches.length > 0) {
                        let corrections = '<strong>Erreurs détectées :</strong><br>';
                        result.matches.forEach(match => {
                            const errorWord = match.context.text.substring(match.context.offset, match.context.offset + match.context.length);
                            const suggestions = match.replacements.map(r => `<span class="suggestion">${r.value}</span>`).join(', ');
                            corrections += `<p class="incorrect">Texte incorrect : <span class="error">${errorWord}</span></p>
                                            <p class="correction">Correction possible : ${suggestions}</p><br>`;
                        });
                        errorText.innerHTML = corrections;
                        errorText.style.display = 'block';
                    } else {
                        errorText.innerHTML = "<strong>Aucune faute détectée</strong>";
                        errorText.className = "alert alert-success";
                        errorText.style.display = 'block';
                    }
                }).catch(err => {
                    console.error("Erreur lors de l'analyse OCR", err);
                });
        </script>
    </body>
    </html>
    <?php
} else {
    echo "Méthode non autorisée.";
}
