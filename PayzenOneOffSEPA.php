<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace PayzenOneOffSEPA;

use Payzen\Model\PayzenConfigQuery;
use Payzen\Payzen;
use Thelia\Core\Translation\Translator;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\Message;
use Thelia\Model\MessageQuery;
use Thelia\Model\Order;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\OrderStatus;
use Thelia\Model\OrderStatusI18n;
use Thelia\Model\OrderStatusQuery;

/**
 * Class PayzenOneOffSEPA
 * @package PayzenOneOffSEPA
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class PayzenOneOffSEPA extends Payzen
{
    const MODULE_DOMAIN = "payzenoneoffsepa";

    /** The confirmation messages identifiers */

    const SEPA_WAITING_MESSAGE_NAME = 'payzen_sepa_payment_waiting';
    const SEPA_WAITING_MESSAGE_TEMPLATE_NAME = 'waiting-payment';
    const SEPA_CONFIRMATION_MESSAGE_NAME = 'payzen_sepa_payment_confirmation';
    const SEPA_CONFIRMATION_MESSAGE_TEMPLATE_NAME = 'confirmation-payment';

    /** @var Translator $translator */
    protected $translator;
    protected function trans($id, $locale, $parameters = [])
    {
        if ($this->translator === null) {
            $this->translator = Translator::getInstance();
        }
        return $this->translator->trans($id, $parameters, self::MODULE_DOMAIN, $locale);
    }

    /**
     * At the module activation, create a new order status 'waiting_payment' to handle SEPA waiting payment state
     *
     * @param ConnectionInterface $con
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        // Check if the 'waiting_payment' status already exist
        $orderStatus = OrderStatusQuery::create()
            ->findOneByCode('waiting_payment');

        // If 'waiting_payment' status doesn't exist, create it
        if ($orderStatus === null) {
            $orderStatus = new OrderStatus();
            $orderStatus
                ->setCode('waiting_payment')
                ->setLocale('en_US')
                ->setTitle('Waiting for payment')
                ->save();

            // Create french translation
            $orderStatusI18n = new OrderStatusI18n();
            $orderStatusI18n
                ->setId($orderStatus->getId())
                ->setLocale('fr_FR')
                ->setTitle('En attente de paiement')
                ->save();
        }

        $languages = LangQuery::create()->find();

        if (null === MessageQuery::create()->findOneByName(self::SEPA_WAITING_MESSAGE_NAME)) {
            $message = new Message();
            $message
                ->setName(self::SEPA_WAITING_MESSAGE_NAME)
                ->setHtmlLayoutFileName('')
                ->setHtmlTemplateFileName(self::SEPA_WAITING_MESSAGE_TEMPLATE_NAME.'.html')
                ->setTextLayoutFileName('')
                ->setTextTemplateFileName(self::SEPA_WAITING_MESSAGE_TEMPLATE_NAME.'.txt')
            ;
            foreach ($languages as $language) {
                /** @var Lang $language */
                $locale = $language->getLocale();
                $message->setLocale($locale);
                $message->setTitle(
                    $this->trans('Order confirmation', $locale)
                );
                $message->setSubject(
                    $this->trans('Order confirmation', $locale)
                );
            }
            $message->save();
        }

        if (null === MessageQuery::create()->findOneByName(self::SEPA_CONFIRMATION_MESSAGE_NAME)) {
            $message = new Message();
            $message
                ->setName(self::SEPA_CONFIRMATION_MESSAGE_NAME)
                ->setHtmlLayoutFileName('')
                ->setHtmlTemplateFileName(self::SEPA_CONFIRMATION_MESSAGE_TEMPLATE_NAME.'.html')
                ->setTextLayoutFileName('')
                ->setTextTemplateFileName(self::SEPA_CONFIRMATION_MESSAGE_TEMPLATE_NAME.'.txt')
            ;
            foreach ($languages as $language) {
                /** @var Lang $language */
                $locale = $language->getLocale();
                $message->setLocale($locale);
                $message->setTitle(
                    $this->trans('Payment confirmation', $locale)
                );
                $message->setSubject(
                    $this->trans('Payment confirmation', $locale)
                );
            }
            $message->save();
        }

    }

    /**
     *
     *  Method used by payment gateway.
     *
     *  If this method return a \Thelia\Core\HttpFoundation\Response instance, this response is send to the
     *  browser.
     *
     *  In many cases, it's necessary to send a form to the payment gateway. On your response you can return this form already
     *  completed, ready to be sent
     *
     * @param  \Thelia\Model\Order $order processed order
     * @return null|\Thelia\Core\HttpFoundation\Response
     */
    public function pay(Order $order)
    {
        return $this->doPay($order, 'SINGLE', 'SDD');
    }

    /**
     *
     * This method is call on Payment loop.
     *
     * If you return true, the payment method will de display
     * If you return false, the payment method will not be display
     *
     * @return boolean
     */
    public function isValidPayment()
    {
        $valid = false;

        $mode = PayzenConfigQuery::read('mode', false);

        // If we're in test mode, do not display Payzen on the front office, except for allowed IP addresses.
        if ('TEST' == $mode) {
            $raw_ips = explode("\n", PayzenConfigQuery::read('allowed_ip_list', ''));

            $allowed_client_ips = array();

            foreach ($raw_ips as $ip) {
                $allowed_client_ips[] = trim($ip);
            }

            $client_ip = $this->getRequest()->getClientIp();

            $valid = in_array($client_ip, $allowed_client_ips);
        } elseif ('PRODUCTION' == $mode) {
            $valid = true;
        }

        return $valid;
    }
}
