<?php

namespace App\Http\Controllers;

use App\Helper\JWTToken;
use App\Mail\OTPMail;
use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Inertia\Controller;
use Inertia\Inertia;

class UserController extends Controller
{
    public function LoginPage(Request $request)
    {
        return Inertia::render('LoginPage');
    }

    public function RegistrationPage(Request $request)
    {
        return Inertia::render('RegistrationPage');
    }

    public function SendOTPPage(Request $request)
    {
        return Inertia::render('SendOTPPage');
    }

    public function VerifyOTPPage(Request $request)
    {
        return Inertia::render('VerifyOTPPage');
    }

    public function ResetPasswordPage(Request $request)
    {
        return Inertia::render('ResetPasswordPage');
    }

    public function UserRegistration(Request $request)
    {

        try {
            $request->validate([
                'username' => 'required|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $profilePicPath = 'profile_pics/default.png';

            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile_pics'), $fileName);
                $profilePicPath = 'uploads/profile_pics/' . $fileName;
            }

            $user = User::create([
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'profile_pic' => $profilePicPath,
            ]);

            return redirect('/login')->with([
                'message' => 'User registered successfully!',
                'status' => true,
            ]);
        } catch (Exception $e) {
            return redirect('/registration')->with([
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'status' => false,
            ]);
        }
    }

    public function UserLogin(Request $request)
    {
        $user = User::where('email', $request->input('email'))
            ->where('password', $request->input('password'))
            ->first();

        if ($user) {
            $request->session()->put('email', $user->email);
            $request->session()->put('user_id', $user->id);

            return redirect('/DashboardPage')->with([
                'message' => 'User login successfully',
                'status' => true,
                'error' => ''
            ]);
        } else {
            return redirect('/login')->with([
                'message' => 'Login failed',
                'status' => false,
                'error' => ''
            ]);
        }
    }

    public function DashboardPage(Request $request)
    {
        $userId = Session::get('user_id');
        $user = User::find($userId);

        if (!$user) {
            abort(404, 'User not found');
        }

        $profilePic = $user->profile_pic && file_exists(public_path($user->profile_pic))
            ? $user->profile_pic
            : '/default-user.png';

        return Inertia::render('DashboardPage', [
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'profile_pic' => $profilePic,
            ],
            'stats' => [
                'posts' => $user->posts()->count(),
                'comments' => $user->comments()->count(),
                'bookmarks' => $user->bookmarks()->count(),
                'drafts' => $user->posts()->where('visibility', 'private')->count(),
            ]
        ]);
    }


    public function UserLogout(Request $request)
    {

        $request->session()->forget('email');
        $request->session()->forget('user_id');

        $data = ['message' => 'User logout successfully', 'status' => true, 'error' => ''];
        return redirect('/login')->with($data);
    }

    public function SendOTPCode(Request $request)
    {
        $email = $request->input('email');
        $otp = rand(1000, 9999);

        $count = User::where('email', $email)->count();

        if ($count == 1) {
            User::where('email', $email)->update(['otp' => $otp]);
            $request->session()->put('email', $email);

            $data = ["message" => "4 Digit {$otp} OTP send successfully", "status" => true, "error" => ''];
            return redirect('/verify-otp')->with($data);
        } else {
            $data = ['message' => 'unauthorized', 'status' => false, 'error' => ''];
            return redirect('/registration')->with($data);
        }
    }

    public function VerifyOTP(Request $request)
    {
        $email = $request->session()->get('email');
        $otp = $request->input('otp');

        $count = User::where('email', $email)->where('otp', $otp)->count();

        if ($count == 1) {
            User::where('email', $email)->update(['otp' => 0]);

            $request->session()->put('otp_verify', 'yes');

            $data = ["message" => "OTP verification successfully", "status" => true, "error" => ''];
            return redirect('/reset-password')->with($data);
        } else {
            $data = ['message' => 'unauthorized', 'status' => false, 'error' => ''];
            return redirect('/login')->with($data);
        }
    }

    public function ResetPassword(Request $request)
    {
        try {
            $email = $request->session()->get('email', 'default');
            $password = $request->input('password');

            $otp_verify = $request->session()->get('otp_verify', 'default');
            if ($otp_verify === 'yes') {
                User::where('email', $email)->update(['password' => $password]);
                $request->session()->flush();

                $data = ['message' => 'Password reset successfully', 'status' => true, 'error' => ''];
                return redirect('/login')->with($data);
            } else {
                $data = ['message' => 'Request fail', 'status' => false, 'error' => ''];
                return redirect('/reset-password')->with($data);
            }
        } catch (Exception $e) {
            $data = ['message' => $e->getMessage(), 'status' => false, 'error' => ''];
            return redirect('/reset-password')->with($data);
        }
    }

    public function ProfilePage(Request $request)
    {
        $email = Session::get('email');
        $user = User::where('email', $email)->first();

        $user->profile_pic = $user->profile_pic && file_exists(public_path($user->profile_pic))
            ? $user->profile_pic
            : '/default-user.png';

        return Inertia::render('ProfilePage', ['user' => $user]);
    }

    public function UserUpdate(Request $request)
    {
        $email = $request->header('email');
        $user = User::where('email', $email)->first();

        $request->validate([
            'username' => 'required|string|max:255',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_pic')) {
            if ($user->profile_pic && file_exists(public_path('uploads/profile_pics/' . $user->profile_pic))) {
                unlink(public_path('uploads/profile_pics/' . $user->profile_pic));
            }

            $image = $request->file('profile_pic');
            // $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/profile_pics'), $imageName);
            $user->profile_pic = $imageName;
        }

        $user->username = $request->input('username');
        $user->save();

        return redirect()->back()->with([
            'message' => 'Profile updated successfully!',
            'status' => true
        ]);
    }
}
