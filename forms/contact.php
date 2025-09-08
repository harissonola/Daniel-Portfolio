<?php
// forms/contact.php
// Handler pour le formulaire .php-email-form (AJAX). Répond "OK" en cas de succès.
// Utilise PHPMailer via Composer (vendor/autoload.php) OU via les fichiers src inclus localement.

// ===== CONFIG SMTP (fourni par le client) =====
const SMTP_HOST = 'smtp-smileup-platform.alwaysdata.net';
const SMTP_PORT = 587;
const SMTP_USER = 'smileup-platform@alwaysdata.net';
const SMTP_PASS = 'WODanielH2006';
const SMTP_FROM = 'smileup-platform@alwaysdata.net'; // Garder identique au SMTP_USER pour éviter le spoofing
const SMTP_FROM_NAME = 'Formulaire Daniel Whannou';
$RECIPIENTS = [
  'danielw@smileupplatform.com',
  'dwhannou229@gmail.com'
];

// ===== SÉCURITÉ DE BASE =====
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Méthode non autorisée');
}
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
  http_response_code(400);
  exit('Requête invalide');
}
if (!empty($_POST['website'] ?? '')) {
  exit('OK');
}

// ===== CHARGEMENT PHPMailer =====
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
$phpmailerLoaded = false;
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  require __DIR__ . '/vendor/autoload.php';
  $phpmailerLoaded = true;
}
if (!$phpmailerLoaded) {
  $base = __DIR__ . '/PHPMailer/src/';
  $need = ['PHPMailer.php','SMTP.php','Exception.php'];
  $ok = true;
  foreach ($need as $f) {
    if (!file_exists($base.$f)) { $ok = false; break; }
  }
  if ($ok) {
    require $base.'Exception.php';
    require $base.'PHPMailer.php';
    require $base.'SMTP.php';
    $phpmailerLoaded = true;
  }
}
if (!$phpmailerLoaded) {
  http_response_code(500);
  exit('Erreur: PHPMailer introuvable. Installez-le via Composer (composer require phpmailer/phpmailer) ou placez les fichiers dans forms/PHPMailer/src');
}

// ===== VALIDATION & SANITIZATION =====
function clean($v) {
  return trim(filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
}
$name    = clean($_POST['name']   ?? '');
$email   = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';
$phone   = clean($_POST['phone']  ?? '');
$subject = clean($_POST['subject']?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
  http_response_code(422);
  exit('Veuillez remplir tous les champs obligatoires.');
}

// ===== CONSTRUCTION DU MAIL =====
try {
  $mail = new PHPMailer(true);
  
  // SMTP
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = SMTP_USER;
  $mail->Password   = SMTP_PASS;
  $mail->Port       = SMTP_PORT;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->CharSet    = 'UTF-8';

  // Expéditeur
  $mail->setFrom(SMTP_FROM, 'Formulaire de Contact');
  global $RECIPIENTS;
  foreach ($RECIPIENTS as $to) {
    $mail->addAddress($to);
  }

  // Contenu du mail
  $mail->isHTML(true);
  $mail->Subject = '[Contact] ' . $subject;

  $html = '<div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">'
        . '<h2>Nouveau message depuis le formulaire de contact</h2>'
        . '<p style="font-size: 16px;"><strong>Nom :</strong> ' . htmlspecialchars($name) . '</p>'
        . '<p style="font-size: 16px;"><strong>Email :</strong> ' . htmlspecialchars($email) . '</p>';
  if ($phone) {
    $html .= '<p style="font-size: 16px;"><strong>Téléphone :</strong> ' . htmlspecialchars($phone) . '</p>';
  }
  $html .= '<p style="font-size: 16px;"><strong>Sujet :</strong> ' . htmlspecialchars($subject) . '</p>'
        . '<p style="font-size: 16px;"><strong>Message :</strong></p>'
        . '<div style="background: #f4f4f4; padding: 15px; border-left: 3px solid #007bff; white-space: pre-wrap;">'
        . htmlspecialchars($message) . '</div>'
        . '<hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">'
        . '<p style="font-size: 12px; color: #999;">Cet e-mail a été envoyé depuis votre site web. Ne répondez pas à cet e-mail, utilisez l\'adresse de l\'expéditeur ci-dessus.</p>'
        . '</div>';

  $plain = "Nouveau message du site\n\n"
         . "Nom: $name\n"
         . "Email: $email\n";
  if ($phone) {
    $plain .= "Téléphone: $phone\n";
  }
  $plain .= "Sujet: $subject\n\n"
         . "Message:\n" . $message . "\n\n"
         . "--\nCet e-mail a été envoyé depuis votre site web.";

  $mail->Body    = $html;
  $mail->AltBody = $plain;

  // Envoi
  if (!$mail->send()) {
    http_response_code(500);
    exit('Erreur d\'envoi: '.$mail->ErrorInfo);
  }

  exit('OK');

} catch (Exception $e) {
  http_response_code(500);
  exit('Erreur serveur: '.$e->getMessage());
}