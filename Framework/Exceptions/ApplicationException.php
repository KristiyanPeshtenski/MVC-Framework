<?php

declare(strict_types = 1);

namespace WDB\Exceptions;


class ApplicationException extends \Exception
{
    /**
     * @var string
     */
    private $_redirectUrl;

    /**
     * ApplicationException constructor.
     * @param string $message
     * @param string $redirectUrl
     * @param int $code
     */
    public function __construct(string $message,string $redirectUrl = "Views/Layouts/error", int $code = 0)
    {
        $this->_redirectUrl = $redirectUrl;
        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->_redirectUrl;
    }
}