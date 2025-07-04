<?php

namespace App\Livewire\Shipping;

use App\Models\Country;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class Index extends Component
{
    public array $sender = [
        'country' => 'US',
        'zip' => '84117'
    ];

    public array $receiver = [
        // ---- Required for api request
        'city' => 'Toronto',
        'country' => 'CA',
        'email' => 'foo@bar.com',
        'zip' => 'M9C5K5',
        // ---- Optional for api request
        'phone' => '',
        'name' => '',
        'company' => '',
        'address' => '',
        'apt' => '',
        'state' => '',
    ];

    public array $pieces = [
        [
            'weight' => '1.4',
            'length' => '5.1',
            'width' => '4',
            'height' => '2.5',
            'insuranceAmount' => '12.15',
            'declaredValue' => '100'
        ]
    ];

    public array $quotes = [];
    public string $errorMessage = '';
    public bool $hasResponse = false;

    // rubber stamps
    public $has_rubber_stamps = false;
    public $rubber_stamp_1 = '';
    public $rubber_stamp_2 = '';

    public function updatedHasRubberStamps($value)
    {
        if ($value) {
            $this->rubber_stamp_1 = '';
            $this->rubber_stamp_2 = '';
        } else {
            $this->rubber_stamp_1 = '';
            $this->rubber_stamp_2 = '';
        }
    }

    public function addItem()
    {
        $this->pieces[] = [
            'weight' => '',
            'length' => '',
            'width' => '',
            'height' => '',
            'insuranceAmount' => '',
            'declaredValue' => ''
        ];
    }

    public function removeItem($index)
    {
        if (count($this->pieces) > 1) {
            unset($this->pieces[$index]);
            $this->pieces = array_values($this->pieces); // Reindex array
        }
    }

    public function getQuote()
    {
        $this->validate([
            'sender.country' => 'required|string',
            'sender.zip' => 'required|string',
            'receiver.city' => 'required|string',
            'receiver.country' => 'required|string',
            'receiver.zip' => 'required|string',
            'receiver.email' => 'required|email',
            'pieces.*.weight' => 'required|numeric|min:0.01',
            'pieces.*.length' => 'required|numeric|min:0.01',
            'pieces.*.width' => 'required|numeric|min:0.01',
            'pieces.*.height' => 'required|numeric|min:0.01',
            'pieces.*.insuranceAmount' => 'required|numeric|min:0',
            'pieces.*.declaredValue' => 'required|numeric|min:0',
        ]);

        // info('Shipping quote request initiated', [
        //     'sender' => $this->sender,
        //     'receiver' => $this->receiver,
        //     'pieces' => $this->pieces
        // ]);

        try {
            // residential, signatureOptionCode, weightUnit, dimUnit, currency
            //  "residential": true,
            //   "signatureOptionCode": "DIRECT",
            //   "contentDescription": "stuff and things",
            //   "weightUnit": "lb",
            //   "dimUnit": "in",
            //   "currency": "USD",
            //   "customsCurrency": "USD",
            $payload = [
                'sender' => $this->sender,
                'receiver' => array_intersect_key($this->receiver, array_flip([
                    'city',
                    'country',
                    'email',
                    'zip',
                ])),
                'pieces' => $this->pieces,
                'residential' => true,
                'signatureOptionCode' => 'DIRECT',
                'contentDescription' => 'stuff and things',
                'weightUnit' => 'lb',
                'dimUnit' => 'in',
                'currency' => 'USD',
                'customsCurrency' => 'USD'
            ];

            $response = Http::withHeaders([
                'Authorization' => 'RSIS AlJz6APUKGp6lHuXU5GmV0kW7BPewp0p',
                'Content-Type' => 'application/json'
            ])->post('https://xpsshipper.com/restapi/v1/customers/12600648/quote', $payload);

            if ($response->status() == 200) {
                $responseData = $response->json();

                info($responseData);
                if (isset($responseData['quotes']) && is_array($responseData['quotes'])) {
                    $this->quotes = $responseData['quotes'];
                    $this->hasResponse = true;
                    $this->errorMessage = '';

                    // For debugging, you can still dd the response
                    // dd([
                    //     'status' => 'SUCCESS',
                    //     'total_quotes' => count($responseData['quotes']),
                    //     'quotes' => $responseData['quotes']
                    // ]);
                } else {
                    $this->errorMessage = 'No quotes found in response';
                    $this->hasResponse = true;
                    dd([
                        'status' => 'SUCCESS but no quotes found',
                        'response_body' => $responseData
                    ]);
                }
            } else {
                $this->errorMessage = 'API Error: ' . $response->status();
                $this->hasResponse = true;
                dd([
                    'status' => 'ERROR',
                    'http_status' => $response->status(),
                    'error_body' => $response->json(),
                    'payload_sent' => $payload
                ]);
            }
        } catch (\Exception $e) {
            dd([
                'error' => $e->getMessage(),
                'payload_sent' => $payload ?? null
            ]);
        }
    }
    #[Computed(persist: true)]
    public function contentTypes(){
        return [
            [
                'value' => 'merchandise',
                'label' => 'Merchandise',
                'description' => 'Products that were purchased by your recipient; they will probably have to show a receipt proving the declared value is correct to receive the package.'
            ],
            [
                'value' => 'documents',
                'label' => 'Documents', 
                'description' => 'For contracts and other printed documents only.'
            ],
            [
                'value' => 'gift',
                'label' => 'Gift',
                'description' => 'Only choose this option if it\'s actually a gift… it will not reduce the chance of your recipient having to pay import duties!'
            ]
        ];
    }

    #[Computed(persist: true)]
    public function countries()
    {
        return Country::select(['id', 'label', 'value'])
            ->where('status', true)
            ->orderBy('label')
            ->get();
    }

    public function render()
    {
        return view('livewire.shipping.index');
    }
}
