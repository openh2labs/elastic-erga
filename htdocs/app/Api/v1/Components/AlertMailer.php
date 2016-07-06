<?php

namespace App\Api\v1\Components;

use App\Alert;
use App\Api\v1\Interfaces\AlertMailerInterface;
use Illuminate\Mail\Mailer;

/**
 * Class AlertMailer
 * @package App\Api\v1\Components
 */
class AlertMailer implements AlertMailerInterface
{
    /**
     * Keep track of mail errors
     * @var array
     */
    protected $errors = [];

    /**
     * Mailer instance used to dispatch emails
     * @var \Illuminate\Mail\Mailer
     */
    protected $mailer;

    /**
     * @param Alert $alert
     * @param string $description
     * @return bool True on success, false on failure
     */
    public function sendAlertMail(Alert $alert, $description)
    {
        if (empty($description)) {
            throw new \InvalidArgumentException('Description can not be empty');
        }

        // template
        $emailTemplate = 'email_alert';

        /**
         * @param array $to destination email(s)
         */
        $to = $this->emailStringToArray($alert->alert_email_recipient);
        /**
         * @param string $from from email
         */
        $from = $alert->alert_email_sender;
        /**
         * @param string $subject email description
         */
        $subject = 'elastic-erga alert:' . $alert->description;

        /**
         * @param \Illuminate\Mail\Message $message
         */
        $closure = function ($message) use ($to, $from, $subject) {
            $message->from($from)->bcc($to)->subject($subject);
        };

        /**
         * @param \Illuminate\Mail\Mailer
         */
        $mailer = $this->getMailer();

        /**
         * @param \Illuminate\Mail\Message $ret
         */
        $mailer->send($emailTemplate, [
            'recipient' => $to,
            'description' => $description, // email template variable
        ], $closure);

        /**
         * @param array $failure List of failed email addresses, when sending in sync mode
         */
        $failures = $mailer->failures();

        // return value
        $ret = empty($failures);
        if (! $ret) {
            $this->addError('Failed to deliver email', $failures);
        }
        return $ret;
    }

    /**
     * Converts comma separated email list into an array of emails
     * Trims whitespace from beginning/end of each email
     * @param string $emailString comma separated email list, or single email
     * @return array
     */
    public function emailStringToArray($emailString)
    {
        return array_map(function ($email) {
            // strip whitespace
            return trim($email);
            // convert comma separated list to array
        }, explode(',', $emailString));
    }

    /**
     * Getter
     * Lazy loads mailer instance
     * @return Mailer
     */
    public function getMailer()
    {
        if (! $this->mailer instanceof Mailer) {
            $this->mailer = $this->getDefaultMailer();
        }
        return $this->mailer;
    }

    /**
     * Setter
     * @param Mailer $mailer
     */
    public function setMailer(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Returns instance of laravel mailer
     * @return \Illuminate\Mail\Mailer
     */
    public function getDefaultMailer()
    {
        return app()['mailer'];
    }

    /**
     * Returns error status of the last email dispatch
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Returns any error messages
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Tracks internal error
     * @param string $message
     * @param array $details
     */
    protected function addError(string $message, array $details)
    {
        $this->errors[] = [
            'message' => $message,
            'details' => $details,
        ];
    }

    /**
     * Checks if email string contains multiple email addresses
     * @param string $emailString comma separated email list, or single email
     * @return bool
     */
    public function hasMultipleRecipients($emailString)
    {
        return (mb_strpos($emailString, ',') !== false);
    }

}
