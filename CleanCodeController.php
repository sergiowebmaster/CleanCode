<?php
session_start();

require_once 'CleanCodeClass.php';
require_once 'CleanCodeUser.php';
require_once 'CleanCodeDaoUri.php';
require_once 'CleanCodeView.php';

class CleanCodeController extends CleanCodeClass
{
	/*
	 * Array with the parts of requested URI.
	 * @var array
	 */
	private static $uri = array();
	
	/*
	 * Instance of CleanCodeLanguage.
	 * @var object
	 */
	protected static $language;

	/*
	 * Instance of CleanCodeUser.
	 * @var object
	 */
	protected static $globalUser;

	/*
	 * Instance of CleanCodeUser.
	 * @var object
	 */
	protected $user;

	/*
	 * Instance of CleanCodeView.
	 * @var object
	 */
	protected static $view;

	/*
	 * The name of the user session.
	 * @var String
	 */
	protected $userSession = 'user';

	/*
	 * The central model of the Controller. Instance of CleanCodeDAO.
	 * @var object
	 */
	protected $model;
	
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
	protected static function files($var = '', $default = '')
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
	 * Add value in a array session.
	 * @access protected
	 * @param String $name The name of the session.
	 * @param String $value The value of the session.
	 * @return void
	 */
	protected function addSessionValue($name, $value)
	{
		if($this->getSession($name) == '')
		{
			$this->setSession($name, array($value));
		}
		else if(!in_array($value, $_SESSION[$name]))
		{
			$_SESSION[$name][] = $value;
		}
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
	 * Check a session and clear the URI, if the session is empty or not found.
	 * @access protected
	 * @param String $sessionName The name of the session.
	 * @return void
	 */
	protected function checkSession($sessionName)
	{
		$this->check($this->getSession($sessionName));
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
	protected function setPersistentCookie($name, $value)
	{
		setcookie($name, $value, time() + (3600 * 24 * 7));
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
	 * Return the IP address.
	 * @access protected
	 * @return String
	 */
	protected function getIP()
	{
		return $_SERVER['REMOTE_ADDR'];
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
	 * Read the URI requested by the user.
	 * @access protected
	 * @return void
	 */
	public function readUri()
	{
		define('URI', preg_replace('/(\/$)|(^[^a-z0-9-_\/]{1,}$)/', '', self::get('uri')));
		self::$uri = explode('/', URI);
	}
	
	/*
	 * Clear the URI info.
	 * @access protected
	 * @return void
	 */
	protected function clearUri()
	{
		self::$uri = array();
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

	/*
	 * Shift the next part of the URI.
	 * @access protected
	 * @return String
	 */
	protected function getNextSlug($default = '')
	{
		$uri = self::$uri? current(self::$uri) : $default;
		next(self::$uri);
		return $uri;
	}

	/*
	 * Return the previous route.
	 * @access protected
	 * @return String
	 */
	protected function getBackURI()
	{
		$uri = self::$uri;
		array_pop($uri);
		return join('/', $uri);
	}

	/*
	 * Load the configuration for a localhost.
	 * @access protected
	 * @return void
	 */
	protected function loadLocalhostConfig()
	{
		// Implement in the Front Controller.
	}

	/*
	 * Load the configuration for a external host.
	 * @access protected
	 * @return void
	 */
	protected function loadOnlineConfig()
	{
		// Implement in the Front Controller.
	}

	/*
	 * Select the configuration for the current host.
	 * @access protected
	 * @return void
	 */
	protected function selectHostConfig()
	{
		switch (self::getHostname())
		{
			case 'localhost':
				$this->loadLocalhostConfig();
				break;
				
			default:
				$this->loadOnlineConfig();
		}
	}

	/*
	 * Create a new instance of Language Class and set the language folder.
	 * @access protected
	 * @param String $lang Specifies the language folder of the translate.
	 * @return void
	 */
	public function setLanguage($lang)
	{
		self::$language = new CleanCodeLanguage($lang);
		CleanCodeView::setLang($lang);
	}
	
	/*
	 * Set the language by a cookie value.
	 * @access public
	 * @param String $defaultLanguage The default language, if not have cookie.
	 * @return void
	 */
	public function setLanguageByCookie($defaultLanguage)
	{
		$this->setLanguage($this->getCookie('lang', $defaultLanguage));
	}

	/*
	 * Set the language of the application, by an URI, and route.
	 * @access	protected
	 * @param	String	$uri		The URI of the language.
	 * @param	String	$persist	If true, save the value in a cookie.
	 * @return String
	 */
	protected function translate($uri, $persist = false)
	{
		if($persist) $this->setPersistentCookie('lang', $uri);
		$this->setLanguage($uri);
		$this->route();
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

	/*
	 * Set the model user for restrict areas.
	 * @access protected
	 * @return void
	 */
	protected function setDefaultUser()
	{
		$this->user = new CleanCodeUser();
	}

	/*
	 * Set the user and filter, by session, for restrict routes.
	 * @access protected
	 * @return void
	 */
	protected function identifyUser($sessionValue)
	{
		$this->user->setID($sessionValue);
	}
	
	/*
	 * Set the login data of the user.
	 * @access protected
	 * @return void
	 */
	protected function prepareLogin()
	{
		$this->user->setEmail($this->post('email'));
		$this->user->setPassword($this->post('password'));
	}

	/*
	 * Get the user session and reload the page.
	 * @access	protected
	 * @param	String		$defaultValue	A default value, if the session is not founded.
	 * @return	void
	 */
	protected function getUserSession($defaultValue = '')
	{
		return $this->getSession($this->userSession, $defaultValue);
	}

	/*
	 * Set the user session and reload the page.
	 * @access	protected
	 * @param	String		$sessionValue	The value for set the session of the user.
	 * @return	void
	 */
	protected function setUserSession($sessionValue)
	{
		$this->setSession($this->userSession, $sessionValue);
		$this->refresh();
	}

	/*
	 * Check if have user instance and if is found.
	 * @access private
	 * @return void
	 */
	private function checkUser()
	{
		return $this->user && $this->user->loadFromDB();
	}

	/*
	 * Set the user session and refresh the page.
	 * @access protected
	 * @return void
	 */
	protected function authUser()
	{
		$this->setUserSession($this->user->getID());
	}
	
	/*
	 * Send the login data of the user and set a session, if this user is founded.
	 * @access private
	 * @return void
	 */
	private function doLogin()
	{
		if($this->getAction() && $this->user->auth())
		{
			$this->authUser();
		}
	}

	/*
	 * Set the messages in the view.
	 * @access protected
	 * @param	String	$error		The error message.
	 * @param	String	$success	The success message.
	 * @return void
	 */
	protected function setMessages($error, $success)
	{
		self::$view->data['error'] = $error;
		self::$view->data['success'] = $success;
	}

	/*
	 * Set the view messages by the model.
	 * @access	protected
	 * @return	void
	 */
	protected function setMessagesByModel()
	{
		$this->setMessages($this->model->getError(), $this->model->getSuccess());
	}

	/*
	 * Set the view messages by the user.
	 * @access	protected
	 * @return	void
	 */
	protected function setMessagesByUser()
	{
		$this->setMessages($this->user->getError(), $this->user->getSuccess());
	}
	
	/*
	 * Call the login function.
	 * @access	protected
	 * @return	void
	 */
	protected function auth()
	{
		if($this->getAction())
		{
			$this->prepareLogin();
			$this->doLogin();
		}
	}

	/*
	 * Destroy the session and redirect for a login page.
	 * @access protected
	 * @param String $path The path for redirect, after destroy the session.
	 * @return void
	 */
	protected function doLogout($path = './')
	{
		$this->unsetSession($this->userSession);
		$this->redirect($path);
	}

	/*
	 * Set the model instance.
	 * @access protected
	 * @return void
	 */
	protected function setDefaultModel()
	{
		$this->model = new CleanCodeDaoUri();
	}

	/*
	 * Return a clone of the model instance.
	 * @access protected
	 * @return void
	 */
	protected function cloneModel()
	{
		return clone $this->model;
	}

	/*
	 * Set the current view instance.
	 * @access protected
	 * @return void
	 */
	protected function setView()
	{
		self::$view = new CleanCodeView('layout.phtml');
	}

	/*
	 * Configure the view for to show the index page.
	 * @access protected
	 * @return void
	 */
	protected function showIndexPage()
	{
		self::$view->setTitle('Welcome');
		self::$view->setDescription('This is the index page.');
	}

	/*
	 * Configure the view for to show a dynamic page.
	 * @access protected
	 * @return void
	 */
	protected function showDynamicPage()
	{
		self::$view->setTitle('Dynamic Page');
		self::$view->setDescription('This is a dynamic page.');
	}

	/*
	 * Configure the view for to show the login page, for a restrict area.
	 * @access protected
	 * @return void
	 */
	protected function showLoginPage()
	{
		self::$view->setTitle('Login');
		self::$view->setDescription('Do login for access the restrict area.');
	}

	/*
	 * Configure the view for to show the internal page, for a restrict area.
	 * @access protected
	 * @return void
	 */
	protected function showRestrictPage()
	{
		self::$view->setTitle('Restrict Page');
		self::$view->setDescription('This is a restrict page.');
	}

	/*
	 * Configure the view for to show the admin index page.
	 * @access protected
	 * @return void
	 */
	protected function showAdminPage()
	{
		self::$view->setTitle('Admin Page');
		self::$view->setDescription('This is a admin page.');
	}

	/*
	 * Configure the view for to show the admin edit page.
	 * @access protected
	 * @return void
	 */
	protected function showEditPage($id)
	{
		self::$view->setTitle('Edit Page');
		self::$view->setDescription('Edit your model.');
	}

	/*
	 * Configure the view for to show the 404 page.
	 * @access protected
	 * @return void
	 */
	protected function show404Error()
	{
		self::$view->setRobots(false, false);
		self::$view->setTitle('404 Error');
		self::$view->setDescription('This page is not found.');
	}

	/*
	 * Render the view.
	 * @access protected
	 * @return void
	 */
	private function showView()
	{
		self::$view->show();
	}
	
	/*
	 * Search the dao model by your primary key. If not found, show the 404 error.
	 * @access protected
	 * @param String $pk The identifier value.
	 * @return void
	 */
	protected function searchPK($pk)
	{
		$this->model && $this->model->loadByPK($pk)? $this->showAdminPage() : $this->show404Error();
	}

	/*
	 * Search the dao model by your URI slug. If not found, show the 404 error.
	 * @access protected
	 * @param String $uri The URI value in the database table.
	 * @return void
	 */
	public function searchUri($uri)
	{
		$this->model && $this->model->loadByURI($uri)? $this->showDynamicPage() : $this->show404Error();
	}
	
	/*
	 * Select the admin page by the current URI slug.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectAdminRoute($uri)
	{
		switch ($uri)
		{
			case '':
				$this->showAdminPage();
				break;
				
			default:
				$this->showEditForm($uri);
		}
	}

	/*
	 * Select the public page by the current URI slug.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectPublicRoute($uri)
	{
		switch ($uri)
		{
			case '':
				$this->showIndexPage();
				break;
				
			default:
				$this->searchUri($uri);
		}
	}

	/*
	 * Select the public page by the current URI slug, with login page how index.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectLoginRoute($uri)
	{
		switch ($uri)
		{
			case '':
				$this->auth();
				$this->setMessagesByUser();
				$this->showLoginPage();
				break;
		
			default:
				$this->selectPublicRoute($uri);
		}
	}

	/*
	 * Select the restrict page by the current URI slug.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectRestrictRoute($uri)
	{
		switch ($uri)
		{
			case '':
				$this->showRestrictPage();
				break;
				
			case 'logout':
				$this->doLogout();
				break;
				
			default:
				$this->selectPublicRoute($uri);
		}
	}

	/*
	 * Select the ajax page by the current URI slug.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectAjaxRoute($uri)
	{
		switch ($uri)
		{
			case '':
				if($this->model) echo json_encode($this->model->toArray());
				break;
				
			default:
				$this->show404Error();
		}
	}

	/*
	 * Select the ajax page by the current URI slug.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectRestrictAjax($uri)
	{
		switch ($uri)
		{
			case '':
				if($this->model) echo json_encode($this->model->toArray());
				break;
				
			default:
				$this->selectAjaxRoute($uri);
		}
	}

	/*
	 * Send the current URI for select the ajax page.
	 * @access public
	 * @return void
	 */
	public function ajax()
	{
		self::$view = new CleanCodeView('');
		$uri = $this->getNextSlug();
		$this->checkUser()? $this->selectRestrictAjax($uri) : $this->selectAjaxRoute($uri);
	}

	/*
	 * Send the current URI for select the admin page.
	 * @access public
	 * @return void
	 */
	public function administrate()
	{
		$this->selectAdminRoute($this->getNextSlug());
	}

	/*
	 * Send the current URI for select the page.
	 * @access public
	 * @return void
	 */
	public function route()
	{
		$uri = $this->getNextSlug();
		
		if($this->user)
		{
			$this->identifyUser($this->getUserSession(0));
			$this->user->loadFromDB()? $this->selectRestrictRoute($uri) : $this->selectLoginRoute($uri);
		}
		else
		{
			$this->selectPublicRoute($uri);
		}
	}

	/*
	 * Verify if is out route. Show 404 error, if have.
	 * @access private
	 * @return void
	 */
	private function verifyOutURI()
	{
		if($this->getNextSlug()) $this->show404Error();
	}

	/*
	 * Start the routing by the front controller.
	 * @access public
	 * @return void
	 */
	public function start()
	{
		$this->readUri();
		$this->selectHostConfig();
		$this->setView();
		$this->route();
		$this->verifyOutURI();
		$this->showView();
	}
}