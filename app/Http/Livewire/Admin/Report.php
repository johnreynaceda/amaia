<?php

namespace App\Http\Livewire\Admin;

use App\Models\AmenityRequest;
use App\Models\MaintenanceRequest;
use App\Models\Pass;
use App\Models\PassRequest;
use Carbon\Carbon;
use Livewire\Component;

class Report extends Component
{
    public $type;
    public function render()
    {
        return view('livewire.admin.report', [
            'reports' => $this->generateQuery(),
        ]);
    }

    public function generateQuery()
    {
        if ($this->type == 1) {
            return MaintenanceRequest::whereMonth('request_date', now()->month)->get();
        } elseif ($this->type == 2) {
            return AmenityRequest::whereMonth('request_date', now()->month)->get();
        } elseif ($this->type == 3) {
            return PassRequest::where('pass_id', Pass::where('name', 'like', '%' . 'Gate Pass' . '%')->first()->id)->whereMonth('request_date', now()->month)->get();
        } elseif ($this->type == 4) {
            return PassRequest::where('pass_id', Pass::where('name', 'like', '%' . 'Visitor Pass' . '%')->first()->id)->whereMonth('request_date', now()->month)->get();
        } elseif ($this->type == 5) {
            return PassRequest::where('pass_id', Pass::where('name', 'like', '%' . 'Parcel Pass' . '%')->first()->id)->whereMonth('request_date', now()->month)->get();
        } else {

        }
    }
}
