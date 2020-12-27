<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libs/vendor/autoload.php';

class EmailHelper
{
  private $login;
  private $senha;
  private $nome;
  private $smtpHost;
  private $smtpPort;
  private $smtpTlsMode;
  private $mailer;
  private $debugMode = false;

  /**
   * EmailHelper constructor.
   * @param string $login Login ou endereco de email do remetente.
   * @param string $senha Senha do email remetente.
   * @param string $nome Nome de apresentacao do remetente.
   * @param string $smtpHost Endereco/hostname do servidor SMTP.
   * @param int $smtpPort Porta do servidor SMTP.
   * @param bool $smtpTlsMode Usar TSL (true) ou SSL (false).
   */
  public function __construct($login, $senha, $nome, $smtpHost, $smtpPort = 587, $smtpTlsMode = true)
  {
    $this->login = $login;
    $this->senha = $senha;
    $this->nome = $nome;
    $this->smtpHost = $smtpHost;
    $this->smtpPort = $smtpPort;
    $this->smtpTlsMode = $smtpTlsMode;
    $this->mailer = new PHPMailer(true);
  }

  /**
   * Adiciona um destinatario a mensagem.
   * @param string $email Endereco de email.
   * @param string $nome Nome do destinatario.
   * @return bool Indica o sucesso.
   */
  public function addDestinatario($email, $nome = '')
  {
    try {
      $this->mailer->addAddress($email, $nome);
      return true;
    } catch (Exception $e) {
      if ($this->debugMode) HttpHelper::erroJson(500, "Não foi possível adicionar um destinatario", 0, [$this->mailer->ErrorInfo, $e->getMessage()]);
      else return false;
    }
  }

  /**
   * Adiciona um destinatario CC.
   * @param string $email Endereco de email do destinatario.
   * @param string $nome Nome do destinatario.
   * @return bool Indica o sucesso.
   */
  public function addCC($email, $nome = '') {
    try {
      $this->mailer->addCC($email, $nome);
      return true;
    } catch (Exception $e) {
      if ($this->debugMode) HttpHelper::erroJson(500, "Não foi possível adicionar um CC", 0, [$this->mailer->ErrorInfo, $e->getMessage()]);
      else return false;
    }
  }

  /**
   * Adiciona um destinatario oculto (CCO).
   * @param string $email Endereco de email do destinatario.
   * @param string $nome Nome do destinatario.
   * @return bool Indica o sucesso.
   */
  public function addCCO($email, $nome = '')
  {
    try {
      $this->mailer->addBCC($email, $nome);
      return true;
    } catch (Exception $e) {
      if ($this->debugMode) HttpHelper::erroJson(500, "Não foi possível adicionar um CCO", 0, [$this->mailer->ErrorInfo, $e->getMessage()]);
      else return false;
    }
  }

  /**
   * Adiciona um anexo.
   * @param string $path Caminho relativo ou absolito ate o arquivo.
   * @param string $nome Nome apresentavel do arquivo para os leitores do email.
   * @return bool Indica o sucesso.
   */
  public function addAnexo($path, $nome = '')
  {
    try {
      $this->mailer->addAttachment($path, $nome);
      return true;
    } catch (Exception $e) {
      if ($this->debugMode) HttpHelper::erroJson(500, "Não foi possível adicionar um anexo", 0, [$this->mailer->ErrorInfo, $e->getMessage()]);
      else return false;
    }
  }

  /**
   * Define um endereco para receber mensagens caso os leitores cliquem em Responder a mensagem.
   * @param string $email Endereco de email.
   * @param string $nome Nome do destinatario.
   * @return bool Indica o sucesso.
   */
  public function addResponderPara($email, $nome) {
    try {
      $this->mailer->addReplyTo($email, $nome);
      return true;
    } catch (Exception $e) {
      if ($this->debugMode) HttpHelper::erroJson(500, "Não foi possível adicionar um respondedor", 0, [$this->mailer->ErrorInfo, $e->getMessage()]);
      else return false;
    }
  }

  /**
   * Realiza o envio da mensagem.
   * @param string $assunto Assunto da mensagem.
   * @param string $mensagem Texto da mensagem.
   * @param bool $html Ajusta um envio de texto HTML ou texto puro.
   * @return bool Indica o sucesso.
   */
  public function enviarMensagem($assunto, $mensagem, $html = false) {
    $mail = $this->mailer;
    try {
      $mail->SMTPDebug = $this->debugMode ? SMTP::DEBUG_CONNECTION : SMTP::DEBUG_OFF;
      $mail->isSMTP();
      $mail->Host = $this->smtpHost;
      $mail->SMTPAuth = true;
      $mail->Username = $this->login;
      $mail->Password = $this->senha;
      $mail->SMTPSecure = $this->smtpTlsMode ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port = $this->smtpPort;

      //Recipients
      $mail->setFrom($this->login, $this->nome);

      // Content
      $mail->isHTML($html);
      $mail->Subject = $assunto;
      $mail->Body = $mensagem;
      $mail->CharSet = PHPMailer::CHARSET_UTF8;

      $mail->send();
      return true;
    } catch (Exception $e) {
      if ($this->debugMode) HttpHelper::erroJson(500, "Não foi possível enviar email", 0, [$mail->ErrorInfo, $e->getMessage()]);
      else return false;
    }
  }

  /**
   * Define se usara o modo DEBUG
   * @param bool $debugMode
   */
  public function setDebugMode($debugMode)
  {
    $this->debugMode = $debugMode;
  }

}