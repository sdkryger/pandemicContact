<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Contact;
use Carbon\Carbon;

class ContactController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function contactCheck(Request $request){
      $response  = new \stdClass();
      $response->error = true;
      $response->message = 'not authenticated or missing contactId';
      if(isset($request['contactId']) and Auth::user()){
        $response->message = 'should proceed...';
        $user = Auth::user();
        $contactId = $request['contactId'];
        //$myContacts = Contact::where('parent_id',$user->id)->orWhere('child_id',$user->id)->get();
        $myContactIds = array();
        $theirContactIds = array();
        $now = Carbon::now();
        $now->subDays(14);
        $this->getContacts($user->id,$myContactIds,$now);
        $this->getContacts($contactId,$theirContactIds,$now);
        $response->myContacts = $myContactIds;
        $response->theirContacts = $theirContactIds;
        $ourContacts = $myContactIds;
        foreach($theirContactIds as $contact){
          if(!in_array($contact, $ourContacts))
            array_push($ourContacts, $contact);
        }
        $response->ourContacts = $ourContacts;

        $myContactIds = array();
        $theirContactIds = array();
        $date = Carbon::now();
        $date->subDays(10);
        $this->getContacts($user->id,$myContactIds,$date);
        $this->getContacts($contactId,$theirContactIds,$date);
        $ourContacts = $myContactIds;
        foreach($theirContactIds as $contact){
          if(!in_array($contact, $ourContacts))
            array_push($ourContacts, $contact);
        }
        $response->ourContactsLastTenDays = $ourContacts;

        $myContactIds = array();
        $theirContactIds = array();
        $date = Carbon::now();
        $date->subDays(5);
        $this->getContacts($user->id,$myContactIds,$date);
        $this->getContacts($contactId,$theirContactIds,$date);
        $ourContacts = $myContactIds;
        foreach($theirContactIds as $contact){
          if(!in_array($contact, $ourContacts))
            array_push($ourContacts, $contact);
        }
        $response->ourContactsLastFiveDays = $ourContacts;


        if(count($myContactIds) > 0 and count($theirContactIds)>0){
          $response->error = false;
          $response->message = 'success';
        }
      }
      return response(json_encode($response))->header('Content-Type','application/json');
    }

    public function myContacts(){
      $response  = new \stdClass();
      $response->error = true;
      $response->message = 'not authenticated';
      if(Auth::user()){
        $myContactIds = array();
        $lastTwoWeeks = Carbon::now();
        $lastTwoWeeks->subDays(14);
        $response->myContactIds = $this->getContacts(Auth::user()->id,$myContactIds,$lastTwoWeeks);
        $response->daysAgo14 = $lastTwoWeeks;

        $myContactIds = array();
        $lastTenDays = Carbon::now();
        $lastTenDays->subDays(10);
        $response->myContactIdsLastTenDays = $this->getContacts(Auth::user()->id,$myContactIds,$lastTenDays);
        $response->daysAgo10 = $lastTenDays;

        $myContactIds = array();
        $lastFiveDays = Carbon::now();
        $lastFiveDays->subDays(5);
        $response->myContactIdsLastFiveDays = $this->getContacts(Auth::user()->id,$myContactIds,$lastFiveDays);
        $response->daysAgo5 = $lastFiveDays;

        $response->error = false;
        $response->message = 'success';
      }
      return response(json_encode($response))->header('Content-Type','application/json');
    }

    public function contactCreate(Request $request){
      $response  = new \stdClass();
      $response->error = true;
      $response->message = 'not authenticated or missing contactId';
      if(isset($request['contactId']) and isset($request['sameResidence']) and Auth::user()){
        $contactId = $request['contactId'];
        $sameResidence = $request['sameResidence'] == 'true';
        $user = Auth::user();
        $contact = User::where('id',$contactId)->first();
        $response->contact = $contact;
        if($contact){
          if($contact->id == $user->id){
            $response->message = 'Entered your own id';
          }else{
            $response->error = false;
            $response->message = 'success';
            $contactEntry = new Contact;
            $contactEntry->same_residence = $sameResidence;
            if($user->id < $contact->id){
              $contactEntryOld = Contact::where('parent_id',$user->id)->where('child_id',$contact->id)->first();
              if($contactEntryOld)
                $contactEntryOld->delete();
              $contactEntry->parent_id = $user->id;
              $contactEntry->child_id = $contact->id;
            }else{
              $contactEntryOld = Contact::where('parent_id',$contact->id)->where('child_id',$user->id)->first();
              if($contactEntryOld)
                $contactEntryOld->delete();
              $contactEntry->parent_id = $contact->id;
              $contactEntry->child_id = $user->id;
            }
            if(isset($request['contactDate']) and $request['contactDate'] != ''){
              $contactDate = $request['contactDate'];
              $contactDate = Carbon::createFromFormat('Y-m-d',$contactDate);
              $response->contactDate = $contactDate;
              $contactEntry->created_at = $contactDate;
              $contactEntry->updated_at = $contactDate;
            }
            $contactEntry->save();
            $response->contactEntry = $contactEntry;
          }
        }else{
          $response->message = 'Could not find specified user';
        }
        //echo "should create contact with id: ".$contactId;
      }
      return response(json_encode($response))->header('Content-Type','application/json');
      
    }

    public function getContacts($id,&$foundRelatives,$fromDate){
      //echo "looing for contacts for id: ".$id.", conidering adding to ".implode('-',$foundRelatives);
      if(!in_array($id,$foundRelatives)){
        //echo "did not find id so will add to array...";
        array_push($foundRelatives, intval($id));
      }
      
      $this->getParents($id,$foundRelatives,$fromDate);
      $this->getChildren($id,$foundRelatives,$fromDate);

      return $foundRelatives;
    }

    public function getParents($id, &$foundRelatives, $fromDate){
      //echo 'looking for parents of id: '.$id." with foundRelatives of ".implode('-',$foundRelatives);
      //echo "fromDate: ".$fromDate;  
      $contacts = Contact::where('child_id',$id)
        ->where(function($query) use($fromDate){
          $query->where('same_residence',true)
            ->orWhere('created_at','>=',$fromDate);
        })
        ->get();    
      foreach($contacts as $contact){
        //echo 'iterating through parent of '.$id." (child id: ".$contact->parent_id."), ";
        if(!in_array($contact->parent_id,$foundRelatives))
          $this->getContacts($contact->parent_id,$foundRelatives,$fromDate);
      }

      return $foundRelatives;
    }

    public function getChildren($id, &$foundRelatives, $fromDate){
      //echo 'looking for children of id: '.$id." with foundRelatives of ".implode('-',$foundRelatives);
      $contacts = Contact::where('parent_id',$id)
      ->where(function($query) use($fromDate){
        $query->where('same_residence',true)
          ->orWhere('created_at','>=',$fromDate);
      })
      ->get(); 
      foreach($contacts as $contact){
        if(!in_array($contact->child_id,$foundRelatives))
          $this->getContacts($contact->child_id, $foundRelatives, $fromDate);
      }

      return $foundRelatives;
    }
}