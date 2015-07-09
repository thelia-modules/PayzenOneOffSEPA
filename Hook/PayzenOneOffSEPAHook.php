<?php

namespace PayzenOneOffSEPA\Hook;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Class PayzenOneOffSEPAHook
 * @package PayzenOneOffSEPA\Hook
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class PayzenOneOffSEPAHook extends BaseHook
{
    public function onOrderLabelCSS(HookRenderEvent $event)
    {
        $content = $this->addCSS('styles.css');
        $event->add($content);
    }
}
