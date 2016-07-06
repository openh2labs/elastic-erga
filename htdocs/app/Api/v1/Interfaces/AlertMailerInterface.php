<?php

namespace App\Api\v1\Interfaces;

use App\Alert;

/**
 * Interface AlertMailerInterface
 * @package App\Api\v1\Components
 */
interface AlertMailerInterface
{
    /**
     * Dispatches email
     * @param Alert $alert
     * @param string $description
     */
    public function sendAlertMail(Alert $alert, $description);

    /**
     * Returns error status of the last email dispatch
     * @return bool
     */
    public function hasErrors();

    /**
     * Returns any error messages
     * @return array
     */
    public function getErrors();
}