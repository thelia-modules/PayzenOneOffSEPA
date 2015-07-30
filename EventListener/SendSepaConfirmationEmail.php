<?php

namespace PayzenOneOffSEPA\EventListener;

use PayzenOneOffSEPA\PayzenOneOffSEPA;
use \Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Template\ParserInterface;
use Thelia\Log\Tlog;
use Thelia\Mailer\MailerFactory;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MessageQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderStatusQuery;

/**
 * Class SendSepaConfirmationEmail
 * @package PayzenOneOffSEPA\EventListener
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class SendSepaConfirmationEmail implements EventSubscriberInterface
{
    /**
     * @var MailerFactory
     */
    protected $mailer;

    /**
     * @var ParserInterface
     */
    protected $parser;

    public function __construct(ParserInterface $parser, MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @return \Thelia\Mailer\MailerFactory
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * Check order status to send 'Waiting for payment' or 'Payment done' mail
     * @param OrderEvent $orderEvent
     * @throws \Exception
     */
    public function checkPaymentStatus(OrderEvent $orderEvent)
    {
        $payzenSepaOneOff = new PayzenOneOffSEPA();
        $order = $orderEvent->getOrder();


        // Check order payment module
        if ($payzenSepaOneOff->isPaymentModuleFor($order)) {
            $orderStatus = $order->getOrderStatus()->getId();
            $newStatusId = $orderEvent->getStatus();

            $paidStatusId = OrderStatusQuery::getPaidStatus()->getId();

            // Get "waiting_payment" status ID
            $waitingPaymentId = OrderStatusQuery::create()
                ->filterByCode('waiting_payment')
                ->select('ID')
                ->findOne();

            // If the order was unpaid and is being set to paid, set it waiting for payment
            if ($orderStatus == OrderStatusQuery::getNotPaidStatus()->getId() && $newStatusId == $paidStatusId) {
                $orderEvent->setStatus($waitingPaymentId);
                $this->sendSepaPaymentMail($order, PayzenOneOffSEPA::SEPA_WAITING_MESSAGE_NAME);
            } // Else if the order was waiting for payment and is being set to paid, set it to paid
            elseif ($orderStatus == $waitingPaymentId && $newStatusId == $paidStatusId) {
                $orderEvent->setStatus($paidStatusId);
                $this->sendSepaPaymentMail($order, PayzenOneOffSEPA::SEPA_CONFIRMATION_MESSAGE_NAME);
            } else {
                Tlog::getInstance()->debug("No confirmation email sent (order not paid, or not waiting for payment).");
            }
        } else {
            Tlog::getInstance()->debug("No confirmation email sent (not the proper payment module).");
        }
    }

    /**
     * Send email to customer to confirm either the transaction registration or the payment confirmation
     *
     * @param Order $order
     * @param $messageName
     * @throws \Exception
     */
    public function sendSepaPaymentMail(Order $order, $messageName)
    {
        $contact_email = ConfigQuery::read('store_email', false);

        Tlog::getInstance()->debug("Sending SEPA confirmation email from store contact e-mail $contact_email");

        if ($contact_email) {
            $message = MessageQuery::create()
                ->filterByName($messageName)
                ->findOne();

            if (false === $message) {
                throw new \Exception(sprintf("Failed to load message '%s'.", $messageName));
            }

            $this->parser->assign('order_id', $order->getId());
            $this->parser->assign('order_ref', $order->getRef());

            $message
                ->setLocale($order->getLang()->getLocale());

            $customer = $order->getCustomer();

            $instance = \Swift_Message::newInstance()
                ->addTo($customer->getEmail(), $customer->getFirstname()." ".$customer->getLastname())
                ->addFrom($contact_email, ConfigQuery::read('store_name'))
            ;

            // Build subject and body
            $message->buildMessage($this->parser, $instance);

            $this->getMailer()->send($instance);

            Tlog::getInstance()->debug("SEPA confirmation email sent to customer ".$customer->getEmail());
        }
    }
    
    public static function getSubscribedEvents()
    {
        return array(
            TheliaEvents::ORDER_UPDATE_STATUS => array("checkPaymentStatus", 129)
        );
    }
}
