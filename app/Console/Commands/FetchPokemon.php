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
        $this->info('Importing...');
        $this->newLine();

        $data = $this->getData('https://pokeapi.co/api/v2/pokemon?limit=151&offset=0');

        if ($data) {
            $this->withProgressBar($data, function ($datum) {
                Pokemon::updateOrCreate(
                    ['name' => $datum['name']],
                    [
                        'number' => $datum['number'],
                        'name' => $datum['name'],
                        'url' => $datum['url'],
                        'sprite_url' => $datum['sprite_url']
                    ]
                );
            });

            $this->newLine();
            $this->info('The pokemon were imported!');
        } else {
            $this->newLine();
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

        $data = json_decode($response, true);

        $refinedData = [];
        foreach ($data['results'] as $key => $value) {
            $c = curl_init($value['url']);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

            $res = curl_exec($c);
            $itemData = json_decode($res);

            $refinedData[$key] = $value;
            $refinedData[$key]['number'] = $key + 1;
            $refinedData[$key]['sprite_url'] = $itemData->sprites->back_default;
        }

        return $refinedData;
    }
}
