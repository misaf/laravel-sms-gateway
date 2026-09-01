<?php

declare(strict_types=1);

use Misaf\LaravelSmsGateway\Tests\TestCase;
use Misaf\LaravelSmsGatewayGhasedak\Tests\ReversedOrderTestCase as GhasedakReversedOrderTestCase;
use Misaf\LaravelSmsGatewayGhasedak\Tests\TestCase as GhasedakTestCase;
use Misaf\LaravelSmsGatewayIppanel\Tests\ReversedOrderTestCase as IppanelReversedOrderTestCase;
use Misaf\LaravelSmsGatewayIppanel\Tests\TestCase as IppanelTestCase;
use Misaf\LaravelSmsGatewayKavenegar\Tests\ReversedOrderTestCase as KavenegarReversedOrderTestCase;
use Misaf\LaravelSmsGatewayKavenegar\Tests\TestCase as KavenegarTestCase;
use Misaf\LaravelSmsGatewayMagfa\Tests\ReversedOrderTestCase as MagfaReversedOrderTestCase;
use Misaf\LaravelSmsGatewayMagfa\Tests\TestCase as MagfaTestCase;
use Misaf\LaravelSmsGatewayMelipayamak\Tests\ReversedOrderTestCase as MelipayamakReversedOrderTestCase;
use Misaf\LaravelSmsGatewayMelipayamak\Tests\TestCase as MelipayamakTestCase;
use Misaf\LaravelSmsGatewayMessageBird\Tests\ReversedOrderTestCase as MessageBirdReversedOrderTestCase;
use Misaf\LaravelSmsGatewayMessageBird\Tests\TestCase as MessageBirdTestCase;
use Misaf\LaravelSmsGatewayPlivo\Tests\ReversedOrderTestCase as PlivoReversedOrderTestCase;
use Misaf\LaravelSmsGatewayPlivo\Tests\TestCase as PlivoTestCase;
use Misaf\LaravelSmsGatewaySmsIr\Tests\ReversedOrderTestCase as SmsIrReversedOrderTestCase;
use Misaf\LaravelSmsGatewaySmsIr\Tests\TestCase as SmsIrTestCase;
use Misaf\LaravelSmsGatewaySunway\Tests\ReversedOrderTestCase as SunwayReversedOrderTestCase;
use Misaf\LaravelSmsGatewaySunway\Tests\TestCase as SunwayTestCase;
use Misaf\LaravelSmsGatewayTextlocal\Tests\ReversedOrderTestCase as TextlocalReversedOrderTestCase;
use Misaf\LaravelSmsGatewayTextlocal\Tests\TestCase as TextlocalTestCase;
use Misaf\LaravelSmsGatewayTwilio\Tests\ReversedOrderTestCase as TwilioReversedOrderTestCase;
use Misaf\LaravelSmsGatewayTwilio\Tests\TestCase as TwilioTestCase;
use Misaf\LaravelSmsGatewayVonage\Tests\ReversedOrderTestCase as VonageReversedOrderTestCase;
use Misaf\LaravelSmsGatewayVonage\Tests\TestCase as VonageTestCase;

pest()->extend(TestCase::class)->in('Feature');

pest()->extend(GhasedakTestCase::class)->in('../Drivers/laravel-sms-gateway-ghasedak/tests/Feature');
pest()->extend(IppanelTestCase::class)->in('../Drivers/laravel-sms-gateway-ippanel/tests/Feature');
pest()->extend(KavenegarTestCase::class)->in('../Drivers/laravel-sms-gateway-kavenegar/tests/Feature');
pest()->extend(MagfaTestCase::class)->in('../Drivers/laravel-sms-gateway-magfa/tests/Feature');
pest()->extend(MelipayamakTestCase::class)->in('../Drivers/laravel-sms-gateway-melipayamak/tests/Feature');
pest()->extend(MessageBirdTestCase::class)->in('../Drivers/laravel-sms-gateway-messagebird/tests/Feature');
pest()->extend(PlivoTestCase::class)->in('../Drivers/laravel-sms-gateway-plivo/tests/Feature');
pest()->extend(SmsIrTestCase::class)->in('../Drivers/laravel-sms-gateway-smsir/tests/Feature');
pest()->extend(SunwayTestCase::class)->in('../Drivers/laravel-sms-gateway-sunway/tests/Feature');
pest()->extend(TextlocalTestCase::class)->in('../Drivers/laravel-sms-gateway-textlocal/tests/Feature');
pest()->extend(TwilioTestCase::class)->in('../Drivers/laravel-sms-gateway-twilio/tests/Feature');
pest()->extend(VonageTestCase::class)->in('../Drivers/laravel-sms-gateway-vonage/tests/Feature');

// Registration-order coverage boots the providers in the reverse order, so it
// needs its own base test case — and therefore its own directory.
pest()->extend(GhasedakReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-ghasedak/tests/Registration');
pest()->extend(IppanelReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-ippanel/tests/Registration');
pest()->extend(KavenegarReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-kavenegar/tests/Registration');
pest()->extend(MagfaReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-magfa/tests/Registration');
pest()->extend(MelipayamakReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-melipayamak/tests/Registration');
pest()->extend(MessageBirdReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-messagebird/tests/Registration');
pest()->extend(PlivoReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-plivo/tests/Registration');
pest()->extend(SmsIrReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-smsir/tests/Registration');
pest()->extend(SunwayReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-sunway/tests/Registration');
pest()->extend(TextlocalReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-textlocal/tests/Registration');
pest()->extend(TwilioReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-twilio/tests/Registration');
pest()->extend(VonageReversedOrderTestCase::class)->in('../Drivers/laravel-sms-gateway-vonage/tests/Registration');
