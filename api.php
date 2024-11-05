<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Connexion à la base de données
$host = 'localhost';
$dbname = 'errorfinder';
$username = 'root'; // Remplacez par votre nom d'utilisateur
$password = ''; // Remplacez par votre mot de passe

// Dossier où seront stockées les images
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Connexion échouée: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_GET['action']) ? $_GET['action'] : '';

switch($request) {
    case 'addimage':
        if ($method === 'POST') {
            addImage($pdo, $uploadDir);
        }
        break;
    
    case 'allimages':
        if ($method === 'GET') {
            getAllImages($pdo);
        }
        break;
        
    case 'imagebyid':
        if ($method === 'GET') {
            getImageById($pdo);
        }
        break;
        
    default:
        echo json_encode(['error' => 'Action non valide']);
        break;
}

function addImage($pdo, $uploadDir) {
    try {
        if (!isset($_FILES['image'])) {
            echo json_encode(['error' => 'Aucun fichier uploadé']);
            return;
        }

        $file = $_FILES['image'];
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $fileName;
        
        // Vérification du type de fichier
        $allowedTypes = ['image/jpeg', 'image/jpg','image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            echo json_encode(['error' => 'Type de fichier non autorisé']);
            return;
        }

        // Déplacement du fichier
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $stmt = $pdo->prepare('INSERT INTO image (image) VALUES (:image)');
            $stmt->execute(['image' => $targetPath]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Image ajoutée avec succès',
                'id' => $pdo->lastInsertId(),
                'path' => $targetPath
            ]);
        } else {
            echo json_encode(['error' => 'Erreur lors de l\'upload du fichier']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de l\'ajout: ' . $e->getMessage()]);
    }
}

function getAllImages($pdo) {
    try {
        $stmt = $pdo->query('SELECT * FROM image ORDER BY id DESC');
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($images);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération: ' . $e->getMessage()]);
    }
}

function getImageById($pdo) {
    try {
        if (!isset($_GET['id'])) {
            echo json_encode(['error' => 'ID requis']);
            return;
        }
        
        $stmt = $pdo->prepare('SELECT * FROM image WHERE id = :id');
        $stmt->execute(['id' => $_GET['id']]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($image) {
            echo json_encode($image);
        } else {
            echo json_encode(['error' => 'Image non trouvée']);
        }
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Erreur lors de la récupération: ' . $e->getMessage()]);
    }
}