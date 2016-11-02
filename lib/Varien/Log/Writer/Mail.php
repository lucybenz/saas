<?php
/**
 * Varien Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Varien
 * @package    Varien_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2015 Varien Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Varien_Log_Writer_Abstract */
#require_once 'Varien/Log/Writer/Abstract.php';

/** Varien_Log_Exception */
#require_once 'Varien/Log/Exception.php';

/** Varien_Log_Formatter_Simple*/
#require_once 'Varien/Log/Formatter/Simple.php';

/**
 * Class used for writing log messages to email via Varien_Mail.
 *
 * Allows for emailing log messages at and above a certain level via a
 * Varien_Mail object.  Note that this class only sends the email upon
 * completion, so any log entries accumulated are sent in a single email.
 *
 * @category   Varien
 * @package    Varien_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2015 Varien Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Varien_Log_Writer_Mail extends Varien_Log_Writer_Abstract
{
    /**
     * Array of formatted events to include in message body.
     *
     * @var array
     */
    protected $_eventsToMail = array();

    /**
     * Array of formatted lines for use in an HTML email body; these events
     * are formatted with an optional formatter if the caller is using
     * Varien_Layout.
     *
     * @var array
     */
    protected $_layoutEventsToMail = array();

    /**
     * Varien_Mail instance to use
     *
     * @var Varien_Mail
     */
    protected $_mail;

    /**
     * Varien_Layout instance to use; optional.
     *
     * @var Varien_Layout
     */
    protected $_layout;

    /**
     * Optional formatter for use when rendering with Varien_Layout.
     *
     * @var Varien_Log_Formatter_Interface
     */
    protected $_layoutFormatter;

    /**
     * Array keeping track of the number of entries per priority level.
     *
     * @var array
     */
    protected $_numEntriesPerPriority = array();

    /**
     * Subject prepend text.
     *
     * Can only be used of the Varien_Mail object has not already had its
     * subject line set.  Using this will cause the subject to have the entry
     * counts per-priority level appended to it.
     *
     * @var string|null
     */
    protected $_subjectPrependText;

    /**
     * MethodMap for Varien_Mail's headers
     *
     * @var array
     */
    protected static $_methodMapHeaders = array(
        'from' => 'setFrom',
        'to' => 'addTo',
        'cc' => 'addCc',
        'bcc' => 'addBcc',
    );

    /**
     * Class constructor.
     *
     * Constructs the mail writer; requires a Varien_Mail instance, and takes an
     * optional Varien_Layout instance.  If Varien_Layout is being used,
     * $this->_layout->events will be set for use in the layout template.
     *
     * @param  Varien_Mail $mail Mail instance
     * @param  Varien_Layout $layout Layout instance; optional
     * @return void
     */
    public function __construct(Varien_Mail $mail, Varien_Layout $layout = null)
    {
        $this->_mail = $mail;
        if (null !== $layout) {
            $this->setLayout($layout);
        }
        $this->_formatter = new Varien_Log_Formatter_Simple();
    }

    /**
     * Create a new instance of Varien_Log_Writer_Mail
     *
     * @param  array|Varien_Config $config
     * @return Varien_Log_Writer_Mail
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $mail = self::_constructMailFromConfig($config);
        $writer = new self($mail);

        if (isset($config['layout']) || isset($config['layoutOptions'])) {
            $writer->setLayout($config);
        }
        if (isset($config['layoutFormatter'])) {
            $layoutFormatter = new $config['layoutFormatter'];
            $writer->setLayoutFormatter($layoutFormatter);
        }
        if (isset($config['subjectPrependText'])) {
            $writer->setSubjectPrependText($config['subjectPrependText']);
        }

        return $writer;
    }

    /**
     * Set the layout
     *
     * @param Varien_Layout|array $layout
     * @return Varien_Log_Writer_Mail
     * @throws Varien_Log_Exception
     */
    public function setLayout($layout)
    {
        if (is_array($layout)) {
            $layout = $this->_constructLayoutFromConfig($layout);
        }

        if (!$layout instanceof Varien_Layout) {
            #require_once 'Varien/Log/Exception.php';
            throw new Varien_Log_Exception('Mail must be an instance of Varien_Layout or an array');
        }
        $this->_layout = $layout;

        return $this;
    }

    /**
     * Construct a Varien_Mail instance based on a configuration array
     *
     * @param array $config
     * @return Varien_Mail
     * @throws Varien_Log_Exception
     */
    protected static function _constructMailFromConfig(array $config)
    {
        $mailClass = 'Varien_Mail';
        if (isset($config['mail'])) {
            $mailClass = $config['mail'];
        }

        if (!array_key_exists('charset', $config)) {
            $config['charset'] = null;
        }
        $mail = new $mailClass($config['charset']);
        if (!$mail instanceof Varien_Mail) {
            throw new Varien_Log_Exception($mail . 'must extend Varien_Mail');
        }

        if (isset($config['subject'])) {
            $mail->setSubject($config['subject']);
        }

        $headerAddresses = array_intersect_key($config, self::$_methodMapHeaders);
        if (count($headerAddresses)) {
            foreach ($headerAddresses as $header => $address) {
                $method = self::$_methodMapHeaders[$header];
                if (is_array($address) && isset($address['name'])
                    && !is_numeric($address['name'])
                ) {
                    $params = array(
                        $address['email'],
                        $address['name']
                    );
                } else if (is_array($address) && isset($address['email'])) {
                    $params = array($address['email']);
                } else {
                    $params = array($address);
                }
                call_user_func_array(array($mail, $method), $params);
            }
        }

        return $mail;
    }

    /**
     * Construct a Varien_Layout instance based on a configuration array
     *
     * @param array $config
     * @return Varien_Layout
     * @throws Varien_Log_Exception
     */
    protected function _constructLayoutFromConfig(array $config)
    {
        $config = array_merge(array(
            'layout' => 'Varien_Layout',
            'layoutOptions' => null
        ), $config);

        $layoutClass = $config['layout'];
        $layout = new $layoutClass($config['layoutOptions']);
        if (!$layout instanceof Varien_Layout) {
            throw new Varien_Log_Exception($layout . 'must extend Varien_Layout');
        }

        return $layout;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * Handles the formatting of both plaintext entries, as well as those
     * rendered with Varien_Layout.
     *
     * @param  array $event Event data
     * @return void
     */
    protected function _write($event)
    {
        // Track the number of entries per priority level.
        if (!isset($this->_numEntriesPerPriority[$event['priorityName']])) {
            $this->_numEntriesPerPriority[$event['priorityName']] = 1;
        } else {
            $this->_numEntriesPerPriority[$event['priorityName']]++;
        }

        $formattedEvent = $this->_formatter->format($event);

        // All plaintext events are to use the standard formatter.
        $this->_eventsToMail[] = $formattedEvent;

        // If we have a Varien_Layout instance, use a specific formatter for the
        // layout if one exists.  Otherwise, just use the event with its
        // default format.
        if ($this->_layout) {
            if ($this->_layoutFormatter) {
                $this->_layoutEventsToMail[] =
                    $this->_layoutFormatter->format($event);
            } else {
                $this->_layoutEventsToMail[] = $formattedEvent;
            }
        }
    }

    /**
     * Gets instance of Varien_Log_Formatter_Instance used for formatting a
     * message using Varien_Layout, if applicable.
     *
     * @return Varien_Log_Formatter_Interface|null The formatter, or null.
     */
    public function getLayoutFormatter()
    {
        return $this->_layoutFormatter;
    }

    /**
     * Sets a specific formatter for use with Varien_Layout events.
     *
     * Allows use of a second formatter on lines that will be rendered with
     * Varien_Layout.  In the event that Varien_Layout is not being used, this
     * formatter cannot be set, so an exception will be thrown.
     *
     * @param  Varien_Log_Formatter_Interface $formatter
     * @return Varien_Log_Writer_Mail
     * @throws Varien_Log_Exception
     */
    public function setLayoutFormatter(Varien_Log_Formatter_Interface $formatter)
    {
        if (!$this->_layout) {
            throw new Varien_Log_Exception(
                'cannot set formatter for layout; ' .
                    'a Varien_Layout instance is not in use');
        }

        $this->_layoutFormatter = $formatter;
        return $this;
    }

    /**
     * Allows caller to have the mail subject dynamically set to contain the
     * entry counts per-priority level.
     *
     * Sets the text for use in the subject, with entry counts per-priority
     * level appended to the end.  Since a Varien_Mail subject can only be set
     * once, this method cannot be used if the Varien_Mail object already has a
     * subject set.
     *
     * @param  string $subject Subject prepend text.
     * @return Varien_Log_Writer_Mail
     * @throws Varien_Log_Exception
     */
    public function setSubjectPrependText($subject)
    {
        if ($this->_mail->getSubject()) {
            throw new Varien_Log_Exception(
                'subject already set on mail; ' .
                    'cannot set subject prepend text');
        }

        $this->_subjectPrependText = (string) $subject;
        return $this;
    }

    /**
     * Sends mail to recipient(s) if log entries are present.  Note that both
     * plaintext and HTML portions of email are handled here.
     *
     * @return void
     */
    public function shutdown()
    {
        // If there are events to mail, use them as message body.  Otherwise,
        // there is no mail to be sent.
        if (empty($this->_eventsToMail)) {
            return;
        }

        if ($this->_subjectPrependText !== null) {
            // Tack on the summary of entries per-priority to the subject
            // line and set it on the Varien_Mail object.
            $numEntries = $this->_getFormattedNumEntriesPerPriority();
            $this->_mail->setSubject(
                "{$this->_subjectPrependText} ({$numEntries})");
        }


        // Always provide events to mail as plaintext.
        $this->_mail->setBodyText(implode('', $this->_eventsToMail));

        // If a Varien_Layout instance is being used, set its "events"
        // value to the lines formatted for use with the layout.
        if ($this->_layout) {
            // Set the required "messages" value for the layout.  Here we
            // are assuming that the layout is for use with HTML.
            $this->_layout->events =
                implode('', $this->_layoutEventsToMail);

            // If an exception occurs during rendering, convert it to a notice
            // so we can avoid an exception thrown without a stack frame.
            try {
                $this->_mail->setBodyHtml($this->_layout->render());
            } catch (Exception $e) {
                trigger_error(
                    "exception occurred when rendering layout; " .
                        "unable to set html body for message; " .
                        "message = {$e->getMessage()}; " .
                        "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                    E_USER_NOTICE);
            }
        }

        // Finally, send the mail.  If an exception occurs, convert it into a
        // warning-level message so we can avoid an exception thrown without a
        // stack frame.
        try {
            $this->_mail->send();
        } catch (Exception $e) {
            trigger_error(
                "unable to send log entries via email; " .
                    "message = {$e->getMessage()}; " .
                    "code = {$e->getCode()}; " .
                        "exception class = " . get_class($e),
                E_USER_WARNING);
        }
    }

    /**
     * Gets a string of number of entries per-priority level that occurred, or
     * an emptry string if none occurred.
     *
     * @return string
     */
    protected function _getFormattedNumEntriesPerPriority()
    {
        $strings = array();

        foreach ($this->_numEntriesPerPriority as $priority => $numEntries) {
            $strings[] = "{$priority}={$numEntries}";
        }

        return implode(', ', $strings);
    }
}