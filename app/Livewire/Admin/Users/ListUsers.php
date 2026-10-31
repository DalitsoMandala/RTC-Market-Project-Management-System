<?php

namespace App\Livewire\Admin\Users;

use App\Mail\SendEmails;
use App\Models\Organisation;
use App\Models\User;
use App\Notifications\NewUserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class ListUsers extends Component
{
    // ---------------------------------------------------------------------
    // Add / edit user form
    // ---------------------------------------------------------------------
    public $name;
    public $email;
    public $phone;
    public $organisation;
    public $role;
    public $rowId;
    public $password;
    public $password_confirmation;
    public $changePassword = true;

    // Read-only data for the browser (cannot be tampered with from the client)
    #[Locked]
    public array $roles = [];
    #[Locked]
    public array $organisations = [];

    // ---------------------------------------------------------------------
    // Send-email composer
    // ---------------------------------------------------------------------
    public $subject;
    public $message;

    /** IDs of the users who will receive the email (managed by the browser). */
    public array $recipientIds = [];

    /** Every active user, used by the browser for the @-mention / role pickers. */
    #[Locked]
    public array $allUsers = [];

    // =====================================================================
    // Setup
    // =====================================================================

    public function mount()
    {
        $this->roles = Role::pluck('name')->toArray();
        $this->organisations = Organisation::get(['id', 'name'])->toArray();
        $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.admin.users.list-users');
    }

    private function loadUsers(): void
    {
        $this->allUsers = User::with(['roles', 'organisation'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()?->name,
                'organisation' => $user->organisation?->name,
            ])
            ->all();
    }

    /** Call after anything that changes users: reloads the picker list and refreshes the table/modals. */
    private function refreshAll(): void
    {
        $this->loadUsers();
        $this->dispatch('refresh');
    }

    // =====================================================================
    // Add / edit / delete / restore a single user
    // =====================================================================

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->rowId)],
            'phone' => 'nullable|string|max:40',
            'organisation' => 'required|exists:organisations,id',
            'role' => ['required', Rule::in($this->roles)],
        ];

        if ($this->changePassword) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        return $rules;
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->reset([
            'name',
            'email',
            'phone',
            'organisation',
            'role',
            'rowId',
            'password',
            'password_confirmation',
        ]);
        $this->changePassword = true;
        $this->resetErrorBag();
    }

    #[On('showModal-edit')]
    public function edit($rowId)
    {
        $this->resetForm();

        $user = User::with(['roles', 'organisation'])->findOrFail($rowId);

        $this->rowId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone_number;
        $this->organisation = $user->organisation_id;
        $this->role = $user->roles->first()?->name;
        $this->changePassword = false;
    }

    public function save()
    {
        $this->validate();

        $isNew = ! $this->rowId;

        try {
            $user = DB::transaction(function () {
                $data = [
                    'name' => $this->name,
                    'email' => strtolower(trim($this->email)),
                    'phone_number' => $this->phone,
                    'organisation_id' => $this->organisation,
                ];

                if ($this->changePassword && $this->password) {
                    $data['password'] = Hash::make($this->password);
                }

                if ($this->rowId) {
                    $user = User::findOrFail($this->rowId);
                    $user->update($data);
                } else {
                    $user = User::create($data);
                }

                $user->syncRoles($this->role);

                return $user;
            });
        } catch (\Throwable $e) {
            Log::error($e);
            session()->flash('error', 'An error occurred while saving the user.');
            return;
        }

        // Notify only after the transaction has been committed.
        if ($isNew) {
            $this->notifyNewUser($user, $this->password, $this->role);
        }

        session()->flash('success', $isNew ? 'User successfully added!' : 'User successfully updated!');
        $this->resetForm();
        $this->refreshAll();
    }

    #[On('showModal-delete')]
    public function delete($rowId)
    {
        $this->rowId = $rowId;
    }

    public function deleteUser()
    {
        User::where('id', $this->rowId)->update(['is_active' => false]);

        session()->flash('success', 'User successfully deleted!');
        $this->rowId = null;
        $this->refreshAll();
    }

    #[On('showModal-restore')]
    public function restore($rowId)
    {
        $this->rowId = $rowId;
    }

    public function restoreUser()
    {
        User::where('id', $this->rowId)->update(['is_active' => true]);

        session()->flash('success', 'User successfully restored!');
        $this->rowId = null;
        $this->refreshAll();
    }

    private function notifyNewUser(User $user, string $password, string $role): void
    {
        try {
            $user->notify(new NewUserNotification($user->email, $password, $role));
        } catch (\Throwable $e) {
            Log::error($e);
        }
    }

    // =====================================================================
    // Import users from Excel
    // =====================================================================

    public function noFile()
    {
        session()->flash('error', 'Please choose a file to upload.');
    }

    /** Called from the browser with the rows read from the spreadsheet. */
    public function importUsers(array $rows)
    {
        if (empty($rows)) {
            session()->flash('error', 'No data found in the file.');
            $this->dispatch('refresh');
            return;
        }

        $rows = array_map(fn($row) => [
            'email' => strtolower(trim((string) ($row['email'] ?? ''))),
            'name' => trim((string) ($row['name'] ?? '')),
            'organisation' => trim((string) ($row['organisation'] ?? '')),
            'role' => trim((string) ($row['role'] ?? '')),
        ], $rows);

        $validator = Validator::make($rows, [
            '*.email' => 'required|email|distinct|unique:users,email',
            '*.name' => 'required|string|max:255',
            '*.organisation' => 'required|exists:organisations,name',
            '*.role' => ['required', Rule::in($this->roles)],
        ]);

        if ($validator->fails()) {
            $lines = [];

            foreach ($validator->errors()->messages() as $field => $errors) {
                // "3.email" => spreadsheet row 5 (index starts at 0, row 1 is the header)
                $spreadsheetRow = (int) explode('.', $field)[0] + 2;
                $cleanErrors = preg_replace('/\b\d+\./', '', implode(' ', $errors));
                $lines[] = "Row {$spreadsheetRow}: {$cleanErrors}";
            }

            session()->flash('error', implode('<br>', $lines));
            $this->dispatch('refresh');
            return;
        }

        try {
            $organisationIds = Organisation::pluck('id', 'name');

            $created = DB::transaction(function () use ($rows, $organisationIds) {
                return collect($rows)->map(function ($row) use ($organisationIds) {
                    $password = Str::random(10);

                    $user = User::create([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'phone_number' => '+9999999999', // placeholder: the template has no phone column
                        'organisation_id' => $organisationIds[$row['organisation']],
                        'password' => Hash::make($password),
                    ]);
                    $user->assignRole($row['role']);

                    return [$user, $password, $row['role']];
                });
            });
        } catch (\Throwable $e) {
            Log::error($e);
            session()->flash('error', 'An error occurred while importing users.');
            $this->dispatch('refresh');
            return;
        }

        foreach ($created as [$user, $password, $role]) {
            $this->notifyNewUser($user, $password, $role);
        }

        session()->flash('success', $created->count() . ' user(s) imported successfully!');
        $this->refreshAll();
    }

    // =====================================================================
    // Send email
    // =====================================================================

    public function sendEmails()
    {
        try {
            $this->validate(
                [
                    'subject' => 'required|string|max:255',
                    'message' => 'required|string',
                    'recipientIds' => 'required|array|min:1',
                    'recipientIds.*' => 'integer',
                ],
                [],
                ['recipientIds' => 'recipients'],
            );
        } catch (ValidationException $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        // Never trust names/emails from the browser: load them from the database.
        $users = User::whereIn('id', $this->recipientIds)
            ->where('is_active', true)
            ->get();

        $failed = 0;



        foreach ($users as $user) {
            try {
                $mail = Mail::to($user->email);

                if ($debugEmail = config('app.debug_email')) {
                    $mail->bcc($debugEmail);
                }

                $mail->send(new SendEmails($this->subject, $this->message, $user->name));
            } catch (\Throwable $e) {
                $failed++;
                Log::error($e);
            }
        }

        $sent = $users->count() - $failed;

        if ($sent === 0) {
            session()->flash('error', 'No emails could be sent. Please check the logs.');
            return;
        }

        session()->flash(
            'success',
            $failed
                ? "Email sent to {$sent} user(s); {$failed} failed (see logs)."
                : "Email sent to {$sent} user(s)."
        );

        $this->reset(['subject', 'message', 'recipientIds']);
        $this->dispatch('emails-sent'); // lets the browser clear the editor
    }
}
