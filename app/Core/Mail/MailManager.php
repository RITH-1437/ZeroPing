<?php

namespace App\Core\Mail;

use App\Core\Mail\Drivers\ArrayDriver;
use App\Core\Mail\Drivers\LogDriver;
use App\Core\Mail\Drivers\NullDriver;
use App\Core\Mail\Drivers\SMTPDriver;
use App\Core\Support\Config;

class MailManager
{
    protected array $mailers = [];

    public function __construct()
    {
        $this->registerDriver('smtp', function ($config) {
            return new Mailer(new SMTPDriver($config));
        });

        $this->registerDriver('log', function () {
            return new Mailer(new LogDriver());
        });

        $this->registerDriver('array', function () {
            return new Mailer(new ArrayDriver());
        });

        $this->registerDriver('null', function () {
            return new Mailer(new NullDriver());
        });
    }

    public function mailer(string $name = null): Mailer
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->mailers[$name])) {
            return $this->mailers[$name];
        }

        $config = Config::get("mail.mailers.{$name}");

        return $this->mailers[$name] = $this->resolve($name, $config);
    }

    public function driver(string $driver = null): Mailer
    {
        return $this->mailer($driver);
    }

    protected function resolve(string $name, array $config): Mailer
    {
        $driverMethod = 'create' . ucfirst($name) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new \InvalidArgumentException("Driver [{$name}] is not supported.");
        }
    }

    protected function createSmtpDriver(array $config): Mailer
    {
        return new Mailer(new SMTPDriver($config));
    }

    protected function createLogDriver(): Mailer
    {
        return new Mailer(new LogDriver());
    }

    protected function createArrayDriver(): Mailer
    {
        return new Mailer(new ArrayDriver());
    }

    protected function createNullDriver(): Mailer
    {
        return new Mailer(new NullDriver());
    }

    public function getDefaultDriver(): string
    {
        return Config::get('mail.default');
    }

    public function __call(string $method, array $parameters)
    {
        return $this->mailer()->$method(...$parameters);
    }
}
