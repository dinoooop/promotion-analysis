<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Stock;
use App\Gform;
use App\AppForms;
use App\FormHtmlJq;

class UsersController extends Controller {

    private $gform;
    private $form_html_jq;
    private $users;

    public function __construct() {
        $this->middleware('role')->only('index');
        
        $this->gform = new Gform;
        $this->form_html_jq = new FormHtmlJq;
        $this->users = new User;
    }

    public function index() {

        $data = array();

        

        $data['records'] = User::paginate(10);
        return View::make('admin.users.index', $data);
    }

    public function create() {

        $data = array();

        $form = $this->gform->set_form(AppForms::get_form('pv_form_user'));
        $data['form_create'] = $this->form_html_jq->create_form($form);

        return View::make('admin.users.create', $data);
    }

    public function store() {
        $input = Input::all();
        $validation = Validator::make($input, User::$rule_create);

        if ($validation->passes()) {

            $input['password'] = bcrypt($input['password']);

            User::create($input);

            return Redirect::route('users.index');
        }

        return Redirect::route('users.create')
                        ->withInput()
                        ->withErrors($validation)
                        ->with('message', 'There were validation errors.');
    }

    /**
     * 
     * Not using
     * @param type $id
     * @return type
     */
    public function show($id) {
        $user = User::find($id);
        if (is_null($user)) {
            return Redirect::route('users.index');
        }
        return View::make('admin.users.show', compact('user'));
    }

    public function edit($id) {

        if (Auth::user()->id == $id) {
            return Redirect::to('admin/profile');
        }

        return $this->show_edit($id);
    }

    
    public function update($id) {

        $input = Input::all();

        $validation = Validator::make($input, User::rule_edit($id));

        $password_update = true;
        if (isset($input['password']) && $input['password'] != '') {
            if (($input['password'] == $input['confirm_password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $password_update = false;
            }
        } else {
            unset($input['password']);
        }

        if ($validation->passes() && $password_update) {
            $user = User::find($id);
            $user->update($input);
            return Redirect::route('users.index');
        }

        return Redirect::route('users.edit', $id)
                        ->withInput()
                        ->withErrors($validation)
                        ->withErrors(['Error', 'Failed to update'])
                        ->with('message', 'There were validation errors.');
    }

    public function destroy($id) {
        User::find($id)->delete();
        exit();
    }

    // custom function 

    public function login() {

        //return View::make('admin.users.login');

        $input = Input::all();

        if (isset($input['username'], $input['password'])) {

            if (Auth::attempt(array('username' => $input['username'], 'password' => $input['password']))) {
                return Redirect::to('admin/profile');
            } else {
                $message = "Invalid username or password.";
                return View::make('admin.users.login', compact('message'));
            }
        }

        if (Auth::check()) {
            return Redirect::to('admin/profile');
        } else {
            return View::make('admin.users.login');
        }
    }

    function logout() {
        Auth::logout();
        return Redirect::to('admin');
    }

    function profile() {

        if (!isset(Auth::user()->id)) {
            return Redirect::to('admin');
        }

        $id = Auth::user()->id;

        return $this->show_edit($id);
    }

    function show_edit($id) {
        $data = array();
        $data['record'] = User::findOrFail($id)->toArray();

        $form = AppForms::get_form('pv_form_user');
        $form['form_name'] = 'pv_form_edit_user';
        $form = $this->gform->set_form($form, $data['record']);

        if (!User::hasrole('user_edit_role', $id)) {
            $form['fields'] = $this->gform->set_not_required_fields($form['fields'], array('role'));
        }

        $data['form_edit'] = $this->form_html_jq->create_form($form);
        return View::make('admin.users.edit', $data);
    }

}
