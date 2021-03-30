<?php

declare (strict_types = 1);

namespace Laket\Admin\Command;

use think\helper\Arr;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\console\Table;

use Laket\Admin\Support\File;

/**
 * 推送
 *
 * php think laket-admin:publish --tag=tag-name
 *
 * @create 2021-3-26
 * @author deatil
 */
class Publish extends Command
{
    /**
     * The provider to publish.
     *
     * @var string
     */
    protected $provider = null;

    /**
     * The tags to publish.
     *
     * @var array
     */
    protected $tags = [];
    
    /**
     * 配置
     */
    protected function configure()
    {
        $this
            ->setName('laket-admin:publish')
            ->addOption('force', null, Option::VALUE_NONE, 'force')
            ->addOption('all', null, Option::VALUE_NONE, 'all')
            ->addOption('provider', null, Option::VALUE_OPTIONAL, 'provider')
            ->addOption('tag', null, Option::VALUE_OPTIONAL, 'tag')
            ->setDescription('Publish any publishable assets from vendor packages');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $this->determineWhatShouldBePublished();

        foreach ($this->tags ?: [null] as $tag) {
            $this->publishTag($tag);
        }

        $output->info('Publishing complete.');
    }

    /**
     * Determine the provider or tag(s) to publish.
     *
     * @return void
     */
    protected function determineWhatShouldBePublished()
    {
        if ($this->input->getOption('all')) {
            return;
        }

        [$this->provider, $this->tags] = [
            $this->input->getOption('provider'), 
            (array) $this->input->getOption('tag'),
        ];

        if (! $this->provider && ! $this->tags) {
            $this->promptForProviderOrTag();
        }
    }

    /**
     * Prompt for which provider or tag to publish.
     *
     * @return void
     */
    protected function promptForProviderOrTag()
    {
        $choice = $this->output->choice(
            $this->input,
            "Which provider or tag's files would you like to publish?",
            $choices = $this->publishableChoices()
        );

        if ($choice == $choices[0] || is_null($choice)) {
            return;
        }

        $this->parseChoice($choice);
    }

    /**
     * The choices available via the prompt.
     *
     * @return array
     */
    protected function publishableChoices()
    {
        return array_merge(
            ['<comment>Publish files from all providers and tags listed below</comment>'],
            preg_filter('/^/', '<comment>Provider: </comment>', Arr::sort(app('laket-admin.publish')->publishableProviders())),
            preg_filter('/^/', '<comment>Tag: </comment>', Arr::sort(app('laket-admin.publish')->publishableGroups()))
        );
    }

    /**
     * Parse the answer that was given via the prompt.
     *
     * @param  string  $choice
     * @return void
     */
    protected function parseChoice($choice)
    {
        [$type, $value] = explode(': ', strip_tags($choice));

        if ($type === 'Provider') {
            $this->provider = $value;
        } elseif ($type === 'Tag') {
            $this->tags = [$value];
        }
    }

    /**
     * Publishes the assets for a tag.
     *
     * @param  string  $tag
     * @return mixed
     */
    protected function publishTag($tag)
    {
        $published = false;

        $pathsToPublish = $this->pathsToPublish($tag);

        foreach ($pathsToPublish as $from => $to) {
            $this->publishItem($from, $to);

            $published = true;
        }

        if ($published === false) {
            $this->output->error('Unable to locate publishable resources.');
        }
    }

    /**
     * Get all of the paths to publish.
     *
     * @param  string  $tag
     * @return array
     */
    protected function pathsToPublish($tag)
    {
        return app('laket-admin.publish')->pathsToPublish(
            $this->provider, $tag
        );
    }

    /**
     * Publish the given item from and to the given location.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishItem($from, $to)
    {
        if (is_file($from)) {
            return $this->publishFile($from, $to);
        } elseif (is_dir($from)) {
            return $this->publishDirectory($from, $to);
        }

        $this->output->error("Can't locate path: <{$from}>");
    }

    /**
     * Publish the file to the given path.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishFile($from, $to)
    {
        if (! file_exists($to) || $this->input->getOption('force')) {
            $this->createParentDirectory(dirname($to));

            copy($from, $to);

            $this->status($from, $to, 'File');
        }
    }

    /**
     * Publish the directory to the given directory.
     *
     * @param  string  $from
     * @param  string  $to
     * @return void
     */
    protected function publishDirectory($from, $to)
    {
        $this->createParentDirectory(dirname($to));
        
        File::copyDir($from, $to);

        $this->status($from, $to, 'Directory');
    }

    /**
     * Create the directory to house the published files if needed.
     *
     * @param  string  $directory
     * @return void
     */
    protected function createParentDirectory($directory)
    {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Write a status message to the console.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  string  $type
     * @return void
     */
    protected function status($from, $to, $type)
    {
        $from = str_replace(root_path(), '', realpath($from));

        $to = str_replace(root_path(), '', realpath($to));

        $this->output->writeln('<info>Copied '.$type.'</info> <comment>['.$from.']</comment> <info>To</info> <comment>['.$to.']</comment>');
    }
}
