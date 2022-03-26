<?php

namespace Webkul\PriceDropAlert\Http\Controllers\Admin;

use Illuminate\Support\Facades\Event;
use Webkul\PriceDropAlert\Repositories\EmailTemplateRepository;

class EmailTemplateController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * EmailTemplateRepository object
     *
     * @var \Webkul\PriceDropAlert\Repositories\EmailTemplateRepository
     */
    protected $emailTemplateRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\PriceDropAlert\Repositories\EmailTemplateRepository  $emailTemplateRepository
     * @return void
     */
    public function __construct(EmailTemplateRepository $emailTemplateRepository)
    {
        $this->emailTemplateRepository = $emailTemplateRepository;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'              => 'required|string',
            'subject'           => 'required|string',
            'message'           => 'required|string',
        ]);

        $data = request()->all();

        $channel = $this->emailTemplateRepository->create($data);

        session()->flash('success', trans('price_drop::app.admin.email-template.create-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $email_template = $this->emailTemplateRepository->findOrFail($id);

        return view($this->_config['view'], compact('email_template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $locale = request()->get('locale') ?: app()->getLocale();

        $this->validate(request(), [
            $locale . '.name'       => 'required|string',
            $locale . '.subject'    => 'required|string',
            $locale . '.message'    => 'required|string',
        ]);

        $data = request()->all();

        $email_template = $this->emailTemplateRepository->update($data, $id);

        session()->flash('success', trans('price_drop::app.admin.email-template.update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $email_template = $this->emailTemplateRepository->findOrFail($id);

        try {
            Event::dispatch('admin.pricedrop.delete.before', $id);

            $this->emailTemplateRepository->delete($id);

            Event::dispatch('admin.pricedrop.delete.after', $id);

            session()->flash('success', trans('price_drop::app.admin.email-template.delete-success'));

            return response()->json(['message' => true], 200);
        } catch(\Exception $e) {
            session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Email Template']));
        }

        return response()->json(['message' => false], 400);
    }
}