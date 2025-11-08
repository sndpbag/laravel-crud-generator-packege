<?php
namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log; // <-- Log ইম্পোর্ট করুন
use Illuminate\Support\Str;

class JobGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $stub = $this->getStub('job');
        $modelName = $this->options['modelName'];
        $jobName = "Send{$modelName}CreatedEmailJob";
        $jobNamespace = $this->getFullNamespace('job');

        $modelNamespace = $this->getFullNamespace('model');
        $mailableName = "{$modelName}CreatedMailable";
        $mailableNamespace = $this->getFullNamespace('mail');

        // ডায়নামিক লজিক জেনারেট করুন
        $recipientLogic = $this->generateRecipientLogic();
        $emailFieldFound = $this->findEmailField(); // ইমেল ফিল্ড আছে কিনা দেখুন

        $stub = str_replace('{{namespace}}', $jobNamespace, $stub);
        $stub = str_replace('{{class}}', $jobName, $stub);
        $stub = str_replace('{{modelImport}}', "use {$modelNamespace}\\{$modelName};", $stub);
        $stub = str_replace('{{mailableImport}}', "use {$mailableNamespace}\\{$mailableName};", $stub);
        $stub = str_replace('{{mailableName}}', $mailableName, $stub);
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{modelVariable}}', $this->getModelVariable(), $stub);
        $stub = str_replace('{{recipientLogic}}', $recipientLogic, $stub); // <-- লজিক রিপ্লেস করুন

        // যদি ডায়নামিক লজিক ব্যবহার হয়, তবে Log::class ইম্পোর্ট করুন
        if ($emailFieldFound) {
            $stub = str_replace(
                'use Illuminate\Queue\SerializesModels;',
                "use Illuminate\Queue\SerializesModels;\nuse Illuminate\Support\Facades\Log;",
                $stub
            );
        }

        $path = app_path("Jobs/" . str_replace('\\', '/', $this->options['namespace']) . "/{$jobName}.php");
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);

        return ['success' => true, 'path' => $path];
    }

    /**
     * ফিল্ডস অ্যারে থেকে প্রথম ইমেল ফিল্ডটি খোঁজে
     */
    private function findEmailField(): ?string
    {
        foreach ($this->options['fields'] as $field) {
            if ($field['type'] === 'email') {
                return $field['name'];
            }
        }
        return null;
    }

    /**
     * handle() মেথডের জন্য ডায়নামিক পিএইচপি কোড জেনারেট করে
     */
    private function generateRecipientLogic(): string
    {
        $emailField = $this->findEmailField();
        $modelVariable = $this->getModelVariable();
        $mailableName = $this->options['modelName'] . 'CreatedMailable';

        if ($emailField) {
            // যদি 'email' টাইপের ফিল্ড পাওয়া যায়
            return <<<PHP
            // ডায়নামিকভাবে মডেলের '{$emailField}' ফিল্ড থেকে ইমেল নেওয়া হচ্ছে
            \$recipientEmail = \$this->{$modelVariable}->{$emailField};
            
            if (\$recipientEmail) {
                \$mailable = new {$mailableName}(\$this->{$modelVariable});
                Mail::to(\$recipientEmail)->send(\$mailable);
            } else {
                Log::warning("Email field '{$emailField}' is empty for {$this->options['modelName']} ID: {\$this->{$modelVariable}}->id.");
            }
PHP;
        } else {
            // যদি কোনো 'email' ফিল্ড না পাওয়া যায়
            return <<<PHP
            // কোনো ইমেল ফিল্ড পাওয়া যায়নি, অ্যাডমিন ইমেলে পাঠানো হচ্ছে
            \$recipientEmail = config('crud-generator.admin_email', 'admin@example.com');
            
            \$mailable = new {$mailableName}(\$this->{$modelVariable});
            Mail::to(\$recipientEmail)->send(\$mailable);
PHP;
        }
    }
}