<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // GET all students
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Student::all()
        ], 200);
    }

    // GET one student
    public function show(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $student
        ], 200);
    }

    // POST create student
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_number' => [
                'required', 'string', 'max:50',
                'unique:students,student_number'
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                'unique:students,email'
            ],
            'course' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'integer', 'between:1,4'],
        ]);

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully.',
            'data' => $student
        ], 201);
    }

    // PUT update student
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        $validated = $request->validate([
            'student_number' => [
                'required', 'string', 'max:50',
                Rule::unique('students', 'student_number')
                    ->ignore($student->id)
            ],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('students', 'email')
                    ->ignore($student->id)
            ],
            'course' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'integer', 'between:1,4'],
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => $student->fresh()
        ], 200);
    }

    // DELETE student
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.'
        ], 200);
    }
}