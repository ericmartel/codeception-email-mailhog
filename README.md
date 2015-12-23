# MailHog Service Provider for the Codeception Email Testing Framework

This Codeception Module implements the required methods to test emails using the [Codeception Email Testing Framework][CodeceptionEmailTestingFramework] with [MailHog]

### Added Methods
This Module adds a few public methods for the user, such as:
```
deleteAllEmails()
```
Deletes all emails in MailHog
```
fetchEmails()
```
Fetches all email headers from MailHog, sorts them by timestamp and assigns them to the current and unread inboxes
```
accessInboxFor($address)
```
Filters emails to only keep those that are received by the provided address
```
openNextUnreadEmail()
```
Pops the most recent unread email and assigns it as the email to conduct tests on

### Example Test
Here is a simple scenario where we test the content of an email.  For a detailed list of all available test methods, please refer to the [Codeception Email Testing Framework][CodeceptionEmailTestingFramework].
```
<?php 
$I = new FunctionalTester($scenario);
$I->am('a member');
$I->wantTo('request a reset password link');

// First, remove all existing emails in the MailHog inbox
$I->deleteAllEmails();

// Implementation is up to the user, use this as an example
$I->requestAPasswordResetLink();

// Query MailHog and fetch all available emails
$I->fetchEmails();

// This is optional, but will filter the emails in case you're sending multiple emails or use the BCC field
$I->accessInboxFor('testuser@example.com');

// A new email should be available and it should be unread
$I->haveEmails();
$I->haveUnreadEmails();

// Set the next unread email as the email to perform operations on
$I->openNextUnreadEmail();

// After opening the only available email, the unread inbox should be empty
$I->dontHaveUnreadEmails();

// Validate the content of the opened email, all of these operations are performed on the same email
$I->seeInOpenedEmailSubject('Your Password Reset Link');
$I->seeInOpenedEmailBody('Follow this link to reset your password');
$I->seeInOpenedEmailRecipients('testuser@example.com');
```

### License
Copyright (c) 2015 Eric Martel, http://github.com/ericmartel <emartel@gmail.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

   [MailHog]: https://github.com/mailhog/MailHog
   [CodeceptionEmailTestingFramework]: https://github.com/ericmartel/codeception-email
   

