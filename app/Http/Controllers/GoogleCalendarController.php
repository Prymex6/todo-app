<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime as Google_Service_Calendar_EventDateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use phpDocumentor\Reflection\PseudoTypes\True_;

class GoogleCalendarController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar',
                'https://www.googleapis.com/auth/calendar.events'
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->with(['access_type' => 'offline', 'prompt' => 'consent'])
                ->user();

            $user = Auth::user();
            $user->update([
                'google_id' => $googleUser->id,
                'google_access_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_expires_at' => now()->addSeconds($googleUser->expiresIn),
            ]);
    
            return redirect()->route('tasks.index')
            ->with('success', 'Successfully connected with Google Calendar!');
    
        } catch (\Exception $e) {
            \Log::error('Google Auth Error: '.$e->getMessage());
            return redirect('/login')->with('error', 'Błąd autoryzacji Google');
        }
    }

    protected function getGoogleClient(User $user): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($user->google_access_token);
        // $client->setRefreshToken($user->google_refresh_token);
        $client->addScope(GoogleCalendar::CALENDAR_EVENTS);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $user->update([
                'google_access_token' => json_encode($client->getAccessToken()),
                'google_expires_at' => now()->addSeconds(3600),
            ]);
        }

        return $client;
    }

    public function syncTask(Task $task, string $action = 'create'): bool
    {
        try {
            $user = $task->user;
            
            if (!$user->google_refresh_token) {
                throw new \Exception('User not connected to Google Calendar');
            }
    
            $client = $this->getGoogleClient($user);
            $service = new GoogleCalendar($client);
    
            $eventData = [
                'summary' => $task->name,
                'description' => $task->description,
                'start' => ['dateTime' => $task->due_date->toIso8601String()],
                'end' => ['dateTime' => $task->due_date->addHour()->toIso8601String()],
            ];
            
            if ($action == 'create' || !$task->google_calendar_event_id) {
                // Tworzenie nowego wydarzenia
                $event = new GoogleEvent($eventData);
                $createdEvent = $service->events->insert('primary', $event);
                $task->update(['google_calendar_event_id' => $createdEvent->getId(), 'sync_with_google_calendar' => true]);
            } else {
                // Aktualizacja istniejącego wydarzenia
                $event = $service->events->get('primary', $task->google_calendar_event_id);
                
                // Aktualizuj tylko zmienione pola
                $event->setSummary($task->name);
                $event->setDescription($task->description);
                $event->setStart(new Google_Service_Calendar_EventDateTime([
                    'dateTime' => $task->due_date->toIso8601String()
                ]));
                $event->setEnd(new Google_Service_Calendar_EventDateTime([
                    'dateTime' => $task->due_date->addHour()->toIso8601String()
                ]));
                
                $updatedEvent = $service->events->update('primary', $event->getId(), $event);
            }
    
            return true;
    
        } catch (\Exception $e) {
            logger()->error('Google Calendar sync failed: ' . $e->getMessage());
            throw new \Exception('Failed to sync with Google Calendar: ' . $e->getMessage());
        }
    }

    public function deleteEvent(Task $task): bool
    {
        try {
            $user = $task->user; // Zakładam, że task należy do usera
            if (!$user->google_refresh_token || !$task->google_calendar_event_id) {
                return false;
            }

            $client = $this->getGoogleClient($user);
            $service = new GoogleCalendar($client);

            $service->events->delete('primary', $task->google_calendar_event_id);

            // Wyczyść dane w tasku
            $task->update([
                'google_event_id' => null,
                'sync_with_google_calendar' => false
            ]);

            return true;

        } catch (\Exception $e) {
            logger()->error('Google Calendar delete failed: ' . $e->getMessage());
            return false;
        }
    }
}