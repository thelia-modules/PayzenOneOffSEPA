<?php

namespace PayzenOneOffSEPA\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class RequestListener
 * @package PayzenOneOffSEPA\EventListener
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class RequestListener implements EventSubscriberInterface
{
    protected $smarty;

    public function __construct(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    public function addConfigDir(GetResponseEvent $event)
    {
        $this->smarty->configLoad(__DIR__.DS."..".DS."templates".DS."backOffice".DS."default".DS."configs".DS."variables.conf");
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => "addConfigDir"
        ];
    }
}
