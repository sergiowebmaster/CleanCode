<?php
session_start();

require_once 'CleanCodeClass.php';
require_once 'CleanCodeURI.php';
require_once 'CleanCodeModel.php';

class CleanCodeController extends CleanCodeClass
{
    protected static $servers = array();
    protected static $uri;
    protected static $view;
    
    protected $user;
	
	/*
	 * Get the value of a GET variable.
	 * @access protected
	 * @param String $var The name of the GET variable.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function get($var = '', $default = '')
	{
		return $var? self::searchPos($_GET, $var, $default) : $_GET;
	}

	/*
	 * Get the value of a POST variable.
	 * @access protected
	 * @param String $var The name of the POST variable.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function post($var = '', $default = '')
	{
		return $var? self::searchPos($_POST, $var, $default) : $_POST;
	}

	/*
	 * Get the value of a FILES variable.
	 * @access protected
	 * @param String $var The name of the FILES variable.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function files($var = '', $default = array())
	{
		return $var? self::searchPos($_FILES, $var, $default) : $_FILES;
	}

	/*
	 * Get the value of a session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @param String $default The default value, if the variable is not found.
	 * @return String
	 */
	protected static function getSession($name, $default = '')
	{
		return self::searchPos($_SESSION, $name, $default);
	}

	/*
	 * Set the value of a session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @param String $value The value of the session.
	 * @return void
	 */
	protected function setSession($name, $value)
	{
		$_SESSION[$name] = $value;
	}

	/*
	 * Destroy a session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @return void
	 */
	protected function unsetSession($name)
	{
		unset($_SESSION[$name]);
	}
	
	/*
	 * Get the value of a cookie.
	 * @access protected
	 * @param String $name The name of the cookie.
	 * @param String $default The default value, if the cookie is not found.
	 * @return void
	 */
	protected static function getCookie($name, $default = '')
	{
		return self::searchPos($_COOKIE, $name, $default);
	}

	/*
	 * Get the value of a cookie.
	 * @access protected
	 * @param String $name The name of the cookie.
	 * @param String $value The value of cookie.
	 * @return void
	 */
	protected function setCookie($name, $value, $duration)
	{
	    setcookie($name, $value, time() + $duration);
	}
	
	protected function setDefaultCookie($name, $value)
	{
	    $this->setCookie($name, $value, (3600 * 24 * 7));
	}
	
	/*
	 * Get the requested URI in the server.
	 * @access protected
	 * @return String
	 */
	protected static function getRequestUri()
	{
		return $_SERVER['REQUEST_URI'];
	}
	
	/*
	 * Get the hostname of the server.
	 * @access protected
	 * @return String
	 */
	protected static function getHostname()
	{
		return $_SERVER['HTTP_HOST'];
	}

	/*
	 * Verify if the host is HTTPS.
	 * @access protected
	 * @return Boolean
	 */
	protected function isHTTPS()
	{
		return $_SERVER['HTTPS'] == 'on';
	}
	
	/*
	 * Return the IP address.
	 * @access protected
	 * @return String
	 */
	protected function getIP()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	
	/*
	 * Return the port of remote address.
	 * @access protected
	 * @return String
	 */
	protected function getPort()
	{
	    return $_SERVER['REMOTE_PORT'];
	}

	/*
	 * Return the servername.
	 * @access protected
	 * @return String
	 */
	protected function getServerName()
	{
		return $_SERVER['SERVER_NAME'];
	}

	/*
	 * Return the user agent of the client.
	 * @access protected
	 * @return String
	 */
	protected function getUserAgent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

	/*
	 * Allow the origin for external websites.
	 * @access protected
	 * @param String $urlOrigin	The url of the external website.
	 * @return void
	 */
	protected function allowOrigin($urlOrigin)
	{
		header('access-control-allow-origin: ' . $urlOrigin);
	}
	
	/*
	 * Redirect for a new path.
	 * @access protected
	 * @param String $path The address for to redirect.
	 * @return void
	 */
	protected function redirect($path)
	{
		header('Location:' . $path);
	}

	/*
	 * Back to the previows directory.
	 * @access protected
	 * @return void
	 */
	protected function back()
	{
		$this->redirect('./');
	}

	/*
	 * Reload the page.
	 * @access protected
	 * @return void
	 */
	protected function refresh()
	{
		$this->redirect(self::getRequestUri());
	}
	
	public function readURI()
	{
	    self::$uri = new CleanCodeURI(self::get('uri'));
	}
	
	protected function getNextSlug($default = '')
	{
	    return self::$uri->nextSlug($default);
	}
	
	protected function getServerData($var)
	{
	    $data = self::searchPos(static::$servers, $this->getHostname());
	    $args = func_get_args();
	    
	    while (is_array($data) && $var = current($args))
	    {
	        $data = self::searchPos($data, $var);
	        next($args);
	    }
	    
	    return $data;
	}
	
	/*
	 * Get the post value of the action input of the form.
	 * @access protected
	 * @return void
	 */
	protected function getAction()
	{
	    return $this->post('action');
	}
	
	protected function getFormData()
	{
	    return array_merge(self::post(), self::files());
	}
	
	protected function createHtmlView($filename)
	{
	    self::$view = new CleanCodeHtmlView($filename);
	}
	
	protected function setViewRobots($index, $follow)
	{
	    self::$view->data['robots'] = ($index? 'index' : 'noindex') . ',' . ($follow? 'follow' : 'nofollow');
	}
	
	protected function setViewData($var, $value)
	{
	    self::$view->data[$var] = $value;
	}
	
	protected function setViewTitle($title)
	{
	    $this->setViewData('title', $title);
	}
	
	protected function setViewDescription($description)
	{
	    $this->setViewData('description', $description);
	}
	
	protected function setViewKeywords($keywords)
	{
	    $this->setViewData('keywords', $keywords);
	}
	
	protected function showView()
	{
	    self::$view->show();
	}
	
	protected function showHtmlView($filename, $data)
	{
	    self::$view = new CleanCodeHtmlView($filename);
	    self::$view->data = $data;
	    self::$view->show();
	}
	
	protected function showJsonView($data)
	{
	    self::$view = new CleanCodeJsonView();
	    self::$view->data = $data;
	    self::$view->show();
	}
	
	protected function showXmlView($data)
	{
	    self::$view = new CleanCodeXmlView();
	    self::$view->data = $data;
	    self::$view->show();
	}
	
	protected function setLayout($filename)
	{
	    $this->createHtmlView($filename);
	}
	
	protected function show404Error()
	{
	    echo 'This page is not found!';
	}
	
	protected function selectPublicRoute($uri)
	{
	    $this->show404Error();
	}
	
	protected function selectRestrictRoute($uri)
	{
	    $this->show404Error();
	}
	
	protected function selectAdminRoute($uri)
	{
	    $this->show404Error();
	}
	
	protected function defineUser()
	{
	    $this->user = new CleanCodeUser();
	}
	
	protected function getUserSession($defaultValue = '')
	{
	    return $this->user? $this->getSession($this->user->sessionName, $defaultValue) : $defaultValue;
	}
	
	protected function setUserSession($value, $errorMessage)
	{
	    if ($this->user && $value)
	    {
	        $this->setSession($this->user->sessionName, $value);
	        $this->refresh();
	    }
	    else if ($errorMessage)
	    {
	        $this->user->setError($errorMessage);
	    }
	}
	
	protected function doLogout()
	{
	    $this->unsetSession($this->user->sessionName);
	    $this->back();
	}
	
	protected function defineAdminUser()
	{
	    echo 'No administrator user object defined!';
	}
	
	protected function checkUser()
	{
	    return $this->user && $this->user->isLoaded();
	}
	
	protected function checkAdminUser()
	{
	    return false;
	}
	
	protected function redirectToAdmin()
	{
	    $this->redirect('admin');
	}
	
	private function defineRestrictRoute($uri)
	{
	    $this->defineUser();
	    $this->checkUser()? $this->selectRestrictRoute($uri) : $this->selectPublicRoute($uri);
	}
	
	private function defineAdminRoute($uri)
	{
	    $this->defineAdminUser();
	    $this->checkAdminUser()? $this->selectAdminRoute($uri) : $this->redirectToAdmin();
	}
	
	public function toRestrictAccess()
	{
	    $this->defineRestrictRoute($this->getNextSlug());
	}
	
	public function toRoute()
	{
	    $this->selectPublicRoute($this->getNextSlug());
	}
	
	public function toAdminister()
	{
	    $this->defineAdminRoute($this->getNextSlug());
	}
}
?>