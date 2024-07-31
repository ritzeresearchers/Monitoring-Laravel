<?php

namespace Tests\Feature;

use App\Models\User;
use Faker\Factory;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\Feature\Helpers\BusinessHelper;
use Tests\Feature\Helpers\JobHelper;
use Tests\Feature\Helpers\LeadHelper;
use Tests\Feature\Helpers\QuoteHelper;
use Tests\Feature\Helpers\ResourceStructureHelper;
use Tests\Feature\Helpers\ScheduleHelper;
use Tests\Feature\Helpers\UserHelper;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestUtils extends TestCase
{
    use BusinessHelper;
    use UserHelper;
    use JobHelper;
    use LeadHelper;
    use QuoteHelper;
    use ResourceStructureHelper;
    use ScheduleHelper;

    use WithFaker;
    use TestDatabaseMigrations;

    protected string $password = 'password';
    protected string $email = '';
    protected string $token = '';

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        config(['env' => 'test']);
        config(['constants.countryCode' => '23']);

        //$this->runDatabaseMigrations();

        $this->setHttpFaker();
    }

    /**
     * @return null|User
     */
    public function user(): ?User
    {
        return User::firstWhere('email', $this->email);
    }

    /**
     * @return string
     */
    public function generateEmail(): string
    {
        $faker = Factory::create();
        return preg_replace('/@example\..*/', '@domain.com', $faker->unique()->safeEmail);
    }

    /**
     * @param string $url
     * @param array $payload
     * @return TestResponse
     */
    public function postRequest(string $url, array $payload = []): TestResponse
    {
        return $this
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->post($url, $payload, ['Accept' => 'application/json']);
    }

    /**
     * @param string $url
     * @param array $payload
     * @return TestResponse
     */
    public function deleteRequest(string $url, array $payload = []): TestResponse
    {
        return $this
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->delete($url, $payload, ['Accept' => 'application/json']);
    }

    /**
     * @param string $url
     * @param array $payload
     * @return TestResponse
     */
    public function putRequest(string $url, array $payload = []): TestResponse
    {
        return $this
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->put($url, $payload, ['Accept' => 'application/json']);
    }

    /**
     * @param string $url
     * @return TestResponse
     */
    public function getRequest(string $url): TestResponse
    {
        return $this
            ->withHeader('Authorization', 'Bearer ' . $this->token)
            ->get($url, ['Accept' => 'application/json']);
    }

    /**
     * @return TestUtils
     */
    public function loginAdmin(): self
    {
        $this->token = $this
            ->postRequest('api/auth/login', [
                'email'    => config('config.adminEmail'),
                'password' => config('config.adminPwd'),
            ])
            ->assertOk()
            ->json()['token'];

        return $this;
    }

    /**
     * @return TestUtils
     */
    public function loginWithCredential(): self
    {
        $this->token = JWTAuth::attempt([
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        return $this;
    }

    /**
     * @return void
     */
    private function setHttpFaker()
    {
        Http::fake([
            config('config.acBaseUrl') . 'api/v3/client/*/customer/*' =>
                function ($request) {
                    if ($request->method() == 'PATCH') {
                        return Http::response(['Message' => '']);
                    }
                    if ($request->method() == 'GET') {
                        return Http::response(['Contracts' => []]);
                    }
                },
            config('config.acBaseUrl') . 'api/v3/client/*/customer/*/contract' =>
                function ($request) {
                    if ($request->method() == 'POST') {
                        return Http::response([
                            'DirectDebitRef' => Str::random(5),
                            'Id'             => Str::random(5),
                        ]);
                    }
                },
            config('config.acBaseUrl') . 'api/*' => Http::response([
                'Id'        => 1,
                'Contract'  => 1,
                'DueDate'   => 1,
                'Amount'    => 1,
                'Contracts' => [],
            ]),
        ]);
    }
}
