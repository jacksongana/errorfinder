<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Images</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .file-input-container {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .file-input-container input[type="file"] {
            display: none;
        }
        .file-input-label {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
        }
        .file-input-label:hover {
            background-color: #0056b3;
        }
        .selected-file {
            margin-left: 10px;
            color: #666;
        }
        button {
            padding: 8px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        button.verify {
            background-color: #28a745;
        }
        button.extract {
            background-color: #ffc107;
            color: black;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 4px;
        }
        .preview-container {
            margin-top: 10px;
        }
        .status-icon {
            font-size: 20px;
        }
        .valid {
            color: #28a745;
        }
        .invalid {
            color: #dc3545;
        }
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            margin-top: 10px;
            display: none;
        }
        .progress {
            width: 0%;
            height: 100%;
            background-color: #007bff;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Images</h1>
        
        <!-- Formulaire d'upload -->
        <div class="form-group">
            <h2>Ajouter une nouvelle image</h2>
            <div class="file-input-container">
                <label class="file-input-label">
                    <i class="fas fa-upload"></i> Choisir une image
                    <input type="file" id="imageInput" accept="image/*" onchange="handleFileSelect(event)">
                </label>
                <span class="selected-file" id="selectedFileName"></span>
            </div>
            <div class="preview-container">
                <img id="imagePreview" style="display: none; max-width: 200px; max-height: 200px; margin-top: 10px;">
            </div>
            <div class="progress-bar" id="progressBar">
                <div class="progress" id="progress"></div>
            </div>
            <button onclick="uploadImage()" style="margin-top: 10px;">Ajouter</button>
        </div>

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
                <!-- Les images seront insérées ici -->
            </tbody>
        </table>
    </div>

    <script>
        const API_URL = 'http://localhost:2000/api.php';
        let selectedFile = null;

        document.addEventListener('DOMContentLoaded', loadImages);

        function handleFileSelect(event) {
            selectedFile = event.target.files[0];
            document.getElementById('selectedFileName').textContent = selectedFile.name;
            
            // Prévisualisation de l'image
            const preview = document.getElementById('imagePreview');
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(selectedFile);
        }

        async function uploadImage() {
            if (!selectedFile) {
                showAlert('Veuillez sélectionner une image', 'danger');
                return;
            }

            const formData = new FormData();
            formData.append('image', selectedFile);

            const progressBar = document.getElementById('progressBar');
            const progress = document.getElementById('progress');
            progressBar.style.display = 'block';

            try {
                const response = await fetch(`${API_URL}?action=addimage`, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showAlert('Image ajoutée avec succès', 'success');
                    document.getElementById('imageInput').value = '';
                    document.getElementById('selectedFileName').textContent = '';
                    document.getElementById('imagePreview').style.display = 'none';
                    loadImages();
                } else {
                    showAlert(data.error || 'Erreur lors de l\'ajout', 'danger');
                }
            } catch (error) {
                showAlert('Erreur lors de la communication avec le serveur', 'danger');
            } finally {
                progressBar.style.display = 'none';
                progress.style.width = '0%';
            }
        }

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
                            <td><img src="${image.image}" class="image-preview" onerror="this.src='placeholder.jpg'"></td>
                            <td>${image.image.split('/').pop()}</td>
                            <td>
                                <button class="extract" onclick="downloadImage('${image.image}')">
                                    <i class="fas fa-download"></i> Extraire
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } catch (error) {
                showAlert('Erreur lors du chargement des images', 'danger');
            }
        }

        function downloadImage(imagePath) {
            const link = document.createElement('a');
            link.href = imagePath;
            link.download = imagePath.split('/').pop();
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.textContent = message;
            
            const container = document.querySelector('.container');
            container.insertBefore(alertDiv, container.firstChild);
            
            setTimeout(() => alertDiv.remove(), 3000);
        }
    </script>
</body>
</html>