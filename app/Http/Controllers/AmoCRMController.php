<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AmoCRMService;
use App\Models\Contact;

class AmoCRMController extends Controller {

    protected $amocrm;

    public function __construct(AmoCRMService $amocrm) {
        $this->amocrm = $amocrm;
    }

    public function index()
    {
        $this->amocrm->auth();
        $contacts = Contact::all();
        return view('contacts.index', compact('contacts'));
    }

    public function contacts() {
        $contacts = Contact::all()->get();
        return view('contacts', compact('contacts'));
    }

    public function getContacts(Request $request) {
        $contacts = $this->amocrm->getContacts();
        $this->saveContacts($contacts);
    }

    private function saveContacts($contacts) {
        foreach($contacts as $contact) {
            $email = $contact["custom_fields_values"][1]["values"][0]["value"];
            $phone = $contact["custom_fields_values"][0]["values"][0]["value"];
            $res = Contact::where('email', $email)->first();
            if(empty($res)) {
                $data = [
                    "email" => $email,
                    "phone" => $phone??'9891',
                    "contact_id" => $contact["id"],
                    "name"=> $contact["name"],
                    "first_name"=> $contact["first_name"],
                    "last_name"=> $contact["last_name"],
                    "responsible_user_id"=> $contact["responsible_user_id"],
                    "group_id"=> $contact["group_id"],
                    "created_by"=> $contact["created_by"],
                    "updated_by"=> $contact["updated_by"],
                    "created_at"=> $contact["created_at"],
                    "updated_at"=> $contact["updated_at"],
                    "closest_task_at"=> $contact["closest_task_at"],
                    "custom_fields_values"=> $contact["custom_fields_values"],
                    "account_id"=> $contact["account_id"]
                ];
                Contact::create($data);
            }
        }
        return redirect()->route('success');
    }
}
