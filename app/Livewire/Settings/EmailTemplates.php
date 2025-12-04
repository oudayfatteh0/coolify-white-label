<?php

namespace App\Livewire\Settings;

use App\Models\InstanceSettings;
use App\Models\Team;
use App\Notifications\TransactionalEmails\Test;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class EmailTemplates extends Component
{
    public InstanceSettings $settings;

    #[Validate('nullable|string|max:2000')]
    public ?string $test_email_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $invitation_email_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $password_reset_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $email_verification_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $deployment_success_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $deployment_failed_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $backup_success_template = null;

    #[Validate('nullable|string|max:2000')]
    public ?string $backup_failed_template = null;

    public ?string $selected_template = 'test';

    #[Validate('nullable|email')]
    public ?string $test_email_address = null;

    public Team $team;

    public function mount()
    {
        if (! isInstanceAdmin()) {
            return redirect()->route('dashboard');
        }
        $this->settings = instanceSettings();
        $this->team = auth()->user()->currentTeam();
        $this->test_email_address = auth()->user()->email;

        $templates = $this->settings->email_templates ?? [];

        $this->test_email_template = $templates['test'] ?? null;
        $this->invitation_email_template = $templates['invitation'] ?? null;
        $this->password_reset_template = $templates['password_reset'] ?? null;
        $this->email_verification_template = $templates['email_verification'] ?? null;
        $this->deployment_success_template = $templates['deployment_success'] ?? null;
        $this->deployment_failed_template = $templates['deployment_failed'] ?? null;
        $this->backup_success_template = $templates['backup_success'] ?? null;
        $this->backup_failed_template = $templates['backup_failed'] ?? null;
    }

    public function selectTemplate(string $template)
    {
        $this->selected_template = $template;
    }

    public function getDefaultTemplate(string $template): string
    {
        return match ($template) {
            'test' => 'If you are seeing this, it means that your Email settings are correct.',
            'invitation' => "You have been invited to \"{team_name}\" on \"{product_name}\".\n\nPlease [click here]({invitation_link}) to accept the invitation.\n\nIf you have any questions, please contact the team owner.\n\nIf it was not you who requested this invitation, please ignore this email.",
            'password_reset' => "You are receiving this email because we received a password reset request for your account.\n\n[Reset Password]({reset_link})\n\nThis password reset link will expire in {expire_minutes} minutes.\n\nIf you did not request a password reset, no further action is required.",
            'email_verification' => "Please verify your email address by clicking the link below:\n\n[Verify Email]({verification_link})\n\nIf you did not create an account, no further action is required.",
            'deployment_success' => "Your deployment for **{application_name}** was successful!\n\n**Deployment Details:**\n- Application: {application_name}\n- Environment: {environment}\n- Status: ✅ Success\n\nView your application: [Open Application]({application_url})",
            'deployment_failed' => "Your deployment for **{application_name}** has failed.\n\n**Deployment Details:**\n- Application: {application_name}\n- Environment: {environment}\n- Status: ❌ Failed\n\nPlease check the deployment logs for more information: [View Logs]({logs_url})",
            'backup_success' => "Your backup for **{database_name}** was completed successfully.\n\n**Backup Details:**\n- Database: {database_name}\n- Size: {backup_size}\n- Status: ✅ Success",
            'backup_failed' => "Your backup for **{database_name}** has failed.\n\n**Backup Details:**\n- Database: {database_name}\n- Status: ❌ Failed\n\nPlease check your backup configuration and try again.",
            default => '',
        };
    }

    public function resetTemplate(string $template)
    {
        $property = match ($template) {
            'test' => 'test_email_template',
            'invitation' => 'invitation_email_template',
            'password_reset' => 'password_reset_template',
            'email_verification' => 'email_verification_template',
            'deployment_success' => 'deployment_success_template',
            'deployment_failed' => 'deployment_failed_template',
            'backup_success' => 'backup_success_template',
            'backup_failed' => 'backup_failed_template',
            default => null,
        };

        if ($property) {
            $this->$property = null;
        }
    }

    public function sendTestEmail()
    {
        try {
            if (! is_transactional_emails_enabled()) {
                throw new \Exception('Transactional emails are not enabled. Please configure SMTP or Resend settings first.');
            }
            $this->validate([
                'test_email_address' => 'required|email',
            ], [
                'test_email_address.required' => 'Test email address is required.',
                'test_email_address.email' => 'Please enter a valid email address.',
            ]);

            $executed = RateLimiter::attempt(
                'test-email:'.$this->team->id,
                $perMinute = 0,
                function () {
                    $this->team?->notifyNow(new Test($this->test_email_address));
                    $this->dispatch('success', 'Test Email sent.');
                },
                $decaySeconds = 10,
            );

            if (! $executed) {
                throw new \Exception('Too many messages sent! Please wait a few seconds before trying again.');
            }
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function submit()
    {
        try {
            $this->validate();

            $templates = [
                'test' => $this->test_email_template,
                'invitation' => $this->invitation_email_template,
                'password_reset' => $this->password_reset_template,
                'email_verification' => $this->email_verification_template,
                'deployment_success' => $this->deployment_success_template,
                'deployment_failed' => $this->deployment_failed_template,
                'backup_success' => $this->backup_success_template,
                'backup_failed' => $this->backup_failed_template,
            ];

            // Remove null values to keep JSON clean
            $templates = array_filter($templates, fn ($value) => $value !== null && $value !== '');

            $this->settings->email_templates = $templates;
            $this->settings->save();

            // Clear email templates cache
            \Cache::forget('instance_settings_email_templates');

            $this->dispatch('success', 'Email templates updated successfully!');
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function render()
    {
        return view('livewire.settings.email-templates');
    }
}
