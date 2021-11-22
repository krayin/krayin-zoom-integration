<?php

namespace Webkul\ZoomMeeting\Http\Controllers;

use Webkul\ZoomMeeting\Services\Zoom;
use Webkul\ZoomMeeting\Repositories\UserRepository;
use Webkul\ZoomMeeting\Repositories\AccountRepository;

class AccountController extends Controller
{
    /**
     * Zoom object
     *
     * @var \Webkul\ZoomMeeting\Services\Zoom
     */
    protected $zoom;

    /**
     * UserRepository object
     *
     * @var \Webkul\ZoomMeeting\Repositories\UserRepository
     */
    protected $userRepository;

    /**
     * AccountRepository object
     *
     * @var \Webkul\ZoomMeeting\Services\AccountRepository
     */
    protected $accountRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\ZoomMeeting\Services\Zoom  $zoom
     * @param \Webkul\ZoomMeeting\Repositories\UserRepository  $userRepository
     * @param \Webkul\ZoomMeeting\Repositories\AccountRepository  $accountRepository
     *
     * @return void
     */
    public function __construct(
        Zoom $zoom,
        UserRepository $userRepository,
        AccountRepository $accountRepository
    )
    {
        $this->zoom = $zoom;

        $this->userRepository = $userRepository;

        $this->accountRepository = $accountRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $account = $this->accountRepository->findOneByField('user_id', auth()->user()->id);

        return view('zoom_meeting::index', compact('account'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        if (! request()->has('code')) {
            return redirect($this->zoom->createAuthUrl());
        }
        
        $token = $this->zoom->getAccessToken(request()->get('code'));

        $account = $this->zoom->getUserInfo($token);
        
        $this->userRepository->find(auth()->user()->id)->accounts()->updateOrCreate(
            [
                'zoom_id' => $account['account_id'],
            ],
            [
                'name'   => $account['email'],
                'token'  => $token,
            ]
        );
    
        return redirect()->route('admin.zoom_meeting.index');
    }

    /**
     * Create zoom meeting link
     * 
     * @return \Illuminate\Http\Response
     */
    public function createLink()
    {
        $account = $this->accountRepository->findOneByField('user_id', auth()->user()->id);

        $meeting = $this->zoom->createMeeting($account, request()->all());

        if (is_string($meeting)) {
            return response()->json([
                'message' => $meeting,
            ], 401);
        }

        return response()->json([
            'link'    => $meeting->join_url,
            'comment' => '──────────<br/><br/>You are invited to join Zoom meeting.<br/><br/>Join the Zoom meeting: <a href="' . $meeting->join_url . '" target="_blank">' . $meeting->join_url . '</a><br/>Password: ' . $meeting->password . '<br/><br/>──────────'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  integer  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->accountRepository->destroy($id);

        session()->flash('success', trans('zoom_meeting::app.destroy-success'));

        return redirect()->back();
    }
}
