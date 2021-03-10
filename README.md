# sentry-logger

install:
composer require sitmpcz/sentry-logger

in config neon add:
1) in section parameters

sentry_url: https://something@abc.sentry.io/someproject

2) in section services

tracy.logger: Sitmpcz\SentryLogger(%sentry_url%)
