<?php

namespace Webkul\Mobikul\Http\Controllers;

use Webkul\Mobikul\Helpers\SendNotification;

/**
 * Order controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class OrderController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * SendNotification object
     *
     * @var array
     */
    protected $sendNotification;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;


    public function __construct(
        SendNotification $sendNotification
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->sendNotification = $sendNotification;
    }

    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * send the notification to the device
     *
     * @return Response
     */
    public function sendNotification($id)
    {
        $message = "Bagisto Api Noification";
        $this->sendNotification->sendGCM($message, $id);
    }
}