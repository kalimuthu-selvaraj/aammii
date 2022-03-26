<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;

/**
 * Notifications Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class NotificationsRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\Notification';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Mobikul\Contracts\Notification
     */
    public function create(array $data)
    {
        Event::dispatch('mobikul.notification.create.before');

        $notification = $this->model->create($data);

        $this->uploadImages($data, $notification);

        Event::dispatch('mobikul.notification.create.after', $notification);
        
        return $notification;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Mobikul\Contracts\Notification
     */
    public function update(array $data, $id, $attribute = "id")
    {
        Event::dispatch('mobikul.notification.update.before', $id);

        $notification = $this->find($id);
        
        $notification->update($data);
        
        $this->uploadImages($data, $notification);
        
        Event::dispatch('mobikul.notification.update.after', $notification);

        return $notification;
    }

    /**
     * @param  array  $data
     * @param  \Webkul\Mobikul\Contracts\Notification  $notification
     * @return void
     */
    public function uploadImages($data, $notification, $type = "image")
    {
        if (isset($data[$type])) {
            $request = request();

            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'notification/images/' . $notification->id;

                if ($request->hasFile($file)) {
                    if ($notification->{$type}) {
                        Storage::delete($notification->{$type});
                    }

                    $notification->{$type} = $request->file($file)->store($dir);
                    $notification->save();
                }
            }
        } else {
            if ($notification->{$type}) {
                Storage::delete($notification->{$type});
            }

            $notification->{$type} = null;
            $notification->save();
        }
    }
}

