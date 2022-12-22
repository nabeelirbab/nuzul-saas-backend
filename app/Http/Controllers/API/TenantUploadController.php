<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Uploads\Tenants\PresignedURLRequest;
use App\Models\Upload;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class TenantUploadController extends Controller
{
    public function requestPresignedURL(PresignedURLRequest $request)
    {
        $access = new Credentials(env('AWS_ACCESS_KEY_ID'), env('AWS_SECRET_ACCESS_KEY'));
        $s3Client = new S3Client([
            'credentials' => $access,
            'region' => env('AWS_DEFAULT_REGION'),
            'version' => '2006-03-01',
        ]);

        if ('property' === $request->model) {
            $uuid = Uuid::uuid4();
            $key = 'tenants/'.tenant()->id.'/properties/'.$request->reference_id.'/'.$uuid.'.'.$request->extension;
            // Creating a presigned URL
            $cmd = $s3Client->getCommand('putObject', [
                'Bucket' => env('AWS_BUCKET'),
                'Key' => $key,
            ]);
        }

        $awsRequest = $s3Client->createPresignedRequest($cmd, '+20 minutes');

        // Get the actual presigned-url
        $presignedUrl = (string) $awsRequest->getUri();

        return response()->json([
            'presigned_url' => $presignedUrl,
            'url' => $s3Client->getObjectUrl(env('AWS_BUCKET'), $key),
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Upload $upload)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Upload $upload)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Upload $upload)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Upload $upload)
    {
    }
}
