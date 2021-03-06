<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Section;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = Section::all();

        return view('admin.sections.list', ['sections' => $sections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = Section::all();

        return view('admin.sections.create', ['sections' => $sections]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string|max:2000|nullable',
            'status' => 'boolean'
        ]);

        if ($request->has('parent_id')) {
            $section = Section::findOrFail($request->input('parent_id'));
            $data['parent_id'] = $section->id;
            $data['depth_level'] = ++$section->depth_level;
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('sections', ['disk' => 'public']);
        }

        $data['status'] = $request->has('status') ? (int)$request->input('status') : 0;

        $user = $request->user();

        $user->sections()->create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Раздел успешно создан');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $section = Section::findOrFail($id);

        return view('admin.sections.edit', ['section' => $section]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $section = Section::findOrFail($id);

        $data = $request->except(
            '_token',
            '_method',
            'status',
            'delete_photo'
        );

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('sections', ['disk' => 'public']);
        }

        $data['status'] = $request->has('status') ? $request->input('status') : 0;

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Раздел успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
