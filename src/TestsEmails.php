<?php

/*
 * This file is part of the Email test framework for Codeception.
 * (c) 2015-2016 Eric Martel
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Codeception\Module;

trait TestsEmails
{
    /**
     * Have Emails
     *
     * Checks if there are any emails in the inbox
     */
    public function haveEmails(): void
    {
        $currentInbox = $this->getCurrentInbox();
        $this->assertGreaterThan(0, count($currentInbox));
    }

    /**
     * Have Number Of Emails
     *
     * Checks that the amount of emails in the inbox is exactly $expected
     * @params int $expected Number of expected emails
     */
    public function haveNumberOfEmails(int $expected): void
    {
        $currentInbox = $this->getCurrentInbox();
        $this->assertEquals($expected, count($currentInbox));
    }

    /**
     * Dont Have Emails
     *
     * Checks that there are no emails in the inbox
     */
    public function dontHaveEmails(): void
    {
        $currentInbox = $this->getCurrentInbox();
        $this->assertEquals(0, count($currentInbox));
    }

    /**
     * Have Unread Emails
     *
     * Checks that there is at least one unread email
     **/
    public function haveUnreadEmails(): void
    {
        $unreadInbox = $this->getUnreadInbox();
        $this->assertGreaterThan(0, count($unreadInbox));
    }

    /**
     * Have Number Of Unread Emails
     *
     * Checks that the amount of emails in the unread inbox is exactly $expected
     * @params int $expected Number of expected emails
     */
    public function haveNumberOfUnreadEmails(int $expected): void
    {
        $unreadInbox = $this->getUnreadInbox();
        $this->assertEquals($expected, count($unreadInbox));
    }

    /**
     * Dont Have Unread Emails
     *
     * Checks that there are no unread emails in the inbox
     */
    public function dontHaveUnreadEmails(): void
    {
        $unreadInbox = $this->getUnreadInbox();
        $this->assertEquals(0, count($unreadInbox));
    }

    /**
     * See In Opened Email Body
     *
     * Validates that $expected can be found in the opened email body
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailBody(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailBody($email, $expected);
    }

    /**
     * See In Opened Email Subject
     *
     * Validates that $expected can be found in the opened email subject
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailSubject(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailSubject($email, $expected);
    }

    /**
     * Dont See In Opened Email Body
     *
     * Checks that $expected cannot be found in the opened email body
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailBody(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailBody($email, $expected);
    }

    /**
     * Dont See In Opened Email Subject
     *
     * Checks that $expected cannot be found in the opened email subject
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailSubject(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailSubject($email, $expected);
    }

    /**
     * See In Email Body
     *
     * Checks that the body of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailBody(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailBody($email), 'Email Body Contains');
    }

    /**
     * Dont See In Email Body
     *
     * Checks that the body of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailBody(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailBody($email), "Email Body Doesn't Contain");
    }

    /**
     * See In Email Subject
     *
     * Checks that the subject of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailSubject(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailSubject($email), 'Email Subject Contains');
    }

    /**
     * Dont See In Email Subject
     *
     * Checks that the subject of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailSubject(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailSubject($email), "Email Subject Doesn't Contain");
    }

    /**
     * See In Opened Email Sender
     *
     * Checks if the sender of the opened email contains $expected
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailSender(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailSender($email, $expected);
    }

    /**
     * Dont See In Opened Email Sender
     *
     * Checks if the sender of the opened email does not contain $expected
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailSender(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailSender($email, $expected);
    }

    /**
     * See In Email Sender
     *
     * Checks if the sender of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailSender(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailSender($email));
    }

    /**
     * Dont See In Email Sender
     *
     * Checks if the sender of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailSender(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailSender($email));
    }

    /**
     * See In Opened Email Reply To
     *
     * Checks if the ReplyTo of the opened email contains $expected
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailReplyTo(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailReplyTo($email, $expected);
    }

    /**
     * Dont See In Opened Email Reply To
     *
     * Checks if the ReplyTo of the opened email does not contain $expected
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailReplyTo(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailReplyTo($email, $expected);
    }

    /**
     * See In Email Reply To
     *
     * Checks if the ReplyTo of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailReplyTo(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailReplyTo($email));
    }

    /**
     * Dont See In Email Reply To
     *
     * Checks if the ReplyTo of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailReplyTo(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailReplyTo($email));
    }

    /**
     * See In Opened Email Recipients
     *
     * Checks that the recipients of the opened email contain $expected
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailRecipients(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailRecipients($email, $expected);
    }

    /**
     * Dont See In Opened Email Recipients
     *
     * Checks that the recipients of the opened email do not contain $expected
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailRecipients(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailRecipients($email, $expected);
    }

    /**
     * See In Email Recipients
     *
     * Checks that the recipients of $email contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailRecipients(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailRecipients($email));
    }

    /**
     * Dont See In Email Recipients
     *
     * Checks that the recipients of $email do not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailRecipients(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailRecipients($email));
    }

    /**
     * See In Opened Email To Field
     *
     * Checks that the To field of the opened email contains $expected
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailToField(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailToField($email, $expected);
    }

    /**
     * Dont See In Opened Email To Field
     *
     * Checks that the To field of the opened email does not contain $expected
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailToField(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailToField($email, $expected);
    }

    /**
     * See In Email To Field
     *
     * Checks that the To field of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailToField(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailTo($email));
    }

    /**
     * Dont See In Email To Field
     *
     * Checks that the To field of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailToField(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailTo($email));
    }

    /**
     * See In Opened Email CC Field
     *
     * Checks that the CC field of the opened email contains $expected
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailCCField(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailCCField($email, $expected);
    }

    /**
     * Dont See In Opened Email CC Field
     *
     * Checks that the CC field of the opened email does not contain $expected
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailCCField(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailCCField($email, $expected);
    }

    /**
     * See In Email CC Field
     *
     * Checks that the CC field of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailCCField(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailCC($email));
    }

    /**
     * Dont See In Email CC Field
     *
     * Checks that the CC field of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailCCField(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailCC($email));
    }

    /**
     * See In Opened Email BCC Field
     *
     * Checks that the BCC field of the opened email contains $expected
     *
     * Warning: it is possible for an email to have its BCC field empty, it doesn't mean that another instance of the same email doesn't exist.
     *
     * @param string $expected Text
     */
    public function seeInOpenedEmailBCCField(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailBCCField($email, $expected);
    }

    /**
     * Dont See In Opened Email BCC Field
     *
     * Checks that the BCC field of the opened email does not contain $expected
     *
     * Warning: it is possible for an email to have its BCC field empty, it doesn't mean that another instance of the same email doesn't exist.
     *
     * @param string $expected Text
     */
    public function dontSeeInOpenedEmailBCCField(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailBCCField($email, $expected);
    }

    /**
     * See In Email BCC Field
     *
     * Checks that the BCC field of $email contains $expected
     *
     * Warning: it is possible for an email to have its BCC field empty, it doesn't mean that another instance of the same email doesn't exist.
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function seeInEmailBCCField(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailBCC($email));
    }

    /**
     * Dont See In Email BCC Field
     *
     * Checks that the BCC field of $email does not contain $expected
     *
     * Warning: it is possible for an email to have its BCC field empty, it doesn't mean that another instance of the same email doesn't exist.
     *
     * @param object $email a JSON encoded email
     * @param string $expected Text
     */
    public function dontSeeInEmailBCCField(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailBCC($email));
    }

    /**
     * See In Opened Email Priority
     *
     * Checks that the priority of the opened email contains $expected
     *
     * @param string $expected priority
     */
    public function seeInOpenedEmailPriority(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->seeInEmailPriority($email, $expected);
    }

    /**
     * Dont See In Opened Email Priority
     *
     * Checks that the priority of the opened email does not contain $expected
     *
     * @param string $expected priority
     */
    public function dontSeeInOpenedEmailPriority(string $expected): void
    {
        $email = $this->getOpenedEmail();
        $this->dontSeeInEmailPriority($email, $expected);
    }

    /**
     * See In Email Priority
     *
     * Checks that the priority of $email contains $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected priority
     */
    public function seeInEmailPriority(object $email, string $expected): void
    {
        $this->assertStringContainsString($expected, $this->getEmailPriority($email));
    }

    /**
     * Dont See In Email Priority
     *
     * Checks that the priority of $email does not contain $expected
     *
     * @param object $email a JSON encoded email
     * @param string $expected priority
     */
    public function dontSeeInEmailPriority(object $email, string $expected): void
    {
        $this->assertStringNotContainsString($expected, $this->getEmailPriority($email));
    }

    /**
     * @param string $content_type_alternative      MIME-part Content-Type
     * @return null|string Body
     */
    public function grabBodyFromEmail(string $content_type_alternative = null): ?string
    {
        $email = $this->getOpenedEmail();

        if (isset($content_type_alternative)) {
            foreach ($email->MIME->Parts as $part) {
                if (!empty($part->Headers->{'Content-Type'}[0]) &&
                    strpos($part->Headers->{'Content-Type'}[0], $content_type_alternative) !== false) {
                    return $this->getDecodedEmailProperty($part, $part->Body);  //return first entry
                }
            }
            return null;                                                        //not found
        }
        return $this->getDecodedEmailProperty($email, $email->Content->Body);   //return full email
    }
}
