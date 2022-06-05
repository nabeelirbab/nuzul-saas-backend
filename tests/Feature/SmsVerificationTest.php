<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SmsVerificationTest extends TestCase
{
    /**
     * As a User, I should be able to get a code to my phone number, so that I can use it for verification.
     */
    public function testUserCanGenerateSMSUsingMobile()
    {
        $this->withoutExceptionHandling();
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);
    }

    /**
     * As a User, I should be able to use the code to verify my phone number, so that I can get a valid token.
     */
    public function testUserCanVerifyUsingMobileAndCode()
    {
        $phoneNumber = '+966501111112';
        $response = $this->post('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        $code = '1111';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);

        $response->assertSuccessful();
        $response->assertJsonStructure(['token']);
    }

    /**
     * User should not be able to verify using the code if it was used before.
     */
    public function testUserCanNotGenerateSMSUsingMobile()
    {
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        $code = '1111';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);

        $response->assertSuccessful();
        $response->assertJsonStructure(['token']);

        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);

        $response->assertStatus(422);
        $response->assertSeeText('Code expired, request another code.');
    }

    /**
     * As a User, I should be able to see a validation message when entering a wrong code, so that I can get a correct it.
     */
    public function testUserCanNotVerifyUsingMobileAndWrongCode()
    {
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        $code = '2222';
        $response = $this->post('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);

        $response->assertStatus(422);
    }

    /**
     * User should not be able use the correct code after the 3rd attempt.
     */
    public function testUserCanNotVerifyUsingMobileAndCodeAfter3rdAttmpt()
    {
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        $code = '4444';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);
        $response->assertStatus(422);
        $response->assertSeeText('Wrong code.');

        $code = '3333';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);
        $response->assertStatus(422);
        $response->assertSeeText('Wrong code.');

        $code = '2222';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);
        $response->assertStatus(422);
        $response->assertSeeText('Wrong code.');

        $code = '1111';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);
        $response->assertStatus(422);

        $response->assertSeeText('Attempts exceeded, request another code.');
    }

    /**
     * User should not be able to request a code without a phone number.
     */
    public function testUserCanNotVerifyWithoutUsingMobile()
    {
        $phoneNumber = '';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertStatus(422);
    }

    /**
     * User should not be able to verify a code without a phone number.
     */
    public function testUserCanNotVerifyWithoutUsingMobileWithCode()
    {
        $phoneNumber = '';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertStatus(422);

        $code = '1111';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);
        $response->assertStatus(422);
    }

    /**
     * User should not be able to verify a phone number without a code.
     */
    public function testUserCanNotVerifyUsingMobileWithoutCode()
    {
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        $code = '';
        $response = $this->postJson('/api/verify-code', ['mobile_number' => $phoneNumber, 'code' => $code]);
        $response->assertStatus(422);
    }

    /**
     * User should not be able to request more than 1 code for the same phone number for a period of 30 seconds.
     */
    public function testWithin15SecondsUserCanNotGenerateSMSUsingMobile()
    {
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertStatus(403);
        $response->assertSeeText('You cannot send verification now');
    }

    /**
     * User should be able to request more than 1 code for the same phone number after a period of 30 seconds.
     */
    public function testAfter15SecondsUserCanGenerateSMSUsingMobile()
    {
        $phoneNumber = '+966501111112';
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);

        for ($i = 1; $i <= 3; ++$i) {
            dump('Waiting for '.$i.'/3 Seconds');
            sleep(1);
        }

        sleep(1);
        $response = $this->postJson('/api/send-sms', ['mobile_number' => $phoneNumber]);
        $response->assertSuccessful();
        static::assertSame($response->json()['mobile_number'], $phoneNumber);
    }
}
