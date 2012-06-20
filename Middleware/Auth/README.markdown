# Slim Authentication Middlewares

## HttpBasic

This will provide you with basic user Authentication based on username and password set.

### How to use

	$app = new Slim();
	$app->add(new Middleware_Auth_HttpBasic('theUsername', 'thePassword'));


## Strong

### How to use

You will need to pass Strong a config with all your secured routes and any information that is needed
for your Provider.

Here is some sample code for using PDO provider and securing some routes using regex.

	$config = array(
	    'provider' => 'PDO',
	    'dsn' => 'mysql:host=localhost;dbname=slimdev',
	    'dbuser' => 'serverside',
	    'dbpass' => 'password',
	    'auth_type' => 'form',
	    'login.url' => '/',
	    'security.urls' => array(
	        array('path' => '/test'),
	        array('path' => '/about/.+'),
	    ),
	);

	$app->add(new Middleware_Auth_Strong($config));