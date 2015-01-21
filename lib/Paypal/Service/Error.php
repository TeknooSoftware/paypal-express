<?php

namespace UniAlteri\Paypal\Express\Service;

class Error implements ErrorInterface
{
    /**
     * Code of the error
     * @var string
     */
    protected $code;

    /**
     * Short message of the error
     * @var string
     */
    protected $shortMessage;

    /**
     * Long message of the error
     * @var string
     */
    protected $longMessage;

    /**
     * Severity of the error
     * @var string
     */
    protected $severity;

    /**
     * Initialize object error
     * @param string $code
     * @param string $shortMessage
     * @param string $longMessage
     * @param string $severity
     */
    public function __construct($code, $shortMessage, $longMessage, $severity)
    {
        $this->code = $code;
        $this->shortMessage = $shortMessage;
        $this->longMessage = $longMessage;
        $this->severity = $severity;
    }

    /**
     * Get the code of the error
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Return the short message of the error
     * @return string
     */
    public function getShortMessage()
    {
        return $this->shortMessage;
    }

    /**
     * Return the long message of the error
     * @return string
     */
    public function getLongMessage()
    {
        return $this->longMessage;
    }

    /**
     * Return the severity of the error
     * @return string
     */
    public function getSeverity()
    {
        return $this->severity;
    }
}