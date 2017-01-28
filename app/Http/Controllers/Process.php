<?php

namespace App\Http\Controllers;

use App\Stock;
use Illuminate\Http\Request;
use Validator, Input, Redirect, Session;

use App\Http\Requests;

class Process extends Controller
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

    //show login form
    public function indexlogin()
    {
        return redirect('login');
    }

    //show homepage
    public function homepage()
    {
        return view('pages.home');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //list all, select *
        $liststock = Stock::paginate(2); //change 2 to number of data you want to display in 1 page
        return view('pages.view',array('liststock'=>$liststock));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {  
        // validate
        $this->validate($request, [
            'stype' => 'required',
            'sname' => 'required',
            'ssize' => 'required',
            'squantity' => 'required|numeric',
            'fileUpload' => 'mimes:jpeg,jpg|required|max:3000',
        ]);

        //get input and store into variables
        $stype = $request->input('stype');
        $sname = $request->input('sname');
        $ssize = $request->input('ssize');
        $squantity = $request->input('squantity');
        $file = $request->file('fileUpload');

        //create new object
        $instock = new Stock;

        //set all input to insert to db
        $instock->STK_TYPE = $stype;
        $instock->STK_NAME = $sname;
        $instock->STK_SIZE = $ssize;
        $instock->STK_QTY = $squantity;

        //save to db
        $instock->save();
        //upload photo
        $path = $file->move(public_path('/images'), $instock->id.'.jpg');

        Session::flash('message', "Insert stock success!");
        return redirect("/home");

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $STK_NAME =  $request->input('sname');
        if($STK_NAME)
        {
            $search = Stock::where('stk_name','LIKE',"%$STK_NAME%")->paginate(2); //change 2 to number of data you want to display in 1 page

        return view('pages.search',array('search'=>$search));
        }
        else
        {
            return view('pages.search');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //show update form
        $editstock = Stock::find($id);
        return view('pages.edit',array('editstock'=>$editstock));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //update data in db
        $sid = $request->input('sid');
        $stype = $request->input('stype');
        $sname = $request->input('sname');
        $ssize = $request->input('ssize');
        $squantity = $request->input('squantity');

        $upstock = Stock::find($sid);
        $upstock->STK_TYPE = $stype;
        $upstock->STK_NAME = $sname;
        $upstock->STK_SIZE = $ssize;
        $upstock->STK_QTY = $squantity;

        $upstock->save();
        echo "<script>alert('Data updated!')</script>";
        return $this->index();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //delete data
        $STK_ID = $request->input('delstock');

        $delstock = Stock::find($STK_ID);
        $delstock->delete();

        //delete image
        unlink(public_path("images/".$STK_ID.".jpg"));

        return $this->index();
    }
}
