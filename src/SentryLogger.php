<?php
/*
 * register in  config.neon
 * services:
 *    tracy.logger: SentryLogger
 *
 *
 */


namespace Sitmpcz;

use Tracy\ILogger,
    Tracy\Dumper,
    Tracy\Debugger,
    Nette\Security\User,
    Nette\Http\Session,
    Nette\Http\Request,
    Throwable,
    Sentry;

class SentryLogger implements ILogger
{
    private array $allowedPriority = [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL];
    private bool $ready = false;
    private User $user;
    private Session $session;
    private Request $request;

    public function __construct(string $url ,User $user, Session $session, Request $request)
    {
        $this->user = $user;
        $this->session = $session;
        $this->request = $request;
        // log only in production
        if (($url != '') && (Debugger::$productionMode)) {
            // is registration OK?
            try {
                // how to parametrize traces_sample_rate without changes in __construct syntax?
                Sentry\init([
                    'dsn' => $url,
                    'traces_sample_rate' => 0.1
                ]);
                $this->ready = true;
            } catch (Exception $e) {
                // what now?
            }
        }

    }

    public function log($value, $priority = ILogger::INFO)
    {
        if ($this->ready) {
            if (!in_array($priority, $this->allowedPriority, true)) {
                return;
            }
            Sentry\configureScope(function (Sentry\State\Scope $scope): void {
                // add user info into scope if available
                if ($this->user->isLoggedIn()) {
                    $userFields = ['id' => $this->user->getIdentity()->getId()];
                    // give other user data?
                    foreach ($this->user->getIdentity()->data as $key => $item) {
                        if (in_array(gettype($item),["string","integer"])) $userFields[$key] = $item;
                    }
                    $scope->setUser($userFields);
                }
                // add session info  into scope if available
                if ($this->session) {
                    $data = [];
                    foreach ($this->session->getIterator() as $section) {
                        foreach ($this->session->getSection($section)->getIterator() as $key => $val) {
                            $data[$section][$key] = $val;
                        }
                    }
                    $scope->setExtra('session', $data);
                }
                if (isset($_SERVER['HTTP_X_REAL_IP'])) {
                    $scope->setExtra('IP', $_SERVER['HTTP_X_REAL_IP']);
                } else {
                    if ($this->request) $scope->setExtra('IP', $this->request->getRemoteAddress());
                }
            });
            if ($value instanceof Throwable) {
                Sentry\captureException($value);
            } else {
                Sentry\captureMessage(is_string($value) ? $value : Dumper::toText($value));
            }
        }
    }
}
