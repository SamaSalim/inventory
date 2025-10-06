<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public $fromEmail;
    public $fromName;
    public $recipients;

    /**
     * The "user agent"
     */
    public $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public $protocol;

    /**
     * The server path to Sendmail.
     */
    public $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public $SMTPHost;

    /**
     * SMTP Username
     */
    public $SMTPUser;

    /**
     * SMTP Password
     */
    public $SMTPPass;

    /**
     * SMTP Port
     */
    public $SMTPPort;

    /**
     * SMTP Timeout (in seconds)
     */
    public $SMTPTimeout;

    /**
     * Enable persistent SMTP connections
     */
    public $SMTPKeepAlive;

    /**
     * SMTP Encryption. '', 'tls' or 'ssl'
     */
    public $SMTPCrypto;

    /**
     * Enable word-wrap
     */
    public $wordWrap;

    /**
     * Character count to wrap at
     */
    public $wrapChars;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public $mailType;

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public $charset;

    /**
     * Whether to validate the email address
     */
    public $validate;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public $priority;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public $CRLF;

    /**
     * Newline character. (Use “\r\n” to comply with RFC 822)
     */
    public $newline;

    /**
     * Enable BCC Batch Mode.
     */
    public $BCCBatchMode;

    /**
     * Number of emails in each BCC batch
     */
    public $BCCBatchSize;

    /**
     * Enable notify message from server
     */
    public $DSN;

    public function __construct()
    {
        parent::__construct();

        // Read from .env or fallback defaults
        $this->fromEmail    = getenv('email.fromEmail') ?: 'kamctester@gmail.com';
        $this->fromName     = getenv('email.fromName') ?: 'KAMC Inventory System';
        $this->recipients   = '';

        $this->protocol     = getenv('email.protocol') ?: 'smtp';
        $this->SMTPHost     = getenv('email.SMTPHost') ?: 'smtp.gmail.com';
        $this->SMTPUser     = getenv('email.SMTPUser') ?: 'kamctester@gmail.com';
        $this->SMTPPass     = getenv('email.SMTPPass') ?: 'yourAppPasswordHere';
        $this->SMTPPort     = (int) (getenv('email.SMTPPort') ?: 587);
        $this->SMTPTimeout  = (int) (getenv('email.SMTPTimeout') ?: 10);
        $this->SMTPKeepAlive= (bool)(getenv('email.SMTPKeepAlive') ?: false);
        $this->SMTPCrypto   = getenv('email.SMTPCrypto') ?: 'tls';
        $this->wordWrap     = (bool)(getenv('email.wordWrap') ?: true);
        $this->wrapChars    = (int)(getenv('email.wrapChars') ?: 76);
        $this->mailType     = getenv('email.mailType') ?: 'html';
        $this->charset      = getenv('email.charset') ?: 'UTF-8';
        $this->validate     = (bool)(getenv('email.validate') ?: true);
        $this->priority     = (int)(getenv('email.priority') ?: 3);
        $this->CRLF         = "\r\n";
        $this->newline      = "\r\n";
        $this->BCCBatchMode = false;
        $this->BCCBatchSize = 200;
        $this->DSN          = false;
    }
}
