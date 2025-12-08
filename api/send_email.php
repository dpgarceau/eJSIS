<?php
/**
 * eJSIS Email Sender
 * Sends PDF reports via email using PHPMailer
 */

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EJSIS_EmailSender {

    private $smtpHost;
    private $smtpPort;
    private $smtpUser;
    private $smtpPass;
    private $fromEmail;
    private $fromName;
    private $supportEmail;

    public function __construct() {
        // Load email config
        $this->loadConfig();
    }

    private function loadConfig() {
        // Default values - override in jsis_emailconfig.php
        $this->smtpHost = 'localhost';
        $this->smtpPort = 25;
        $this->smtpUser = '';
        $this->smtpPass = '';
        $this->fromEmail = 'noreply@ejsis.isstrckr.com';
        $this->fromName = 'eJSIS System';
        $this->supportEmail = 'david.garceau@gemaire.com'; // Testing email

        // Load custom config if exists
        $configFile = __DIR__ . '/jsis_emailconfig.php';
        if (file_exists($configFile)) {
            include $configFile;
            if (defined('SMTP_HOST')) $this->smtpHost = SMTP_HOST;
            if (defined('SMTP_PORT')) $this->smtpPort = SMTP_PORT;
            if (defined('SMTP_USER')) $this->smtpUser = SMTP_USER;
            if (defined('SMTP_PASS')) $this->smtpPass = SMTP_PASS;
            if (defined('FROM_EMAIL')) $this->fromEmail = FROM_EMAIL;
            if (defined('FROM_NAME')) $this->fromName = FROM_NAME;
            if (defined('SUPPORT_EMAIL')) $this->supportEmail = SUPPORT_EMAIL;
        }
    }

    /**
     * Send JSIS report email
     *
     * @param string $techEmail Technician email
     * @param string $techName Technician name
     * @param string $pdfPath Path to PDF file
     * @param int $recordId Record ID
     * @param string $jsisType Type of JSIS (ac, heatpump, gasfurnace)
     * @param string $homeownerName Homeowner name for subject
     * @return array Success status and message
     */
    public function sendReport($techEmail, $techName, $pdfPath, $recordId, $jsisType, $homeownerName = '') {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            if (!empty($this->smtpUser)) {
                $mail->isSMTP();
                $mail->Host = $this->smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $this->smtpUser;
                $mail->Password = $this->smtpPass;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $this->smtpPort;
            } else {
                // Use PHP mail() function
                $mail->isMail();
            }

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($techEmail, $techName);
            $mail->addCC($this->supportEmail); // Support copy

            // Format JSIS type for subject
            $typeLabel = 'JSIS';
            if ($jsisType === 'heatpump') {
                $typeLabel = 'Heat Pump JSIS';
            } elseif ($jsisType === 'ac') {
                $typeLabel = 'A/C JSIS';
            } elseif ($jsisType === 'gasfurnace') {
                $typeLabel = 'Gas Furnace JSIS';
            }

            // Subject
            $subject = $typeLabel . ' Report #' . $recordId;
            if (!empty($homeownerName)) {
                $subject .= ' - ' . $homeownerName;
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $this->getEmailBody($techName, $recordId, $typeLabel, $homeownerName);
            $mail->AltBody = $this->getPlainTextBody($techName, $recordId, $typeLabel, $homeownerName);

            // Attachment
            if (file_exists($pdfPath)) {
                $mail->addAttachment($pdfPath, 'JSIS_Report_' . $recordId . '.pdf');
            }

            $mail->send();

            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];

        } catch (Exception $e) {
            error_log("eJSIS Email Error: " . $mail->ErrorInfo);
            return [
                'success' => false,
                'message' => 'Email could not be sent: ' . $mail->ErrorInfo
            ];
        }
    }

    private function getEmailBody($techName, $recordId, $typeLabel, $homeownerName) {
        $html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background-color: #b41e1e; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        .record-id { font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>eJSIS Report Submitted</h1>
    </div>
    <div class="content">
        <p>Hello ' . htmlspecialchars($techName) . ',</p>

        <p>Your <strong>' . htmlspecialchars($typeLabel) . '</strong> report has been successfully submitted and recorded.</p>

        <p class="record-id">Record ID: #' . $recordId . '</p>';

        if (!empty($homeownerName)) {
            $html .= '<p><strong>Customer:</strong> ' . htmlspecialchars($homeownerName) . '</p>';
        }

        $html .= '
        <p>The complete report is attached as a PDF for your records.</p>

        <p>Thank you for using eJSIS.</p>
    </div>
    <div class="footer">
        <p>This is an automated message from the eJSIS system.<br>
        Please do not reply to this email.</p>
    </div>
</body>
</html>';

        return $html;
    }

    private function getPlainTextBody($techName, $recordId, $typeLabel, $homeownerName) {
        $text = "eJSIS Report Submitted\n\n";
        $text .= "Hello $techName,\n\n";
        $text .= "Your $typeLabel report has been successfully submitted and recorded.\n\n";
        $text .= "Record ID: #$recordId\n";

        if (!empty($homeownerName)) {
            $text .= "Customer: $homeownerName\n";
        }

        $text .= "\nThe complete report is attached as a PDF for your records.\n\n";
        $text .= "Thank you for using eJSIS.\n\n";
        $text .= "---\nThis is an automated message from the eJSIS system.\n";

        return $text;
    }

    /**
     * Delete temporary PDF file after sending
     */
    public static function cleanupTempFile($filepath) {
        if (file_exists($filepath) && strpos($filepath, '/temp/') !== false) {
            unlink($filepath);
            return true;
        }
        return false;
    }
}
