# sentry-logger

Install
--------
composer require sitmpcz/sentry-logger

Sentry event logger usage
--------
1) run Sentry, register your project and get client DSN url 

2) in config neon add in section parameters

> sentry_url: https://something@abc.sentry.io/someproject

3) in config neon add in section services

> tracy.logger: Sitmpcz\SentryLogger(%sentry_url%)

Manual write to Sentry Error log
--------
Using  DI load Sentry Logger 
Example for presenter
/** @var \Sitmpcz\SentryLogger @inject */
public \Sitmpcz\SentryLogger $sentryLogger;

Then you can write error to Sentry manually:
$this->sentryLogger->log(new \Exception("test sentry"),\Tracy\ILogger::ERROR);


--------------------------------------------------------------
Sentry performance usage (optional)
--------

Example for Nette - BasePresenter:

```php
private ?object $sentryPerformance = null;

function startup(): void
{
   parent::startup();
   $this->sentryPerformance = Sitmpcz\SentryPerformance::startPerformaceMonitoring($this->getName(), $this->getAction());
}

function shutdown(Nette\Application\Response $response): void
{
   parent::shutdown($response);
   if ($this->sentryPerformance) Sitmpcz\SentryPerformance::endPerformaceMonitoring($this->sentryPerformance);
}
```

