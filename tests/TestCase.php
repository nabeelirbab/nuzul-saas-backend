<?php

namespace Tests;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use BeyondCode\QueryDetector\QueryDetector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;

    public static int $maxDBQueriesPerTest = 300;

    public static int $maxDBQueriesPerRequest = 50;

    public static bool $skipLazyLoadedRelationsException = false;

    protected static bool $logsRequestsAndResponses = false;

    protected static bool $clearsUserAfterRequest = false;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed --class=RoleSeeder');
        // $this->artisan('db:seed');

        config()->set(['current_test' => $this->getName()]);

        DB::enableQueryLog();
    }

    protected function tearDown(): void
    {
        $currentTest = config('current_test');
        $queriesCount = \count(DB::getQueryLog());

        if ($queriesCount > static::$maxDBQueriesPerTest) {
            // Maybe you want to divide it to multiple tests?
            throw new \Exception("Test [{$currentTest}] executed {$queriesCount} Query. That's ALOT.");
        }

        parent::tearDown();
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $databaseQueriesCount = \count(DB::getQueryLog());

        $response = parent::call(...\func_get_args());

        if ($response->status() >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $exceptionMessage = substr($response->baseResponse->__toString(), 0, 700);

            if (
                $response->baseResponse instanceof JsonResponse
                && ($data = $response->baseResponse->getData(true))
                && isset($data['trace'])
            ) {
                $exceptionMessage .= "\n\n".
                    collect($data['trace'])
                        ->filter(fn ($trace) => !empty($trace['file']))
                        ->filter(fn ($trace) => !empty($trace['line']))
                        ->filter(fn ($trace) => !Str::contains($trace['file'], base_path('vendor')))
                        ->map(fn ($trace) => $trace['file'].':'.$trace['line'])
                        ->implode("\n")
                ;
            }

            throw new \Exception($exceptionMessage);
        }

        $requestQueries = \count(DB::getQueryLog()) - $databaseQueriesCount;

        if ($requestQueries > static::$maxDBQueriesPerRequest) {
            // Maybe you want to review N+1 issues? Use: "info(\DB::getQueryLog());" to check out the queries
            Log::channel('single')->info("\n\n".collect(\DB::getQueryLog())->map(fn ($sql) => $sql['query'].(empty($sql['bindings']) ? '' : (' ('.implode(',', $sql['bindings']).')')))->implode("\n\n"));

            throw new \Exception("Request {$method} {$uri} Executed {$requestQueries} Query. That's ALOT. Check Log file [storage/logs/laravel.log] to see the executed queries.");
        }

        $latestDetectedLazyQueries = app(QueryDetector::class)->getDetectedQueries()->first();

        if (
            !$this::$skipLazyLoadedRelationsException
            && ($latestDetectedLazyQueries && $latestDetectedLazyQueries['count'] > 3)
        ) {
            $test = config('current_test');

            $message = implode("\n", [
                '',
                '|  N+1 Queries issue detected! .. Maybe you want to eager load relations.',
                "|  Request : {$method} {$uri}",
                "|  Test : {$test}",
                '|  Model : '.$latestDetectedLazyQueries['model'],
                '|  Related model : '.$latestDetectedLazyQueries['relatedModel'],
                "|  Relation : [{$latestDetectedLazyQueries['relation']}] called ({$latestDetectedLazyQueries['count']}) times.",
                '|  |  Query : '.$latestDetectedLazyQueries['query'],
                '|  |  App Trace: '.collect($latestDetectedLazyQueries['sources'])
                    ->map(fn ($source) => ($source->name ?? '').':'.($source->line ?? ''))
                    ->filter(fn ($source) => Str::startsWith($source, ['/app', '/tests']))
                    ->map(fn ($source) => trim($source, '/'))
                    ->implode(' '),
            ]);

            throw new \Exception($message, 1);
        }

        if (static::$clearsUserAfterRequest) {
            auth()->clearUser();

            Model::clearBootedModels();
        }

        if (static::$logsRequestsAndResponses && $response->baseResponse instanceof \Illuminate\Http\JsonResponse) {
            (new \Illuminate\Filesystem\FilesystemManager($this->app))
                ->disk('testing')
                ->append(
                    'requests/'.cached_time().'.md',
                    implode("\n", [
                        '',
                        '--- Test: `'.config('current_test').'`',
                        '',
                        '### '.$response->status().' '.$response->getStatusText().' | '.$method.' '.urldecode($uri),
                        '```',
                        $content,
                        '```',
                        '```',
                        $response->baseResponse->__toString(),
                        '```',
                        '----',
                    ])
                )
            ;
        }

        return $response;
    }

    public function adminLogin()
    {
        $user = User::factory()->create(['role_id' => Role::ADMIN]);

        return Sanctum::actingAs($user);
    }

    public function companyOwnerLogin()
    {
        $user = User::factory()->create(['role_id' => Role::COMPANY_OWNER]);

        return Sanctum::actingAs($user);
    }

    public function companyAccountLogin()
    {
        $userData = [
            'name' => 'Raz',
            'password' => bcrypt('secret'),
            'mobile_number' => '966501175111',
            'email' => 'user@nuzul.app',
            'role_id' => Role::COMPANY,
        ];

        $user = User::create($userData);

        $tenant = Tenant::create([
            'id' => 1,
            'name_en' => 'Nuzul x',
            'name_ar' => 'نزل اكس',
        ]);

        $tenant->users()->attach($user->id, ['company_role_id' => Role::COMPANY_OWNER]);

        $centralDomains = explode(',', env('CENTRAL_DOMAINS'));
        $tenant->domains()->create(['domain' => readable_random_string().$tenant->id.'.'.$centralDomains[1]]);

        return Sanctum::actingAs($user);
    }
}
