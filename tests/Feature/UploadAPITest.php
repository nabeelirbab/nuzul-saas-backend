<?php

namespace Tests\Feature;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class UploadAPITest extends TestCase
{
    public function testUserCanRequestPresignedURL()
    {
        // $access = new Credentials(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));
        // $s3Client = new S3Client([
        //     'credentials' => $access,
        //     'region' => env('AWS_DEFAULT_REGION'),
        //     'version' => '2006-03-01',
        // ]);
        // $uuid = Uuid::uuid4();

        // // Creating a presigned URL
        // $cmd = $s3Client->getCommand('putObject', [
        //     'Bucket' => env('AWS_BUCKET'),
        //     'Key' => 'tenants/3/'.$uuid,
        // ]);

        // // dd($cmd);

        // // dd($uuid);
        // $request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

        // // Get the actual presigned-url
        // $presignedUrl = (string) $request->getUri();
        // dd($presignedUrl);
    }
}
