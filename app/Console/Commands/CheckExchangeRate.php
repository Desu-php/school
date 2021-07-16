<?php

namespace App\Console\Commands;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class CheckExchangeRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:exchange-rate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Exchange Rate';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        try {
            $client = new \GuzzleHttp\Client();
            $current_date = Carbon::now()->timezone('Europe/Moscow')->format('d/m/Y');
            $url = 'https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $current_date;
            $request = $client->request('GET', $url);
            $xml_response = $request->getBody()->getContents();
            $currencies = simplexml_load_string($xml_response);
            foreach ($currencies as $currency) {
                $int_value = str_replace(',', '.', $currency->Value);
                Currency::where('code', (string) $currency->CharCode)->update(['currency_rate' => (float)number_format((float)$int_value, 4)]);
            }
        } catch (\Exception $e) {
            Log::info("Check Exchange Rate Error:: " . $e->getMessage());
        }

    }
}
