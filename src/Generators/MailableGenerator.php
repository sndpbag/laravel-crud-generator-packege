<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MailableGenerator extends BaseGenerator
{
    public function generate(): array
    {
        $mailablePath = $this->generateMailable();
        $viewPath = $this->generateEmailView();

        return [
            'success' => true,
            'path' => $mailablePath,
            'view_path' => $viewPath
        ];
    }

    protected function generateMailable()
    {
        $stub = $this->getStub('mailable'); // stubs/mailable.stub
        $modelName = $this->options['modelName'];
        $modelNamespace = $this->getFullNamespace('model');
        $mailableName = "{$modelName}CreatedMailable";
        $mailableNamespace = $this->getFullNamespace('mail'); // config-এ যোগ করতে হবে

        $stub = str_replace('{{namespace}}', $mailableNamespace, $stub);
        $stub = str_replace('{{class}}', $mailableName, $stub);
        $stub = str_replace('{{modelImport}}', "use {$modelNamespace}\\{$modelName};", $stub);
        $stub = str_replace('{{modelName}}', $modelName, $stub);
        $stub = str_replace('{{modelVariable}}', $this->getModelVariable(), $stub);
        $stub = str_replace('{{viewPath}}', $this->getEmailViewPath(), $stub);

        $path = app_path("Mail/" . str_replace('\\', '/', $this->options['namespace']) . "/{$mailableName}.php");
        $this->ensureDirectoryExists(dirname($path));
        File::put($path, $stub);
        return $path;
    }

    protected function generateEmailView()
    {
        $stub = $this->getStub('views/mail/email'); // stubs/views/mail/email.stub

        $stub = str_replace('{{modelName}}', $this->options['modelName'], $stub);
        $stub = str_replace('{{modelVariable}}', $this->getModelVariable(), $stub);
        $stub = str_replace('{{routePath}}', $this->getRoutePath(), $stub);

        $viewName = Str::snake($this->options['modelName']);
        $namespacePath = strtolower(str_replace('\\', '/', $this->options['namespace']));
        $path = $namespacePath ? "{$namespacePath}/{$viewName}" : $viewName;

        $fullPath = resource_path('views/emails/' . $path . '.blade.php');
        $this->ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, $stub);
        return $fullPath;
    }

    // ইমেল ভিউ পাথ (e.g., 'emails.admin.test_profile')
    protected function getEmailViewPath()
    {
        $viewName = Str::snake($this->options['modelName']);
        $namespacePath = strtolower(str_replace('\\', '/', $this->options['namespace']));
        $path = $namespacePath ? "{$namespacePath}.{$viewName}" : $viewName;
        return 'emails.' . $path;
    }
}