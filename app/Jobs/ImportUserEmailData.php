<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportUserEmailData implements ShouldQueue
{
    use Queueable;
    public $page;
    /**
     * Create a new job instance.
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $url = 'https://dhrtrp.efsme.com/scikiq/dataset/DSETCLNT0002000054';
        $token = '74c443b5b38cad3753155d55eeb0b0b963fbce81';

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $token,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post($url, [
            'output_type' => 'json',
            'top' => 100,
            'page' => $this->page,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Handle the successful response
            $data = $response->json(); // Parse the JSON response
            foreach($data['data'] as $key => $value){
                $name = strtolower(explode(' ', $value['name'])[0]);
                User::create([
                    'id' => $value['staff_id'],
                    'name' => $value['name'],
                    'email' => $name. $value['staff_id'].'@efsme.com',
                ]);
            }
            if($data['next_page'] != null && $this->page < $data['next_page']){
                ImportUserEmailData::dispatch($data['next_page']);
            }
        } else {
            // Handle errors
            $error = $response->body(); // Get the raw response body
            Log::error("PAGE NUMBER : " - $this->page . " - " . $error);
        }
    }
}
