# sentry-logger

install:
composer require sitmpcz/sentry-logger

Sentry event logger
--------
in config neon add:
1) in section parameters

sentry_url: https://something@abc.sentry.io/someproject

2) in section services

tracy.logger: Sitmpcz\SentryLogger(%sentry_url%)

--------------------------------------------------------------
Sentry performance usage (optional)
--------

Example for Nette - BasePresenter

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

