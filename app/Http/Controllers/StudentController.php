<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Display all students
    public function index()
    {
        $students = Student::all();
        return view('students.index', compact('students'));
    }

    // Show create form
    public function create()
    {
        return view('students.create');
    }

    // Store new student
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students',
            'cell' => 'required',
            'address' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('images'), $imageName);
            $data['photo'] = $imageName;
        }

        Student::create($data);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    // Update student
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students,email,'.$id,
            'cell' => 'required',
            'address' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && file_exists(public_path('images/'.$student->photo))) {
                unlink(public_path('images/'.$student->photo));
            }

            $image = $request->file('photo');
            $imageName = time().'.'.$image->extension();
            $image->move(public_path('images'), $imageName);
            $data['photo'] = $imageName;
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    // Delete student
    public function destroy($id)
    {
        $student = Student::findOrFail($id);

        // Delete photo if exists
        if ($student->photo && file_exists(public_path('images/'.$student->photo))) {
            unlink(public_path('images/'.$student->photo));
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }
}