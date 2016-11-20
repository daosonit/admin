<?php namespace App\Http\Controllers\System;

use App\Http\Requests;
use App\Http\Requests\DepartmentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use App\Models\Department;
use Log;

class DepartmentController extends Controller {


	public function __construct(Department $department)
	{
		$this->department = $department;
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$departments = $this->department->all();
		return view('components.system.department.index')->with('departments', $departments);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('components.system.department.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(DepartmentRequest $request)
	{
		$this->department->fill($request->all());
		$this->department->save();
		return redirect()->route('system.departments.index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		try {
			$department = $this->department->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Department not found!', 404);
		}

		return view('components.system.department.edit')->with('department', $department);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(DepartmentRequest $request, $id)
	{
		try {
			$department = $this->department->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Department not found!', 404);
		}

		$this->department->fill($request->all());
		$this->department->save();
		return redirect()->route('system.departments.index');

	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$department = $this->department->findOrFail($id);
		} catch (ModelNotFoundException $e) {
			Log::error($e->getMessage());
			return response('Department not found!', 404);
		}
		$department->delete();
		return redirect()->route('system.departments.index');
	}

}
