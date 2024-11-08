<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Images</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Styles de base */
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: 20px auto; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: center; }
        .verify-btn {
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Images</h1>
        
        <!-- Tableau des images -->
        <table id="imagesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Aperçu</th>
                    <th>Nom du fichier</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Les images seront insérées ici par JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        const API_URL = 'http://localhost:2000/api.php';

        document.addEventListener('DOMContentLoaded', loadImages);

        async function loadImages() {
            try {
                const response = await fetch(`${API_URL}?action=allimages`);
                const images = await response.json();

                const tbody = document.querySelector('#imagesTable tbody');
                tbody.innerHTML = '';
                
                images.forEach(image => {
                    const row = `
                        <tr>
                            <td>${image.id}</td>
                            <td><img src="${image.image}" width="50" onerror="this.src='placeholder.jpg'"></td>
                            <td>${image.image.split('/').pop()}</td>
                            <td>
                                <form action="verification.php" method="post">
                                    <input type="hidden" name="imagePath" value="${image.image}">
                                    <button type="submit" class="verify-btn">
                                        <i class="fas fa-check-circle"></i> Vérifier
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } catch (error) {
                console.error("Erreur lors du chargement des images", error);
            }
        }
    </script>
</body>
</html>
