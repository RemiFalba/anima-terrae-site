<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$nom     = htmlspecialchars(trim($data['nom']     ?? ''));
$email   = htmlspecialchars(trim($data['email']   ?? ''));
$objet   = htmlspecialchars(trim($data['objet']   ?? ''));
$message = htmlspecialchars(trim($data['message'] ?? ''));

if (!$nom || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs requis manquants']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email invalide']);
    exit;
}

$destinataire = 'remi@animaterrae.fr';
$sujet        = $objet ? "[$objet] Message de $nom via animaterrae.fr" : "Message de $nom via animaterrae.fr";

$corps = "Nouveau message reçu depuis le formulaire de contact ANIMA TERRAE\n";
$corps .= "─────────────────────────────────────────\n\n";
$corps .= "Nom    : $nom\n";
$corps .= "Email  : $email\n";
$corps .= "Objet  : $objet\n\n";
$corps .= "Message :\n$message\n\n";
$corps .= "─────────────────────────────────────────\n";
$corps .= "Envoyé depuis animaterrae.fr";

$headers  = "From: noreply@animaterrae.fr\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$envoye = mail($destinataire, $sujet, $corps, $headers);

if ($envoye) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'envoi']);
}
