<?php

declare(strict_types = 1);

namespace WDB\HttpContext;

class HttpContext
{
    private static $_inst = null;

    /**
     * @var HttpRequest
     */
    private $_request;

    /**
     * @var HttpCookies
     */
    private $_cookie;

    /**
     * @var HttpSession
     */
    private $_session;

    private $_user;

    private function __construct(HttpRequest $request, HttpCookies $cookie, HttpSession $session, HttpUser $user)
    {
        $this->_request = $request;
        $this->_cookie = $cookie;
        $this->_session = $session;
        $this->_user = $user;
    }

    /**
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return HttpCookies
     */
    public function getCookie()
    {
        return $this->_cookie;
    }

    /**
     * @return HttpSession
     */
    public function getSession()
    {
        return $this->_session;
    }

    public function getIdentity()
        :HttpUser
    {
        return $this->_user;
    }

    /**
     * @return HttpContext
     * @throws \Exception
     */
    public static function getInstance() : HttpContext
    {
        if(self::$_inst == null)
        {
            throw new \Exception('HttpContext not set', 500);
        }

        return self::$_inst;
    }

    /**
     * @param HttpRequest $request
     * @param HttpCookies $cookie
     * @param HttpSession $session
     * @throws \Exception
     */
    public static function setInstance(HttpRequest $request, HttpCookies $cookie, HttpSession $session, HttpUser $user)
    {
        if(self::$_inst !== null)
        {
            throw new \Exception('There already has HttpContext instance', 500);
        }

        self::$_inst = new HttpContext($request, $cookie, $session, $user);
    }
}