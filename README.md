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
```php
/** @var \Sitmpcz\SentryLogger @inject */
public \Sitmpcz\SentryLogger $sentryLogger;
```

Then you can write error to Sentry manually:
```php
$this->sentryLogger->log(new \Exception("test sentry"),\Tracy\ILogger::ERROR);
```

If you want to write to log manually, you can use Tracy\Debugger::log too, but you must specify higher priority (Napr CRITICAL)
Example
```php
\Tracy\Debugger::log("Test zapistu do logu",\Tracy\ILogger::CRITICAL);
```


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

