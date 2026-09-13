<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

// ── Chargement du fichier .env ────────────────────────────────────
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    echo json_encode(['success' => false, 'error' => 'Configuration manquante.']);
    exit;
}
foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $_ENV[trim($key)] = trim($value);
}
function env(string $key, string $default = ''): string {
    return $_ENV[$key] ?? $default;
}
// ─────────────────────────────────────────────────────────────────

require 'config/PHPMailer-master/src/Exception.php';
require 'config/PHPMailer-master/src/PHPMailer.php';
require 'config/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Sujets autorisés = options du <select> FR et EN
const ALLOWED_SUBJECTS = [
    'Cours particulier', 'Formation entreprise', 'Événement', 'Demande de devis', 'Autre',
    'Private lesson', 'Corporate training', 'Event', 'Request a quote', 'Other',
];
const RATE_LIMIT_MAX    = 5;    // envois max par IP…
const RATE_LIMIT_WINDOW = 3600; // …sur cette fenêtre glissante (secondes)

// Enregistre la tentative et indique si l'IP a dépassé son quota
function rateLimitExceeded(string $ip): bool {
    $fp = fopen(__DIR__ . '/config/form_rate_limit.json', 'c+');
    if (!$fp) return false; // stockage indisponible : on ne bloque pas l'envoi
    flock($fp, LOCK_EX);
    $data = json_decode(stream_get_contents($fp), true) ?: [];
    $now  = time();
    foreach ($data as $key => $times) {
        $data[$key] = array_values(array_filter($times, fn($t) => $now - $t < RATE_LIMIT_WINDOW));
        if (!$data[$key]) unset($data[$key]);
    }
    $exceeded = count($data[$ip] ?? []) >= RATE_LIMIT_MAX;
    if (!$exceeded) $data[$ip][] = $now;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    flock($fp, LOCK_UN);
    fclose($fp);
    return $exceeded;
}

// ── Protection anti-spam (honeypot + timing) ─────────────────────
$honeypot  = $_POST['website']   ?? '';
$form_time = intval($_POST['form_time'] ?? 0);

// form_time absent = soumission hors du site → traité comme un bot
if ($honeypot !== '' || $form_time <= 0 || (time() - $form_time) < 3) {
    echo json_encode(['success' => true]); // réponse silencieuse
    exit;
}
// ─────────────────────────────────────────────────────────────────

// ── Validation des champs requis ──────────────────────────────────
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'error' => 'Champs requis manquants.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Adresse e-mail invalide.']);
    exit;
}

if (!in_array($subject, ALLOWED_SUBJECTS, true)) {
    echo json_encode(['success' => false, 'error' => 'Sujet invalide.']);
    exit;
}

if (rateLimitExceeded($_SERVER['REMOTE_ADDR'] ?? '')) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => 'Trop de messages envoyés, réessayez plus tard.']);
    exit;
}
// ─────────────────────────────────────────────────────────────────

$mail = new PHPMailer(true);

try {
    // Relai mail local OVH (pas de SMTP externe)
    $mail->isMail();
    $mail->CharSet    = 'UTF-8';

    // Expéditeur & destinataire principal
    $mail->setFrom(env('MAIL_FROM'), env('MAIL_FROM_NAME', 'Genevavelo'));
    $mail->addAddress(env('MAIL_TO'), env('MAIL_TO_NAME'));
    $mail->addReplyTo($email, htmlspecialchars($name));

    // Copie cachée (BCC)
    $bcc = env('MAIL_BCC');
    if ($bcc !== '') {
        $mail->addBCC($bcc);
    }

    // Contenu
    $prefix        = env('MAIL_SUBJECT_PREFIX', '[Genevavelo]');
    $mail->Subject = $prefix . ' ' . $subject; // texte brut, sujet déjà whitelisté

    $nameSafe    = htmlspecialchars($name);
    $emailSafe   = htmlspecialchars($email);
    $phoneSafe   = htmlspecialchars($phone);
    $subjectSafe = htmlspecialchars($subject);
    $messageSafe = nl2br(htmlspecialchars($message));
    $phoneRow    = $phone
        ? "<tr><td style='padding:6px 0;color:#6b7a99;font-size:13px;width:120px'>Téléphone</td><td style='padding:6px 0;font-size:14px;color:#001b38'>{$phoneSafe}</td></tr>"
        : '';
    $date        = (new DateTime('now', new DateTimeZone('Europe/Zurich')))->format('d/m/Y à H:i');

    $mail->isHTML(true);
    $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4f8;font-family:'Inter',Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:40px 20px">
    <tr><td align="center">
      <table width="100%" cellpadding="0" cellspacing="0" style="max-width:580px">

        <!-- En-tête -->
        <tr><td style="background:#003c80;border-radius:12px 12px 0 0;padding:32px 40px;text-align:center">
          <p style="margin:0;color:#ffbf80;font-size:12px;font-weight:600;letter-spacing:2px;text-transform:uppercase">Nouveau message</p>
          <h1 style="margin:8px 0 0;color:#ffffff;font-size:24px;font-weight:700;line-height:1.2">Genevavelo</h1>
        </td></tr>

        <!-- Corps -->
        <tr><td style="background:#ffffff;padding:36px 40px">

          <!-- Sujet -->
          <div style="background:#f0f4f8;border-left:4px solid #ff7a00;border-radius:0 8px 8px 0;padding:14px 18px;margin-bottom:28px">
            <p style="margin:0;font-size:11px;color:#6b7a99;text-transform:uppercase;letter-spacing:1px">Sujet</p>
            <p style="margin:4px 0 0;font-size:16px;font-weight:600;color:#001b38">{$subjectSafe}</p>
          </div>

          <!-- Coordonnées -->
          <p style="margin:0 0 12px;font-size:12px;font-weight:600;color:#6b7a99;text-transform:uppercase;letter-spacing:1px">Coordonnées</p>
          <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e4ebf3;border-radius:8px;padding:4px 16px;margin-bottom:28px">
            <tr>
              <td style="padding:6px 0;color:#6b7a99;font-size:13px;width:120px">Nom</td>
              <td style="padding:6px 0;font-size:14px;font-weight:600;color:#001b38">{$nameSafe}</td>
            </tr>
            <tr style="border-top:1px solid #f0f4f8">
              <td style="padding:6px 0;color:#6b7a99;font-size:13px">E-mail</td>
              <td style="padding:6px 0;font-size:14px;color:#001b38"><a href="mailto:{$emailSafe}" style="color:#004a9a;text-decoration:none">{$emailSafe}</a></td>
            </tr>
            {$phoneRow}
          </table>

          <!-- Message -->
          <p style="margin:0 0 12px;font-size:12px;font-weight:600;color:#6b7a99;text-transform:uppercase;letter-spacing:1px">Message</p>
          <div style="background:#f9fafb;border:1px solid #e4ebf3;border-radius:8px;padding:20px;margin-bottom:32px;font-size:14px;color:#001b38;line-height:1.7">
            {$messageSafe}
          </div>

          <!-- CTA répondre -->
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td align="center">
              <a href="mailto:{$emailSafe}?subject=Re: {$subjectSafe}"
                 style="display:inline-block;background:#004a9a;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 32px;border-radius:6px">
                Répondre à {$nameSafe}
              </a>
            </td></tr>
          </table>

        </td></tr>

        <!-- Pied de page -->
        <tr><td style="background:#f0f4f8;border-radius:0 0 12px 12px;padding:20px 40px;text-align:center;border-top:1px solid #e4ebf3">
          <p style="margin:0;font-size:12px;color:#6b7a99">Message reçu le {$date} via le formulaire de <a href="https://www.genevavelo.ch" style="color:#004a9a;text-decoration:none">genevavelo.ch</a></p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;

    // Fallback texte brut
    $mail->AltBody = "Nouveau message – Genevavelo\n"
                   . "==============================\n\n"
                   . "Sujet : $subject\n\n"
                   . "Nom      : $name\n"
                   . "E-mail   : $email\n"
                   . ($phone ? "Téléphone: $phone\n" : '')
                   . "\nMessage :\n$message\n\n"
                   . "-------------------------------\n"
                   . "Reçu le $date via genevavelo.ch";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Détail technique dans les logs serveur uniquement
    error_log('send-mail.php : ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'error' => "Échec de l'envoi."]);
}
