<?php

namespace App\Services;

use App\Models\CalEvent;
use App\Models\CalEventGuest;
use App\Models\CalEventNotification;
use App\Traits\FireNotification;
use App\Traits\UserInfoCollector;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalEventServices
{
    use UserInfoCollector, FireNotification;

    public function storeEvent($validated_data): array
    {
        DB::beginTransaction();
        try {

            $recurrence_count = 0;
            $recurrence = $validated_data['recurrence'];
            if ($recurrence == 'weekly') {
                $recurrence_count = 51;
            }
            if ($recurrence == 'daily') {
                $recurrence_count = 364;
            }
            if ($recurrence == 'monthly') {
                $recurrence_count = 11;
            }

            $start_date = Carbon::parse($validated_data['start_date'])->format('Y-m-d');
            $start_time = $validated_data['start_time'] ?: Carbon::createFromDate($start_date)->startOfDay()->format('H:i A');

            if ($validated_data['all_day'] == 1) {
                $end_date = $validated_data['end_date'] ? Carbon::parse($validated_data['end_date'])->format('Y-m-d') : $start_date;
            } else {
                $end_date = $start_date;
            }
            $end_time = $validated_data['end_time'] ?: Carbon::createFromDate($end_date)->endOfDay()->format('H:i A');

            $start_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$start_date $start_time", new \DateTimeZone('Asia/Dhaka'));
            $end_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$end_date $end_time", new \DateTimeZone('Asia/Dhaka'));

            $event_data = [
                'event_title_en' => $validated_data['title'],
                'event_title_bn' => $validated_data['title'],
                'event_description' => \Arr::has($validated_data, 'description') ? $validated_data['description'] : null,
                'event_start_date_time' => $start_date_time->format('Y-m-d'),
                'event_end_date_time' => $end_date_time->format('Y-m-d'),
                'event_start_date' => $start_date,
                'event_end_date' => $end_date,
                'event_start_time' => $start_date_time->format('H:i:s'),
                'event_end_time' => $end_date_time->format('H:i:s'),
                'all_day' => $validated_data['all_day'],
                'event_location' => \Arr::has($validated_data, 'location') ? $validated_data['location'] : null,
                'event_type' => 'workshop',
                'event_visibility' => $validated_data['visibility'],
                'event_previous_link' => \Arr::has($validated_data, 'event_previous_link') ? $validated_data['event_previous_link'] : null,
                'status' => 'active',
                'recurrence' => $recurrence,
            ];

            $created_event = CalEvent::create($event_data);

            $event_guests = [];

            $invited_participants = json_decode($validated_data['invited_participants'], true);

            if ($invited_participants) {
                foreach ($invited_participants as $invited_participant) {
                    $event_guests[] = [
                        'event_id' => $created_event->id,
                        'user_email' => $invited_participant['user_email'],
                        'user_name_en' => $invited_participant['user_name_en'],
                        'user_name_bn' => $invited_participant['user_name_bn'],
                        'username' => \Arr::has($invited_participant, 'username') ? $invited_participant['username'] : null,
                        'user_phone' => \Arr::has($invited_participant, 'user_phone') ? $invited_participant['user_phone'] : null,
                        'user_officer_id' => \Arr::has($invited_participant, 'user_officer_id') ? $invited_participant['user_officer_id'] : null,
                        'user_designation_id' => \Arr::has($invited_participant, 'user_designation_id') ? $invited_participant['user_designation_id'] : null,
                        'user_office_id' => \Arr::has($invited_participant, 'user_office_id') ? $invited_participant['user_office_id'] : null,
                        'user_office_name_en' => \Arr::has($invited_participant, 'user_office_name_en') ? $invited_participant['user_office_name_en'] : null,
                        'user_office_name_bn' => \Arr::has($invited_participant, 'user_office_name_bn') ? $invited_participant['user_office_name_bn'] : null,
                        'user_unit_id' => \Arr::has($invited_participant, 'user_unit_id') ? $invited_participant['user_unit_id'] : null,
                        'user_office_unit_name_en' => \Arr::has($invited_participant, 'user_office_unit_name_en') ? $invited_participant['user_office_unit_name_en'] : null,
                        'user_office_unit_name_bn' => \Arr::has($invited_participant, 'user_office_unit_name_bn') ? $invited_participant['user_office_unit_name_bn'] : null,
                        'user_designation_name_en' => \Arr::has($invited_participant, 'user_designation_name_en') ? $invited_participant['user_designation_name_en'] : null,
                        'user_designation_name_bn' => \Arr::has($invited_participant, 'user_designation_name_bn') ? $invited_participant['user_designation_name_bn'] : null,
                        'user_type' => \Arr::has($invited_participant, 'user_type') ? $invited_participant['user_type'] : null,
                        'visibility_type' => \Arr::has($invited_participant, 'user_designation_name_bn') ? $validated_data['visibility'] : 'public',
                        'tag_color' => \Arr::has($validated_data, 'tag_color') ? $validated_data['tag_color'] : 'fc fc-event-success',
                        'acceptance_status' => 'accepted',
                    ];
                }
            }

            $created_guests = CalEventGuest::insert($event_guests);

            $notifications = json_decode($validated_data['notifications'], true);
            $processed_notifications = [];
            if ($notifications) {
                foreach ($event_guests as $event_guest) {
                    foreach ($notifications as $notification) {
                        if ($notification['unit'] == 'minutes') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subMinutes($notification['interval']);
                        } elseif ($notification['unit'] == 'hours') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subHours($notification['interval']);
                        } elseif ($notification['unit'] == 'days') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subDays($notification['interval']);
                        } elseif ($notification['unit'] == 'weeks') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subWeeks($notification['interval']);
                        } else {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'));
                        }

                        $processed_notification = [
                            'event_id' => $created_event->id,
                            'user_email' => $event_guest['user_email'],
                            'user_officer_id' => $event_guest['user_officer_id'],
                            'user_designation_id' => $event_guest['user_designation_id'],
                            'username' => $event_guest['username'],
                            'event_notification' => $notification_time->format('Y-m-d H:i:s'),
                            'notification_medium' => $notification['medium'],
                            'unit' => $notification['unit'],
                            'interval' => $notification['interval'],
                            'is_dispatched' => $notification['is_dispatched'],
                        ];
                        $processed_notifications[] = $processed_notification;

                        $mail_notification_data = [
                            'event_title' => $validated_data['title'],
                            'user_email' => $event_guest['user_email'],
                            'event_description' => $validated_data['description'],
                            'event_start' => $start_date_time->format('Y-m-d H:i:s'),
                            'event_end' => $end_date_time->format('Y-m-d H:i:s'),
                            'event_location' => $validated_data['location'],
                        ];

                        $this->sendMailNotification($mail_notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
                    }
                }
            }
            $created_notifications = CalEventNotification::insert($processed_notifications);

            if ($recurrence_count > 0) {
                for ($i = 1; $i <= $recurrence_count; $i++) {
                    $start_date = Carbon::parse($validated_data['start_date'])->format('Y-m-d');
                    $start_time = $validated_data['start_time'] ?: Carbon::createFromDate($start_date)->startOfDay()->format('H:i A');

                    if ($validated_data['all_day'] == 1) {
                        $end_date = $validated_data['end_date'] ? Carbon::parse($validated_data['end_date'])->format('Y-m-d') : $start_date;
                    } else {
                        $end_date = $start_date;
                    }
                    $end_time = $validated_data['end_time'] ?: Carbon::createFromDate($end_date)->endOfDay()->format('H:i A');

                    $start_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$start_date $start_time", new \DateTimeZone('Asia/Dhaka'));
                    $end_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$end_date $end_time", new \DateTimeZone('Asia/Dhaka'));

                    if ($recurrence == 'daily') {
                        $start_date_time = $start_date_time->add(new \DateInterval('P' . $i . 'D'));
                        $end_date_time = $end_date_time->add(new \DateInterval('P' . $i . 'D'));
                        $start_date = Carbon::parse($start_date)->addDays($i);
                        $end_date = Carbon::parse($end_date)->addDays($i);
                    }
                    if ($recurrence == 'weekly') {
                        $start_date_time = $start_date_time->add(new \DateInterval('P' . $i . 'W'));
                        $end_date_time = $end_date_time->add(new \DateInterval('P' . $i . 'W'));
                        $start_date = Carbon::parse($start_date)->addWeeks($i);
                        $end_date = Carbon::parse($end_date)->addWeeks($i);
                    }
                    if ($recurrence == 'monthly') {
                        $start_date_time = $start_date_time->add(new \DateInterval('P' . $i . 'M'));
                        $end_date_time = $end_date_time->add(new \DateInterval('P' . $i . 'M'));
                        $start_date = Carbon::parse($start_date)->addMonths($i);
                        $end_date = Carbon::parse($end_date)->addMonths($i);
                    }

                    $event_data = [
                        'event_title_en' => $validated_data['title'],
                        'event_title_bn' => $validated_data['title'],
                        'event_description' => \Arr::has($validated_data, 'description') ? $validated_data['description'] : null,
                        'event_start_date_time' => $start_date_time->format('Y-m-d H:i:s'),
                        'event_end_date_time' => $end_date_time->format('Y-m-d H:i:s'),
                        'event_start_date' => $start_date,
                        'event_end_date' => $end_date,
                        'event_start_time' => $start_date_time->format('H:i:s'),
                        'event_end_time' => $end_date_time->format('H:i:s'),
                        'all_day' => $validated_data['all_day'],
                        'event_location' => \Arr::has($validated_data, 'location') ? $validated_data['location'] : null,
                        'event_type' => 'workshop',
                        'event_visibility' => $validated_data['visibility'],
                        'event_previous_link' => \Arr::has($validated_data, 'event_previous_link') ? $validated_data['event_previous_link'] : null,
                        'status' => 'active',
                        'recurrent_cal_id' => $created_event->id,
                    ];

                    $created_recurring_event = CalEvent::create($event_data);

                    $event_guests = [];

                    $invited_participants = json_decode($validated_data['invited_participants'], true);

                    if ($invited_participants) {
                        foreach ($invited_participants as $invited_participant) {
                            $event_guests[] = [
                                'event_id' => $created_recurring_event->id,
                                'user_email' => $invited_participant['user_email'],
                                'user_name_en' => $invited_participant['user_name_en'],
                                'user_name_bn' => $invited_participant['user_name_bn'],
                                'username' => \Arr::has($invited_participant, 'username') ? $invited_participant['username'] : null,
                                'user_phone' => \Arr::has($invited_participant, 'user_phone') ? $invited_participant['user_phone'] : null,
                                'user_officer_id' => \Arr::has($invited_participant, 'user_officer_id') ? $invited_participant['user_officer_id'] : null,
                                'user_designation_id' => \Arr::has($invited_participant, 'user_designation_id') ? $invited_participant['user_designation_id'] : null,
                                'user_office_id' => \Arr::has($invited_participant, 'user_office_id') ? $invited_participant['user_office_id'] : null,
                                'user_office_name_en' => \Arr::has($invited_participant, 'user_office_name_en') ? $invited_participant['user_office_name_en'] : null,
                                'user_office_name_bn' => \Arr::has($invited_participant, 'user_office_name_bn') ? $invited_participant['user_office_name_bn'] : null,
                                'user_unit_id' => \Arr::has($invited_participant, 'user_unit_id') ? $invited_participant['user_unit_id'] : null,
                                'user_office_unit_name_en' => \Arr::has($invited_participant, 'user_office_unit_name_en') ? $invited_participant['user_office_unit_name_en'] : null,
                                'user_office_unit_name_bn' => \Arr::has($invited_participant, 'user_office_unit_name_bn') ? $invited_participant['user_office_unit_name_bn'] : null,
                                'user_designation_name_en' => \Arr::has($invited_participant, 'user_designation_name_en') ? $invited_participant['user_designation_name_en'] : null,
                                'user_designation_name_bn' => \Arr::has($invited_participant, 'user_designation_name_bn') ? $invited_participant['user_designation_name_bn'] : null,
                                'user_type' => \Arr::has($invited_participant, 'user_type') ? $invited_participant['user_type'] : null,
                                'visibility_type' => \Arr::has($invited_participant, 'user_designation_name_bn') ? $validated_data['visibility'] : 'public',
                                'tag_color' => \Arr::has($validated_data, 'tag_color') ? $validated_data['tag_color'] : 'fc fc-event-success',
                                'acceptance_status' => 'accepted',
                            ];
                        }
                    }

                    $created_guests = CalEventGuest::insert($event_guests);

                    $notifications = json_decode($validated_data['notifications'], true);
                    $processed_notifications = [];
                    if ($notifications) {
                        foreach ($event_guests as $event_guest) {
                            foreach ($notifications as $notification) {
                                if ($notification['unit'] == 'minutes') {
                                    $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subMinutes($notification['interval']);
                                } elseif ($notification['unit'] == 'hours') {
                                    $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subHours($notification['interval']);
                                } elseif ($notification['unit'] == 'days') {
                                    $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subDays($notification['interval']);
                                } elseif ($notification['unit'] == 'weeks') {
                                    $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subWeeks($notification['interval']);
                                } else {
                                    $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'));
                                }

                                $processed_notification = [
                                    'event_id' => $created_recurring_event->id,
                                    'user_email' => $event_guest['user_email'],
                                    'user_officer_id' => $event_guest['user_officer_id'],
                                    'user_designation_id' => $event_guest['user_designation_id'],
                                    'username' => $event_guest['username'],
                                    'event_notification' => $notification_time->format('Y-m-d H:i:s'),
                                    'notification_medium' => $notification['medium'],
                                    'unit' => $notification['unit'],
                                    'interval' => $notification['interval'],
                                    'is_dispatched' => $notification['is_dispatched'],
                                ];
                                $processed_notifications[] = $processed_notification;

                                $mail_notification_data = [
                                    'event_title' => $validated_data['title'],
                                    'user_email' => $event_guest['user_email'],
                                    'event_description' => $validated_data['description'],
                                    'event_start' => $start_date_time->format('Y-m-d H:i:s'),
                                    'event_end' => $end_date_time->format('Y-m-d H:i:s'),
                                    'event_location' => $validated_data['location'],
                                ];

                                $this->sendMailNotification($mail_notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
                            }
                        }
                    }
                    $created_notifications = CalEventNotification::insert($processed_notifications);
                }
            }

            if ($created_guests && $created_event) {
                DB::commit();
                return ['status' => 'success', 'data' => 'Created!'];
            } else {
                throw new \Exception('guests =>' . $created_guests . ' event => ' . json_encode($created_event->toArray()));
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }

    public function updateEvent($validated_data): array
    {
        DB::beginTransaction();
        try {
            $start_date = Carbon::parse($validated_data['start_date'])->format('Y-m-d');
            $start_time = $validated_data['start_time'] ?: Carbon::createFromDate($start_date)->startOfDay()->format('H:i A');

            if ($validated_data['all_day'] == 1) {
                $end_date = $validated_data['end_date'] ? Carbon::parse($validated_data['end_date'])->format('Y-m-d') : $start_date;
            } else {
                $end_date = $start_date;
            }
            $end_time = $validated_data['end_time'] ?: Carbon::createFromDate($end_date)->endOfDay()->format('H:i A');

            $start_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$start_date $start_time", new \DateTimeZone('Asia/Dhaka'));
            $end_date_time = \DateTime::createFromFormat('Y-m-d H:i A', "$end_date $end_time", new \DateTimeZone('Asia/Dhaka'));

            $event_data = [
                'event_title_en' => $validated_data['title'],
                'event_title_bn' => $validated_data['title'],
                'event_description' => \Arr::has($validated_data, 'description') ? $validated_data['description'] : null,
                'event_start_date_time' => $start_date_time->format('Y-m-d H:i:s'),
                'event_end_date_time' => $end_date_time->format('Y-m-d H:i:s'),
                'event_start_date' => $start_date,
                'event_end_date' => $end_date,
                'event_start_time' => $start_date_time->format('H:i:s'),
                'event_end_time' => $end_date_time->format('H:i:s'),
                'all_day' => $validated_data['all_day'],
                'event_location' => \Arr::has($validated_data, 'location') ? $validated_data['location'] : null,
                'event_type' => 'workshop',
                'event_visibility' => $validated_data['visibility'],
                'event_previous_link' => \Arr::has($validated_data, 'event_previous_link') ? $validated_data['event_previous_link'] : null,
                'status' => 'active',
            ];

            $updated_event = CalEvent::where('id', $validated_data['event_id'])->update($event_data);

            $update_event_current_user_data = ['tag_color' => $validated_data['tag_color']];
            $updated_event_current_user = CalEventGuest::where('event_id', $validated_data['event_id'])->where('user_officer_id', $this->getOfficerId())->update($update_event_current_user_data);

            $created_event = CalEvent::where('id', $validated_data['event_id'])->first();

            $event_guests = [];

            $invited_participants = json_decode($validated_data['invited_participants'], true);

            $existing_assigned_users = CalEventGuest::where('event_id', $validated_data['event_id'])->pluck('user_officer_id');
            $existing_assigned_users = $existing_assigned_users && $existing_assigned_users->count() > 0 ? $existing_assigned_users->toArray() : [];
            if ($invited_participants) {
                foreach ($invited_participants as $invited_participant) {
                    if (!in_array($invited_participant['user_officer_id'], $existing_assigned_users)) {
                        $event_guests[] = [
                            'event_id' => $created_event->id,
                            'user_email' => $invited_participant['user_email'],
                            'user_name_en' => $invited_participant['user_name_en'],
                            'user_name_bn' => $invited_participant['user_name_bn'],
                            'username' => \Arr::has($invited_participant, 'username') ? $invited_participant['username'] : null,
                            'user_phone' => \Arr::has($invited_participant, 'user_phone') ? $invited_participant['user_phone'] : null,
                            'user_officer_id' => \Arr::has($invited_participant, 'user_officer_id') ? $invited_participant['user_officer_id'] : null,
                            'user_designation_id' => \Arr::has($invited_participant, 'user_designation_id') ? $invited_participant['user_designation_id'] : null,
                            'user_office_id' => \Arr::has($invited_participant, 'user_office_id') ? $invited_participant['user_office_id'] : null,
                            'user_office_name_en' => \Arr::has($invited_participant, 'user_office_name_en') ? $invited_participant['user_office_name_en'] : null,
                            'user_office_name_bn' => \Arr::has($invited_participant, 'user_office_name_bn') ? $invited_participant['user_office_name_bn'] : null,
                            'user_unit_id' => \Arr::has($invited_participant, 'user_unit_id') ? $invited_participant['user_unit_id'] : null,
                            'user_office_unit_name_en' => \Arr::has($invited_participant, 'user_office_unit_name_en') ? $invited_participant['user_office_unit_name_en'] : null,
                            'user_office_unit_name_bn' => \Arr::has($invited_participant, 'user_office_unit_name_bn') ? $invited_participant['user_office_unit_name_bn'] : null,
                            'user_designation_name_en' => \Arr::has($invited_participant, 'user_designation_name_en') ? $invited_participant['user_designation_name_en'] : null,
                            'user_designation_name_bn' => \Arr::has($invited_participant, 'user_designation_name_bn') ? $invited_participant['user_designation_name_bn'] : null,
                            'user_type' => \Arr::has($invited_participant, 'user_type') ? $invited_participant['user_type'] : null,
                            'visibility_type' => \Arr::has($invited_participant, 'user_designation_name_bn') ? $validated_data['visibility'] : 'public',
                            'tag_color' => \Arr::has($validated_data, 'tag_color') ? $validated_data['tag_color'] : 'fc fc-event-success',
                            'acceptance_status' => 'accepted',
                        ];
                    }
                }
            }
            $created_guests = CalEventGuest::insert($event_guests);


            $notifications = json_decode($validated_data['notifications'], true);
            $processed_notifications = [];
            if ($notifications) {
                $user_office_id = $this->getOfficerId();
                $deleted_notification = CalEventNotification::where('user_officer_id', $user_office_id)->where('event_id', $validated_data['event_id'])->where('is_dispatched', 0)->delete();
                $event_guest = CalEventGuest::where('user_officer_id', $user_office_id)->where('event_id', $validated_data['event_id'])->first();
                $event_organizer = CalEventGuest::where('user_type', 'organizer')->where('event_id', $validated_data['event_id'])->first();
                if ($event_guest) {
                    foreach ($notifications as $notification) {
                        if ($notification['unit'] == 'minutes') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subMinutes($notification['interval']);
                        } elseif ($notification['unit'] == 'hours') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subHours($notification['interval']);
                        } elseif ($notification['unit'] == 'days') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subDays($notification['interval']);
                        } elseif ($notification['unit'] == 'weeks') {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'))->subWeeks($notification['interval']);
                        } else {
                            $notification_time = Carbon::create($start_date_time->format('Y-m-d H:i:s'));
                        }

                        $processed_notification = [
                            'event_id' => $created_event->id,
                            'user_email' => $event_guest['user_email'],
                            'user_officer_id' => $event_guest['user_officer_id'],
                            'user_designation_id' => $event_guest['user_designation_id'],
                            'username' => $event_guest['username'],
                            'event_notification' => $notification_time->format('Y-m-d H:i:s'),
                            'notification_medium' => $notification['medium'],
                            'unit' => $notification['unit'],
                            'interval' => $notification['interval'],
                            'is_dispatched' => $notification['is_dispatched'],
                        ];
                        $processed_notifications[] = $processed_notification;

                        if ($notification['medium'] == 'email') {
                            if ($event_guest['user_type'] == 'organizer') {
                                $mail_notification_data = [
                                    'event_title' => $created_event->event_title_en,
                                    'event_description' => $created_event->event_description,
                                    'event_start' => $start_date_time->format('Y-m-d H:i:s'),
                                    'event_end' => $end_date_time->format('Y-m-d H:i:s'),
                                    'sender_email' => $event_guest['user_email'],
                                    'sender_en' => $event_guest['user_name_en'],
                                    'sender_bn' => $event_guest['user_name_bn'],
                                    'receiver_email' => $event_guest['user_email'],
                                    'receiver_en' => $event_guest['user_name_en'],
                                    'receiver_bn' => $event_guest['user_name_bn'],
                                ];
                            } else {
                                $mail_notification_data = [
                                    'event_title' => $created_event->event_title_en,
                                    'event_description' => $created_event->event_description,
                                    'event_start' => $start_date_time->format('Y-m-d H:i:s'),
                                    'event_end' => $end_date_time->format('Y-m-d H:i:s'),
                                    'sender_email' => $event_organizer->user_email,
                                    'sender_en' => $event_organizer->user_name_en,
                                    'sender_bn' => $event_organizer->user_name_bn,
                                    'receiver_email' => $event_guest['user_email'],
                                    'receiver_en' => $event_guest['user_name_en'],
                                    'receiver_bn' => $event_guest['user_name_bn'],
                                ];
                            }

                            $this->sendMailNotification($mail_notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
                        } elseif ($notification['medium'] == 'notification') {
                            if (\Arr::has($event_guest, 'user_officer_id')) {
                                $notification_data = [
                                    'recipient' => "task_manager_rid_" . $event_guest['user_officer_id'],
                                    "title" => $created_event->event_title_en,
                                    "message" => "You Have A Event Shared ",
                                ];
                                $this->sendPushNotification($notification_data, ['dispatch' => 1, 'delay' => $notification_time]);
                            }
                        }
                    }
                }
            }
            $created_notifications = CalEventNotification::insert($processed_notifications);

            if ($created_guests && $created_event) {
                DB::commit();
                return ['status' => 'success', 'data' => 'Created!'];
            } else {
                throw new \Exception('guests =>' . $created_guests . ' event => ' . json_encode($created_event->toArray()));
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return ['status' => 'error', 'data' => $exception->getMessage()];
        }
    }


    public function dailyEvents()
    {
        $user_event_ids = CalEventGuest::all();
    }

    public function loadEventsByEmail(Request $request): array
    {
        try {
            $start_date = $request->start ?? Carbon::now()->startOfMonth();
            $end_date = $request->end ?? Carbon::now()->endOfMonth();
            $events = [];
            if (!$request->has('user_email')) {
                throw new Exception('User Email Not Found!');
            }
            $user_events = CalEventGuest::where('user_email', $request->user_email)->whereHas('event', function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('event_start_date', [$start_date, $end_date])->where('event_visibility', 'public');
            })->with('event', function ($query) use ($start_date, $end_date) {
                return $query->whereBetween('event_start_date', [$start_date, $end_date])->where('event_visibility', 'public');
            })->when($request->filter_office_ids, function ($q) use ($request) {
                return $q->whereNotIn('user_office_id', explode(',', $request->filter_office_ids));
            })->get()->toArray();
            foreach ($user_events as $user_event) {
                if ($user_event['event']) {
                    $event['backgroundColor'] = $user_event['tag_color'];
                    $event['className'] = 'fc-event-danger';
                    $event['event_id'] = $user_event['event']['id'];
                    $event['title'] = $user_event['event']['event_title_en'];
                    $event['start'] = $user_event['event']['event_start_date_time'];
                    $event['end'] = $user_event['event']['event_end_date_time'];
                    $event['description'] = $user_event['event']['event_description'];
                    $event['location'] = $user_event['event']['event_location'];
                    $event['previous_link'] = $user_event['event']['event_previous_link'];
                    $event['office_id'] = $user_event['user_office_id'];
                    $events[] = $event;
                }
            }

            return responseFormat('success', $events);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }

    public function loadEventsByOfficerId(Request $request): array
    {

        try {
            $start_date = $request->start ?? Carbon::now()->startOfMonth();
            $end_date = $request->end ?? Carbon::now()->endOfMonth();
            $events = [];
            $event = [];

            $user_officer_id = $request->has('user_officer_id') ? $request->user_officer_id : $this->getOfficerId();
            $user_office_id = $request->has('user_office_id') ? $request->user_office_id : $this->current_office_id();

            if ($request->filter_office_ids) {
                $user_events = CalEventGuest::where(function ($query) use ($request) {
                    $query->whereNotIn('user_office_id', explode(',', $request->filter_office_ids))->where('user_type', 'organizer');
                })->whereHas('event', function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('event_start_date', [$start_date, $end_date]);
                })->with('event', function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('event_start_date', [$start_date, $end_date]);
                })->get();
                $user_events = $user_events->toArray();
            } else {
                $user_public_events = CalEventGuest::where('user_office_id', $user_office_id)->whereHas('event', function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('event_start_date', [$start_date, $end_date])->where('event_visibility', 'public');
                })->with('event', function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('event_start_date', [$start_date, $end_date])->where('event_visibility', 'public');
                })->get();
                $user_events = CalEventGuest::where('user_officer_id', $user_officer_id)->whereHas('event', function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('event_start_date', [$start_date, $end_date]);
                })->with('event', function ($query) use ($start_date, $end_date) {
                    return $query->whereBetween('event_start_date', [$start_date, $end_date]);
                })->get();

                $user_events = $user_events->merge($user_public_events);
                $user_events = $user_events->toArray();
            }

            $unique_event_flag = 1;
            foreach ($user_events as $user_event) {
                $event['backgroundColor'] = $user_event['tag_color'];
                $event['className'] = 'fc-event-danger';
                $event['event_id'] = $user_event['event']['id'];
                $event['title'] = $user_event['event']['event_title_en'];
                $event['start'] = $user_event['event']['event_start_date_time'];
                $event['end'] = $user_event['event']['event_end_date_time'];
                $event['description'] = $user_event['event']['event_description'];
                $event['location'] = $user_event['event']['event_location'];
                $event['previous_link'] = $user_event['event']['event_previous_link'];
                $event['office_id'] = $user_event['user_office_id'];
                foreach ($events as $added_event) {
                    if ($added_event['event_id'] == $event['event_id']) {
                        $unique_event_flag = 0;
                    }
                }
                if ($unique_event_flag == 1) {
                    $events[] = $event;
                }
                $unique_event_flag = 1;
            }

            return responseFormat('success', $events);
        } catch (\Exception $exception) {
            return responseFormat('error', $exception->getMessage());
        }
    }
}
