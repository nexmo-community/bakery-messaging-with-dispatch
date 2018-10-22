# Bakery Messaging Demo Application

This is a very simple Slim PHP application to showcase using the [Nexmo Messages API](https://developer.nexmo.com/messages/overview) to send messages to customers of an imaginary Cupcake Bakery business. If the message isn't delivered, the [Nexmo Dispatch API](https://developer.nexmo.com/dispatch/overview) can be used to fall back to an alternative messaging channel.

## Try This Project Yourself

1. You will need a Nexmo account and the [Nexmo CLI tool](https://github.com/Nexmo/nexmo-cli) tool installed.
2. Clone this repository
3. Run `composer install` to get the dependencies
4. Copy `config.php.sample` to `config.php` and add the settings there as required - the comments direct you to creating the [JWT](https://jwt.io) you will need for access
5. Run `php -S localhost:8080` in the `public/` directory

The sample project should now be available at <http://localhost:8080>.
