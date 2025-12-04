<div>
    <x-slot:title>
        Email Templates | {{ branding()->productName() }}
    </x-slot>
    <x-settings.navbar />
    <div class="flex flex-col h-full gap-8 sm:flex-row">
        <x-settings.sidebar activeMenu="email_templates" />
        <form wire:submit='submit' class="flex flex-col w-full">
            <div class="flex items-center gap-2">
                <h2>Email Templates</h2>
                <x-forms.button canGate="update" :canResource="$settings" type="submit">
                    Save
                </x-forms.button>
                @if (is_transactional_emails_enabled() && isInstanceAdmin())
                    <x-modal-input buttonTitle="Test Email" title="Send Test Email">
                        <form wire:submit.prevent="sendTestEmail" class="flex flex-col w-full gap-2">
                            <x-forms.input wire:model="test_email_address" placeholder="test@example.com" id="test_email_address"
                                label="Recipient Email" required />
                            <p class="text-sm dark:text-neutral-400">
                                This will send a test email using your current email template settings.
                            </p>
                            <x-forms.button type="submit" @click="modalOpen=false">
                                Send Test Email
                            </x-forms.button>
                        </form>
                    </x-modal-input>
                @endif
            </div>
            <div class="pb-4">
                Customize the content of transactional emails sent by {{ branding()->productName() }}. 
                Templates support Markdown formatting and placeholders.
            </div>

            <div class="flex flex-col gap-6">
                <!-- Template Selector -->
                <div class="flex flex-col gap-4">
                    <h3 class="text-lg font-semibold dark:text-white">Select Template</h3>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" wire:click="selectTemplate('test')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'test' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Test Email
                        </button>
                        <button type="button" wire:click="selectTemplate('invitation')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'invitation' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Invitation
                        </button>
                        <button type="button" wire:click="selectTemplate('password_reset')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'password_reset' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Password Reset
                        </button>
                        <button type="button" wire:click="selectTemplate('email_verification')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'email_verification' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Email Verification
                        </button>
                        <button type="button" wire:click="selectTemplate('deployment_success')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'deployment_success' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Deployment Success
                        </button>
                        <button type="button" wire:click="selectTemplate('deployment_failed')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'deployment_failed' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Deployment Failed
                        </button>
                        <button type="button" wire:click="selectTemplate('backup_success')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'backup_success' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Backup Success
                        </button>
                        <button type="button" wire:click="selectTemplate('backup_failed')"
                            class="px-4 py-2 rounded-md border transition-colors {{ $selected_template === 'backup_failed' ? 'bg-coollabs text-white border-coollabs' : 'bg-white dark:bg-coolgray-200 border-neutral-300 dark:border-coolgray-200' }}">
                            Backup Failed
                        </button>
                    </div>
                </div>

                <!-- Template Editor -->
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold dark:text-white">
                            @if ($selected_template === 'test')
                                Test Email Template
                            @elseif ($selected_template === 'invitation')
                                Invitation Email Template
                            @elseif ($selected_template === 'password_reset')
                                Password Reset Template
                            @elseif ($selected_template === 'email_verification')
                                Email Verification Template
                            @elseif ($selected_template === 'deployment_success')
                                Deployment Success Template
                            @elseif ($selected_template === 'deployment_failed')
                                Deployment Failed Template
                            @elseif ($selected_template === 'backup_success')
                                Backup Success Template
                            @elseif ($selected_template === 'backup_failed')
                                Backup Failed Template
                            @endif
                        </h3>
                        <x-forms.button type="button" wire:click="resetTemplate('{{ $selected_template }}')"
                            class="bg-neutral-200 dark:bg-coolgray-200 hover:bg-neutral-300 dark:hover:bg-coolgray-300">
                            Reset to Default
                        </x-forms.button>
                    </div>

                    <!-- Placeholders Info -->
                    <div class="p-4 border rounded dark:border-coolgray-200 bg-neutral-50 dark:bg-coolgray-100">
                        <p class="text-sm font-semibold dark:text-white mb-2">Available Placeholders:</p>
                        <div class="text-xs dark:text-neutral-400 space-y-1">
                            @if ($selected_template === 'invitation')
                                <div><code>{team_name}</code> - Team name</div>
                                <div><code>{product_name}</code> - Product name</div>
                                <div><code>{invitation_link}</code> - Invitation acceptance link</div>
                            @elseif ($selected_template === 'password_reset')
                                <div><code>{reset_link}</code> - Password reset link</div>
                                <div><code>{expire_minutes}</code> - Link expiration time</div>
                            @elseif ($selected_template === 'email_verification')
                                <div><code>{verification_link}</code> - Email verification link</div>
                            @elseif ($selected_template === 'deployment_success' || $selected_template === 'deployment_failed')
                                <div><code>{application_name}</code> - Application name</div>
                                <div><code>{environment}</code> - Environment name</div>
                                <div><code>{application_url}</code> - Application URL</div>
                                <div><code>{logs_url}</code> - Deployment logs URL</div>
                            @elseif ($selected_template === 'backup_success' || $selected_template === 'backup_failed')
                                <div><code>{database_name}</code> - Database name</div>
                                <div><code>{backup_size}</code> - Backup file size</div>
                            @else
                                <div><code>{product_name}</code> - Product name</div>
                            @endif
                            <div class="mt-2 pt-2 border-t dark:border-coolgray-300">
                                <strong>Common placeholders:</strong><br>
                                <code>{product_name}</code> - Product name<br>
                                <code>{support_url}</code> - Support email or docs URL<br>
                                <code>{docs_url}</code> - Documentation URL<br>
                                <code>{marketing_url}</code> - Marketing site URL
                            </div>
                        </div>
                    </div>

                    <!-- Template Editor -->
                    @if ($selected_template === 'test')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="test_email_template"
                            label="Test Email Template" wire:model="test_email_template"
                            placeholder="{{ $this->getDefaultTemplate('test') }}"
                            helper="Customize the test email template. Leave empty to use default. Supports Markdown." rows="8" />
                    @elseif ($selected_template === 'invitation')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="invitation_email_template"
                            label="Invitation Email Template" wire:model="invitation_email_template"
                            placeholder="{{ $this->getDefaultTemplate('invitation') }}"
                            helper="Customize the invitation email template. Leave empty to use default. Supports Markdown." rows="10" />
                    @elseif ($selected_template === 'password_reset')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="password_reset_template"
                            label="Password Reset Template" wire:model="password_reset_template"
                            placeholder="{{ $this->getDefaultTemplate('password_reset') }}"
                            helper="Customize the password reset email template. Leave empty to use default. Supports Markdown." rows="10" />
                    @elseif ($selected_template === 'email_verification')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="email_verification_template"
                            label="Email Verification Template" wire:model="email_verification_template"
                            placeholder="{{ $this->getDefaultTemplate('email_verification') }}"
                            helper="Customize the email verification template. Leave empty to use default. Supports Markdown." rows="10" />
                    @elseif ($selected_template === 'deployment_success')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="deployment_success_template"
                            label="Deployment Success Template" wire:model="deployment_success_template"
                            placeholder="{{ $this->getDefaultTemplate('deployment_success') }}"
                            helper="Customize the deployment success email template. Leave empty to use default. Supports Markdown." rows="12" />
                    @elseif ($selected_template === 'deployment_failed')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="deployment_failed_template"
                            label="Deployment Failed Template" wire:model="deployment_failed_template"
                            placeholder="{{ $this->getDefaultTemplate('deployment_failed') }}"
                            helper="Customize the deployment failed email template. Leave empty to use default. Supports Markdown." rows="12" />
                    @elseif ($selected_template === 'backup_success')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="backup_success_template"
                            label="Backup Success Template" wire:model="backup_success_template"
                            placeholder="{{ $this->getDefaultTemplate('backup_success') }}"
                            helper="Customize the backup success email template. Leave empty to use default. Supports Markdown." rows="10" />
                    @elseif ($selected_template === 'backup_failed')
                        <x-forms.textarea canGate="update" :canResource="$settings" id="backup_failed_template"
                            label="Backup Failed Template" wire:model="backup_failed_template"
                            placeholder="{{ $this->getDefaultTemplate('backup_failed') }}"
                            helper="Customize the backup failed email template. Leave empty to use default. Supports Markdown." rows="10" />
                    @endif

                    <!-- Preview Note -->
                    <div class="p-4 border rounded dark:border-coolgray-200 bg-blue-50 dark:bg-blue-900/20">
                        <p class="text-sm dark:text-neutral-300">
                            <strong>Note:</strong> Email templates use the header and footer configured in 
                            <a href="{{ route('settings.branding') }}" class="text-coollabs underline">Branding Settings</a>. 
                            The template content above will be inserted between the header and footer.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
