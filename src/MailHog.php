<?php

/*
 * This file is part of the Mailhog service provider for the Codeception Email Testing Framework.
 * (c) 2015-2016 Eric Martel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Codeception\Module;

use Codeception\Module;
use Codeception\TestInterface;
use Exception;

class MailHog extends Module
{
    use TestsEmails;

    /**
     * HTTP Client to interact with MailHog
     *
     * @var \GuzzleHttp\Client
     */
    protected $mailhog;

    /**
     * Raw email header data converted to JSON
     *
     * @var array<object>
     */
    protected $fetchedEmails;

    /**
     * Currently selected set of email headers to work with
     *
     * @var array<object>
     */
    protected $currentInbox;

    /**
     * Starts as the same data as the current inbox, but items are removed as they're used
     *
     * @var array<object>
     */
    protected $unreadInbox;

    /**
     * Contains the currently open email on which test operations are conducted
     *
     * @var object
     */
    protected $openedEmail;

    /**
     * Codeception exposed variables
     *
     * @var array<string>
     */
    protected array $config = ['url', 'port', 'deleteEmailsAfterScenario', 'timeout'];

    /**
     * Codeception required variables
     *
     * @var array<string>
     */
    protected array $requiredFields = ['url', 'port'];

    public function _initialize(): void
    {
        $url = trim($this->config['url'], '/') . ':' . $this->config['port'];

        $timeout = 1.0;
        if (isset($this->config['timeout'])) {
            $timeout = $this->config['timeout'];
        }
        $this->mailhog = new \GuzzleHttp\Client(['base_uri' => $url, 'timeout' => $timeout]);
    }

    /**
     * Method executed after each scenario
     */
    public function _after(TestInterface $test): void
    {
        if (isset($this->config['deleteEmailsAfterScenario']) && $this->config['deleteEmailsAfterScenario']) {
            $this->deleteAllEmails();
        }
    }

    /**
     * Delete All Emails
     *
     * Accessible from tests, deletes all emails
     */
    public function deleteAllEmails(): void
    {
        try {
            $this->mailhog->request('DELETE', '/api/v1/messages');
        } catch(Exception $e) {
            $this->fail('Exception: ' . $e->getMessage());
        }
    }

    /**
     * Fetch Emails
     *
     * Accessible from tests, fetches all emails
     */
    public function fetchEmails(): void
    {
        $this->fetchedEmails = [];

        try {
            $response = $this->mailhog->request('GET', '/api/v1/messages');
            $this->fetchedEmails = json_decode($response->getBody());
        } catch(Exception $e) {
            $this->fail('Exception: ' . $e->getMessage());
        }

        $this->sortEmails($this->fetchedEmails);

        // by default, work on all emails
        $this->setCurrentInbox($this->fetchedEmails);
    }

    /**
     * Access Inbox For *
     *
     * Filters emails to only keep those that are received by the provided address
     *
     * @param string $address Recipient address' inbox
     */
    public function accessInboxFor(string $address): void
    {
        $inbox = [];

        foreach ($this->fetchedEmails as $email) {
            if (strpos($email->Content->Headers->To[0], $address) !== false) {
                array_push($inbox, $email);
            }

            if (isset($email->Content->Headers->Cc) && array_search($address, $email->Content->Headers->Cc)) {
                array_push($inbox, $email);
            }

            if (isset($email->Content->Headers->Bcc) && array_search($address, $email->Content->Headers->Bcc)) {
                array_push($inbox, $email);
            }
        }
        $this->setCurrentInbox($inbox);
    }

    /**
     * Access Inbox For To
     *
     * Filters emails to only keep those that are received by the provided address
     *
     * @param string $address Recipient address' inbox
     */
    public function accessInboxForTo(string $address): void
    {
        $inbox = [];

        foreach ($this->fetchedEmails as $email) {
            if (strpos($email->Content->Headers->To[0], $address) !== false) {
                array_push($inbox, $email);
            }
        }
        $this->setCurrentInbox($inbox);
    }

    /**
     * Access Inbox For CC
     *
     * Filters emails to only keep those that are received by the provided address
     *
     * @param string $address Recipient address' inbox
     */
    public function accessInboxForCc(string $address): void
    {
        $inbox = [];

        foreach ($this->fetchedEmails as $email) {
            if (isset($email->Content->Headers->Cc) && array_search($address, $email->Content->Headers->Cc)) {
                array_push($inbox, $email);
            }
        }
        $this->setCurrentInbox($inbox);
    }

    /**
     * Access Inbox For BCC
     *
     * Filters emails to only keep those that are received by the provided address
     *
     * @param string $address Recipient address' inbox
     */
    public function accessInboxForBcc(string $address): void
    {
        $inbox = [];

        foreach ($this->fetchedEmails as $email) {
            if (isset($email->Content->Headers->Bcc) && array_search($address, $email->Content->Headers->Bcc)) {
                array_push($inbox, $email);
            }
        }
        $this->setCurrentInbox($inbox);
    }

    /**
     * Open Next Unread Email
     *
     * Pops the most recent unread email and assigns it as the email to conduct tests on
     */
    public function openNextUnreadEmail(): void
    {
        $this->openedEmail = $this->getMostRecentUnreadEmail();
    }

    /**
     * Get Opened Email
     *
     * Main method called by the tests, providing either the currently open email or the next unread one
     *
     * @param bool $fetchNextUnread Goes to the next Unread Email
     * @return object Returns a JSON encoded Email
     */
    protected function getOpenedEmail(bool $fetchNextUnread = false): object
    {
        if ($fetchNextUnread || $this->openedEmail == null) {
            $this->openNextUnreadEmail();
        }

        return $this->openedEmail;
    }

    /**
     * Get Most Recent Unread Email
     *
     * Pops the most recent unread email, fails if the inbox is empty
     *
     * @return object Returns a JSON encoded Email
     */
    protected function getMostRecentUnreadEmail(): object
    {
        if (empty($this->unreadInbox)) {
            $this->fail('Unread Inbox is Empty');
        }

        $email = array_shift($this->unreadInbox);
        return $this->getFullEmail($email->ID);
    }

    /**
     * Get Full Email
     *
     * Returns the full content of an email
     *
     * @param string $id ID from the header
     * @return object Returns a JSON encoded Email
     */
    protected function getFullEmail(string $id): object
    {
        try {
            $response = $this->mailhog->request('GET', "/api/v1/messages/{$id}");
        } catch(Exception $e) {
            $this->fail('Exception: ' . $e->getMessage());
        }
        $fullEmail = json_decode($response->getBody());
        return $fullEmail;
    }

    /**
     * Get Email Subject
     *
     * Returns the subject of an email
     */
    protected function getEmailSubject(object $email): string
    {
        return $this->getDecodedEmailProperty($email, $email->Content->Headers->Subject[0]);
    }

    /**
     * Get Email Body
     *
     * Returns the body of an email
     */
    protected function getEmailBody(object $email): string
    {
        return $this->getDecodedEmailProperty($email, $email->Content->Body);
    }

    /**
     * Get Email To
     *
     * Returns the string containing the persons included in the To field
     */
    protected function getEmailTo(object $email): string
    {
        return $this->getDecodedEmailProperty($email, $email->Content->Headers->To[0]);
    }

    /**
     * Get Email CC
     *
     * Returns the string containing the persons included in the CC field
     */
    protected function getEmailCC(object $email): string
    {
        $emailCc = '';
        if (isset($email->Content->Headers->Cc)) {
            $emailCc = $this->getDecodedEmailProperty($email, $email->Content->Headers->Cc[0]);
        }
        return $emailCc;
    }

    /**
     * Get Email BCC
     *
     * Returns the string containing the persons included in the BCC field
     */
    protected function getEmailBCC(object $email): string
    {
        $emailBcc = '';
        if (isset($email->Content->Headers->Bcc)) {
            $emailBcc = $this->getDecodedEmailProperty($email, $email->Content->Headers->Bcc[0]);
        }
        return $emailBcc;
    }

    /**
     * Get Email Recipients
     *
     * Returns the string containing all of the recipients, such as To, CC and if provided BCC
     */
    protected function getEmailRecipients(object $email): string
    {
        $recipients = [];
        if (isset($email->Content->Headers->To)) {
            $recipients[] = $this->getEmailTo($email);
        }
        if (isset($email->Content->Headers->Cc)) {
            $recipients[] = $this->getEmailCC($email);
        }
        if (isset($email->Content->Headers->Bcc)) {
            $recipients[] = $this->getEmailBCC($email);
        }

        $recipients = implode(' ', $recipients);

        return $recipients;
    }

    /**
     * Get Email Sender
     *
     * Returns the string containing the sender of the email
     */
    protected function getEmailSender(object $email): string
    {
        return $this->getDecodedEmailProperty($email, $email->Content->Headers->From[0]);
    }

    /**
     * Get Email Reply To
     *
     * Returns the string containing the address to reply to
     */
    protected function getEmailReplyTo(object $email): string
    {
        return $this->getDecodedEmailProperty($email, $email->Content->Headers->{'Reply-To'}[0]);
    }

    /**
     * Get Email Priority
     *
     * Returns the priority of the email
     */
    protected function getEmailPriority(object $email): string
    {
        return $this->getDecodedEmailProperty($email, $email->Content->Headers->{'X-Priority'}[0]);
    }

    /**
     * Returns the decoded email property
     */
    protected function getDecodedEmailProperty(object $email, string $property): string
    {
        if ((string)$property != '') {
            if (!empty($email->Content->Headers->{'Content-Transfer-Encoding'}) &&
              in_array('quoted-printable', $email->Content->Headers->{'Content-Transfer-Encoding'})
            ) {
                $property = quoted_printable_decode($property);
            }
            if (!empty($email->Content->Headers->{'Content-Type'}[0]) &&
                strpos($email->Content->Headers->{'Content-Type'}[0], 'multipart/mixed') !== false
            ) {
                $property = quoted_printable_decode($property);
            }
            if (strpos($property, '=?utf-8?Q?') !== false && extension_loaded('mbstring')) {
                $property = mb_decode_mimeheader($property);
            }
        }
        return $property;
    }

    /**
     * Set Current Inbox
     *
     * Sets the current inbox to work on, also create a copy of it to handle unread emails
     *
     * @param array<object> $inbox Inbox
     */
    protected function setCurrentInbox(array $inbox): void
    {
        $this->currentInbox = $inbox;
        $this->unreadInbox = $inbox;
    }

    /**
     * Get Current Inbox
     *
     * Returns the complete current inbox
     *
     * @return array<object> Current Inbox
     */
    protected function getCurrentInbox(): array
    {
        return $this->currentInbox;
    }

    /**
     * Get Unread Inbox
     *
     * Returns the inbox containing unread emails
     *
     * @return array<object> Unread Inbox
     */
    protected function getUnreadInbox(): array
    {
        return $this->unreadInbox;
    }

    /**
     * Sort Emails
     *
     * Sorts the inbox based on the timestamp
     *
     * @param array<object> $inbox Inbox to sort
     */
    protected function sortEmails(array $inbox): void
    {
        usort($inbox, [$this, 'sortEmailsByCreationDatePredicate']);
    }

    /**
     * Get Email To
     *
     * Returns the string containing the persons included in the To field
     */
    public static function sortEmailsByCreationDatePredicate(object $emailA, object $emailB): int
    {
        $sortKeyA = $emailA->Content->Headers->Date;
        $sortKeyB = $emailB->Content->Headers->Date;
        return ($sortKeyA > $sortKeyB) ? -1 : 1;
    }
}
