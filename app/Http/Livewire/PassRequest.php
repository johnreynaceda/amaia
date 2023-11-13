<?php

namespace App\Http\Livewire;

use App\Models\Pass;
use Livewire\Component;

class PassRequest extends Component
{
    public $gate_modal = false;
    public $gate_form = false;
    public $visitor_modal = false;
    public $visitor_form = false;
    public $visitor_clause = false;

    //gate pass
    public $visitor, $building, $unit, $particulars, $quantity, $purpose;
    public $request_date, $request_time;
    public $relation, $contact, $reason;
    public function render()
    {
        return view('livewire.pass-request');
    }

    public function submitGatePass()
    {
        $this->validate([
            'visitor' => 'required',
            'building' => 'required',
            'unit' => 'required',
            'purpose' => 'required',
            'particulars' => 'required',
            'quantity' => 'required',
            'request_date' => 'required',
            'request_time' => 'required',
        ]);
        $data = Pass::where('name', 'like', '%' . 'Gate Pass' . '%')->first();

        \App\Models\PassRequest::create([
            // 'user_id' => auth()->user()->id,
            'pass_id' => $data->id,
            'visitor_name' => $this->visitor,
            'building' => $this->building,
            'unit' => $this->unit,
            'purpose' => $this->purpose,
            'particulars' => $this->particulars,
            'quantity' => $this->quantity,
            'request_date' => $this->request_date,
            'preffered_time' => $this->request_time
        ]);
        sleep(2);
        sweetalert()->addSuccess('Request Submitted');
        return redirect()->route('login');
    }

    public function submitVisitorPass()
    {
        $this->validate([
            'visitor' => 'required',
            'unit' => 'required',
            'relation' => 'required',
            'request_date' => 'required',
            'request_time' => 'required',
        ]);
        $data = Pass::where('name', 'like', '%' . 'Visitor Pass' . '%')->first();

        \App\Models\PassRequest::create([
            'pass_id' => $data->id,
            'visitor_name' => $this->visitor,
            'unit' => $this->unit,
            'relation' => $this->relation,
            'request_date' => $this->request_date,
            'preffered_time' => $this->request_time

        ]);
        sleep(2);
        sweetalert()->addSuccess('Request Submitted');
        return redirect()->route('login');
    }
}
