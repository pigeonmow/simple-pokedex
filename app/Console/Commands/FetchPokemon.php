<?php

namespace App\Console\Commands;

use App\Models\Pokemon;
use Illuminate\Console\Command;

class FetchPokemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pokemon:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets a list of all 151 original Pokemon from the Poke API and stores them in local database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->getData('https://pokeapi.co/api/v2/pokemon?limit=151&offset=0');

        if ($data['results']) {
            $this->withProgressBar($data['results'], function ($result) {
                Pokemon::updateOrCreate(
                    ['name' => $result['name']],
                    ['name' => $result['name'], 'url' => $result['url']]
                );
            });

            $this->info('The pokemon were imported!');
        } else {
            $this->info('No pokemon found.');
        }

    }

    /**
     * Gets data from API.
     *
     * @param $url
     * @return mixed
     */
    private function getData($url)
    {
        $client = curl_init($url);

        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($client);

        return json_decode($response, true);
    }
}
