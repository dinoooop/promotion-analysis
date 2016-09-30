<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class PostsController extends BaseController {

    private $gform;
    private $ngform_html;
    private $posts;

    public function __construct() {
        $this->gform = new Gform;
        $this->ngform_html = new FormHtmlNg;
        $this->posts = new Post;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $data = array();
        $data['records'] = Post::paginate(10);
        return View::make('posts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {

        $data = array();

        $form = $this->gform->set_form(AppForms::get_form('pv_create_post'));

        $data['form_create'] = $this->ngform_html->create_form($form);

        return View::make('posts.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        $input = Input::all();

        $validation = Validator::make($input, Post::$form_create_rules);

        if ($validation->passes()) {

            $input['slug'] = $this->posts->get_slug($input['title']);
            Post::create($input);

            return Redirect::route('posts.index');
        }

        return Redirect::route('posts.create')
                        ->withInput()
                        ->withErrors($validation)
                        ->with('message', 'There were validation errors.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {

        $data = array();
        $data['records'] = Post::find($id);
        return View::make('posts.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $data = array();
        $data['record'] = Post::find($id);
        $form = $this->gform->set_form(AppForms::get_form('pv_edit_post', $id));
        $data['form_edit'] = $this->ngform_html->create_form($form);
        return View::make('posts.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id) {

        $input = Input::all();
        $validation = Validator::make($input, Post::$form_edit_rules);

        if ($validation->passes()) {
            $input['slug'] = $this->posts->get_slug($input['title'], $id);
            $record = Post::find($id);
            $record->update($input);
            return Redirect::route('posts.index');
        }

        return Redirect::route('posts.edit', $id)
                        ->withInput()
                        ->withErrors($validation)
                        ->with('message', 'There were validation errors.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Post::find($id)->delete();
        exit();
        //return Redirect::route('posts.index');
    }

}
