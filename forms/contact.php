<?php
// forms/contact.php
// Handler pour le formulaire .php-email-form (AJAX). Répond "OK" en cas de succès.
// Utilise PHPMailer via Composer (vendor/autoload.php) OU via les fichiers src inclus localement.

// ===== CONFIG SMTP (fourni par le client) =====

use PHPMailer\PHPMailer\PHPMailer;

const SMTP_HOST = 'smtp-smileup-platform.alwaysdata.net';
const SMTP_PORT = 587;
const SMTP_USER = 'smileup-platform@alwaysdata.net';
const SMTP_PASS = 'WODanielH2006';
const SMTP_FROM = 'smileup-platform@alwaysdata.net';
const SMTP_FROM_NAME = 'Formulaire Daniel Whannou';
$RECIPIENTS = [
  'danielw@smileupplatform.com',
  'dwhannou229@gmail.com'
];

// Fichier de log pour le débogage
const LOG_FILE = __DIR__ . '/logs.txt';

// Fonction de journalisation
function log_message($message) {
    file_put_contents(LOG_FILE, date('Y-m-d H:i:s') . ' - ' . $message . "\n", FILE_APPEND);
}

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
  log_message('Honeypot détecté. Arrêt du traitement.');
  exit('OK');
}

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
  log_message('Erreur: PHPMailer introuvable.');
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
  log_message('Erreur de validation: champs obligatoires manquants.');
  exit('Veuillez remplir tous les champs obligatoires.');
}

// ===== CONSTRUCTION DU MAIL =====
try {
  $mail = new PHPMailer(true);
  
  // Debug PHPMailer
  $mail->SMTPDebug = 2; // Active un mode de débogage détaillé
  $mail->Debugoutput = function($str, $level) {
      log_message('PHPMailer DEBUG: ' . $str);
  };
  
  // SMTP
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->SMTPAuth   = true;
  $mail->Username   = SMTP_USER;
  $mail->Password   = SMTP_PASS;
  $mail->Port       = SMTP_PORT;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->CharSet    = 'UTF-8';

  $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
  // $mail->addReplyTo($email, $name);
  global $RECIPIENTS;
  foreach ($RECIPIENTS as $to) {
    $mail->addAddress($to);
  }

  // Contenu
  $mail->isHTML(true);
  $mail->Subject = '[Contact] ' . $subject;
  $html = '<h2>Nouveau message du site</h2>'
        . '<p><strong>Nom:</strong> '.htmlspecialchars($name).'</p>'
        . '<p><strong>Email:</strong> '.htmlspecialchars($email).'</p>'
        . ($phone ? '<p><strong>Téléphone:</strong> '.htmlspecialchars($phone).'</p>' : '')
        . '<p><strong>Message:</strong><br>' . nl2br(htmlspecialchars($message)) . '</p>'
        . '<hr><p style="font-size:12px;color:#666">Envoyé depuis le formulaire de danielwhannou • IP: '.($_SERVER['REMOTE_ADDR'] ?? 'N/A').'</p>';
  $plain = "Nouveau message du site\n\n"
         . "Nom: $name\n"
         . "Email: $email\n"
         . ($phone ? "Téléphone: $phone\n" : '')
         . "Sujet: $subject\n\n"
         . "Message:\n$message\n\n"
         . "--\nEnvoyé depuis le formulaire de danielwhannou";
  $mail->Body    = $html;
  $mail->AltBody = $plain;

  // Envoi
  if (!$mail->send()) {
    http_response_code(500);
    log_message('Échec de l\'envoi du mail : ' . $mail->ErrorInfo);
    exit('Erreur d\'envoi: '.$mail->ErrorInfo);
  }

  log_message('Mail envoyé avec succès.');
  exit('OK');

} catch (Exception $e) {
  http_response_code(500);
  log_message('Erreur serveur: ' . $e->getMessage());
  exit('Erreur serveur: '.$e->getMessage());
}