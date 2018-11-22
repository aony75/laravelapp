<?php

namespace App\Http\Controllers;

use Validator;
use App\People;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\People as PeopleResource;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pagination = ($request->input('per_page_selected') == '') ? 5 : $request->input('per_page_selected');
        $orderField = $request->input('sort_by') ? $request->input('sort_by') : 'lastName';
        $orderDirection = $request->input('sort_desc') == 'true' ? 'desc' : 'asc';
        $search = $request->input('search');
        $statusFilterSelected  = ($request->input('status_filter_selected') == '') ? 'active' : $request->input('status_filter_selected');
        $currentSignedInUser = $request->input('current_signed_in_user');
        
        //returns data based on based on user filters and and sort options
        if($statusFilterSelected == 'all'){
            $people = People::where('created_by', $currentSignedInUser)
            ->where(function($query) use ($search){
                $query->where('UUID', 'like', '%'.$search.'%')
                ->orWhere('firstName', 'like', '%'.$search.'%')
                ->orWhere('lastName', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('code', 'like', '%'.$search.'%')
                ->orWhere('status', 'like', '%'.$search.'%');
            })
            ->orderBy($orderField, $orderDirection)
            ->paginate($pagination);
        }else{
            $people = People::where('created_by', $currentSignedInUser)
            ->where('status', '=', $statusFilterSelected)
            ->where(function($query) use ($search){
                $query->where('UUID', 'like', '%'.$search.'%')
                ->orWhere('firstName', 'like', '%'.$search.'%')
                ->orWhere('lastName', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhere('code', 'like', '%'.$search.'%');
            })
            ->orderBy($orderField, $orderDirection)
            ->paginate($pagination); 
        }

        return PeopleResource::collection($people);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        /*ensures that every field is filled out in the form.
        No need to validate UUID since it's not in the form
        */
        $validator = Validator::make($request->all(), [
            'firstName' => 'required', 
            'lastName' => 'required',
            'code' => 'required',
            'status' => [
                'required',
                Rule::in(['active', 'inactive']) 
            ]
        ]);


        if($validator->fails()){

            return response()->json(['errors'=>$validator->errors()]);

        }else{

            /*if the request method is put it will update existing record
            in people table, but if it is not, then it will create a new 
            record in people table.
            */
            $person = $request->isMethod('put') ? People::where('UUID', $request->people_id)->firstOrFail() : new People;

            /*sets description to null so database will accept value if the request is empty 
            since description isn't required*/
            $description = empty($request->input('description')) ? null : $request->input('description');

            //inserts data from form to database
            $person->UUID = $request->input('people_id');
            $person->firstName = $request->input('firstName');
            $person->lastName = $request->input('lastName');
            $person->description = $description;
            $person->code = $request->input('code');
            $person->status = $request->input('status');
            $person->created_by = $request->input('current_signed_in_user');

            //returns submitted data to view
            if($person->save()){
                return new PeopleResource($person);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //retrieves single record from people based on id
        $person = People::findOrFail($id);

        //returns info based on people resource template
        return new PeopleResource($person);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        //creates an array from id and deletes all submitted users to be deleted
        $id_set = explode(',', $id);
        $people = People::whereIn('UUID',$id_set)->delete();

        //returns true if items are deleted
       if(!empty($people)){
            return response()->json(['success' => true]);
       }
        
    }
}
