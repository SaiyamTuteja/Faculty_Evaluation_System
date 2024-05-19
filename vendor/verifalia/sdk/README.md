![Verifalia API](https://img.shields.io/badge/Verifalia%20API-v2.5-green)
[![Packagist](https://img.shields.io/packagist/v/verifalia/sdk.svg?maxAge=2592000)](http://packagist.org/packages/verifalia/sdk)

Verifalia API - PHP SDK and helper library
==========================================

This SDK library integrates with [Verifalia](https://verifalia.com) and allows to [verify email addresses](https://verifalia.com)
in **PHP v7.0 and higher**.

[Verifalia](https://verifalia.com/) is an online service that provides email verification and mailing list cleaning; it helps businesses reduce
their bounce rate, protect their sender reputation, and ensure their email campaigns reach the intended recipients.
Verifalia can [verify email addresses](https://verifalia.com/) in real-time and in bulk, using its API or client area; it also
offers various features and settings to customize the verification process according to the user’s needs.

Verifalia's email verification process consists of several steps, each taking fractions of a second: it checks the **formatting
and syntax** (RFC 1123, RFC 2821, RFC 2822, RFC 3490, RFC 3696, RFC 4291, RFC 5321, RFC 5322, and RFC 5336) of each email address,
the **domain and DNS records**, the **mail exchangers**, and the **mailbox existence**, with support for internationalized domains
and mailboxes. It also detects risky email types, such as **catch-all**, **disposable**, or **spam traps** / **honeypots**.

Verifalia provides detailed and **accurate reports** for each email verification: it categorizes each email address as `Deliverable`,
`Undeliverable`, `Risky`, or `Unknown`, and assigns one of its exclusive set of over 40 [status codes](https://verifalia.com/developers#email-validations-status-codes).
It also explains the undeliverability reason and provides **comprehensive verification details**. The service allows the user to choose the desired
quality level, the waiting timeout, the deduplication preferences, the data retention settings, and the callback preferences
for each verification.

Of course, Verifalia never sends emails to the contacts or shares the user's data with anyone.

To learn more about Verifalia please see [https://verifalia.com](https://verifalia.com/)

## Table of contents

* [Getting started](#getting-started)
  * [Naming conventions](#naming-conventions-)
  * [Authentication](#authentication)
    * [Authenticating via Basic Auth](#authenticating-via-basic-auth)
    * [Authenticating via bearer token](#authenticating-via-bearer-token)
    * [Authenticating via X.509 client certificate (TLS mutual authentication)](#authenticating-via-x509-client-certificate-tls-mutual-authentication)
* [Validating email addresses](#validating-email-addresses)
  * [How to validate / verify an email address](#how-to-validate--verify-an-email-address)
  * [How to validate / verify a list of email addresses](#how-to-validate--verify-a-list-of-email-addresses)
  * [Processing options](#processing-options)
    * [Quality level](#quality-level)
    * [Deduplication mode](#deduplication-mode)
    * [Data retention](#data-retention)
  * [Wait options](#wait-options)
    * [Avoid waiting](#avoid-waiting)
    * [Progress tracking](#progress-tracking)
  * [Completion callbacks](#completion-callbacks)
  * [Retrieving jobs](#retrieving-jobs)
  * [Don't forget to clean up, when you are done](#dont-forget-to-clean-up-when-you-are-done)
* [Managing credits](#managing-credits-)
  * [Getting the credits balance](#getting-the-credits-balance-)
* [Changelog / What's new](#changelog--whats-new)
  * [v3.0](#v30)

## Getting started

The most efficient way to add the Verifalia email verification library into your PHP project is by using [composer](https://getcomposer.org),
which will automatically download and install the required files [from Packagist](http://packagist.org/packages/verifalia/sdk). With composer installed,
run the following command from your project's root directory:

```bash
php composer.phar require verifalia/sdk
```

For Windows users, the alternative command to run is:

```batch
composer require verifalia/sdk
```

### Naming conventions ###

> This package follows the `PSR-4` convention names for its classes, meaning you can even load them easily with your own
> autoloader.

### Authentication

First things first: authentication to the Verifalia API is performed by way of either
the credentials of your root Verifalia account or of one of your users (previously
known as sub-accounts): if you don't have a Verifalia account, just [register for a free one](https://verifalia.com/sign-up). For security reasons,
it is always advisable to [create and use a dedicated user](https://verifalia.com/client-area#/users/new) for accessing the API, as doing so will allow to assign
only the specific needed permissions to it.

Learn more about authenticating to the Verifalia API at [https://verifalia.com/developers#authentication](https://verifalia.com/developers#authentication)

#### Authenticating via Basic Auth

The most straightforward method for authenticating against the Verifalia API involves using a username and password pair.
These credentials can be applied during the creation of a new instance of the `VerifaliaRestClient` class, serving as the
initial step for all interactions with the Verifalia API: the provided username and password will be automatically
transmitted to the API using the HTTP Basic Auth method.

```php
use Verifalia\VerifaliaRestClient;
use Verifalia\VerifaliaRestClientOptions;

$verifalia = new VerifaliaRestClient([
    VerifaliaRestClientOptions::USERNAME => 'your-username-here',
    VerifaliaRestClientOptions::PASSWORD => 'your-password-here'
]);
```

#### Authenticating via bearer token

Bearer authentication offers higher security over HTTP Basic Auth, as the latter requires sending the actual credentials
on each API call, while the former only requires it on a first, dedicated authentication request. On the other side, the
first authentication request needed by Bearer authentication takes a non-negligible time.

> ⚠️ If you need to perform only a single request, **using HTTP Basic Auth** (see above) **provides the same degree of security and is
> also faster**.

```php
use Verifalia\VerifaliaRestClient;
use Verifalia\VerifaliaRestClientOptions;
use Verifalia\Security\BearerAuthenticationProvider;

$verifalia = new VerifaliaRestClient([
    VerifaliaRestClientOptions::AUTHENTICATION_PROVIDER => 
        new BearerAuthenticationProvider('your-username-here', 'your-password-here')
]);
```

#### Authenticating via X.509 client certificate (TLS mutual authentication)

In addition to the aforementioned authentication methods, this SDK also supports using a cryptographic X.509 client
certificate to authenticate against the Verifalia API, through the TLS protocol. This method, also
called mutual TLS authentication (mTLS) or two-way authentication, offers the highest degree of
security, as only a cryptographically-derived key (and not the actual credentials) is sent over
the wire on each request. [What is X.509 TLS client-certificate authentication?](https://verifalia.com/help/sub-accounts/what-is-x509-tls-client-certificate-authentication)

```php
use Verifalia\VerifaliaRestClient;
use Verifalia\VerifaliaRestClientOptions;

$verifalia = new VerifaliaRestClient([
    VerifaliaRestClientOptions::CERTIFICATE => '/home/gfring/Documents/pollos.pem'
]);
```

## Validating email addresses

Every operation related to verifying / validating email addresses is performed through the `emailValidations` field 
exposed by the instance of the `VerifaliaRestClient` class you created above. The property exposes some useful functions:
in the next few paragraphs we are looking at the most used ones, so it is strongly advisable to explore the library and
look at the embedded help for other opportunities.

**The library automatically waits for the completion of email verification jobs**: if needed, it is possible to adjust
the wait options and have more control over the entire underlying polling process. Please refer to the [Wait options](#wait-options)
section below for additional details.

### How to validate / verify an email address

To validate an email address from a PHP application you can invoke the `submit()` method: it accepts one or more email
addresses and any eventual verification options you wish to pass to Verifalia, including the expected results quality,
deduplication preferences, processing priority.

> Note In the event you need to verify a list of email addresses, it is advisable to submit them all at once through the
> `submit()` method (see the next sections), instead of iterating over the source set and submitting the addresses one 
> by one. Not only the all-at-once method would be faster, it would also allow to detect and mark duplicated items - a
> feature which is unavailable while verifying the email addresses one by one.

In the following example, we verify an email address with this library, using the default options:

```php
use Verifalia\VerifaliaRestClient;

$verifalia = new VerifaliaRestClient(...); // See above

// Verifies an email address

$job = $verifalia->emailValidations->submit('batman@gmail.com');

// Print some results

$entry = $job->entries[0];

echo 'Classification: ' . $entry->classification;
echo 'Status: ' . $entry->status;

// Output:
// Classification: Deliverable
// Status: Success
```

Once `submit()` completes successfully, the resulting verification job
is guaranteed to be completed and its results' data (e.g. its `entries` field) to be available for use.

As you may expect, each entry may include various additional details about the verified email address:

| Attribute                     | Description                                                                                                                                                                                                                                                      |
|-------------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `asciiEmailAddressDomainPart` | Gets the domain part of the email address, converted to ASCII if needed and with comments and folding white spaces stripped off.                                                                                                                                 |
| `classification`              | A string with the classification for this entry; see the `ValidationEntyClassification` class for a list of the values supported at the time this SDK has been released.                                                                                         |
| `completedOn`                 | The date this entry has been completed, if available.                                                                                                                                                                                                            |
| `custom`                      | A custom, optional string which is passed back upon completing the validation. To pass back and forth a custom value, use the `custom` field of `ValidationRequestEntry`.                                                                                        |
| `duplicateOf`                 | The zero-based index of the first occurrence of this email address in the parent `Validation`, in the event the `status` field for this entry is `Duplicate`; duplicated items do not expose any result detail apart from this and the eventual `custom` values. |
| `index`                       | The index of this entry within its `Validation` container; this property is mostly useful in the event the API returns a filtered view of the items.                                                                                                             |
| `inputData`                   | The input string being validated.                                                                                                                                                                                                                                |
| `emailAddress`                | Gets the email address, without any eventual comment or folding white space. Returns null if the input data is not a syntactically invalid e-mail address.                                                                                                       |
| `emailAddressDomainPart`      | Gets the domain part of the email address, without comments and folding white spaces.                                                                                                                                                                            |
| `emailAddressLocalPart`       | Gets the local part of the email address, without comments and folding white spaces.                                                                                                                                                                             |
| `hasInternationalDomainName`  | If true, the email address has an international domain name.                                                                                                                                                                                                     |
| `hasInternationalMailboxName` | If true, the email address has an international mailbox name.                                                                                                                                                                                                    |
| `isDisposableEmailAddress`    | If true, the email address comes from a disposable email address (DEA) provider. <a href="https://verifalia.com/help/email-validations/what-is-a-disposable-email-address-dea">What is a disposable email address?</a>                                           |
| `isFreeEmailAddress`          | If true, the email address comes from a free email address provider (e.g. gmail, yahoo, outlook / hotmail, ...).                                                                                                                                                 |
| `isRoleAccount`               | If true, the local part of the email address is a well-known role account.                                                                                                                                                                                       |
| `status`                      | The status for this entry; see the `ValidationEntryStatus` class for a list of the values supported at the time this SDK has been released.                                                                                                                      |
| `suggestions`                 | The potential corrections for the input data, in the event Verifalia identified potential typos during the verification process.                                                                                                                                 |
| `syntaxFailureIndex`          | The position of the character in the email address that eventually caused the syntax validation to fail.                                                                                                                                                         |

Here is another example, showing some of the additional result details provided by Verifalia:

```php
use Verifalia\VerifaliaRestClient;

$verifalia = new VerifaliaRestClient(...); // See above

// Verifies an email address

$job = $verifalia->emailValidations->submit('bat[man@gmal.com');

// Print some results

$entry = $job->entries[0];

echo 'Classification: ' . $entry->classification . "\n";
echo 'Status: ' . $entry->status . "\n";
echo 'Syntax failure index: ' . $entry->syntaxFailureIndex . "\n";

if (!empty(entry->suggestions)) {
    echo "Suggestions\n";
    
    foreach ($entry->suggestions as $suggestion) {
        echo '- ' . $suggestion . "\n";
    }
}

// Output:
// Classification: Undeliverable
// Status: InvalidCharacterInSequence
// Syntax failure index: 3
// Suggestions:
// - batman@gmail.com
```

### How to validate / verify a list of email addresses

To verify a list of email addresses you can still call the `submit()` function, which also accepts an array of strings
with the email addresses to verify:

```php
use Verifalia\VerifaliaRestClient;

$verifalia = new VerifaliaRestClient(...); // See above

// Verifies the list of email addresses

$job = $verifalia->emailValidations->submit([
    'batman@gmail.com',
    'steve.vai@best.music',
    'samantha42@yahoo.de',
]);

// Print some results

foreach ($job->entries as $entry) {
    echo $entry->emailAddress . ' => ' . $entry->classification;
}

// Output:
// batman@gmail.com => Deliverable
// steve.vai@best.music => Undeliverable
// samantha42@yahoo.de => Deliverable
```

### Processing options

While submitting one or more email addresses for verification, it is possible to specify several
options which affect the behavior of the Verifalia processing engine as well as the verification flow
from the API consumer standpoint.

#### Quality level

Verifalia offers three distinct quality levels - namely, _Standard_, _High_ and _Extreme_  - which rule out how the email
verification engine should deal with temporary undeliverability issues, with slower mail exchangers and other potentially transient
problems which can affect the quality of the verification results. The `ValidationRequest` class accepts a `quality` field which allows
to specify the desired quality level; here is an example showing how to verify an email address using
the _High_ quality level:

```php
$request = new ValidationRequest('batman@gmail.com');
$request->quality = QualityLevelName::HIGH;

$job = $verifalia->emailValidations->submit($request);
```

#### Deduplication mode

The `submit()` method can also accept and verify multiple email addresses in bulk, and allows to specify how to
deal with duplicated entries pertaining to the same input set; Verifalia supports a _Safe_ deduplication
mode, which strongly adheres to the old IETF standards, and a _Relaxed_ mode which is more in line with
what can be found in the majority of today's mail exchangers configurations.

In the next example, we show how to import and verify a list of email addresses and mark duplicated
entries using the _Relaxed_ deduplication mode:

```php
$request = new ValidationRequest([
    'batman@gmail.com',
    'steve.vai@best.music',
    'samantha42@yahoo.de',
]);
$request->deduplication = DeduplicationMode::RELAXED;

$job = $verifalia->emailValidations->submit($request);
```

#### Data retention

Verifalia automatically deletes completed email verification jobs according to the data retention
policy defined at the account level, which can be eventually overridden at the user level: one can
use the [Verifalia clients area](https://verifalia.com/client-area) to configure these settings.

It is also possible to specify a per-job data retention policy which govern the time to live of a submitted
email verification job; to do that, set the `retention` field of the `ValidationRequest` instance accordingly.

Here is how, for instance, one can set a data retention policy of 10 minutes while verifying
an email address:

```php
$request = new ValidationRequest('batman@gmail.com');
$request->retention = DateInterval::createFromDateString('10 minutes');

$job = $verifalia->emailValidations->submit($request);
```

### Wait options

**By default, the `submit()` method submits an email verification job to Verifalia and waits
for its completion**; the entire process may require some time to complete depending on the plan of the
Verifalia account, the number of email addresses the submission contains, the specified quality level
and other network factors including the latency of the mail exchangers under test.

In waiting for the completion of a given email verification job, the library automatically polls the
underlying Verifalia API until the results are ready; by default, it tries to take advantage of the long
polling mode introduced with the Verifalia API v2.4, which allows to minimize the number of requests
and get the verification results faster.

#### Avoid waiting

In certain scenarios (in a microservice architecture, for example), however, it may be preferable to avoid
waiting for a job completion and ask the Verifalia API, instead, to just queue it: in that case, the library
would just return the job overview (and not its verification results) and it will be necessary to retrieve
the verification results using the `get()` method.

To do that, it is possible to specify `WaitOptions::$noWait` as the value for the `waitOptions` parameter
of the `submit()` method, as shown in the next example:

```php
$verifalia = new VerifaliaRestClient(...); // See above
$request = new ValidationRequest(...) // See above;

$job = $verifalia->emailValidations->submit($request, WaitOptions::$noWait);

echo 'Status: ' . $job->overview->status;

// Status: InProgress
```

#### Progress tracking

For jobs with a large number of email addresses, it could be useful to track progress as they are processed
by the Verifalia email verification engine; to do that, it is possible to create an instance of the
`WaitOptions` class and provide a callable which eventually receives progress notifications through the
`progress` field.

Here is how to define a progress notification handler which displays the progress percentage of a submitted
job to the console window:

```php
use Verifalia\EmailValidations\ValidationOverview;
use Verifalia\EmailValidations\WaitOptions;

$verifalia = new VerifaliaRestClient(...); // See above
$request = new ValidationRequest(...) // See above;

$waitOptions = new WaitOptions(function (ValidationOverview $overview) {
    echo 'Job status: ' . $overview->status;
    
    if ($overview->progress !== null) {
        echo 'Progress: ' . $overview->progress->percentage . '%';
    }
});

$job = $verifalia->emailValidations->submit($request, $waitOptions);
```

### Completion callbacks

Along with each email validation job, it is possible to specify a URL which
Verifalia will invoke (POST) once the job completes: this URL must use the HTTPS or HTTP
scheme and be publicly accessible over the Internet.
To learn more about completion callbacks, please see https://verifalia.com/developers#email-validations-completion-callback

To specify a completion callback URL, pass a `ValidationRequest` instance to the `submit()` method and set its `completionCallback`
field accordingly, as shown in the example below:

```php
$verifalia = new VerifaliaRestClient(...); // See above
$request = new ValidationRequest(...) // See above;

$request->completionCallback = new CompletionCallback('https://your-website-here/foo/bar');

$job = $verifalia->emailValidations->submit($request, $waitOptions);
```

Note that completion callbacks are invoked asynchronously, and it could take up to several seconds for your callback URL
to get invoked.

### Retrieving jobs

It is possible to retrieve a job through the `get()` method, which returns a `Validation` instance for the desired email
verification job. While doing that, the library automatically waits for the completion of the job, and it is possible to
adjust this behavior by passing to the aforementioned methods a `waitOptions` parameter, in the exactly same fashion as
described for the `submit()` method; please see the [Wait options](#wait-options) section for additional details.

Here is an example showing how to retrieve a job, given its identifier:

```php
$job = $verifalia->emailValidations->get('ec415ecd-0d0b-49c4-a5f0-f35c182e40ea');
```

### Don't forget to clean up, when you are done

Verifalia automatically deletes completed jobs after a configurable
data-retention policy (see the related section) but it is strongly advisable that
you delete your completed jobs as soon as possible, for privacy and security reasons. To do that, you can invoke the
`delete()` method passing the job Id you wish to get rid of: 

```php
$verifalia->emailValidations->delete('ec415ecd-0d0b-49c4-a5f0-f35c182e40ea');
```

Once deleted, a job is gone and there is no way to retrieve its email validation results.

## Managing credits ##

To manage the Verifalia credits for your account you can use the `credits` property exposed by the `VerifaliaRestClient`
instance created above.

### Getting the credits balance ###

One of the most common tasks you may need to perform on your account is retrieving the available number of free daily
credits and credit packs. To do that, you can use the `getBalance()` method, which returns a `Balance` object, as shown
in the next example:

```php
$balance = $verifalia
    ->credits
    ->getBalance();

echo 'Credit packs: ' . $balance->creditPacks . "\n";
echo 'Free daily credits: ' . $balance->freeCredits . "\n";
echo 'Free daily credits will reset in ' . $balance->freeCreditsResetIn . "\n";

// Prints out something like:
// Credit packs: 956.332
// Free daily credits: 128.66
// Free daily credits will reset in 09:08:23
```

To add credit packs to your Verifalia account visit [https://verifalia.com/client-area#/credits/add](https://verifalia.com/client-area#/credits/add).

## Changelog / What's new

This section lists the changelog for the current major version of the library: for older versions,
please see the [project releases](https://github.com/verifalia/verifalia-php-sdk/releases).

### v3.0

Released on February 1<sup>st</sup>, 2024

- Added support for Verifalia API v2.5
- Added support for classification override rules
- Added support for AI-powered suggestions
- Added support for client-certificate authentication
- Added support for bearer authentication
- Added support for completion callbacks
- Added support for submission and polling wait time
- Added PHPDoc annotations
- Improved sleeping time coercion while waiting for job completion
- Breaking change: minimum PHP version requirement increased to **PHP 7.0**
- Breaking change: the `submit()` method now, by default, waits for the email verification job to complete
- Breaking change: renamed `IAuthenticator` interface to `AuthenticationProvider`