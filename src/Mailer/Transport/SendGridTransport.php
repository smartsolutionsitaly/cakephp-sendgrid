<?php
/**
 * cakephp-sendgrid (https://github.com/smartsolutionsitaly/cakephp-sendgrid)
 * Copyright (c) 2018 Smart Solutions S.r.l. (https://smartsolutions.it)
 *
 * SendGrid Transport for CakePHP
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @category  cakephp-plugin
 * @package   cakephp-sendgrid
 * @author    Lucio Benini <dev@smartsolutions.it>
 * @copyright 2018 Smart Solutions S.r.l. (https://smartsolutions.it)
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 * @link      https://smartsolutions.it Smart Solutions
 * @since     1.0.0
 */
namespace SmartSolutionsItaly\CakePHP\SendGrid\Mailer\Transport;

use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;

/**
 * SendGrid Transport
 *
 * @author Lucio Benini <dev@smartsolutions.it>
 * @since 1.0.0
 */
class SendGridTransport extends AbstractTransport
{

    /**
     * Headers blacklist.
     *
     * @var array
     * @since 1.0.0
     */
    protected $_headersBlacklist = [
        'Content-Type',
        'Content-Transfer-Encoding'
    ];

    /**
     *
     * {@inheritdoc}
     *
     * @see \Cake\Mailer\AbstractTransport::send()
     */
    public function send(Email $email)
    {
        $from = $email->from();
        
        $se = new \SendGrid\Mail\Mail();
        $se->setFrom(key($from), current($from));
        $se->setSubject($email->subject());
        $se->addContent('text/plain', $email->message(Email::MESSAGE_TEXT));
        $se->addContent('text/html', $email->message(Email::MESSAGE_HTML));
        
        foreach ($email->getTo() as $address => $name) {
            $se->addTo($address, $name);
        }
        
        foreach ($email->getCc() as $address => $name) {
            $se->addCc($address, $name);
        }
        
        foreach ($email->getBcc() as $address => $name) {
            $se->addBcc($address, $name);
        }
        
        foreach ($email->getHeaders() as $key => $value) {
            if (! in_array($key, $this->_headersBlacklist)) {
                $se->addHeader($key, $value);
            }
        }
        
        $client = new \SendGrid($this->getConfig('key'));
        
        return [
            'code' => $res->statusCode(),
            'headers' => $res->headers(),
            'body' => $res->body()
        ];
    }
}
