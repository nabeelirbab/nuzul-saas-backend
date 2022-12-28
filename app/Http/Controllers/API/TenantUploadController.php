<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Uploads\Tenants\PresignedURLRequest;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class TenantUploadController extends Controller
{
    public function requestPresignedURL(PresignedURLRequest $request)
    {
        $s3 = Storage::disk('s3');
        $client = $s3->getDriver()->getAdapter()->getClient();
        $expiry = '+10 minutes';

        $uuid = Uuid::uuid4();
        $key = 'tenants/'.tenant()->id.'/properties/'.$request->reference_id.'/'.$uuid.'.'.$request->extension;

        $cmd = $client->getCommand('PutObject', [
            'Bucket' => env('AWS_BUCKET'),
            'Key' => $key,
        ]);

        $request = $client->createPresignedRequest($cmd, $expiry);

        $presignedUrl = (string) $request->getUri();

        return response()->json([
            'presigned_url' => $presignedUrl,
            'url' => $client->getObjectUrl(env('AWS_BUCKET'), $key),
        ], 201);
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
