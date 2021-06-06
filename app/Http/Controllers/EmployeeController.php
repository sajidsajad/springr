<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function addEmployee(Request $request){ 
        // dd($request);
        $validator = $this->checkValidation($request);

        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors(),
                "responseCode" => "400",
            ],400);
        }

        if ($img = $request->file('image')) {
            $destinationPath = 'image/';
            $image = $request->fullName.'-'.time().'.'.$img->getClientOriginalExtension();
            $img->move($destinationPath, $image);
        }

        $join_date = \Carbon\Carbon::createFromFormat('d-m-Y',$request->dateOfJoining);

        $employee = new Employee;
        $employee->full_name = $request->fullName;
        $employee->email = $request->email;
        $employee->date_of_joining = $join_date;
        $employee->current_date = \Carbon\Carbon::now();;
        $employee->image = $image;
          
        $time_interval = $join_date->diff(\Carbon\Carbon::now());
        $employee->experience = $time_interval->format('%y years %m months');    

        if ($employee->save()) {
            return response()->json([
                "message" => "Employee has been added successfully!",
                "responseCode" => "200",
            ], 200);
        }
            
        return response()->json([
            'errors' => [
                'serverError' => ['Something went wrong']
            ],
            "responseCode" => "500",
        ],500);
    }

    public function checkValidation($request){

        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string',
            'email' => 'required|string|email|unique:employees',
            'dateOfJoining' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
        ]);

        return $validator;
    }

    public function getEmployeeList(){

        $data['employees'] = Employee::select('id','full_name','email','experience','image')->orderBy('id','desc')->paginate(5);   
        return view('employee',$data);

        
        // to test the API in postman
        // $employees = Employee::orderBy('id','desc')->get(['id','full_name','email','experience','image']);
        // return response()->json([
        //     "responseCode" => "200",
        //     "totalEmployees" => count($employees),
        //     "data" => $employees
        // ], 200);

    }

    public function deleteEmployee(Request $request){

        $employee = Employee::find($request->id);
        if(!$employee){
            return response()->json([
                "errors" => ["Employee not found"],
                "responseCode" => "404",
            ], 404);
        }

        $filename = public_path().'/image/'.$employee->image;
        \File::delete($filename);

        $employee->delete();

        return response()->json([
            "responseCode" => "200",
            "message" => "Employee deleted successfully!"
        ], 200);
    }
}
