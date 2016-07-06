<?php

namespace tests\spec\App\Api\v1\Components;

use App\Alert;
use App\Api\v1\Components\AlertMailer;
use Illuminate\Mail\Mailer;
use PhpSpec\Laravel\LaravelObjectBehavior;
use Prophecy\Argument;

/**
 * Class AlertMailerSpec
 * @package tests\spec\App\Api\v1\Components
 * @mixin AlertMailer
 */
class AlertMailerSpec extends LaravelObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('App\Api\v1\Components\AlertMailer');
    }

    function it_implements_the_alert_mailer_interface()
    {
        $this->shouldHaveType('App\Api\v1\Interfaces\AlertMailerInterface');
    }

    function it_initializes_without_errors()
    {
        $this->shouldNotHaveErrors();
    }
    
    function it_can_send_two_emails(Mailer $mailer)
    {
        $mailer->send(Argument::type('string'), Argument::type('array'), Argument::type('Closure'))->willReturn();
        $mailer->failures()->willReturn([]);
        $this->setMailer($mailer);

        $alert = new Alert();
        $alert->alert_email_recipient = 'to_one@example.com,to_two@example.com';
        $alert->alert_email_sender = 'from@example.com';
        $alert->description = __METHOD__;
        $this->sendAlertMail($alert, __METHOD__)->shouldBe(true);
        $this->shouldNotHaveErrors();
    }

    function it_reports_failures(Mailer $mailer)
    {
        $failEmail = ['i_fail@example.com'];
        $mock = [[
            'message' => 'Failed to deliver email',
            'details' => $failEmail,
        ],];
        $mailer->send(Argument::type('string'), Argument::type('array'), Argument::type('Closure'))->willReturn();
        $mailer->failures()->willReturn($failEmail);
        $this->setMailer($mailer);

        $this->sendAlertMail(new Alert, __METHOD__)->shouldBe(false);
        $this->shouldHaveErrors();
        $this->getErrors()->shouldReturn($mock);
    }

    function it_throws_error_when_description_is_empty()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('sendAlertMail', [new Alert, '']);
    }

    function it_detects_single_email()
    {
        $this->hasMultipleRecipients('one@example.com')->shouldReturn(false);
    }

    function it_detects_multiple_emails()
    {
        $this->hasMultipleRecipients('one@example.com,two@example.com')->shouldReturn(true);
    }

    function it_returns_errors()
    {
        $this->getErrors()->shouldBeArray();
    }

    function it_converts_one_email_into_array()
    {
        $this->emailStringToArray('foo@bar.baz')->shouldReturn(['foo@bar.baz']);
    }

    function it_converts_multiple_emails_into_array()
    {
        $ret = $this->emailStringToArray('foo@bar.baz,a@b.c');
        //var_dump(get_class($ret));
        $ret->shouldReturn(['foo@bar.baz', 'a@b.c']);
    }

    function it_returns_a_mailer()
    {
        $this->getMailer()->shouldHaveType(Mailer::class);
    }

    function it_can_accept_a_mailer(Mailer $mailer)
    {
        $this->setMailer($mailer);
    }

    function it_returns_a_default_mailer()
    {
        $this->getDefaultMailer()->shouldHaveType(Mailer::class);
    }

}
