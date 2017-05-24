<?php

namespace Mpociot\ApiDoc\Generators;

use DateTime;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

class PassportGenerator extends LaravelGenerator
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $user;

    /** @var  string */
    protected $token;

    /**
     * PassportGenerator constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * @return array
     */
    protected function getHeaders()
    {
        return array_merge(parent::getHeaders(), $this->getAuthHeaders());
    }

    /**
     * @return array
     */
    protected function getAuthHeaders()
    {
        if (!$this->token) {
            $this->token = $this->makeToken();
        }

        return [
            'Authorization' => 'Bearer ' . $this->token,
        ];
    }

    /**
     * @return string
     */
    protected function makeToken()
    {
        $clientRepository = new ClientRepository();

        $client = $clientRepository->createPersonalAccessClient(
            null, 'Documentation Personal Access Client', 'http://localhost'
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => new DateTime,
            'updated_at' => new DateTime,
        ]);

        return $this->user->createToken('TestToken', [])->accessToken;
    }

}
