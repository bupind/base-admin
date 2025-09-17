<?php

namespace Base\Admin\Controllers;

use Base\Admin\Facades\Admin;
use Base\Admin\Form;
use Base\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @var string
     */
    protected $loginView = 'backend::login';

    /**
     * Show the login page.
     *
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function getLogin()
    {
        if($this->guard()->check()) {
            return redirect($this->redirectPath());
        }
        return view($this->loginView);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Admin::guard();
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if(method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }
        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('backend.route.prefix');
    }

    /**
     * Handle a login request.
     *
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $rate_limit_key = 'login-tries-' . Admin::guardName();
        $this->loginValidator($request->all())->validate();
        $credentials = $request->only([$this->username(), 'password']);
        $remember    = $request->get('remember', false);
        if($this->guard()->attempt($credentials, $remember)) {
            RateLimiter::clear($rate_limit_key);
            return $this->sendLoginResponse($request);
        }
        if(config('backend.auth.throttle_logins')) {
            $throttle_timeout = config('backend.auth.throttle_timeout', 600);
            RateLimiter::hit($rate_limit_key, $throttle_timeout);
        }
        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Get a validator for an incoming login request.
     *
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            $this->username() => 'required',
            'password'        => 'required',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

    /**
     * Send the response after the user was authenticated.
     *
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        admin_toastr(trans('backend.login_successful'));
        $request->session()->regenerate();
        return redirect()->intended($this->redirectPath());
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? trans('auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect(config('backend.route.prefix'));
    }

    /**
     * User setting page.
     *
     *
     * @return Content
     */
    public function getSetting(Content $content)
    {
        $form = $this->settingForm();
        $form->tools(
            function(Form\Tools $tools) {
                $tools->disableList();
                $tools->disableDelete();
                $tools->disableView();
            }
        );
        return $content
            ->title(trans('backend.user_setting'))
            ->body($form->edit(Admin::user()->id));
    }

    /**
     * Model-form for user setting.
     *
     * @return Form
     */
    protected function settingForm()
    {
        $class = config('backend.database.users_model');
        $form  = new Form(new $class);
        $form->display('username', trans('backend.username'));
        $form->text('name', trans('backend.name'))->rules('required');
        $form->image('avatar', trans('backend.avatar'));
        $form->password('password', trans('backend.password'))->rules('confirmed|required');
        $form->password('password_confirmation', trans('backend.password_confirmation'))->rules('required')
            ->default(function($form) {
                return $form->model()->password;
            });
        $form->setAction(admin_url('auth/setting'));
        $form->ignore(['password_confirmation']);
        $form->saving(function(Form $form) {
            if($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });
        $form->saved(function() {
            admin_toastr(trans('backend.update_succeeded'));
            return redirect(admin_url('auth/setting'));
        });
        return $form;
    }

    /**
     * Update user setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putSetting()
    {
        return $this->settingForm()->update(Admin::user()->id);
    }
}
