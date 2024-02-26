<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Book;

class BookController extends Controller
{

    public function add()
    { 
        $genres = [
            'Fiction',
            'Narrative',
            'Science fiction',
            'Historical Fiction',
            'Mystery',
            'Non-fiction',
            'Novel',
            'Autobiography',
            'Young adult literature',
            'Horror',
            'Detective fiction'
        ];
        return view('book.add',compact('genres'));
    }

    public function addBooks(Request $request)
    {  
        try {
            $customMsgs = [
                'title.required' => 'Please Provide Title',
                'author.required' => 'Please Provide Author',
                'published_year.required' => 'Please Provide Published Year',
                'genre.required' => 'Please Provide Genre',
            ];
            $validator = Validator::make($request->all(),
                [
                    'title' => 'required|string|min:0|max:50|unique:books',
                    'author' => 'required|string|min:0|max:20',
                    'published_year' => 'required|integer|digits:4',
                    'genre' => 'required',
                ], $customMsgs
            );
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }        
            $data = $request->all();
            $check = $this->create($data);
            if ($check) {
                return response()->json(['success' => true, 'message' => "Book added successfully"], 200);
            } else {
                return response()->json(['success' => false, 'message' => "Failed to create data"], 500);
            }
        } catch(\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while adding.'], 500);
        }        
    }


    public function getBooks()
    {
        $genres = [
            'Fiction',
            'Narrative',
            'Science fiction',
            'Historical Fiction',
            'Mystery',
            'Non-fiction',
            'Novel',
            'Autobiography',
            'Young adult literature',
            'Horror',
            'Detective fiction'
        ];
        $books = Book::all();
        return view('book.view',compact('books', 'genres'));
    }
    
    public function create(array $data)
    {
        return Book::create([
            'title' => $data['title'],
            'author' => $data['author'],
            'published_year' => $data['published_year'],
            'genre' => $data['genre'],
        ]);
    }

    public function update(array $data)
    {
        $book = Book::where('id', $data['id'])->first();
        if($book){
            $book->update([
                'title' => $data['title'],
                'author' => $data['author'],
                'published_year' => $data['published_year'],
                'genre' => $data['genre'],
            ]);
            return $book;
        } else {
            return response()->json(['success' => false, 'message' => "Book not exist"], 404);
        }
    }

    public function delBooks(Request $request)
    {
        $delete = Book::destroy($request->data_id);         
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Book deleted Successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'An error occurred while deleting.'], 500);
        }
    }

    public function updateBooks(Request $request)
    {
        try {
            $customMsgs = [
                'title.required' => 'Please Provide Title',
                'author.required' => 'Please Provide Author',
                'published_year.required' => 'Please Provide Published Year',
                'genre.required' => 'Please Provide Genre',
            ];
            
            $validator = Validator::make($request->all(), [
                'title' => [
                    'required',
                    'string',
                    'min:0',
                    'max:50',
                    Rule::unique('books')->ignore($request->id),
                ],
                'author' => 'required|string|min:0|max:20',
                'published_year' => 'required|integer|digits:4',
                'genre' => 'required',
            ], $customMsgs);
            
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }        
            $data = $request->all();
            $check = $this->update($data);
            if ($check) {
                return response()->json(['success' => true, 'message' => "Book updated successfully"], 200);
            } else {
                return response()->json(['success' => false, 'message' => "Failed to create data"], 500);
            }
        } catch(\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An error occurred while updating.'], 500);
        } 
    }
}