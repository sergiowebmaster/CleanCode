<?php
session_start();

require_once 'CleanCodeClass.php';
require_once 'CleanCodeURI.php';
require_once 'CleanCodeUser.php';
require_once 'CleanCodeDaoUri.php';
require_once 'CleanCodeView.php';

class CleanCodeController extends CleanCodeClass
{
	/*
	 * Array with the parts of requested URI.
	 * @var object
	 */
	protected static $uri;
	
	/*
	 * Instance of CleanCodeLanguage.
	 * @var object
	 */
	protected static $language;

	/*
	 * Instance of CleanCodeUser.
	 * @var object
	 */
	protected static $administrator;

	/*
	 * Instance of CleanCodeUser.
	 * @var object
	 */
	protected $user;

	/*
	 * The uri name of the admin area.
	 * @var string
	 */
	protected $adminUri = 'admin';

	/*
	 * Instance of CleanCodeView.
	 * @var object
	 */
	protected static $view;

	/*
	 * Base file of CleanCodeView.
	 * @var String
	 */
	protected static $baseLayout = 'base.phtml';

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
	 * The form actions of the page.
	 * @var array
	 */
	protected $actions = array();

	/*
	 * The label of the login button.
	 * @var string
	 */
	protected $loginAction = 'Login';
	
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
	 * Verify if the host is HTTPS.
	 * @access protected
	 * @return Boolean
	 */
	protected function isHTTPS()
	{
		return $_SERVER['HTTPS'] == 'on';
	}
	
	protected function getBaseUrl()
	{
		return str_replace(array(URI, '//'), array('', '/'), $this->getHostname() . $this->getRequestUri());
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
	 * Return the URI requested by the user.
	 * @access protected
	 * @return String
	 */
	protected function getURI()
	{
		return self::get('uri');
	}
	
	/*
	 * Read the URI requested by the user.
	 * @access public
	 * @return void
	 */
	public function readUri()
	{
		self::$uri = new CleanCodeURI($this->getURI());
		define('URI', self::$uri->toString());
	}
	
	protected function getNextSlug($default = '')
	{
		return self::$uri->nextSlug($default);
	}
	
	protected function getBackUri()
	{
		return self::$uri->getBack();
	}
	
	protected function getPageNumber()
	{
		return self::$uri->getPageNumber();
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
	
	protected function configureDB($host, $database, $user, $password)
	{
		CleanCodeDAO::openConnection(new CleanCodeMySQL($host, $database, $user, $password));
	}
	
	public function closeConnection()
	{
		CleanCodeDAO::closeConnection();
	}
	
	protected function configurePaths()
	{
		// Implement into frontcontroller
	}
	
	protected function configureApplication($baseHref, $host, $database, $dbUser, $dbPassword)
	{
		$this->setBaseHref($baseHref);
		$this->configureDB($host, $database, $dbUser, $dbPassword);
		$this->configurePaths();
	}

	/*
	 * Load the configuration for a localhost.
	 * @access protected
	 * @return void
	 */
	protected function configureToLocalhost()
	{
		// Implement in the Front Controller.
	}

	/*
	 * Load the configuration for a external host.
	 * @access protected
	 * @return void
	 */
	protected function configureToHost()
	{
		// Implement in the Front Controller.
	}

	/*
	 * Check the configuration for the current host.
	 * @access public
	 * @return void
	 */
	protected function stripSubdomain($subdomain, $host)
	{
		return preg_replace("/^$subdomain./", '', $host);
	}
	
	protected function stripWWW($domain)
	{
		return $this->stripSubdomain('www', $domain);
	}

	/*
	 * Select the configuration for the current host.
	 * @access public
	 * @param String $host	The current hostname.
	 * @return void
	 */
	protected function selectEnvironment($host)
	{
		switch ($host)
		{
			case 'localhost':
				$this->configureToLocalhost();
				break;
				
			default:
				$this->configureToHost();
		}
	}

	/*
	 * Check the configuration for the current host.
	 * @access public
	 * @return void
	 */
	public function checkEnvironment()
	{
		$this->selectEnvironment(self::getHostname());
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
	
	protected function submitForm()
	{
		echo 'Submit!';
	}
	
	protected function doAction($action)
	{
		$this->submitForm();
	}
	
	protected function listenForm()
	{
		if($this->getAction()) $this->doAction($this->getAction());
	}
	
	protected function defineUser()
	{
		$this->user = new CleanCodeUser();
		$this->user->sessionName = 'user';
	}
	
	public function setUser(CleanCodeUser $user)
	{
		$this->user = $user;
	}
	
	protected function loadUserBySession()
	{
		$this->user->setBySession($_SESSION);
		$this->user->loadFromDB();
	}
	
	protected function prepareLogin()
	{
		$this->user->setEmail(self::post('email'));
		$this->user->setPassword(self::post('password'));
	}
	
	protected function setUserVar($var)
	{
		$this->setPageVar($var, $this->user->toArray());
	}
	
	protected function loadUserData()
	{
		$this->setUserVar('user');
	}

	/*
	 * Set the user session and reload the page.
	 * @access	protected
	 * @param	String		$sessionValue	The value for set the session of the user.
	 * @return	void
	 */
	protected function setUserSession()
	{
		$this->setSession($this->user->sessionName, $this->user->getSession());
	}
	
	private function doAuth()
	{
		if($this->user->loadForAuth())
		{
			$this->setUserSession();
			$this->refresh();
		}
	}
	
	/*
	 * Send the login data of the user and set a session, if this user is founded.
	 * @access protected
	 * @return void
	 */
	protected function doLogin()
	{
		$this->prepareLogin();
		$this->doAuth();
	}

	/*
	 * Destroy the session and redirect for a login page.
	 * @access protected
	 * @param String $path The path for redirect, after destroy the session.
	 * @return void
	 */
	protected function doLogout($path = './')
	{
		$this->show404Error();
		$this->unsetSession($this->user->sessionName);
		$this->redirect($path);
	}
	
	protected function selectLoginAction($action)
	{
		if($action) $this->doLogin();
	}
	
	public function checkLogin()
	{
		$this->selectLoginAction($this->getAction());
		$this->setMessagesByUser();
	}
	
	public function checkUser()
	{
		$this->defineUser();
		$this->loadUserBySession();
		$this->loadUserData();
	}
	
	protected function showLogin()
	{
		$this->defineUser();
		$this->selectLoginAction($this->getAction());
		$this->setMessagesByUser();
		$this->showLoginPage();
	}
	
	/*
	 * Clear the messages in the view.
	 * @access	public
	 * @return	void
	 */
	public function clearMessages()
	{
		$this->setMessages('', '');
	}

	/*
	 * Set the messages in the view, by a model object.
	 * @access protected
	 * @param	CleanCodeModel	$model		The model object.
	 * @return	void
	 */
	protected function setMessagesBy(CleanCodeModel $model)
	{
		$this->setMessages($model->getError(), $model->getSuccess());
	}

	/*
	 * Set the view messages by the model.
	 * @access	public
	 * @return	void
	 */
	public function setMessagesByModel()
	{
		$this->setMessagesBy($this->model);
	}

	/*
	 * Set the view messages by the user.
	 * @access	public
	 * @return	void
	 */
	public function setMessagesByUser()
	{
		$this->setMessagesBy($this->user);
	}
	
	protected function defineModel()
	{
		$this->model = new CleanCodeDaoUri();
	}
	
	protected function getModelClone()
	{
		return clone $this->model;
	}
	
	protected function setModelPK($pk)
	{
		$this->model->setID($pk);
	}
	
	protected function setModelUri($uri)
	{
		$this->model->setUri($uri);
	}
	
	protected function loadModel()
	{
		$this->model->loadFromDB();
	}
	
	protected function deleteModel()
	{
		$this->model->delete();
	}
	
	protected function updateModel()
	{
		$this->model->update();
	}
	
	protected function searchModelPage()
	{
		$this->model->loadFromDB()? $this->showDynamicPage() : $this->show404Error();
	}
	
	protected function loadModelByPK($pk)
	{
		$this->defineModel();
		$this->setModelPK($pk);
		$this->loadModel();
	}
	
	public function searchModel($uri)
	{
		$this->defineModel();
		$this->setModelUri($uri);
		$this->searchModelPage();
	}
	
	protected function edit($pk)
	{
		$this->defineModel();
		$this->setModelPK($pk);
		$this->updateModel();
		$this->setMessagesByModel();
	}
	
	protected function editForAjax($pk)
	{
		$this->createJsonView();
		$this->edit($pk);
	}
	
	protected function editForForm($pk)
	{
		$this->loadModelByPK($pk);
		$this->listenForm();
		$this->setMessagesByModel();
		$this->showAdminForm();
	}
	
	protected function delete($pk)
	{
		$this->defineModel();
		$this->setModelPK($pk);
		$this->deleteModel();
		$this->setMessagesByModel();
	}
	
	protected function deleteForAjax($pk)
	{
		$this->createJsonView();
		$this->delete($pk);
	}
	
	protected function createHtmlView($filename)
	{
		self::$view = new CleanCodeHtmlView($filename);
	}
	
	protected function createJsonView()
	{
		self::$view = new CleanCodeJsonView();
	}
	
	protected function createXmlView()
	{
		self::$view = new CleanCodeXmlView();
	}
	
	protected function createTextView($text)
	{
		self::$view = new CleanCodeTextView($text);
	}
	
	public function createView()
	{
		$this->configureHtmlHeader('pt-br');
		$this->createHtmlView('views/layout.phtml');
	}
	
	protected function setFavicon($filename)
	{
		CleanCodeView::$data['favicon'] = CleanCodeDir::translate($filename);
	}
	
	protected function setRobots($index, $follow)
	{
		CleanCodeView::$data['robots'] = ($index? 'index' : 'no-index') . ',' . ($follow? 'follow' : 'no-follow');
	}
	
	protected function restrictView()
	{
		$this->setRobots(false, false);
	}
	
	protected function setLang($lang)
	{
		CleanCodeView::$data['lang'] = $lang;
	}
	
	protected function setBaseHref($url)
	{
		CleanCodeView::$data['baseHref'] = $url;
	}
	
	private function createAssetsHeader()
	{
		CleanCodeView::$data['meta'] = array();
		CleanCodeView::$data['css'] = array();
		CleanCodeView::$data['js'] = array();
	}
	
	protected function initHtmlView($lang, $filename)
	{
		$this->createHtmlView($filename);
		$this->setLang($lang);
		$this->setRobots(true, true);
		$this->createAssetsHeader();
	}
	
	protected function addMeta($prop, $content)
	{
		CleanCodeView::$data['meta'][$prop] = $content;
	}
	
	private function parseAttributes($array)
	{
		$attributes = '';
		
		foreach ($array as $attr => $value)
		{
			$attributes = " $attr=\"$value\"";
		}
		
		return $attributes;
	}
	
	protected function css($href, $attributes = array())
	{
		CleanCodeView::$data['css'][CleanCodeDir::translate($href)] = $this->parseAttributes($attributes);
	}
	
	protected function js($src, $attributes = array())
	{
		CleanCodeView::$data['js'][CleanCodeDir::translate($src)] = $this->parseAttributes($attributes);
	}
	
	protected function jsDefer($src)
	{
		$this->js($src, array('defer' => 'defer'));
	}
	
	protected function addMetaOg($prop, $content)
	{
		$this->addMeta("og:$prop", $content);
	}
	
	protected function setOgType($type)
	{
		$this->addMetaOg('type', $type);
	}
	
	protected function setOgTypeWebsite()
	{
		$this->setOgType('website');
	}
	
	protected function setOgTypeArticle()
	{
		$this->setOgType('article');
	}
	
	protected function setOgUrl($url)
	{
		$this->addMetaOg('url', $url);
		$this->setBaseHref($url);
	}
	
	protected function setOgImage($filename)
	{
		$this->addMetaOg('image', self::searchPos(CleanCodeView::$data, 'baseHref') . CleanCodeDir::translate($filename));
	}
	
	protected function setOgImageSize($width, $height)
	{
		$this->addMetaOg('image:width', $width);
		$this->addMetaOg('image:height', $height);
	}
	
	protected function setOgLocale($lang)
	{
		$this->addMetaOg('locale', str_replace('_', '-', strtolower($lang)));
		$this->setLang($lang);
	}
	
	protected function setTitle($title)
	{
		CleanCodeView::$data['title'] = $title;
	}
	
	protected function setOgTitle($title)
	{
		$this->addMetaOg('title', $title);
		$this->setTitle($title);
	}
	
	protected function setMetaDescription($description)
	{
		$this->addMeta('description', $description);
	}
	
	protected function setOgDescription($description)
	{
		$this->addMetaOg('description', $description);
		$this->setMetaDescription($description);
	}
	
	protected function setMetaKeywords($keywords)
	{
		$this->addMeta('keywords', $keywords);
	}
	
	protected function setPageInfo($title, $description, $keywords)
	{
		$this->setOgTitle($title);
		$this->setOgDescription($description);
		$this->setMetaKeywords($keywords);
	}
	
	protected function setPageVar($var, $value)
	{
		CleanCodeView::$data[$var] = $value;
	}
	
	protected function setPageData($data)
	{
		CleanCodeView::$data = $data;
	}
	
	protected function addPageData($data)
	{
		$this->setPageData(array_merge(CleanCodeView::$data, $data));
	}

	/*
	 * Set the messages in the view.
	 * @access	protected
	 * @param	String	$error		The error message.
	 * @param	String	$success	The success message.
	 * @return	void
	 */
	protected function setMessages($error, $success)
	{
		$this->setPageVar('error', $error);
		$this->setPageVar('success', $success);
	}
	
	public function showView()
	{
		self::$view->render();
	}
	
	protected function setLayout($filename)
	{
		$this->setPageVar('layout', $filename);
	}
	
	protected function setViewFile($filename)
	{
		$this->createHtmlView($filename);
	}
	
	protected function setPage($title, $description, $keywords, $filename, $data = array())
	{
		$this->setViewFile($filename);
		$this->setPageInfo($title, $description, $keywords);
		$this->addPageData($data);
	}
	
	protected function setRestrictPage($title, $filename, $data = array())
	{
		$this->setPage($title, '', '', $filename, $data);
		$this->restrictView();
	}

	/*
	 * Configure the view for to show the 404 page.
	 * @access protected
	 * @return void
	 */
	protected function show404Error()
	{
		echo 'This page is not found.';
	}

	/*
	 * Configure the view for to show the index page.
	 * @access protected
	 * @return void
	 */
	protected function showIndexPage()
	{
		$this->show404Error();
	}

	/*
	 * Configure the view for to show a dynamic page.
	 * @access protected
	 * @return void
	 */
	protected function showDynamicPage()
	{
		$this->show404Error();
	}

	/*
	 * Configure the view for to show the login page, for a restrict area.
	 * @access protected
	 * @return void
	 */
	protected function showLoginPage()
	{
		$this->show404Error();
	}

	/*
	 * Configure the view for to show the internal page, for a restrict area.
	 * @access protected
	 * @return void
	 */
	protected function showRestrictPage()
	{
		$this->show404Error();
	}

	/*
	 * Configure the view for to show the admin index page.
	 * @access protected
	 * @return void
	 */
	protected function showAdminPage()
	{
		$this->show404Error();
	}

	/*
	 * Configure the view for to show the admin edit page.
	 * @access protected
	 * @return void
	 */
	protected function showAdminForm()
	{
		$this->show404Error();
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
				$this->editForForm($uri);
				break;
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
				$this->searchModel($uri);
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
	 * Select the page if the user is not logged.
	 * @access protected
	 * @param String $uri The current slug.
	 * @return void
	 */
	protected function selectLoginRoute($uri)
	{
		switch ($uri)
		{
			case '':
				$this->showLogin();
				break;
				
			default:
				$this->selectPublicRoute($uri);
		}
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
	
	public function accessRoute($uri)
	{
		$this->selectPublicRoute($uri);
	}

	/*
	 * Send the current URI for select the page.
	 * @access public
	 * @return void
	 */
	public function route()
	{
		$this->selectPublicRoute($this->getNextSlug());
	}
	
	public function checkUserRoute($uri)
	{
		$this->user->getID()? $this->selectRestrictRoute($uri) : $this->selectLoginRoute($uri);
	}
	
	public function restrictUserRoute($uri)
	{
		$this->user->getID()? $this->selectRestrictRoute($uri) : $this->showLogin();
	}
	
	public function routeUser()
	{
		$this->checkUser();
		$this->checkUserRoute($this->getNextSlug());
	}
	
	public function routeIfAuth()
	{
		$this->checkUser();
		$this->restrictUserRoute($this->getNextSlug());
	}
	
	public function checkOutUri()
	{
		if($this->getNextSlug()) $this->show404Error();
	}
}